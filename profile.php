<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION["account"]))
{
	$_SESSION["error"] = "Not logged in.";
	header('Location: index.php');
	return;
}

if(!isset($_GET['email']))
{
	$_SESSION['error'] = "Missing ID";
  	header('Location: index.php');
  	return;
}

if($_SESSION['isfac'] === true)
{
	$sql = "select * from faculty where email = :email";
}
else if($_SESSION['isfac'] === false)
{
	$sql = "select * from student where email = :email";
}
$stmt = $pdo->prepare($sql);
$stmt->execute(array(':email' => $_GET['email']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false)
{
	$_SESSION["error"] = "Bad email entered.";
	header('Location: index.php');
	return;
}

if($_SESSION['email'] != $row['email'])
{
	$_SESSION["error"] = "You dont have the permission to view someone else's profile.";
	header('Location: index.php');
	return;
}
$id = htmlentities($row['id']);
$name = htmlentities($row['name']);
$email = htmlentities($row['email']);

if(isset($_POST["id"]) && isset($_POST["name"]) && isset($_POST["email"]))
{
	if(strlen($_POST["id"]) < 1 || strlen($_POST["name"]) < 1 || strlen($_POST["email"]) < 1)
	{
		$_SESSION["error"] = "All fields are required.";
		header('Location: profile.php');
		return;
	}

	if(strpos($_POST["email"], "@") === false)
	{
		$_SESSION['error'] = "Invalid email address.";
		header('Location: profile.php');
		return;
	}

	if($_SESSION['isfac'] === true)
	{
		$sql = "update faculty set id = :id, name = :name, email = :email where email = :oldemail";
	}
	else
	{
		$sql = "update student set id = :id, name = :name, email = :email where email = :oldemail";	
	}

	$stmt = $pdo->prepare($sql);
	$stmt->execute(array(
		':id' => $_POST['id'],
		':name' => $_POST['name'],
		':email' => $_POST['email'],
		':oldemail' => $_SESSION['email']));
	$_SESSION['account'] = $_POST['name'];
	$_SESSION['id'] = $_POST['id'];
	$_SESSION['email'] = $_POST['email'];
	$_SESSION['success'] = "Profile updated.";
	header('Location: index.php');
	return;
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link rel = "stylesheet" type = "text/css" href = "styles.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script> 
   $(function(){
    $("#navbardiv").load("navbar.php");
   });  
</script>
<title>Profile</title>
</head>
<body class = "mainbody">
<div id="navbardiv"></div>
<form method="post" class = "login">
<p>ID</p><p><input type = "text" name = "id" value = "<?= $id ?>"></p>
<p>Name</p><p><input type = "text" name = "name" value = "<?= $name ?>"></p>
<p>Email</p><p><input type = "text" name = "email" value = "<?= $email ?>"></p>
<p style = "color: black; padding-bottom: 10px;"><input type = "submit" value = "Update"></p>
</form>
<form action = "resetpw.php" class = "resetpw" method = "post">
<input type = "hidden" name = "resetpw" value = "<?= $_SESSION['email'] ?>">
<p style = "color: black; padding-bottom: 10px;"><input type = "submit" value = "Reset Password"></p>
</form>
<p style="text-align: center;"><a href = "index.php" style="color: red;">Cancel update</a></p>
</body>
</html>