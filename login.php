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
			$_SESSION["error"] = "Incorrect username/password.";
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
<body id = "LoginForm" class = "mainbody">
<div id="navbardiv"></div>
<?php

if ( isset($_SESSION['error']) ) {
	    echo ('<div style = "text-align: center;" class="alert alert-danger fade in">');
	    echo $_SESSION['error'];
	    echo ('</div>');
	    unset($_SESSION['error']);
	}
	if ( isset($_SESSION['success']) ) {
	    echo ('<div style = "text-align: center;" class="alert alert-success fade in">');
	    echo $_SESSION['success'];
	    echo ('</div>');
	    unset($_SESSION['success']);
	}

?>
<div class="container">
<div class="login-form">
<div class="main-div">
    <div class="panel">
   <p>Please enter your email and password</p>
   </div>
    <form id="Login" method = "post">
        <div class="form-group">
	        <input type="email" class="form-control" name="account" placeholder="Email Address">
        </div>
        <div class="form-group">
            <input type="password" class="form-control" name="password" placeholder="Password">
        </div>
        <input type="submit" class="btn btn-primary" value="Login">
    </form>
    </div>
</div></div></div>


</body>
</html>