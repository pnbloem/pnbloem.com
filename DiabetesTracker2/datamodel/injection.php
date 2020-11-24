<?php
	class Injection
	{
		//properties
		public $id;
		public $insulin_type;
		public $units;
		public $eventid;
		public $user;
		
		//methods
		public function __construct($data = null) {
			if ($data != null){
				$this->id = $data["id"];
				$this->insulin_type = $data["insulin_type"];
				$this->units = $data["units"];
				$this->eventid = $data["eventid"];
				$this->user = $data["user"];
			}			
		}	
	}
?>