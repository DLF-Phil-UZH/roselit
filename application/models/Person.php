<?php

class Person{
	
	public $firstname = string;
	public $lastname = string;
	public $title = string;
	// public $email = string;
	public $gender = bool; // m <=> true; w <=> false
	
	// Constructor
	function __construct($pFirstname, $pLastname, $pTitle = "", /* $pEmail = "",$*/ $pGender = true){
		$this->firstname = $pFirstname;
		$this->lastname = $pLastname;
		$this->title = $pTitle;
		// $this->email = $pEmail;
		$this->gender = $pGender;
	}
	
	// Setters
	
	public function setFirstname($pFirstname){
		$this->firstname = $pFirstname;
	}
	
	public function setLastname($pLastname){
		$this->lastname = $pLastname;
	}
	
	public function setTitle($pTitle){
		$this->title = $pTitle;
	}
	
	public function setGender($pGender){
		$this->gender = $pGender;
	}
	
	// Getters
	
	public function getFirstname(){
		return $this->firstname;
	}
	
	public function getLastname(){
		return $this->lastname;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getGender(){
		return $this->gender;
	}
	
	// Other functions
	
	public function iterateVisible(){
		echo "Person::iterateVisible:\n";
		foreach($this as $key => $value){
			print "$key => $value\n";
		}
	}
}

?>