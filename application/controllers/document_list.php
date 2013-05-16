<?php

require_once("document_model.php");

// Based on a CI-Tutorial: http://ellislab.com/codeigniter/user-guide/tutorial/news_section.html
class Document_list extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('document_list_mapper');
	}

	public function index()
	{
		$data['documentList'] = $this->Document_list_mapper->get(1); // Temporarily hard coded for testing
		$this->load->view('document_list/index', $data);
	}
	
}