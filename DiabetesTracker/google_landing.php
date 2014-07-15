<?php
session_start();
include('includes/dbconnect.php');
//echo $_GET['openid_ext1_value_firstname'] . " " . $_GET['openid_ext1_value_lastname'] . " " . $_GET['openid_ext1_value_email'];
if (!empty($_GET['openid_ext1_value_firstname']) && !empty($_GET['openid_ext1_value_lastname']) && !empty($_GET['openid_ext1_value_email'])) {    
		$username = $_GET['openid_ext1_value_firstname'] . $_GET['openid_ext1_value_lastname'];
		$email = $_GET['openid_ext1_value_email'];
		
		$uname=$_GET['openid_ext1_value_firstname'].' '.$_GET['openid_ext1_value_lastname'];
		$email=$_GET['openid_ext1_value_email'];
		$checkUserSql="select * from google_login where email= '$email'";
		$checkUserRes=mysql_query($checkUserSql);
		$checkUserCount=mysql_num_rows($checkUserRes);
		if($checkUserCount == 0){
			$sql="insert into google_login(name,email,inserted_on) values('$uname','$email',now())";
			mysql_query($sql);
			$checkUserSql="select * from google_login where email= '$email'";
			$checkUserRes=mysql_query($checkUserSql);
		}
		
		$u = mysql_fetch_assoc($checkUserRes);
		$_SESSION['USERID'] = $u["id"];
		$_SESSION['UNAME']=$_GET['openid_ext1_value_firstname'].' '.$_GET['openid_ext1_value_lastname'];
		$_SESSION['UEMAIL']=$_GET['openid_ext1_value_email'];
		//echo $_SESSION['UNAME'];
		//echo $_SESSION['UEMAIL'];
	}
	header('location:index.php');
?>