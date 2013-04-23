<?php

// Import abstract class for models
require_once("AbstractModel.php");
// Import Person class
require_once("Person.php");

class DocumentList extends Application_Model_Abstract{

	protected $id = integer; // List ID
	private $name = string; // Name of list
	private $documents = array(); // IDs of contained documents
	private $owner = Person; // Administrator of the file (temporary only one person)
	
	private static $count = 0; // For the list IDs
	
	public function __construct($pName, $pDocuments = array(), $pOwner = Person){
		self::$count += 1; // Wird wohl nicht funktionieren
		$this->id = self::$count; // Wird wohl nicht funktionieren
		$this->name = $pName;
		$this->documents = $pDocuments;
		$this->owner = $pOwner;
	}
	
	// Setters
	
	public function setId($pId){
		$this->id = $pId;
	}
	
	public function setName($pName){
		$this->name = $pName;
	}
	
	// Temporarily only one Owner
	public function setOwner(Person $pPerson){
		$this->owner = $pPerson;
	}
	
	// Getters
	
	public function getId(){
		return $this->id;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function getDocuments(){
		return $this->documents;
	}
	
	public function getOwner(){
		return $this->owner;
	}
	
	// Other methods
	
	public function addDocument(Document $pDocument){
		$id = $pDocument->getId();
		if(!array_key_exists($id)){
			$this->documents[$id] = $id; // Entry: Id as key of array, id as value at key of array
		}
	}
	
	public function removeDocument(Document $pDocument){
		$id = $pDocument->getId();
		if(array_key_exists($id)){
			unset($this->documents[$id]);
		}
	}
}

?>