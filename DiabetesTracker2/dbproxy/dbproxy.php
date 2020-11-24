<?php
	class DBProxy {
		function getEvent($config, $id){
			$db = new medoo($config["db"]);
			$return = new Response();
			$data = $db->select("events", "*", array(
				"id" => $id
			));
			if (sizeOf($data) == 0){
				$return->status = false;
				$return->message = "Invalid event ID.";
				return $return;
			}
			$returnData = new Event($data[0]);
			$returnData->injections = $this->getInjections($config, $id)->data;
			$returnData->level = $this->getLevel($config, $id)->data;
			$return->status = true;
			$return->data = $returnData;
			return $return;
		}
		function postEvent($config, $event){ 
			$db = new medoo($config["db"]);
			$return = new Response();
			$evt = $db->insert("events", array(
				"user" => $event->user,
				"purpose" => $event->purpose,
				"timestamp" => $event->timestamp
			));
			
			$lvl = $db->insert("levels", array(
				"user" => $event->level->user,
				"level" => $event->level->level,
				"eventid" => $evt
			));
			echo $lvl."<br/>";
			
			foreach($event->injections as $inj){
				$res = $db->insert("injections", array(
					"user" => $inj->user,
					"units" => $inj->units,
					"insulin_type" => $inj->insulin_type,
					"eventid" => $evt
				));
			}
			$return->status = true;
			$return->data = $evt;
			return $return;
		}
		function putEvent($event){
			$return = new Response();
			$db = new medoo($config["db"]);
			$db->update("events", $event);
			$return->status = true;
			return $return;	
		}
		function deleteEvent($id){
			$db = new medoo($config["db"]);
			$return = new Response();
			$db->delete("injections", array(
				"eventid" => $id
			));
			$db->delete("levels", array(
				"eventid" => $id
			));
			$db->delete("events", array(
				"id" => $id
			));
			$return->status = true;
			return $return;
		}
		
		function getInjections($config, $eventid){
			$returnData = array();
			$db = new medoo($config["db"]);
			$return = new Response();
			$data = $db->select("injections", "*", array(
				"eventid" => $eventid
			));
			foreach($data as $inj){
				array_push($returnData, new Injection($inj));
			}
			$return->status = true;
			$return->data = $returnData;
			return $return;
		}
		function postInjection($config, $inj) {
			$db = new medoo($config["db"]);
			$return = new Response();
			$inj = $db->insert("injections", $inj);
			$return->data = $inj;
			$return->status = true;
			return $return;
		}
		function putInjection($config, $inj) {
			$db = new medoo($config["db"]);
			$return = new Response();
			$inj = $db->update("injections", $inj);
			$return->status = true;
			$return->data = $inj;
			return $return;
		}
		function deleteInjection($id) {
			$db = new medoo($config["db"]);
			$return = new Response();
			$db->delete("injections", array(
				"id" => $id
			));
			$return->status = true;
			return $return;
		}
		
		function getLevel($config, $eventid) {
			$db = new medoo($config["db"]);
			$return = new Response();
			$data = $db->select("levels", "*", array(
				"eventid" => $eventid
			));
			$return->status = true;
			$return->data = new Level($data[0]);
			return $return;
		}
		function getLevelsByPeriod($config, $start, $end){
			$db = new medoo($config["db"]);
			$return = new Response();
			$query = "SELECT levels.level FROM events LEFT JOIN levels ON events.id = levels.eventid WHERE events.timestamp BETWEEN '".$start->format("Y-m-d H:i:s")."' AND '".$end->format("Y-m-d H:i:s")."';";
			$data = $db->query($query)->fetchAll();
			$return->status = true;
			$return->data = $data;
			return $return;
		}
		function postLevel($config, $level) {
			$db = new medoo($config["db"]);
			$return = new Response();
			$level = $db->insert("levels", $level);
			$return->status = true;
			$return->data = $level;
			return $return;	
		}
		function putLevel($config, $level) { 
			$db = new medoo($config["db"]);
			$return = new Response();
			$level = $db->update("levels", $level);
			$return->status = true;
			$return->data = $level;
			return $return;		
		}
		function deleteLevel($config, $id){ 
			$db = new medoo($config["db"]);
			$return = new Response();
			$db->delete("levels", array(
				"id" => $id
			));
			$return->status = true;
			return $return;	
		}
		
		function getMasterData($config){
			$return = new Response();
			$masterdata = new MasterData();
			//TODO: Exception handling
			$database = new medoo($config["db"]);
			
			//Get purposes for master data
			$datas = $database->select("purposes", "*");
			$masterdata->purposes = $datas;
			
			//Get insulin types for master data
			$datas = $database->select("insulin_types", "*");
			$masterdata->insulin_types = $datas;
			$return->status = true;
			$return->data = $masterdata;
			return $return;
		}

	}
		
	
?>