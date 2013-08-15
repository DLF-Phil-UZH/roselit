<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shibboleth_authentication_service {

    private $session;
    private $shibboleth_session_exists = false;

	public function __construct() {
		// TODO: load settings from config file
        if (isset($_SERVER['Shib-Identity-Provider'])) {
			$this->shibboleth_session_exists = true;
        }
	}
	
	/**
	 * Verifies if user has already logged in over AAI
	 * 
	 * @return	boolean	true if session exists, false if not
	 * @access	public
	 *
	 */
	public function verify_shibboleth_session() {
        return $this->$shibboleth_session_exists;
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
            $lAaiId = $_SERVER['Shib-SwissEP-UniqueID'];
            $ci->load->model('user_mapper');
            $user = $ci->user_mapper->getByAaiId($lAaiId);
            if ($user !== false) {
                return $user;
            }
        }
        return false;
	}

    public function verify_user_access_request() {
        if ($this->verify_shibboleth_session() !== false) {
            if (!isset($_SERVER['Shib-SwissEP-UniqueID'])) {
                throw new Exception('Shib-SwissEP-UniqueID not set.');
            }
            $lAaiId = $_SERVER['Shib-SwissEP-UniqueID'];
            $ci = &get_instance();
            $ci->load->database();
            $lQuery = $ci->db->get_where('user_requests', array("aaiId" => $lAaiId), 1);
            if ($lQuery->num_rows() > 0) {
			    return $lQuery->row();
            }
        }
        return false;
    }

}

/* End of file shibboleth_authentication_service.php */
/* Location: ./application/library/shibboleth_authentication_service.php */

