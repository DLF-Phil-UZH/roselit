﻿<?php

require_once("document_model.php");

class Document_mapper extends CI_Model {

	private $tableName = "documents"; // Name of database table
	private $docToListTable = "documents_documentLists"; // Name of mapping table
	
	/* As a document itself has no information about which document lists
	it belongs to, so there won't be made any changes in the table
	"documents_documentLists".
	If a document is deleted, the database will itself delete
	all corresponding entries in "documents_documentLists" */
	
	public function save(Document_model $pDocument){
		// Table "document"
		$lData = array("explicitId" => $pDocument->getExplicitId(),
					   "fileName" => $pDocument->getFileName(),
					   "title" => $pDocument->getTitle(),
					   "authors" => $pDocument->getAuthors(),
					   "publication" => $pDocument->getPublication(),
					   "editors" => $pDocument->getEditors(),
					   "publishingHouseAndPlace" => $pDocument->getPublishingHouseAndPlace(),
					   "year" => $pDocument->getYear(),
					   "pages" => $pDocument->getPages(),
					   // Creator and admin temporarily as person ID
					   "creator" => $pDocument->getCreator(),
					   "admin" => $pDocument->getAdmin()
					   // Creator and admin as person_model objects (later)
					   // "creator" => $pDocument->getCreator()->getId(),
					   // "admin" => $pDocument->getAdmin()->getId(),
					   );
		if($pDocument->isNew()){
			$lData["created"] = (new DateTime())->format("Y-m-d H:i:s"); // Set current timestamp
			$this->db->insert($this->tableName, $lData);
			$pDocument->setId($this->db->insert_id()); // Add id generated by database to the document object
		}
		else{
			$lData["created"] = $pDocument->getCreated();
			$this->db->where("id", $pDocument->getId());
			$this->db->update($this->tableName, $lData);
		}
	}

	public function delete(Document_model $pDocument){
		if(!($pDocument->isNew())){
			$this->db->delete($this->tableName, array("id" => $pDocument->getId()));
		}
	}
	
	public function get($pId){
		$lQuery = $this->db->get_where($this->tableName, array("id" => $pId));
		// Check if there is a document with the id $pId in the database
		if($lQuery->num_rows() == 1){
			$lDocument = $this->_createDocument($lQuery->row());
			return $lDocument;
		}
		// Throw an exception if not
		else{
            throw new Exception('No document in database with id ' . $pId);
        }
	}
	
	public function getByListId($pDocumentListId){
		// Prepare query
		$this->db->select('*');
		$this->db->from($this->tableName);
		$this->db->join($this->docToListTable, "$this->tableName.id = $this->docToListTable.documentId", 'left');
		$this->db->where('documentListId', $pDocumentListId);
		// Execute query on database
		$lQuery = $this->db->get();
		// Create document array
		$lDocuments = array();
		foreach($lQuery->result() as $lRow){
			array_push($lDocuments, $this->_createDocument($lRow));
		}
		return $lDocuments;
	}
	
	private function _createDocument($pRow){
		$lDocument = new Document_model($pRow->explicitId,
										$pRow->fileName,
										$pRow->title,
										$pRow->authors,
										$pRow->publication,
										$pRow->editors,
										$pRow->publishingHouseAndPlace,
										$pRow->year,
										$pRow->pages,
										$pRow->creator);
		$lDocument->setId($pRow->id);
		$lDocument->setAdmin($pRow->admin);
		$lDocument->setLastUpdated(new DateTime($pRow->lastUpdated));
		$lDocument->setCreated(new DateTime($pRow->created));
		return $lDocument;
	}

}

