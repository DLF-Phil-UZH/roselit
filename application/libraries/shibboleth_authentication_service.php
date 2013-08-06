<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shibboleth_authentication_service {

	public function __construct() {
		// TODO: load settings from config file
		$ci = &get_instance();
		$ci->load->database();
	}
	
	/**
	 * Verifies if user has already logged in over AAI
	 * 
	 * @return	boolean	true if session exists, false if not
	 * @access	public
	 *
	 */
	public function verify_shibboleth_session() {
		// TODO: check if shibboleth sesssion exists
		if (isset($_SERVER['Shib-Identity-Provider'])) {
			return true;
		}
		return false;
	}

	/**
	 * Verifies if user already exists in database
	 *
	 * Verifies if the user has already logged in over AAI and if
	 * the user is already registered in database. If both these are
	 * true, returns the user model of the corresponding user,
	 * else returns false.
	 *
	 * @return	user_model/boolean	user_model on success, false else
	 * @access	public
	 */
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

