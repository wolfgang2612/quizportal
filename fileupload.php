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
        /*to do - send notification email about quiz
        $sql = "select name, email from student inner join enrolled on student.id = enrolled.student_id and enrolled.course_id = :course";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':course' => $_POST['course']));
        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $_SESSION['trial'] = $row['email'];
            $to = $row['email'];
            $subject = $_POST['course']." quiz";
            $body = "Hello ".$row['name']." ! A quiz has been scheduled for ".$_POST['course']." on ".$qt." at ".$_POST['venue']." ,duration of ".$_POST['duration']." hours" ;
            $headers = "From: ".$_SESSION['email'];
            mail($to,$subject,$body,$headers);
        }*/
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