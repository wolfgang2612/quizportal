<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION["account"]))
{
	$_SESSION["error"] = "Not logged in.";
	header('Location: index.php');
	return;
}

if(isset($_SESSION['isfac']) && $_SESSION['isfac'] === false)
{
	$_SESSION["error"] = "Only faculty can create quizzes.";
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
<title>Create a quiz</title>
</head>
<body class = "mainbody">
<div id="navbardiv"></div>
<div class = "createquiz">
<a href="upload.php"><i class="fas fa-file-upload"></i>Upload txt file</a>
</div>
<div class = "createquiz">
<a href="manual.php"><i class="fas fa-plus"></i>Create</a>
</div>
</body>
</html>