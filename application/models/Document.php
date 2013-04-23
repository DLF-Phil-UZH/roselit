<?php

// Import abstract class for models
require_once("AbstractModel.php");
// Import Person class
require_once("Person.php");

class Document extends Application_Model_Abstract{

	protected $id = integer; // Document ID
	private $explicitId = string; // Combined document ID
	private $authors = array(); // Array of persons
	private $year = integer; // Release year
	private $title = string; // Title (chapter or article)
	private $bookTitle = string; // Title of publication
	private $publisher = array(); // Array of persons
	private $publicationPlace = string; // City of publication
	private $publishingHouse = string; // Name of publishing house
	private $startPage = integer; // Start page in the book/magazine
	private $endPage = integer; // End page in the book/magazine
	private $fileFormat = string; // File type
	private $sizeOfFile = float; // File size
	private $scanner = Person; // Scanning person
	private $owner = Person; // Administrator of the file
	
	private static $count = 0; // For the document IDs
	
	public function __construct($pTitle, $pAuthors, $pYear, $pBookTitle, $pPublisher, $pPublicationPlace, $pPublishingHouse, $pStartPage, $pEndPage, $pFileFormat, $pFileSize, $pScanner, $pOwner){
		self::$count += 1; // Wird nicht funktionieren
		$this->id = self::$count; // Wird nicht funktionieren
		$this->authors = $pAuthors;
		$this->year = $pYear;
		$this->title = $pTitle;
		$this->bookTitle = $pBookTitle;
		$this->publisher = $pPublisher;
		$this->publicationPlace = $pPublicationPlace;
		$this->publishingHouse = $pPublishingHouse;
		$this->startPage = $pStartPage;
		$this->endPage = $pEndPage;
		$this->fileFormat = $pFileFormat;
		$this->sizeOfFile = $pFileSize;
		$this->scanner = $pScanner;
		$this->owner = $pOwner;
	}
	
	// Setters
	
	public function setId($pId){
		$this->id = $pId;
	}
	
	public function setAuthors($pAuthors){
		$this->authors = $pAuthors;
	}
	
	public function setYear($pYear){
		$this->year = $pYear;
	}
	
	public function setTitle($pTitle){
		$this->title = $pTitle;
	}
	
	public function setBookTitle($pBookTitle){
		$this->bookTitle = $pBookTitle;
	}
	
	public function setPublisher($pPublisher){
		$this->publisher = $pPublisher;
	}
	
	public function setPublicationPlace($pPublicationPlace){
		$this->publicationPlace = $pPublicationPlace;
	}
	
	public function setPublishingHouse($pPublishingHouse){
		$this->publishingHouse = $pPublishingHouse;
	}
	
	public function setStartPage($pStartPage){
		$this->startPage = $pStartPage;
	}
	
	public function setEndPage($pEndPage){
		$this->endPage = $pEndPage;
	}
	
	public function setFileFormat($pFileFormat){
		$this->fileFormat = $pFileFormat;
	}
	
	public function setSizeOfFile($pFileSize){
		$this->sizeOfFile = $pFileSize;
	}
	
	public function setScanner(Person $pScanner){
		$this->scanner = $pScanner;
	}
	
	// Temporarily only one Owner
	public function setOwner(Person $pPerson){
		$this->owner = $pPerson;
	}
	
	// Getters
	
	public function getId(){
		return $this->id;
	}
	
	public function getAuthors(){
		return $this->authors;
	}
	
	public function getYear(){
		return $this->year;
	}
	
	public function getTitle(){
		return $this->title;
	}
	
	public function getBookTitle(){
		return $this->bookTitle;
	}
	
	public function getPublisher(){
		return $this->publisher;
	}
	
	public function getPublicationPlace(){
		return $this->publicationPlace;
	}
	
	public function getPublishingHouse(){
		return $this->publishingHouse;
	}
	
	public function getStartPage(){
		return $this->startPage;
	}
	
	public function getEndPage(){
		return $this->endPage;
	}
	
	public function getFileFormat(){
		return $this->fileFormat;
	}
	
	public function getSizeOfFile(){
		return $this->sizeOfFile;
	}
	
	public function getScanner(){
		return $this->scanner;
	}
	
	// Temporarily only one Owner
	public function getOwner(){
		return $this->owner;
	}
	
}

?>