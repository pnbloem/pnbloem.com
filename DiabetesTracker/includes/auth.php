<?php
	//Is logged in?
	session_start();
	$_SESSION['UNAME'] = "Paul Bloem";
	$_SESSION['USERID'] = "1";
	$userLoggedIn = isset($_SESSION['UNAME']) && !(empty($_SESSION['UNAME'])) && isset($_SESSION['USERID']) && !(empty($_SESSION["USERID"]));	
?>