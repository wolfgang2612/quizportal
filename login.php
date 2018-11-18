<?php
session_start();
require_once "pdo.php";
if(isset($_POST["account"]) && isset($_POST["password"]))
{
	unset($_SESSION["account"]);

	$salt = '5Gaas$ff!';
	$check = hash('md5', $salt.$_POST['password']);
	$stmt = $pdo->prepare('SELECT id, name, email FROM student WHERE email = :em AND password = :pw');
	$stmt->execute(array( ':em' => $_POST['account'], ':pw' => $check));
	$row = $stmt->fetch(PDO::FETCH_ASSOC);
	$isfac = false;

	if(strlen(htmlentities($_POST["account"])) < 1 || strlen(htmlentities($_POST["password"])) < 1)
	{
		$_SESSION["error"] = "User name and password are required.";
		header('Location: login.php');
		return;
	}

	elseif(strpos($_POST["account"], "@") === false)
	{
		$_SESSION["error"] = "Invalid email address.";
		header('Location: login.php');
		return;
	}

	if ($row === false ) 
	{
		$stmt = $pdo->prepare('SELECT id, name, email FROM faculty WHERE email = :em AND password = :pw');
		$stmt->execute(array( ':em' => $_POST['account'], ':pw' => $check));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if($row === false)
		{
			$_SESSION["error"] = "Incorrect password.";
			error_log("Time: ".date("Y-m-d H:i:s")." Failed login of ".htmlentities($_POST["account"])." with password: ".htmlentities($_POST["password"])."\n","3","error.php");
			header('Location: login.php');
			return;
		}

		else $isfac = true;
	}

	$_SESSION["account"] = htmlentities($row["name"]);
	$_SESSION['id'] = htmlentities($row['id']);
	$_SESSION['email'] = htmlentities($row['email']);
	$_SESSION['isfac'] = $isfac;
	$_SESSION["success"] = "Login successful!";
	error_log("Time: ".date("Y-m-d H:i:s")." Login: ".htmlentities($_POST["account"])."\n","3","error.php");
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
<title>Quiz - login</title>
</head>
<body class = "restbody">
<h1 class = "heading">Please enter your login details:</h1>
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
<p>Email:</p><p><input type = "text" id = "email" name = "account" value = ""></p>
<p>Password:</p><p><input type = "password" id = "password" name = "password" value = ""></p>
<p style = "color: black; padding-bottom: 10px;"><input type = "submit" value = "Log In"></p>
</form>
<p style="text-align: center;"><a href = "index.php" style="color: red;">Cancel login</a></p>
</body>
</html>