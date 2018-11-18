<?php
try {
    $pdo = new PDO('mysql:dbname=quiz;','ashu','ashu');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Connection error, please contact support.';
    error_log("Time: ".date("Y-m-d H:i:s")." Error: ".$e->getMessage()."\n","3","error.php");
    return;
}
?>