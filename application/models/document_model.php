<?php

class Document_model extends Abstract_base_model{

	private $explicitId; // Combined document ID
	private $fileName; // Name of document file
	private $title; // Title (chapter or article)
	private $authors; // Authors of document
	private $publication; // Title of publication
	private $editors; // Editors of publication
	private $publishingHouseAndPlace; // Name and place of publishing house
	private $year; // Year of publication, optionally with edition in superior (<sup> element in HTML)
	private $pages; // Start page in the book/magazine
	// Creator and admin are temporarily only represented as foreign keys (person ID) in the Document_list_model object
	// Might be changed later to entire Person_model objects
	private $creator; // Creator
	private $admin; // Admin
	private $lastUpdated; // Date and time of last update (type DateTime)
	private $created; // Date and time of creation (type DateTime)
	
	public function __construct($pExplicitId, $pFileName, $pTitle, $pAuthors, $pPublication, $pEditors, $pPublishingHouseAndPlace, $pYear, $pPages, $pCreator){
		$this->explicitId = $pExplicitId;
		$this->fileName = $pFileName;
		$this->title = $pTitle;
		$this->authors = $pAuthors;
		$this->publication = $pPublication;
		$this->editors = $pEditors;
		$this->publishingHouseAndPlace = $pPublishingHouseAndPlace;
		$this->year = $pYear;
		$this->pages = $pPages;
		$this->creator = $pCreator;
	}
	
	// Setters
	
	public function setId($pId){
		$this->id = $pId;
	}
	
	public function setExplicitId($pExplicitId){
		$this->explicitId = $pExplicitId;
	}
	
	public function setFileName($pFileName){
		$this->fileName = $pFileName;
	}
	
	public function setTitle($pTitle){
		$this->title = $pTitle;
	}
	
	public function setAuthors($pAuthors){
		$this->authors = $pAuthors;
	}
	
	public function setPublication($pPublication){
		$this->publication = $pPublication;
	}
	
	public function setEditors($pEditors){
		$this->editors = $pEditors;
	}
	
	public function setPublishingHouseAndPlace($pPublishingHouseAndPlace){
		$this->publishingHouseAndPlace = $pPublishingHouseAndPlace;
	}
	
	public function setYear($pYear){
		$this->year = $pYear;
	}
	
	public function setPages($pPages){
		$this->pages = $pPages;
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
	
	public function getExplicitId(){
		return $this->explicitId;
	}
	
	public function getFileName(){
		return $this->fileName;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getAuthors(){
		return $this->authors;
	}
	
	public function getPublication(){
		return $this->publication;
	}
	
	public function getEditors(){
		return $this->editors;
	}
	
	public function getPublishingHouseAndPlace(){
		return $this->publishingHouseAndPlace;
	}
	
	public function getYear(){
		return $this->year;
	}
	
	public function getPages(){
		return $this->pages;
	}
	
		// Temporarily only as foreign key (person ID)
	public function getCreator(){
		return $this->creator;
	}
	
	// Temporarily only as foreign key (person ID)
	public function getAdmin(){
		return $this->admin;
	}
	
	public function getLastUpdated(){
		return $this->lastUpdated;
	}
	
	public function getCreated(){
		return $this->created;
	}
	
	// Other methods
	
	// Returns true, if document has not been registered in database so far
	public function isNew(){
		$lIsNew = false;
		if($this->id == null){
			$lIsNew = true;
		}
		return $lIsNew;
	}

}

