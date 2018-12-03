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

if(!($now > $qdateyes))
{	
	$diff = $now->diff($qdateyes);
	$rem = $diff->format('%m months %d days %h hours %i minutes %s seconds');
	$_SESSION["error"] = "Time remaining for quiz: ".$rem;
	header('Location: index.php');
	return;
}

$qs = $pdo->prepare('select * from qsubmit where student_id = "'.$_SESSION['id'].'" and quiz_id = :qid');
$qs->execute(array(
	':qid' => $_GET['id']));
if($pow = $qs->fetch(PDO::FETCH_ASSOC))
{	
	if($now > $end)
	{
		echo ('<p class = "greet">Quiz stats:</p>');
		echo ('<p class = "greet">Marks: '.$pow['marks'].'</p>');
	}
	else
	{
		echo ('<p class = "greet">Quiz stats will be shown after '.$end->format('Y-m-d H:i:s').'</p>');
	}
}
else if($now < $end)
{
	if(isset($_POST['qid']))
	{	
		$sql = $pdo->query('select * from submitted where student_id = "'.$_SESSION['id'].'" and question_id = "'.$_POST['qid'].'"');
		if($row2 = $sql->fetch(PDO::FETCH_ASSOC))
		{
			$sql = $pdo->query('update submitted set answer = '.$_POST[$_POST['qid']].' where student_id = "'.$_SESSION['id'].'" and question_id = "'.$_POST['qid'].'"');
		}
		else
		{
			$sql = $pdo->prepare('insert into submitted (student_id, question_id, answer) values(:sid , :qid, :answer)');
			$sql->execute(array(
				':sid' => $_SESSION['id'],
				':qid' => $_POST['qid'],
				':answer' => $_POST[$_POST['qid']]));
		}	
	}

	if(isset($_POST['reset']))
	{
		$fow = $pdo->prepare('delete from submitted where student_id = "'.$_SESSION['id'].'" and question_id = :qid');
		$fow->execute(array(
			':qid' => $_POST['qresetid']));
	}

	if(!isset($_POST['qsubmit']))
	{
		?>
		<div id="countdown" class="timer"></div>
		<?php

		$stmt = $pdo->prepare('select id from quizzes where course_id = :cid and quiztime = :qt');
		$stmt->execute(array(
			':cid' => $row['course_id'],
			':qt' => $row['quiztime']));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$stmt = $pdo->prepare('select * from questions where quiz_id = :qid and number = 1');
		$quizid = $row['id'];
		$stmt->execute(array(':qid' => $quizid));
		$ques = 1;
		while($row = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			echo '<p class = "login2">'.$row['body'].' - Marks: '.$row['correct'].' , '.$row['wrong'].'</p>';
			echo '<form method = "post" class = "login2">';
			$sql = $pdo->query('select * from options where question_id = '.$row['id']);
			$bow = $sql->fetch(PDO::FETCH_ASSOC);
			$sql2 = $pdo->query('select * from submitted where student_id = "'.$_SESSION['id'].'" and question_id = "'.$row['id'].'"');
			$cow = $sql2->fetch(PDO::FETCH_ASSOC);
			if($cow['answer'] == 1)
			{
				echo ('<p><input type = "radio" name = '.$row['id'].' value = 1 checked = "checked">'.$bow['body'].'</p>');
			}
			else
			{
				echo ('<p><input type = "radio" name = '.$row['id'].' value = 1>'.$bow['body'].'</p>');
			}
			$bow = $sql->fetch(PDO::FETCH_ASSOC);
			if($cow['answer'] == 2)
			{
				echo ('<p><input type = "radio" name = "'.$row['id'].'" value = 2 checked = "checked">'.$bow['body'].'</p>');
			}
			else
			{
				echo ('<p><input type = "radio" name = "'.$row['id'].'" value = 2>'.$bow['body'].'</p>');
			}
			$bow = $sql->fetch(PDO::FETCH_ASSOC);
			if($cow['answer'] == 3)
			{
				echo ('<p><input type = "radio" name = "'.$row['id'].'" value = 3 checked = "checked">'.$bow['body'].'</p>');
			}
			else
			{
				echo ('<p><input type = "radio" name = "'.$row['id'].'" value = 3>'.$bow['body'].'</p>');
			}
			$bow = $sql->fetch(PDO::FETCH_ASSOC);
			if($cow['answer'] == 4)
			{
				echo ('<p><input type = "radio" name = "'.$row['id'].'" value = 4 checked = "checked">'.$bow['body'].'</p>');
			}
			else
			{
				echo ('<p><input type = "radio" name = "'.$row['id'].'" value = 4>'.$bow['body'].'</p>');
			}
			echo ('<p><input type = "hidden" name = "ans'.$row['id'].'" value = '.$row['answer'].'></p>');
			echo ('<p><input type = "hidden" name = "correct'.$row['id'].'" value = '.$row['correct'].'></p>');
			echo ('<p><input type = "hidden" name = "wrong'.$row['id'].'" value = '.$row['wrong'].'></p>');
			echo ('<p><input type = "hidden" name = "qid" value = '.$row['id'].'></p>');
			echo ('<p><input type = "submit" value = "Submit answer"></p>');
			echo '</form>';

			if(isset($cow['answer']))
			{
				echo ('<form method = "post" class = "quiz"');
				echo ('<p><input type = "hidden" name = "reset" value = "reset"></p>');
				echo ('<p><input type = "hidden" name = "qresetid" value = '.$row['id'].'></p>');
				echo ('<p><input type = "submit" value = "Reset answer"></p>');
				echo ('</form>');
			}

			$ques = $ques + 1;
			$stmt = $pdo->prepare('select * from questions where quiz_id = :qid and number = '.$ques);
			$stmt->execute(array(':qid' => $quizid));	
		}

		echo ('<form method = "post" class = "login">');
		echo ('<p><input type = "hidden" name = "qsubmit" value = "qsubmit"></p>');
		echo ('<p><input type = "hidden" name = "qsubmitid" value = '.$quizid.'></p>');
		echo ('<p><input type = "submit" value = "Submit Quiz"></p>');
		echo ('</form>');
	}

	else if(isset($_POST['qsubmit']))
	{
		$pdo->query('drop view if exists seemarks');
		$stmt = $pdo->prepare('create view seemarks as select * from questions where quiz_id = :qid');
		$stmt->execute(array(
			':qid' => $_GET['id']));
		$pdo->query('drop view if exists seestud');
		$pdo->query('create view seestud as select * from submitted where student_id = "'.$_SESSION['id'].'"');
		$pdo->query('drop view if exists marksview');
		$stmt = $pdo->query('create view marksview as select seemarks.answer as a1, seestud.answer as a2, correct, wrong from seemarks inner join seestud on seemarks.id = seestud.question_id');
		$marks = 0;
		$stmt = $pdo->query('select * from marksview');
		while($jow = $stmt->fetch(PDO::FETCH_ASSOC))
		{
			if($jow['a1'] == $jow['a2'])
				$marks = $marks + $jow['correct'];
			else
				$marks = $marks + $jow['wrong'];
		}
		$stmt = $pdo->query('insert into qsubmit (student_id, quiz_id, marks) values ("'.$_SESSION['id'].'" , "'.$_POST['qsubmitid'].'" , '.$marks.')');
		echo '<p class = "greet">Quiz submitted!</p>';
	}
}
else
{
	echo '<h2 class = "greet">You misssed this quiz.</h2>';
}

$timerdiff = $end->getTimestamp() - $now->getTimestamp();
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
<script type="text/javascript">
var initialTime = <?php echo $timerdiff; ?>;

var seconds = initialTime;
function timer() {
    var days        = Math.floor(seconds/24/60/60);
    var hoursLeft   = Math.floor((seconds) - (days*86400));
    var hours       = Math.floor(hoursLeft/3600);
    var minutesLeft = Math.floor((hoursLeft) - (hours*3600));
    var minutes     = Math.floor(minutesLeft/60);
    var remainingSeconds = seconds % 60;
    if (remainingSeconds < 10) {
        remainingSeconds = "0" + remainingSeconds; 
    }
    document.getElementById('countdown').innerHTML = hours + " Hours " + minutes + " minutes " + remainingSeconds+ " seconds left";
    if (seconds == 0) {
        clearInterval(countdownTimer);
        document.getElementById('countdown').innerHTML = "Completed";
    } else {
        seconds--;
    }
}
var countdownTimer = setInterval('timer()', 1000);
</script>
<title>IIT Patna Online Quiz Portal - Home</title>
</head>
<body class = "mainbody">
<div id="navbardiv"></div>
</body>
</html>