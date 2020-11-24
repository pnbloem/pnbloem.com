<?php
class Poem
{
	//properties
	public $id;
	public $title;
	public $date;
	public $firstname;
	public $lastname;
	public $category;
	public $lines;
	public $public;

	function __construct($data){
		if($data != null){
			$this->id = $data['id'];
			$this->title = $data['title'];
			$this->date = $data['date'];
			$this->firstname = $data['firstname'];
			$this->lastname = $data['lastname'];
			$this->category = $data['category'];
			if($data['public'] == 'on' || $data['public'] == 1){
				$this->public = 1;
			} else {
				$this->public = 0;
			}
			if($data['lines'] !== null){
				$this->lines = array();
				foreach ($data['lines'] as $line){
					array_push($this->lines, new Line($line));	
				}
			} else {
				$this->lines=array();
			}
		} else {
			$this->lines = array();
		}
	}
}
class Line
{
	//properties
	public $id;
	public $text;
	public $line;

	function __construct($data){
		if($data !== null){
			$this->id = $data['id'];
			$this->text = $data['text'];
			$this->line = $data['line'];
		}
	}
}
?>