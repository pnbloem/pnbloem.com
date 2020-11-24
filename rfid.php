<?php
	$connection = mysql_connect('localhost:3306', 'pnbloemc_paul', 'W0lverines01*')
		or die('Could not connect: ' . mysql_error());
	#echo 'Connected Successfully.<br/>';

	mysql_select_db('pnbloemc_diabetestracker') or die('Could not choose database: '.mysql_error());
	#echo 'Connected to database.<br/>';
	print_r($_POST);
	$code = $_POST["code"];
	$query = "INSERT INTO BLE(code) VALUES('{$code}')";
	mysql_query($query);

	echo "Received Tag: ".$_POST["code"];

?>