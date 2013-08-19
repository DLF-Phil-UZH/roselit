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
   		} elseif ($user->isAdmin() === false) {
			$this->adminaccess = false;
        } else {
			$this->adminaccess = true;
		}
		$this->load->database();
	}

	public function index(){
		// If user is admin
		if($this->adminaccess === true){
			$this->users();
		}
		// If user is not an admin
		else{
			$this->access_denied();
		}
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

	public function users(){
		if($this->adminaccess === true){
			$this->load->library('Crud_service');
			try{
				$crudOutput = $this->crud_service->getUsersCrud();
				$this->load->view('header', array('title' => 'RoSeLit: Administration',
											  'page' => 'users',
											  'width' => 'normal',
											  'access' => ($this->shib_auth->verify_user() !== false),
											  'admin' => $this->adminaccess));
				$this->load->view('crud.php',$crudOutput);
				$this->load->view('footer');
			}catch(Exception $e){
				show_error($e->getMessage().' --- '.$e->getTraceAsString());
			}
		}
		// If user is not an admin
		else{
			$this->access_denied();
		}
	}

	public function user_requests(){
		if($this->adminaccess === true){
			$this->load->library('Crud_service');
			try{
				$crudOutput = $this->crud_service->getUserRequestsCrud();
				$this->load->view('header', array('title' => 'RoSeLit: Administration',
											  'page' => 'user_requests',
											  'width' => 'normal',
											  'access' => ($this->shib_auth->verify_user() !== false),
											  'admin' => $this->adminaccess));
				$this->load->view('crud.php',$crudOutput);
				$this->load->view('footer');
			}catch(Exception $e){
				show_error($e->getMessage().' --- '.$e->getTraceAsString());
			}
		}
		// If user is not an admin
		else{
			$this->access_denied();
		}
	}
	
	public function access_denied(){
		$this->load->view('header', array('title' => 'RoSeLit: Zugriff verweigert',
										  'page' => 'access_denied',
										  'width' => 'small',
										  'access' => ($this->shib_auth->verify_user() !== false)));
		$this->load->view('access_denied');
		$this->load->view('footer');
	}

	/**
	 *
	 */
	public function accept_user_request($pId) {
		// create a user account for the specified id:
        $this->load->model('user_mapper');
        $status = $this->user_mapper->create_user_from_request($pId);
        // redirect to user_requests admin interface
        redirect(site_url() . '/admin/user_requests');
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */
