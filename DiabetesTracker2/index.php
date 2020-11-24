<?php
include_once('setup.php');
include_once('config.php');
include_once('datamodel/datamodel.php');
include_once('dbproxy/dbproxy.php');
include_once('Slim/Slim.php');
include_once('dbproxy/medoo.php');
include_once('datamodel/masterdata.php');
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
$app->notFound(function() use ($app){
		readfile("404.html");
		http_response_code(404);
	});
$app->get('/', function(){
		//TODO: This is where I'll build the main page.
		echo "Welcome to DiabetesTracker 2.0!";
	});

//Level endpoints
$app->get('/api/level/:eventid', function ($eventid) use ($config, $app) {
		$proxy = new DBProxy();
		$return = $proxy->getLevel($config, $eventid);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});
$app->put('/api/level', function() use ($config, $app){
		$proxy = new DBProxy();
		$body = json_decode($request->getBody(), true);
		$level = new Level($body);
		$return = $proxy->updateLevel($config, $level);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});
$app->delete('/api/level/:id', function($id) use ($config, $app){
		$proxy = new DBProxy();
		$return = $proxy->deleteLevel($config, $id);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});

//Injection endpoints
$app->get('/api/injections/:eventid', function ($eventid) use ($config, $app){
		$proxy = new DBProxy();
		$return = $proxy->getInjections($config, $eventid);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});
$app->put('/api/injection', function() use ($config, $app){
		$proxy = new DBProxy();
		$body = json_decode($request->getBody(), true);
		$inj = new Injection($body);
		$return = $proxy->updateInjection($config, $inj);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});
$app->delete('/api/injection/:id', function($id) use ($config, $app){
		$proxy = new DBProxy();
		$return = $proxy->deleteInjection($config, $id);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});

//Event endpoints
$app->get('/api/event/:id', function ($id) use ($config, $app){
		$proxy = new DBProxy();
		$return = $proxy->getEvent($config, $id);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});
$app->post('/api/event', function() use ($config, $app){
		$app = \Slim\Slim::getInstance();
		$request = $app->request();
		$body = json_decode($request->getBody(), true);
		//$body = json_decode('{"id":"5429","timestamp":"2015-06-22 23:00:00","level":{"id":"10","level":"134","user":"1","eventid":"5429"},"injections":[{"id":"10","insulin_type":"1","units":"3","eventid":"5429","user":"1"},{"id":"10","insulin_type":"2","units":"30","eventid":"5429","user":"1"}],"user":"1","purpose":"1"}',true);
		$event = new Event($body);
		$proxy = new DBProxy();
		$return = $proxy->postEvent($config, $event);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});
$app->put('/api/event', function() use ($config, $app){
		$proxy = new DBProxy();
		$body = json_decode($request->getBody(), true);
		$event = new Event($body);
		$return = $proxy->updateEvent($config, $event);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});
$app->delete('/api/event/:id', function($id) use ($config, $app){
		$proxy = new DBProxy();
		$return = $proxy->deleteEvent($config, $id);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});

//Masterdata endpoints
$app->get('/api/masterdata', function() use ($config, $app){
		$proxy = new DBProxy();
		$return = $proxy->getMasterData($config, $app);
		if($return->status == true){echo json_encode($return);} else {$app->notFound();}
	});

//Stats endpoints
$app->get('/api/average/:days', function($days) use ($config, $app){
		$proxy = new DBProxy();
		$end = new DateTime();
		$start = new DateTime();
		$start->modify("-".$days." days");
		$levels = $proxy->getEventsByPeriod($config, $start, $end);
		$eventCount = sizeof($levels);
		$levelSum = 0;
		foreach($levels as $l){
			$levelSum += $l["level"];
		}
		$average = $levelSum / $eventCount;
		echo json_encode(array(
				"start" => $start,
				"end" => $end,
				"average" => $average
			));
	});

//Legacy endpoints
$app->get('/measurements', function() use ($config, $app){
		$db = new medoo($config["db"]);
		$datas = $db->select("measurements", "*");
		echo json_encode($datas);
	});
$app->get('/migrate', function() use ($config, $app){
		$db = new medoo($config["db"]);
	$old = $db->select("measurements", "*");
	foreach($old as $m){
		$purpose = 0;
		$insulin = 0;
		switch($m["CheckCategory"]){
			case "Before Meal":
				$purpose = 1;
				break;
			case "Before Bed":
				$purpose = 2;
				break;
			case "Feeling Low":
				$purpose = 3;
				break;
			case "Feeling High":
				$purpose = 4;
				break;
			case "After Meal":
				$purpose = 5;
				break;
		}
		switch($m["InsulinType"]){
			case "Novolog":
				$insulin = 1;
				break;
			case "Levemir":
				$insulin = 2;
				break;
			case "Humalog":
				$insulin = 3;
				break;
		}
		$new_meas_id = $db->insert("events", array(
			"timestamp"=>$m["Timestamp"],
			"user"=>$m["UserID"],
			"purpose"=>$purpose
		));
		$db->insert("levels", array(
			"user"=>$m["UserID"],
			"level"=>$m["GlucoseLevel"],
			"eventid"=>$new_meas_id
		));
		if($insulin != 0){
			$db->insert("injections", array(
				"user"=>$m["UserID"],
				"insulin_type"=>$insulin,
				"units"=>$m["InsulinAmt"],
				"eventid"=>$new_meas_id
			));
		}
	}
	echo "Done.";
	});

$app->run();
?>