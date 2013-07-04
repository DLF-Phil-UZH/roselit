<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manager extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->database();
		$this->load->library('Shibboleth_authentication_service', '','shib_auth');
		$user = $this->shib_auth->verify_user();
		if ($user == false) {
			redirect('auth');
		} 
		if ($user !== false && $user->getRole() == 'new') {
			show_error('404');
		}
		// save the user somewhere
		$this->load->library('Crud_service');			
	}
	
	private function _render_output($output = null)
	{
        $this->load->view('header.php', array('title' => 'RoSeLit'));
		$this->load->view('crud.php',$output);	
	}

			
	public function index()
	{
        redirect(site_url('/manager/documents'));
	}
	
    /**
     * Display a CRUD table for Document_models
     */
	public function documents()
	{
	
		try{
			$crudOutput = $this->crud_service->getDocumentsCrud();
			$this->_render_output($crudOutput);
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
    }

    /**
     * Display a CRUD table for Document_list_models  
     */
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

