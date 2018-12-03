<?php
require_once "pdo.php";
session_start();
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
<title>IIT Patna Online Quiz Portal</title>
</head>
<body class="mainbody">
<div id="navbardiv"></div>
<h2>Made by Ashutosh and Shashank</h2>
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
</body>
</html>