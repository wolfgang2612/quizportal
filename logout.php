<?php
session_start();
error_log("Time: ".date("Y-m-d H:i:s")." Logout: ".htmlentities($_SESSION["account"])."\n","3","error.php");
session_destroy();
session_start();
$_SESSION["success"] = "Logout successful!";
header('Location: index.php');
return;
?>