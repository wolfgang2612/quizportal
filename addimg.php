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

if(isset($_POST['uploadimg']))
{
	$currentDir = getcwd();
	$uploadDirectory = "/uploads/";

	$fileName = $_FILES['myfile']['name'];
	$fileSize = $_FILES['myfile']['size'];
	$fileTmpName  = $_FILES['myfile']['tmp_name'];

	if ($fileSize > 2000000)
    {
        $_SESSION["error"] = "This file is more than 2MB. Sorry, it has to be less than or equal to 2MB";
        header('Location: edit.php');
        return;
    }

    $file_info = new finfo(FILEINFO_MIME); 
    $mime_type = $file_info->buffer(file_get_contents($fileTmpName));
    $type = explode("/",$mime_type);
    $typeo = explode(";",$mime_type);

    if($type[0] != "image")
    {
        $_SESSION["error"] = "Invalid file type: ".$typeo[0]." .Please upload image files only.";
        header('Location: edit.php');
        return;
    }

    $uploadPath = $currentDir.$uploadDirectory.$_POST['uploadimg']."$".$_POST['qnum'];
    $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

    if ($didUpload)
    {
    	$pdo->query('update questions set hasimg = 1 where quiz_id = "'.$_POST['uploadimg'].'" and number = "'.$_POST['qnum'].'"');
	    $_SESSION["success"]="The file ".basename($fileName)." has been uploaded";
	    header('Location: edit.php');
	    return;
	}
}

if(!isset($_POST['addimg']))
{
	$_SESSION["error"] = "Select valid quiz from edit page.";
    header('Location: edit.php');
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
<title>Upload images</title>
</head>
<body class = "mainbody">
<div id="navbardiv"></div>
<form method = "post" enctype = "multipart/form-data" class="login">
<div class="form-group">
<label for="qnum">Question number:</label>
<input type="number" name="qnum" min="1" max="100" step="1" value="1">
</div>
<div class="form-group" style="text-align: center;">
<label for="myfile">Choose image:</label>
<p align = "center" style = "margin-left: 7%"><input type = "file" name = "myfile" id = "fileToUpload"></p>
</div>
<input type = "hidden" name = "uploadimg" value = <?=$_POST['addimg']?>>
<p><input type = "submit" name = "submit" value = "Upload image now"></p>
</form>
</body>
</html>