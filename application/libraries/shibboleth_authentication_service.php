<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Shibboleth_authentication_service {

    private $shibboleth_session_exists = false;

	public function __construct() {
		// TODO: load settings from config file
        if (!empty($_SERVER['HTTP_SHIB_IDENTITY_PROVIDER'])) {
			$this->shibboleth_session_exists = true;
        }
    }

    protected function _getCI() {
        return get_instance();
    }
	
	/**
	 * Verifies if user has already logged in over AAI
	 * 
	 * @return	boolean	true if session exists, false if not
	 * @access	public
	 *
	 */
	public function verify_shibboleth_session() {
        return $this->shibboleth_session_exists;
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
            if (!isset($_SERVER['HTTP_UNIQUEID'])) {
                throw new Exception('uniqueID not set.');
            }
            $lAaiId = $_SERVER['HTTP_UNIQUEID'];
			// check if a user with that shibboleth id exists in the db
			$ci = $this->_getCI();            
            $ci->load->model('User_mapper');
            $user = $ci->User_mapper->getByAaiId($lAaiId);
            if ($user !== false) {
                return $user;
            }
        }
        return false;
	}

    public function verify_user_access_request() {
        if ($this->verify_shibboleth_session() !== false) {
            if (!isset($_SERVER['HTTP_UNIQUEID'])) {
                throw new Exception('uniqueID not set.');
            }
            $lAaiId = $_SERVER['HTTP_UNIQUEID'];
            $ci = $this->_getCI(); 
            $ci->load->database();
            $lQuery = $ci->db->get_where('oliv_user_requests', array("aaiId" => $lAaiId), 1);
            if ($lQuery->num_rows() > 0) {
			    return $lQuery->row();
            }
        }
        return false;
    }

    /**
     * Returns the unique user ID provided by the Shibboleth authentication service.
     * 
     * @return {String|NULL} the unique user ID or NULL if no user ID is found.
     */
    public function get_unique_user_id() {
        if (!empty($_SERVER['HTTP_UNIQUEID'])) {
            return $_SERVER['HTTP_UNIQUEID'];
        }
        return false;
    }

}
/* End of file shibboleth_authentication_service.php */
/* Location: ./application/library/shibboleth_authentication_service.php */

