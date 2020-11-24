<?php
	include("../includes/auth.php");
	include("../includes/dbconnect.php");
	include("../includes/lib.php");
	
	if(isset($_POST['method'])){
		switch($_POST['method']){
			case 'submitMeasurement':
				if(isset($_POST['type']) && isset($_POST['time']) 
					&& isset($_POST['level']) && isset($_POST['insulin_type']) 
					&& isset($_POST['insulin_amt']) && isset($_SESSION['USERID'])){
						return submitMeasurement($_POST['type'], $_POST['time'],
							$_POST['level'], $_POST['insulin_type'], $_POST['insulin_amt'], $_SESSION['USERID'], $userLoggedIn);
				} else {
					echo 'Some fields not set.';
					return;
				}
					
			case 'editMeasurement':
				if(isset($_POST['type']) && isset($_POST['time']) 
					&& isset($_POST['level']) && isset($_POST['insulin_type']) 
					&& isset($_POST['insulin_amt']) && isset($_POST['meas_id']) && isset($_SESSION['USERID'])){
						return editMeasurement($_POST['type'], $_POST['time'],
							$_POST['level'], $_POST['insulin_type'], $_POST['insulin_amt'], $_POST['meas_id'], $_SESSION['USERID']);
				} else {
					echo 'Some fields not set.';
					return;
				}

			case 'getMeasurements':
				if(isset($_POST['start']) && isset($_POST['limit']) && isset($_SESSION['USERID'])){
					return getMeasurements($_POST['start'], $_POST['limit'], $_SESSION['USERID'], $userLoggedIn);
				} else {
					echo 'Some fields not set.';
					return;
				}
				
			case 'deleteMeasurement':
				if(isset($_POST['meas_id']) && isset($_SESSION['USERID'])){
					return deleteMeasurement($_POST['meas_id'], $_SESSION['USERID']);
				} else {
					echo 'Some fields not set.';
					return;
				}

			case 'getAverage':
				if(isset($_POST['num_days'])){
					return getAverageMeas($_POST['num_days']);
				} else {
					echo 'Some fields not set.';
					return;
				}

			default:
				echo "-1";
		}
	}

	/*******************
	*    API METHODS   *
	*******************/
	
	//Take in measurement info and store to database
	function submitMeasurement($type, $time, $level, $insulin_type, $insulin_amt, $userid, $loggedIn){
		$responseArray = array();
		$query = "INSERT INTO measurements(CheckCategory, Timestamp, GlucoseLevel, InsulinType, InsulinAmt, UserID) 
				  VALUES('{$type}', '{$time}', {$level}, '{$insulin_type}', '{$insulin_amt}', '{$userid}')";
		if(mysql_query($query)){
			$respQuery = "SELECT * FROM measurements WHERE CheckCategory='{$type}' AND 
						  Timestamp='{$time}' AND GlucoseLevel={$level} AND
						  InsulinType='{$insulin_type}' AND InsulinAmt={$insulin_amt} AND UserID={$userid}";
			if($resp = mysql_query($respQuery)){
				$r = mysql_fetch_assoc($resp);
				if ($r == false){
					$response["status"] = "Failure";
				} else {
					$response["status"] = "Success";
				}
				$response["meas"] = $r;
			} else {
				$response["status"] = mysql_error();
			}
		} else {
			$response["status"] = "Failure";
		}
		$response["loggedIn"] =  $loggedIn ? 'true' : 'false';
		echo json_encode($response);
	}
	
	//Take in measurement info and edit the selected measurement
	function editMeasurement($type, $time, $level, $insulin_type, $insulin_amt, $meas_id, $userid){
		$response = array();
		$query = "UPDATE measurements
				  SET CheckCategory='{$type}',
				  	  Timestamp='{$time}',
				  	  GlucoseLevel={$level},
				  	  InsulinType='{$insulin_type}',
				  	  InsulinAmt={$insulin_amt}
				  WHERE MeasID={$meas_id} AND UserID={$userid}";
		if(mysql_query($query)){
			$responseQuery = "SELECT * FROM measurements WHERE MeasID={$meas_id} AND UserID={$userid}";
			if($resp = mysql_query($responseQuery)){
				$r = mysql_fetch_assoc($resp);
				$response["status"] = "Success";
				$response["meas"] = $r;
			} else {
				$response["status"] = "Failure2";
				$response["error"] = mysql_error();		
			}
		} else {
			$response["status"] = "Failure";
			$response["error"] = mysql_error();
		}
		echo json_encode($response);
	}
	
	//Get and return "limit" measurements
	function getMeasurements($start, $limit, $userid, $loggedIn){
		$query = "";
		if ($limit == 0){
			$query = "SELECT * FROM measurements WHERE UserID={$userid} ORDER BY Timestamp DESC";
			
		} else {
			$query = "SELECT * FROM measurements WHERE UserID={$userid} ORDER BY Timestamp DESC limit {$start}, {$limit}";
			
		}
		if($results = mysql_query($query)){
			$rows = array();
			while($r = mysql_fetch_assoc($results)){
				$rows[] = $r;
			}
		}
		$result["rows"] = $rows;
		$result["loggedIn"] =  $loggedIn ? 'true' : 'false';
		echo json_encode($result);
	}
	
	//Delete a measurement from the database
	function deleteMeasurement($meas_id, $userid){
		$response = array();
		$query = "DELETE FROM measurements WHERE MeasID={$meas_id} AND UserID={$userid}";
		if(mysql_query($query)){
			$response["status"] = "Success";
			$response["deleted_id"] = $meas_id;
		} else {
			$response["status"] = "Failure";
			$response["error"] = mysql_error();
		}
		echo json_encode($response);
	}

	function getAverageMeas($days){
		$response = array();
		$response["status"] = "Success";
		$response["average"] = getAverage($days);
		echo json_encode($response);
	}

?>