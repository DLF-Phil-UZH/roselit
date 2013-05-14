<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {
	
	public function __construct() {
		parent::__construct();

		// if already logged in, redirect to manager
		$this->load->library('shibboleth_authentication_service', NULL, 'shib_auth');
		$this->load->helper('url');
		if ($this->shib_auth->verify_shibboleth_session()) {
			if ($user = $this->shib_auth->verify_user() == false) {
				redirect('login/register');
			}
			redirect('manager/documents');
		}
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
		$this->load->view('login');
	}

	/**
	 *
	 */
	public function register() {
    	$this->load->view('register');
	}

	/**
	 *
	 */
	public function logout() {
    
	}
}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */

