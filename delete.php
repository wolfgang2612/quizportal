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
	$_SESSION["error"] = "Only faculty can delete quizzes.";
	header('Location: index.php');
	return;
}

if(isset($_POST['delquiz']))
{
	$sql = "delete from quizzes where id = ".$_POST['delquiz'];
	$pdo->query($sql);
	$_SESSION["success"] = "Deleted quiz.";
	header('Location: delete.php');
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
<title>Delete</title>
</head>
<body class = "mainbody">
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
<div id="navbardiv"></div>
<?php
		$sql = "select quizzes.id, quizzes.course_id, quiztime, venue, duration from quizzes inner join course on quizzes.course_id = course.id and course.faculty_id = '".$_SESSION['id']."'";
		?>
		<div class = "quiz">
		All quizzes:
		<table style = "width: 100%; border-width: 2px; text-align: center;" border = "1">
			<tr style="font-weight: bold;">
				<td>Course</td>
				<td>Time</td>
				<td>Venue</td>
				<td>Duration(mins)</td>
				<td>Delete</td>
			</tr>
		<?php
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				echo "<tr>";
				echo "<td>"; echo $row['course_id'] ;echo "</td>";
				echo "<td>"; echo $row['quiztime'] ;echo "</td>";
				echo "<td>"; echo $row['venue'] ;echo "</td>";
				echo "<td>"; echo $row['duration'] ;echo "</td>";
				echo "<td>"; 
				echo ('<form method = "post">');
				echo ('<input type = "hidden" name = "delquiz" value = "'.$row['id'].'">');
				echo ('<input type = "submit" value = "Delete">');
				echo ('</form>');
				echo "</td>";
				echo "</tr>";
			}
		?>
		</table>
		</div>
</body>
</html>