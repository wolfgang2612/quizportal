<?php
require_once "pdo.php";
session_start();

if(!isset($_SESSION["account"]))
{
	$_SESSION["error"] = "Not logged in.";
	header('Location: index.php');
	return;
}

if(isset($_SESSION['isfac']) && $_SESSION['isfac'] != false)
{
	$_SESSION["error"] = "Only students can take quizzes.";
	header('Location: index.php');
	return;
}

if(!isset($_GET['id']))
{
	$_SESSION["error"] = "No quiz id given.";
	header('Location: index.php');
	return;
}

$sql = "select course_id, duration, quiztime from quizzes where id = '".$_GET['id']."'";
$stmt = $pdo->query($sql);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if($row === false)
{
	$_SESSION["error"] = "Invalid id given.";
	header('Location: index.php');
	return;
}

$now = new DateTime();
$qdate = new DateTime($row['quiztime']);
$qdateyes = new DateTime($row['quiztime']);
$interv = date_interval_create_from_date_string($row['duration']. "minutes");
$end = $qdate->add($interv);

if($now > $end)
{
	$_SESSION["error"] = "The quiz has expired.";
	header('Location: index.php');
	return;
}

if(!($now > $qdateyes))
{	
	$diff = $now->diff($qdateyes);
	$rem = $diff->format('%y years %m months %d days %h hours %i minutes %s seconds');
	$_SESSION["error"] = "Time remaining for quiz: ".$rem;
	header('Location: index.php');
	return;
}

/*
make a javascript countdown here
*/
$diff = $now->diff($end);
$rem = $diff->format('%h hours, %i minutes, %s seconds left!');
echo $rem."<br/>";

$file = fopen("/var/www/html/Projects/Quiz/uploads/".$row['course_id']."$".$row['quiztime'],"r");
$content = fgetcsv($file);
$loop = 0;
$ques = 1;
foreach ($content as $value)
{
	if($loop > 6)
	{
		$loop = 0;
		$ques = $ques + 1;
		echo "<br/>";
	}
	if($loop == 0)
	{
		echo $value;
		echo "<br/>";
	}
	if($loop >= 1 && $loop <=4)
	{
		echo '<form method = "post">';
		echo ('<p><input type = "radio" name = '.$ques.' value = '.$loop.'>'.$value.'</p>');
	}
	if($loop == 5)
	{
		echo ('<p><input type = "hidden" name = "correct'.$ques.'" value = '.$value.'></p>');
	}
	if($loop == 6)
	{
		echo ('<p><input type = "hidden" name = "wrong'.$ques.'" value = '.$value.'></p>');
		echo ('<p><input type = "submit" value = "Submit answer"></p>');
		echo '</form>';
	}
	$loop = $loop + 1;
}	
fclose($file);
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
<title>IIT Patna Online Quiz Portal - Home</title>
</head>
<body class = "mainbody">
<div id="navbardiv"></div>
</body>
</html>