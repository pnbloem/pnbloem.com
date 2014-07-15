<?php
	//Is logged in?
	session_start();
	$userLoggedIn = isset($_SESSION['UNAME']) && !(empty($_SESSION['UNAME'])) && isset($_SESSION['USERID']) && !(empty($_SESSION["USERID"]));	
?>