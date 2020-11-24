<?php
	class Event
	{
		//properties
		public $id;
		public $timestamp;
		public $level;
		public $injections;
		public $user;
		
		public function __construct($data = null){
			if($data != null){
				$this->timestamp = $data["timestamp"];
				$this->id = $data["id"];
				$this->user = $data["user"];
				$this->purpose = $data["purpose"];
				if($data["level"]){
					$this->level = new Level($data["level"]);					
				}
				if($data["injections"]){
					$this->injections = array();
					foreach ($data["injections"] as $inj){
						array_push($this->injections, new Injection($inj));
					}
				}
			}
		}
	}
	
?>