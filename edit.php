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
	$_SESSION["error"] = "Only faculty can edit quizzes.";
	header('Location: index.php');
	return;
}
	
if(isset($_POST["edit"]))
{
	$quesnum = 1;
	$loop = 0;
	foreach ($_POST as $key => $value)
	{
		if($loop == 0)
		{
			$st = $pdo->prepare('update questions set body = :body where id = :id');
			$st->execute(array(
				'body' => $value,
				'id' => $key));
		}
		else
		{
			$st =$pdo->prepare('update options set body = :body where id = :id');
			$st->execute(array(
				'body' => $value,
				'id' => $key));
		}
		$loop = $loop + 1;
		if($loop == 5)
		{
			$loop = 0;
		}
	}

	$_SESSION["success"] = "Quiz edited!";
	header('Location: edit.php');
	return;
}

if(isset($_POST['endquiz']))
{
	$pdo->query("update quizzes set duration = 0 where id = ".$_POST['endquiz']);
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
<title>Edit</title>
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
if(!isset($_POST['editquiz']))
{
		$sql = "select quizzes.id, quizzes.course_id, quiztime, venue, duration from quizzes inner join course on quizzes.course_id = course.id and course.faculty_id = '".$_SESSION['id']."'";
		?>
		<div class = "quiz">
		All quizzes:
		<table class="table table-hover table-striped " style="text-align: center;">
			<tr style="font-weight: bold;">
				<td>Course</td>
				<td>Time</td>
				<td>Venue</td>
				<td>Duration(mins)</td>
				<td>Edit</td>
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
				echo ('<input type = "hidden" name = "editquiz" value = "'.$row['id'].'">');
				echo ('<input type = "submit" class="btn btn-success" value = "Edit">');
				echo ('</form>');
				echo "</td>";
				echo "</tr>";
			}
		?>
		</table>
		</div>
<?php
}
else
{
	$sql = "select * from quizzes where id = ".$_POST['editquiz'];
	$stmt3 = $pdo->query($sql);
	$how = $stmt3->fetch(PDO::FETCH_ASSOC);

	$now = new DateTime();
	$qdate = new DateTime($how['quiztime']);
	$qdateyes = new DateTime($how['quiztime']);
	$interv = date_interval_create_from_date_string($how['duration']. "minutes");
	$end = $qdate->add($interv);

	echo ('<p class = "greet">Course: '.$how['course_id'].' Time: '.$how['quiztime'].' Venue: '.$how['venue'].'</p>');

	echo ('<p><form method = "post" action = "addimg.php" style = "text-align : center;">');
	echo ('<input type = "hidden" name = "addimg" value = "'.$how['id'].'">');
	echo ('<input type = "submit" class="btn btn-primary" value = "Add images">');
	echo ('</form></p>');

	echo ('<form method = "post" style = "text-align : center;">');
	echo ('<input type = "hidden" name = "endquiz" value = '.$_POST['editquiz'].'>');
	if($now > $qdateyes && $now < $end)
	{
		echo ('<input type = "submit" class="btn btn-danger" value = "End quiz?">');
	}
	echo ('</form>');

	$sql = "select * from questions where quiz_id = ".$_POST['editquiz']." and number = 1";
	$stmt2 = $pdo->query($sql);
	$ques = 1;
	echo ('<form method = post class = "login2">');
	while($pow = $stmt2->fetch(PDO::FETCH_ASSOC))
	{
		echo '<p>';
		$sql = 'select * from options where question_id = "'.$pow['id'].'"';
		$stmt = $pdo->query($sql);
		
		echo ('<p>Question '.$ques.' body:</p>');
		echo ('<input type = "text" name = "'.$pow['id'].'" value = "'.$pow['body'].'">');
		$cow = $stmt->fetch(PDO::FETCH_ASSOC);
		echo ('<p>Option 1:</p>');
		echo ('<input type = "text" name = "'.$cow['id'].'" value = "'.$cow['body'].'">');
		$cow = $stmt->fetch(PDO::FETCH_ASSOC);
		echo ('<p>Option 2:</p>');
		echo ('<input type = "text" name = "'.$cow['id'].'" value = "'.$cow['body'].'">');
		$cow = $stmt->fetch(PDO::FETCH_ASSOC);
		echo ('<p>Option 3:</p>');
		echo ('<input type = "text" name = "'.$cow['id'].'" value = "'.$cow['body'].'">');
		$cow = $stmt->fetch(PDO::FETCH_ASSOC);
		echo ('<p>Option 4:</p>');
		echo ('<input type = "text" name = "'.$cow['id'].'" value = "'.$cow['body'].'">');
		echo '</p>';
		$ques = $ques + 1;
		$sql = "select * from questions where quiz_id = ".$_POST['editquiz']." and number = ".$ques;
		$stmt2 = $pdo->query($sql);
	}
	echo ('<p><input type = "hidden" name = "edit" value = "'.$how['id'].'"></p>');
	echo ('<p><input type = "submit" value = "Update"></p>');
	echo ('<form/>');
}
?>		
</body>
</html>