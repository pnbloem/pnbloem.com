<?php
	class Level
	{
		//properties
		public $id;
		public $level;
		public $user;
		public $eventid;
		
		//methods
		public function __construct($data = null) {
			if ($data != null){
				$this->id = $data["id"];
				$this->level = $data["level"];
				$this->user = $data["user"];
				$this->eventid = $data["eventid"];
			}			
		}	
	}	
?>