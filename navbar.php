<?php
require_once "pdo.php";
session_start();
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<div class="topnav">
  <a href="index.php"><i class="fa fa-fw fa-home"></i>Home</a>
  <a href="contact.php"><i class="fa fa-fw fa-envelope"></i>Contact</a>
<?php
	if(!isset($_SESSION["account"]))
	{
		?>
		<div class="topnav-right">
	    	<a href="login.php"><i class="fa fa-fw fa-sign-in"></i>Login</a>
	    	<a href="signup.php"><i class="fa fa-fw fa-user-plus"></i>Sign Up</a>
  		</div>
		<?php
	}
	else
	{
		?>
		<div class="topnav-right">
		<?php
			echo('<a href="profile.php?email='.$_SESSION['email'].'"><i class="fa fa-fw fa-user"></i>Profile</a>');
		?>
	    	<a href="logout.php"><i class="fa fa-fw fa-sign-out"></i>Logout</a>
  		</div>
  		<?php
	}
?>
</div>