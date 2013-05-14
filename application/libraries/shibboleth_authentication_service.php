<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shibboleth_authentication_service {

	public function __construct() {
		// TODO: load settings from config file
		$ci = &get_instance();
		$ci->load->database();
	}

	public function verify_shibboleth_session() {
		// TODO: check if shibboleth sesssion exists
		if (isset($_SERVER['Shib-Identity-Provider'])) {
			return true;
		}
		return false;
	}

	public function verify_user() {
		if ($this->verify_shibboleth_session() !== false) {
			// check if a user with that shibboleth id exists in the db
			// if not create one, but set role to "not granted"
			$user = true; // $this->user_mapper->get(array('aaiId' => $aaiId));
			if ($user !== false) {
				return $user;
			}	
		}
		return false;
	}

}

/* End of file Shibboleth_authentication_service.php */
