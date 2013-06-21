<?php

class Document_list_model extends Abstract_base_model{

	private $title; // Title of list
	// Creator and admin are temporarily only represented as foreign keys (person ID) in the Document_list_model object
	// Might be changed later to entire Person_model objects
	private $creator; // Creator
	private $admin; // Admin
	private $lastUpdated; // Date and time of last update (type DateTime)
	private $created; // Date and time of creation (type DateTime)
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
	
	// Returns ids of documents, might later be deleted
	public function getDocumentIds(){
		return array_keys($this->documentIds);
	}
	
	// Returns document objects, might be used later
	public function getDocuments(){
		return $this->documents;
	}
	
	/*
	Returns document objects, sorted by the following three criteria:
	1. authors
	2. year
	3. title
	
	Source:
	http://stackoverflow.com/questions/6053994/using-usort-in-php-with-a-class-private-function
	*/
	public function getDocumentsSorted(){
		$lDocuments = $this->documents; // Copy document array first, so that original array keeps untouched
		usort($lDocuments, array("Document_list_model", "compareDocuments")); // Sorting directly on array
		return $lDocuments;
	}
	
	// Other methods
	
	/*
	Used for sorting the document array
	
	Compares documents based on three criteria:
	1. authors (alphabetically increasing)
	2. year (of first publication inside "[" and "]" if given, increasing)
	3. title (alphabetically increasing)
	
	Source:
	http://stackoverflow.com/questions/2286597/using-usort-in-php-to-sort-an-array-of-objects
	*/
	private static function compareDocuments($lDocument1, $lDocument2){
		
		// First sort criterion: authors
		// If author strings are not identical, return alphabetically smaller string
		$lAuthorComparison = strcasecmp($lDocument1->getAuthors(), $lDocument2->getAuthors());
		if($lAuthorComparison != 0){
			return ($lAuthorComparison < 0) ? -1 : 1;
		}
		
		// Second sort criterion: year
		$lYear1 = $lDocument1->getYear();
		$lYear2 = $lDocument2->getYear();
		// Extract first publication at first, if given
		if(strpos($lYear1, "[") !== false && strpos($lYear1, "]") !== false){
			$lYear1 = substr($lYear1, strpos($lYear1, "[") + 1, strpos($lYear1, "]"));
		}
		// Extract first publication at first, if given
		if(strpos($lYear2, "[") !== false && strpos($lYear2, "]") !== false){
			$lYear2 = substr($lYear2, strpos($lYear2, "[") + 1, strpos($lYear2, "]"));
		}
		// If years are not identical, return smaller year
		if((int)$lYear1 != (int)$lYear2){
			return ($lYear1 < $lYear2) ? -1 : 1;
		}
		
		// Third sort criterion: title
		$lTitleComparison = strcasecmp($lDocument1->getTitle(), $lDocument2->getTitle());
		if($lTitleComparison != 0){
			return ($lTitleComparison < 0) ? -1 : 1;
		}
		
		// If all preceding criterions were not decisive, return 0 (random sort)
		return 0;
	}
	
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

