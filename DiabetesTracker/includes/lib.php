<?php
	include("auth.php");
	include("dbconnect.php");

	//If numDays == 0, get total average
	function getAverage($numDays){
		$response = array();
		$query = "";
		if($numDays == 0){
			$query = "SELECT GlucoseLevel FROM measurements WHERE UserID={$_SESSION['USERID']}";
		} else {
			date_default_timezone_set('UTC');
			$date = new DateTime;
			$date->modify("-{$numDays} days");
			$dateString = $date->format('Y-m-d H:i:s');
			$query = "SELECT GlucoseLevel FROM measurements WHERE Timestamp > '{$dateString}' AND UserID={$_SESSION['USERID']}";
		}
		if($results = mysql_query($query)){
			$numMeasurements = mysql_num_rows($results);
			if ($numMeasurements == 0) {
				$numMeasurements = 1;
			}
			$sum = 0;
			while($m = mysql_fetch_array($results)){
				$sum += $m['GlucoseLevel'];
			}
			$average = $sum/$numMeasurements;
			$response['status'] = 'Success';
			$response['average'] = round($average, 2);
		} else {
			$response['status'] = "Failure";
			$response['error'] = mysql_error();
		}
		return $response["average"];
	}
	
?>