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
if(isset($_POST['createquiz']))
{
	if(strlen($_POST['course']) < 1 || strlen($_POST['quiztime']) < 1 || strlen($_POST['duration']) < 1 || strlen($_POST['venue']) < 1)
    {
        $_SESSION["error"] = "All fields are required.";
        header('Location: manual.php');
        return;
    }
    $qt = str_replace("T"," ",$_POST['quiztime']).":00";
    $stmt = $pdo->prepare('insert into quizzes(course_id, quiztime, venue, duration) values( :course , :qt, :venue, :duration) ');
    $stmt->execute(array(
            ':course' => $_POST['course'],
            ':qt' => $qt,
            ':venue' => $_POST['venue'],
            ':duration' => $_POST['duration']));

    $stmt = $pdo->query('select * from quizzes where course_id = "'.$_POST['course'].'" and quiztime = "'.$qt.'"');

	$kow = $stmt->fetch(PDO::FETCH_ASSOC);    
    echo '<p><h2 class="greet">The quiz has been created. Click the button below to start entering questions:</h2></p>';
    echo '<form method = "post" style = "text-align: center;">';
    echo '<input type = "hidden" name = "enterques" value = '.$kow['id'].'>';
    echo ('<input type = "submit" class="btn btn-info" value = "Enter Questions">');
    echo '</form>';
}

else if(isset($_POST['enterques']))
{
	if(isset($_POST['qno']))
		$qno = $_POST['qno'];
	else $qno = 1;
	echo '<form method = "post">';
	echo '<div class="form-group">';
	echo '<label for="question">Question '.$qno.' :</label>';
	echo '<input type="text" class="form-control" name="question">';
	echo '</div>';
	echo '<div class="form-group">';
	echo '<label for="opt1">Option 1:</label>';
	echo '<input type="text" class="form-control" name="opt1">';
	echo '</div>';
	echo '<div class="form-group">';
	echo '<label for="opt2">Option 2:</label>';
	echo '<input type="text" class="form-control" name="opt2">';
	echo '</div>';
	echo '<div class="form-group">';
	echo '<label for="opt3">Option 3:</label>';
	echo '<input type="text" class="form-control" name="opt3">';
	echo '</div>';
	echo '<div class="form-group">';
	echo '<label for="opt4">Option 4:</label>';
	echo '<input type="text" class="form-control" name="opt4">';
	echo '</div>';
	echo '<div class="form-group">';
	echo '<label for="opt4">Correct option(1-4):</label>';
	echo '<input type="number" name="answer" min="1" max="4">';
	echo '</div>';
	echo '<div class="form-group">';
	echo '<label for="opt4">Correct answer marks:</label>';
	echo '<input type="number" name="correct" min="-100" max="100" step="0.01" value="3">';
	echo '</div>';
	echo '<div class="form-group">';
	echo '<label for="opt4">Wrong answer marks:</label>';
	echo '<input type="number" name="wrong" min="-100" max="100" step="0.01" value="3">';
	echo '</div>';
	echo '<input type = "hidden" name = "nextques" value = '.$_POST['enterques'].'>';
	echo '<input type = "hidden" name = "qno" value = '.$qno.'>';
	echo '<div style = "text-align: center;">';
	echo ('<input type = "submit" class="btn btn-info" value = "Add question">');
	echo '</div>';
	echo '</form>';
}

else if(isset($_POST['nextques']))
{
	$stmt = $pdo->prepare('insert into questions (quiz_id, body, answer, correct, wrong, number) values (:qid, :body, :ans, :cor, :wro, :num)');
	$stmt->execute(array(
		':qid' => $_POST['nextques'],
		':body' => $_POST['question'],
		':ans' => $_POST['answer'],
		':cor' => $_POST['correct'],
		':wro' => $_POST['wrong'],
		':num' => $_POST['qno']));
	$stmt = $pdo->query('select * from questions where quiz_id = "'.$_POST['nextques'].'" and number = "'.$_POST['qno'].'"');

	$gow = $stmt->fetch(PDO::FETCH_ASSOC);

	$stmt = $pdo->prepare('insert into options (question_id, body, number) values('.$gow['id'].' , :opt1 , 1)');
	$stmt->execute(array(
		':opt1' => $_POST['opt1']));
	$stmt = $pdo->prepare('insert into options (question_id, body, number) values('.$gow['id'].' , :opt2 , 2)');
	$stmt->execute(array(
		':opt2' => $_POST['opt2']));
	$stmt = $pdo->prepare('insert into options (question_id, body, number) values('.$gow['id'].' , :opt3 , 3)');
	$stmt->execute(array(
		':opt3' => $_POST['opt3']));
	$stmt = $pdo->prepare('insert into options (question_id, body, number) values('.$gow['id'].' , :opt4 , 4)');
	$stmt->execute(array(
		':opt4' => $_POST['opt4']));

	echo '<p><h2 class = "greet">The questions and options have been uploaded.</h2></p>';
	echo '<form method = "post" style = "text-align: center;">';
	echo '<input type = "hidden" name = "enterques" value = '.$_POST['nextques'].'>';
	echo '<input type = "hidden" name = "qno" value = '.($_POST['qno'] + 1).'>';
	echo ('<input type = "submit" class="btn btn-info" value = "Add question">');
	echo '</form>';
}

else
{
?>

<form method = "post" class = "login">
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
<input type="hidden" name="createquiz">
<p><input type = "submit" name = "submit" value = "Enter questions"></p>
</form>
<?php
}
?>
</body>
</html>