<?php

// Based on a CI-Tutorial: http://ellislab.com/codeigniter/user-guide/tutorial/news_section.html
class Document_list extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('document_list_mapper');
		$this->load->helper('url');
	}

	public function view($pId){
		$this->output->set_header("Content-Type: text/html; charset=utf-8");
		$lDocumentList = $this->document_list_mapper->get($pId);
		if($lDocumentList->getPublished()){
			$this->load->view('document_list/index', array("documentList" => $lDocumentList));
		}
		else{
			echo "Anzeige nicht möglich: Ungültige Dokumentenliste.";
		}
	}
	
}