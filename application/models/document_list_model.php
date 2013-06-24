<?php

class Document_list_model extends Abstract_base_model{

	private $title; // Title of list
	// Creator and admin are temporarily only represented as foreign keys (person ID) in the Document_list_model object
	// Might be changed later to entire Person_model objects
	private $creator; // Creator
	private $admin; // Admin
	private $lastUpdated; // Date and time of last update (type DateTime)
	private $created; // Date and time of creation (type DateTime)
	private $published; // Flag (binary), has value 1 if list has been published anywhere at least once
	private $documents = array(); // Document objects that belong to the list, might be used later
	
	public function __construct($pTitle, $pCreator){
		$this->title = $pTitle;
		$this->creator = $pCreator;
	}
	
	// Setters
	
	public function setId($pId){
		$this->id = $pId;
	}
	
	public function setTitle($pTitle){
		$this->title = $pTitle;
	}
	
	// Temporarily only as foreign key (person ID)
	public function setCreator($pCreator){
		$this->creator = $pCreator;
	}
	
	// Temporarily only as foreign key (person ID)
	public function setAdmin($pAdmin){
		$this->admin = $pAdmin;
	}
	
	public function setLastUpdated(DateTime $pLastUpdated){
		$this->lastUpdated = $pLastUpdated;
	}
	
	public function setCreated(DateTime $pCreated){
		$this->created = $pCreated;
	}
	
	public function setPublished($pPublished){
		$this->published = $pPublished;
	}
	
	// Getters
	
	public function getId(){
		return $this->id;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getCreator(){
		return $this->creator;
	}
	
	public function getAdmin(){
		return $this->admin;
	}
	
	public function getLastUpdated(){
		return $this->lastUpdated;
	}
	
	public function getCreated(){
		return $this->created;
	}
	
	public function getPublished(){
		return $this->published;
	}
	
	// Returns ids of documents, might later be deleted
	public function getDocumentIds(){
		return array_keys($this->documentIds);
	}
	
	// Returns document objects, might be used later
	public function getDocuments(){
		return $this->documents;
	}
	
	// Other methods
	
	// Returns true, if document list has not been registered in database so far
	public function isNew(){
		$lIsNew = false;
		if($this->id == null){
			$lIsNew = true;
		}
		return $lIsNew;
	}
	
	public function addDocumentById($pDocumentId){
		if(!array_key_exists($pDocumentId, $this->documents)){
			$this->documents[$lId] = $lId; // Entry: Id as key of array, id as value at key of array
		}
	}
	
	// Document object as parameter
	public function addDocument(Document_model $pDocument){
		$lId = $pDocument->getId();
		if(!array_key_exists($lId, $this->documents)){
			$this->documents[$lId] = $pDocument; // Entry: Id as key of array, id as value at key of array
		}
	}
	
	public function removeDocumentById($pDocumentId){
		if(array_key_exists($pDocumentId, $this->documents)){
			unset($this->documents[$lId]);
		}
	}
	
	// Document object as parameter
	public function removeDocument(Document_model $pDocument){
		$lId = $pDocument->getId();
		if(array_key_exists($lId, $this->documents)){
			unset($this->documents[$lId]);
		}
	}
}

