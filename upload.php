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
	$_SESSION["error"] = "Only faculty can upload quizzes.";
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
<title>Upload</title>
</head>
<body class = "mainbody">
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
<div id="navbardiv"></div>
<form action = "fileupload.php" method = "post" enctype = "multipart/form-data" class = "login">
<p>Upload a text file:</p>
<p align = "center" style = "margin-left: 7%"><input type = "file" name = "myfile" id = "fileToUpload"></p>
<p>Select course:</p>
<p>
<select name = "course">
<?php
$stmt = $pdo->query("select distinct id from course where faculty_id = '".$_SESSION["id"]."'");
while($row = $stmt->fetch(PDO::FETCH_ASSOC))
{
	echo('<option value = "'.htmlentities($row['id']).'" >'.htmlentities($row['id']).'</option>');
}
?>
</select>
</p>
<p>Enter date and time of quiz</p>
<p><input type = "datetime-local" name = "quiztime"></p>
<p>Duration</p>
<p>
<select name = "duration">
<option value = 30>30 mins</option>
<option value = 60>60 mins</option>
<option value = 120>120 mins</option>
<option value = 180>180 mins</option>
</select>
</p>
<p>Venue</p><p><input type = "text" name = "venue"></p>
<p><input type = "submit" name = "submit" value = "Upload file now"></p>
</form>
</body>
</html>