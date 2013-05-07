<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct() {
		parent::__construct();
		
		$this->load->database();
		$this->load->helper('url');
	}


	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{	
		$this->load->database();
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
		$this->load->library('CrudService');		
		try{
			$crudOutput = $this->crudservice->getUsersCrud();
			$this->load->view('crud.php',$output);
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		
	}
}

/* End of file install.php */
/* Location: ./application/controllers/dbadmin.php */
