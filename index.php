<?php
require_once "pdo.php";
session_start();
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
<div class = "mycontainer">
<p class = "heading">Quiz</p>
<?php 
	if ( isset($_SESSION['error']) ) {
	    echo '<p class = "error">'.$_SESSION['error']."</p>\n";
	    unset($_SESSION['error']);
	}
	if ( isset($_SESSION['success']) ) {
	    echo '<p class = "success">'.$_SESSION['success']."</p>\n";
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
		<table style = "width: 100%; border-width: 2px; text-align: center;" border = "1">
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
		<?php
	}
?>
</div>
</body>
</html>