<?php
require_once "pdo.php";
session_start();
if(!isset($_SESSION["account"]))
{
	$_SESSION["error"] = "Not logged in.";
	header('Location: index.php');
	return;
}

if(isset($_POST['oldpw']) && isset($_POST['newpw']))
{
	if(strlen($_POST['oldpw']) < 1 || strlen($_POST['newpw']) < 1)
	{
		$_SESSION["error"] = "All fields are required.";
		header('Location: index.php');
		return;
	}
	$salt = '5Gaas$ff!';
	$check = hash('md5', $salt.$_POST['oldpw']);
	$stmt = $pdo->prepare('select * from student where email = :email and password = :password');
	$stmt->execute(array(
		':email' => $_SESSION['email'],
		':password' => $check));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$isfac = 0;
	if($row === false)
	{
		$stmt = $pdo->prepare('select * from faculty where email = :email and password = :password');
		$stmt->execute(array(
		':email' => $_SESSION['email'],
		':password' => $check));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$isfac = 1;
		if($row === false)
		{
			$_SESSION["error"] = "Bad password.";
			header('Location: index.php');
			return;
		}
	}

	$check = hash('md5', $salt.$_POST['newpw']);
	if($isfac == 1)
	{
		$stmt = $pdo->prepare('update faculty set password = :password where email = :email');
	}
	else
	{
		$stmt = $pdo->prepare('update student set password = :password where email = :email');
	}
	$stmt->execute(array(
		':email' => $_SESSION['email'],
		':password' => $check));
	$_SESSION["success"] = "Password reset!";
	header('Location: index.php');
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
<title>Reset Password</title>
</head>
<body class = "mainbody">
<div id="navbardiv"></div>
<form method = "post" class = "login">
<p>Old Password</p><p><input type = "password" name = "oldpw"></p>
<p>New Password</p><p><input type = "password" name = "newpw"></p>
<p style = "color: black; padding-bottom: 10px;"><input type = "submit" value = "Update"></p>
</form>
</body>
</html>