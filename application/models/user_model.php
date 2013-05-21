<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User_model extends Person_model {

	private $_username;
    private $_aaiId;
    private $_role = 'new';

    public function __construct($pUsername, $pAaiId, $pFirstname, $pLastname, $pTitle = "", /* $pEmail = "",$*/ $pGender = true) {
		parent::__construct($pFirstname, $pLastname, $pTitle, /* $pEmail = "",$*/ $pGender);
		$this->_username = $pUsername;
		$this->_aaiId = $pAaiId;
    }

    /* getters: */
	/**
	 *
	 */
	public function getUsername() {
    	return $this->_username;
	}

    public function getAaiId() {
        return $this->_aaiId;
    }

    public function getRole() {
        return $this->_role;
    }

    public function isAdmin() {
        return ($this->_role == 'admin');
    }


    /* setters: */

	public function setUsername($pUsername) {
		if ( is_string($username) ) { // TODO: Länge des Strings prüfen!
            $this->_username = $pUsername;
        } else {
            throw new InvalidArgumentException('string expected');
        }
        return $this;

	}

    public function setAaiId( $pAaiId ) {
        if ( is_string($pAaiId) ) {
            $this->_aaiId = $pAaiId;
        } else {
            throw new InvalidArgumentException('string expected');
        }
        return $this;
    }

    public function setRole($pRole) {
        if ( is_string($pRole) ) {
            $this->_role = $pRole;
        } else {
            throw new InvalidArgumentException('string expected');
        }
        return $this;
    }
}

