<?php
session_start();
require_once "pdo.php";
$DUPLICATE_ENTRY_CODE = 1062;
if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['confirm_password']))
{
	if(strlen($_POST['email']) < 1 || strlen($_POST['password']) < 1 || strlen($_POST['confirm_password']) < 1 || strlen($_POST['id']) < 1 || strlen($_POST['name']) < 1)
	{
		$_SESSION["error"] = "All fields are required.";
		header('Location: signup.php');
		return;
	}

	if(strpos($_POST['email'], "@") === false)
	{
		$_SESSION["error"] = "Invalid email address.";
		header('Location: signup.php');
		return;
	}

	if($_POST['password'] != $_POST['confirm_password'])
	{
		$_SESSION["error"] = "The two passwords are not the same.";
		header('Location: signup.php');
		return;
	}

	$salt = '5Gaas$ff!';
	$check = hash('md5', $salt.$_POST['password']);
	if($_POST['fac'] != "fac")
	{
		$dupl = $pdo->prepare('select * from faculty where email = :email');
		$stmt = $pdo->prepare('INSERT INTO student VALUES (:id, :name,:email,:password)');
	}
	else
	{
		$dupl = $pdo->prepare('select * from student where email = :email');
		$stmt = $pdo->prepare('INSERT INTO faculty VALUES (:id, :name,:email,:password)');
	}
	$skip = 0;

	$dupl->execute(array(
		':email' => $_POST['email']));
	$row = $dupl->fetch(PDO::FETCH_ASSOC);
	if($row != false)
	{
		$_SESSION["error"] = "Email or ID is not unique, this user aready exists";
		$skip = 1;
		header('Location: signup.php');
		return;
	}

	try
	{
	$stmt->execute(array(
		':id' => $_POST['id'],
    	':name' => $_POST['name'],
    	':email' => $_POST['email'],
    	'password' => $check));
	}
	catch(PDOException $e)
	{
		$err = $stmt->errorInfo();
		if(isset($err[1]) && $err[1] == $DUPLICATE_ENTRY_CODE)
		{
			$_SESSION["error"] = "Email or ID is not unique, this user aready exists";
			$skip = 1;
			header('Location: signup.php');
			return;
		}
	}
	
	if($skip === 0)
	{
		$_SESSION['success'] = 'New user added'.$_POST['fac'];
		if($_POST['fac'] != "fac")
		{
			$stmt = $pdo->prepare('SELECT id, name, email FROM student WHERE email = :em AND password = :pw');
			$isfac = false;
		}
		else
		{
			$stmt = $pdo->prepare('SELECT id, name, email FROM faculty WHERE email = :em AND password = :pw');
			$isfac = true;
		}
		$stmt->execute(array( ':em' => $_POST['email'], ':pw' => $check));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		$_SESSION["account"] = $row["name"];
		$_SESSION["id"] = $row["id"];
		$_SESSION["email"] = $row["email"];
		$_SESSION['isfac'] = $isfac;
	    header('Location: index.php');
	}	
    return;
}
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel = "stylesheet" type = "text/css" href = "styles.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script> 
   $(function(){
    $("#navbardiv").load("navbar.php");
   });  
</script>
<title>Quiz - signup</title>
</head>
<body class = "restbody">
<p class = "heading">Create new user</p>

<?php
	if ( isset($_SESSION['error']) ) {
	    echo '<p class = "error">'.$_SESSION['error']."</p>\n";
	    unset($_SESSION['error']);
	}
	if ( isset($_SESSION['success']) ) {
	    echo '<p class = "success">'.$_SESSION['success']."</p>\n";
	    unset($_SESSION['success']);
	}
?>

<form method = "post" class = "login">
<p>ID:</p><p><input type = "text" name = "id" value = ""></p>
<p>Name:</p><p><input type = "text" name = "name" value = ""></p>
<p>Email id:</p><p><input type = "text" name = "email" value = ""></p>
<p>Password:</p><p><input type = "password" name = "password" value = ""></p>
<p>Confirm password:</p><p><input type = "password" name = "confirm_password" value = ""></p>
<p><input type="checkbox" name="fac" value = "fac">Is Faculty?</p>
<p style = "color: black; padding-bottom: 10px;"><input type = "submit" value = "Add"></p>
</form>
<p style="text-align: center;"><a href = "index.php" style="color: red;">Cancel add</a></p>
</body>
</html>
