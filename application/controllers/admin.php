<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();

		$this->load->helper('url');
		$this->load->library('shibboleth_authentication_service', NULL, 'shib_auth');
		$user = $this->shib_auth->verify_user();
		if ($user == false) {
			redirect('auth');
   		} elseif ($user !== false && !$user->isAdmin()) {
    			// TODO: redirect to not allowed
				show_404();
			}
		$this->load->database();		
	}

	public function index()
	{	
		$this->load->view('db_admin');
	}

	public function create_tables()
	{
		$this->load->library('migration');
		if ( ! $this->migration->version(1))
		{
			show_error($this->migration->error_string());
		}
		$this->load->view('db_setup_complete');
	}

	public function update_tables()
	{
		$this->load->library('migration');
		if ( ! $this->migration->current())
		{
			show_error($this->migration->error_string());
		}
		$this->load->view('db_setup_complete');
	}

	public function users() {
		$this->load->library('Crud_service');		
		try{
			$crudOutput = $this->crud_service->getUsersCrud();
			$this->load->view('crud.php',$crudOutput);
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		
	}

	public function user_requests() {
		$this->load->library('Crud_service');		
		try{
			$crudOutput = $this->crud_service->getUserRequestsCrud();
			$this->load->view('crud.php',$crudOutput);
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}

	}

	/**
	 *
	 */
	public function accept_user_request($pId) {
    	echo $pId;
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */
