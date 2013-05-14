<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manager extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->helper('url');		
		$this->load->database();
		$this->load->library('Shibboleth_authentication_service', '','shib_auth');

		if ($user = $this->shib_auth->verify_user() == false) {
			redirect('login');
		}
		// save the user somewhere
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

