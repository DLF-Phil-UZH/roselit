<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends CI_Controller {
	
	public function __construct() {
		parent::__construct();

		// if already logged in, redirect to manager
		$this->load->library('shibboleth_authentication_service', NULL, 'shib_auth');
		$this->load->helper('url');
		if ($user = $this->shib_auth->verify_user() !== false) {
			// if there is a user and he has access, redirect to
			// the manager page
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
		if ($this->shib_auth->verify_shibboleth_session()) {
			$this->load->view('request_access');
		} else {
			$this->load->view('login');
		}
	}

	/**
	 *
	 */
	public function request_access() {
		if (!$this->shib_auth->verify_shibboleth_session()) {
			redirect('auth');
		}
		$this->load->database();
		$lAaiId = $_SERVER['Shib-SwissEP-UniqueID'];
		// check if request already exists
		$lQuery = $this->db->get_where('user_requests', array("aaiId" => $lAaiId), 1);
		if ($lQuery->num_rows() > 0) {
			$lRow = $lQuery->row();
			// this user has already requested access
			// tell him that
			$lRequestDate = $lRow->created;
		} else {
			// ok make an access request entry in the db
			$lFirstname = $_SERVER['Shib-InetOrgPerson-givenName'];
			$lLastname = $_SERVER['Shib-Person-surname'];
			$lEmail = $_SERVER['Shib-InetOrgPerson-mail'];

			$lData = array(
						'aaiId' => $lAaiId,
						'firstname' => $lFirstname,
						'lastname' => $lLastname,
						'email' => $lEmail
						); 
			$this->db->insert('user_requests', $lData);
			$lRequestDate = date_format(new DateTime(), 'Y-m-d H:i:s');
		}
		$this->load->view('access_requested', array('request_date' => $lRequestDate));
	}

	/**
	 *
	 */
	public function logout() {
    
	}
}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */

