<?php
	$connection = mysql_connect('localhost:3306', 'pnbloemc_paul', 'W0lverines01*')
		or die('Could not connect: ' . mysql_error());
	#echo 'Connected Successfully.<br/>';

	mysql_select_db('pnbloemc_diabetestracker') or die('Could not choose database: '.mysql_error());
	#echo 'Connected to database.<br/>';

?>