<?php
require_once "pdo.php";
session_start();
?>

<!DOCTYPE html lang = "en">
<html>	
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link rel = "stylesheet" type = "text/css" href = "styles.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script> 
   $(function(){
    $("#navbardiv").load("navbar.php");
   });  
</script>
<title>IIT Patna Online Quiz Portal - Home</title>
</head>
<body class = "mainbody">
<div id="navbardiv"></div>
<div class = "mycontainer">
<p class = "heading">Quiz</p>
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
	if(isset($_SESSION["account"]))
	{
		echo('<p class = "greet">Current user: '.$_SESSION['account']);
	}
	if(isset($_SESSION['isfac']) && $_SESSION['isfac'] === true)
	{
		?>
		<div class = "bookroom">
		<a href="create.php"><i class="fas fa-building"></i>Create Quiz</a>
		</div>
		<div class = "bookroom">
		<a href="edit.php"><i class="fas fa-edit"></i>Edit Quiz</a>
		</div>
		<div class = "bookroom">
		<a href="delete.php"><i class="fas fa-trash"></i>Delete Quiz</a>
		</div>
		<?php
		$sql = "select quizzes.id, quizzes.course_id, quiztime, venue, duration from quizzes inner join course on quizzes.course_id = course.id and course.faculty_id = '".$_SESSION['id']."'";
		?>
		<div class = "quiz">
		All quizzes:
		<table class="table table-hover table-striped" style="text-align: center;">
			<tr style="font-weight: bold;">
				<td>Course</td>
				<td>Time</td>
				<td>Venue</td>
				<td>Duration(mins)</td>
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
				echo "</tr>";
			}
		?>
		</table>
		</div>
		<?php
	}
	if(isset($_SESSION['isfac']) && $_SESSION['isfac'] != true)
	{
		$now = new DateTime();
		$sql = "select quizzes.id, quizzes.course_id, quiztime, venue, duration from quizzes inner join enrolled on quizzes.course_id = enrolled.course_id and enrolled.student_id = '".$_SESSION['id']."'";
		$stmt = $pdo->prepare($sql);
		$stmt->execute();
		?>
		<div class = "quiz">
		Active quiz:
		<?php
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			$qdate = new DateTime($row['quiztime']);
			$qdateyes = new DateTime($row['quiztime']);
			$interv = date_interval_create_from_date_string($row['duration']. "minutes");
			$end = $qdate->add($interv);
			if(($now < $end) && ($now > $qdateyes))
			{
				echo ('<a href = "quiz.php?id='.$row['id'].'">'.$row['course_id'].'</a>');
			}
		}
		?>
		</div>
		<div class = "quiz">
		Upcoming quizzes:
		<table class="table table-hover table-striped " style="text-align: center;">
			<tr style="font-weight: bold;">
				<td>Course</td>
				<td>Time</td>
				<td>Venue</td>
				<td>Duration(mins)</td>
			</tr>
		<?php
			$stmt = $pdo->prepare($sql);
			$stmt->execute();
			while($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				$qdate = new DateTime($row['quiztime']);
				if($qdate > $now)
				{
					echo "<tr>";
					echo "<td>"; echo $row['course_id'] ;echo "</td>";
					echo "<td>"; echo $row['quiztime'] ;echo "</td>";
					echo "<td>"; echo $row['venue'] ;echo "</td>";
					echo "<td>"; echo $row['duration'] ;echo "</td>";
					echo "</tr>";
				}
			}
		?>
		</table>
		</div>
		<div class = "quiz">
		All quizzes:
		<table class="table table-hover table-striped " style="text-align: center;">
			<tr style="font-weight: bold;">
				<td>Course</td>
				<td>Time</td>
				<td>Venue</td>
				<td>Duration(mins)</td>
				<td>Link</td>
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
				echo "<td>"; echo ('<a href = "quiz.php?id='.$row['id'].'">Quiz</a>'); echo "</td>";
				echo "</tr>";
			}
		?>
		</table>
		</div>
		<?php
	}
?>
</div>
</body>
</html>