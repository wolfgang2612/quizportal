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

if(!isset($_FILES['myfile']['name']) || !isset($_FILES['myfile']['size']) || !isset($_FILES['myfile']['tmp_name']))
{
    $_SESSION["error"] = "No file uploaded.";
    header('Location: upload.php');
    return;
}

if(isset($_POST['course']) && isset($_POST['quiztime']) && isset($_POST['duration']) && isset($_POST['venue']))
{
    if(strlen($_POST['course']) < 1 || strlen($_POST['quiztime']) < 1 || strlen($_POST['duration']) < 1 || strlen($_POST['venue']) < 1)
    {
        $_SESSION["error"] = "All fields are required.";
        header('Location: upload.php');
        return;
    }
}

if(!isset($_POST['course']) || !isset($_POST['quiztime']) || !isset($_POST['duration']) || !isset($_POST['venue']))
{
    $_SESSION["error"] = "All fields are required.";
    header('Location: upload.php');
    return;
}

$currentDir = getcwd();
$uploadDirectory = "/uploads/";

$fileName = $_FILES['myfile']['name'];
$fileSize = $_FILES['myfile']['size'];
$fileTmpName  = $_FILES['myfile']['tmp_name'];

if (isset($_POST['submit'])) {
    if ($fileSize > 2000000)
    {
        $_SESSION["error"] = "This file is more than 2MB. Sorry, it has to be less than or equal to 2MB";
        header('Location: upload.php');
        return;
    }

    $file_info = new finfo(FILEINFO_MIME); 
    $mime_type = $file_info->buffer(file_get_contents($fileTmpName));
    $type = explode(";",$mime_type);

    if($type[0] != "text/plain")
    {
        $_SESSION["error"] = "Invalid file type: ".$type[0]." .Please upload txt/csv files only.";
        header('Location: index.php');
        return;
    }

    $qt = str_replace("T"," ",$_POST['quiztime']).":00";
    $uploadPath = $currentDir.$uploadDirectory.$_POST['course']."$".$qt;

    $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

    if ($didUpload)
    {
        $stmt = $pdo->prepare('insert into quizzes(course_id, quiztime, venue, duration) values( :course , :qt, :venue, :duration) ');
        $stmt->execute(array(
            ':course' => $_POST['course'],
            ':qt' => $qt,
            ':venue' => $_POST['venue'],
            ':duration' => $_POST['duration']));
        /*to do - send notification email about quiz*/

        $stmt = $pdo->prepare('select id from quizzes where course_id = :cid and quiztime = :qt');
        $stmt->execute(array(
            ':cid' => $_POST['course'],
            ':qt' => $qt));

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $qid = $row['id'];

        $fileup = fopen($uploadPath, "r");
        $content = fgetcsv($fileup);
        $loop = 0;
        $ques = 1;

        foreach ($content as $value)
        {
            if($loop == 0){$body = $value;}
            if($loop == 1){$opt1 = $value;}
            if($loop == 2){$opt2 = $value;}
            if($loop == 3){$opt3 = $value;}
            if($loop == 4){$opt4 = $value;}
            if($loop == 5){$answer = $value;}
            if($loop == 6){$correct = $value;}
            if($loop == 7){$wrong = $value;}
            if($loop == 8)
            {
                $stmt = $pdo->prepare('insert into questions (quiz_id, body, answer, correct, wrong, number) values("'.$qid.'" , "'.$body.'" , "'.$answer.'" , "'.$correct.'" , "'.$wrong.'" , "'.$ques.'")');
                $stmt->execute();

                $stmt = $pdo->prepare('select id from questions where quiz_id = "'.$qid.'" and number = "'.$ques.'"');
                $stmt->execute();
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $quesid = $row['id'];

                $stmt = $pdo->prepare('insert into options (question_id, body, number) values("'.$quesid.'" , "'.$opt1.'" , 1)');
                $stmt->execute();

                $stmt = $pdo->prepare('insert into options (question_id, body, number) values("'.$quesid.'" , "'.$opt2.'" , 2)');
                $stmt->execute();

                $stmt = $pdo->prepare('insert into options (question_id, body, number) values("'.$quesid.'" , "'.$opt3.'" , 3)');
                $stmt->execute();

                $stmt = $pdo->prepare('insert into options (question_id, body, number) values("'.$quesid.'" , "'.$opt4.'" , 4)');
                $stmt->execute();

                $body = $value;
                $ques = $ques + 1;
                $loop = 0;
            }
            $loop = $loop + 1;
        }   
        fclose($fileup);

        $_SESSION["success"]="The file ".basename($fileName)." has been uploaded";
        header('Location: index.php');
        return;
    } 
    else
    {
        $_SESSION["error"] = "Upload failed.";
        header('Location: upload.php');
        return;
    }
}
?>