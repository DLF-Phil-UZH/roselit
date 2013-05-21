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
			if (!isset($_SERVER['Shib-SwissEP-UniqueID'])) {
				throw new Exception('Shib-SwissEP-UniqueID not set.');
			}
			$lAaiId = $_SERVER['Shib-SwissEP-UniqueID'];
			// check if a user with that shibboleth id exists in the db
			// if not create one, but set role to "not granted"
			$ci = &get_instance();
			$ci->load->model('user_mapper');
			$user = $ci->user_mapper->getByAaiId($lAaiId);
			if ($user !== false) {
				return $user;
			}	
		}
		return false;
	}

}

/* End of file shibboleth_authentication_service.php */
/* Location: ./application/library/shibboleth_authentication_service.php */

