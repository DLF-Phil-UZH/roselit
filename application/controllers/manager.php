<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manager extends CI_Controller {

	private $lcrudService;
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
		$this->load->helper('url');
		$this->load->library('Crud_service');			
	}
	
	private function _render_output($output = null)
	{
		$this->load->view('crud.php',$output);	
	}

			
	public function index()
	{
		$this->_render_output((object)array('output' => '' , 'js_files' => array() , 'css_files' => array()));
	}
	
	public function documents()
	{
	
		try{
			$crudOutput = $this->crud_service->getDocumentsCrud();
			$this->_render_output($crudOutput);
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}

	public function lists()
	{
		try {
			$crudOutput = $this->crud_service->getDocumentListsCrud();
			$this->_render_output($crudOutput);
		} catch(Exception $e) {
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}
}

