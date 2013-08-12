<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crud_service {

	private $user = NULL;

	protected function _getCI() {
		return get_instance();
	}
	protected function _getCrud()
	{
		$ci = &get_instance();
		$ci->load->library('grocery_CRUD');
		$crud = new grocery_Crud();
		$crud->set_theme('datatables');
		return $crud;
	}

	protected function _getUser() {
		if (is_null($this->user)) {
			$lCi = $this->_getCI();
			$lCi->load->library('Shibboleth_authentication_service', '', 'shib_auth');
			$lUser = $lCi->shib_auth->verify_user();
			$this->user = $lUser;
		}
		return $this->user;
	}
	
	protected function _getCI(){
		$lCi = &get_instance();
		return $lCi;
	}

	public function getDocumentsCrud() {
		$crud = $this->_getCrud();
		$output = '';

		/** config: */
		try{
			$crud->set_table('documents');
			$crud->set_subject('Dokument');
			$crud->set_relation('creator','users','{firstname} {lastname} ({aaiId})');
			$crud->set_relation('admin','users','{firstname} {lastname} ({aaiId})');
			$crud->set_relation_n_n('Listen', 'documents_documentLists', 'documentLists', 'documentId', 'documentListId', 'title');

			/** columns: */

			$crud->columns('checkbox', 'explicitId', 'authors', 'title', 'publication', 'volume', 'year', 'pages', 'fileName');
			$crud->callback_column('checkbox', array($this, '_callback_checkbox_column'));
			$crud->callback_column('fileName', array($this, '_callback_fileName_column'));
    
			/** fields: */

			$crud->fields('explicitId', 'authors', 'title', 'editors', 'publication',
				'volume', 'places', 'publishingHouse', 'year', 'pages', 
				'fileName', 'creator', 'admin', 'lastUpdated');
			$crud->unset_add_fields('creator', 'admin', 'created', 'lastUpdated');
			$crud->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 ->field_type('creator', 'readonly');
			
			$crud->callback_field('fileName', array($this, '_callback_upload_field'));

			/** Field / column aliases: */

			$crud->display_as('checkbox', '')
				 ->display_as('title', 'Titel')
				 ->display_as('authors', 'Autoren')
				 ->display_as('explicitId', 'explizite ID')
				 ->display_as('publication', 'Werk- oder Zeitschriftentitel')
				 ->display_as('volume', 'Band / Ausgabe')
				 ->display_as('editors', 'Herausgeber / Buchautor')
				 ->display_as('year', 'Jahr')
				 ->display_as('places', 'Ort')
				 ->display_as('publishingHouse', 'Verlag')
				 ->display_as('pages', 'Seiten')
				 ->display_as('fileName', 'Datei')
				 ->display_as('creator', 'erstellt von')
				 ->display_as('admin', 'verwaltet von')
				 ->display_as('lastUpdated', 'zuletzt aktualisiert am');
			
			/** Validation rules of formular entries by user: */
			$crud->set_rules('title', 'Titel', 'required');
			$crud->set_rules('authors', 'Autoren', 'required');
			$crud->set_rules('explicitId', 'explizite ID', 'required|is_unique[documents.explicitId]');
			
			
			/** Callbacks for actions: */
			
			// Will only be called when updating an existing entry
			$crud->callback_before_update(array($this, 'check_edit_state_document'));
			
			// Will only be called when adding a new entry
			$crud->callback_after_insert(array($this, 'update_documents_after_insert'));
            
			// execute:
			$output = $crud->render();
		} catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		return $output;
	}

	/*
	 * Get the code for Document
	 */
	public function getDocumentListsCrud() {
		$crud = $this->_getCrud();
		$output = '';

		/** config: */
		try{
			$crud->set_table('documentLists');
			$crud->set_subject('Liste');
			$crud->set_relation('creator','users','{firstname} {lastname} ({aaiId})');
			$crud->set_relation('admin','users','{firstname} {lastname} ({aaiId})');
			$crud->set_relation_n_n('Dokumente', 'documents_documentLists', 'documents', 'documentListId', 'documentId', '{authors} ({year}), {title}');

			/** columns: */

			$crud->columns('title', 'admin', 'lastUpdated', 'published');

			/** fields: */

			$crud->fields('title', 'creator', 'admin', 'lastUpdated', 'published', 'Dokumente');
            $crud->unset_add_fields('creator', 'admin', 'created', 'lastUpdated', 'published');
			$crud->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 ->field_type('published', 'readonly')
				 ->field_type('creator', 'readonly');

			// Field / column aliases:
			$crud->display_as('checkbox', '')
				 ->display_as('title', 'Titel')
				 ->display_as('creator', 'erstellt von')
				 ->display_as('admin', 'verwaltet von')
				 ->display_as('lastUpdated', 'zuletzt aktualisiert am')
				 ->display_as('published', 'bereits veröffentlicht');
			
			/** Validation rules of formular entries by user: */
			$crud->set_rules('title', 'Titel', 'required');
			$crud->set_rules('admin', 'verwaltet von', 'required');
			
			/** Callbacks for actions: */

			// Will only be called when updating an existing entry
			$crud->callback_before_update(array($this, 'check_edit_state_documentList'));
			
			// Will only be called when adding a new entry
			$crud->callback_after_insert(array($this, 'update_documentlists_after_insert'));

            // execute:
			$output = $crud->render();
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		return $output;
	}

	public function getUsersCrud() {
		$crud = $this->_getCrud();
		$output = '';

		/** config */
		try{
			$crud->set_table('users');
			$crud->set_subject('Benutzer');

			/** columns: */
			$crud->columns('id', 'aaiId', 'firstname', 'lastname', 'email');

			/** fields: */
			$crud->field_type('created', 'readonly')
				 ->field_type('lastLogin', 'readonly')
			$crud->unset_add_fields('created', 'lastLogin');

			/** Field / column aliases: */
			$crud->display_as('id','ID')
				  ->display_as('aaiId', 'AAI UniqueID')
				  ->display_as('firstname', 'Vorname')
				  ->display_as('lastname', 'Nachname')
				  ->display_as('email', 'E-Mail')
				  ->display_as('lastLogin', 'letzter Login am')
				  ->display_as('created', 'registriert seit');
			
			/** Validation rules of formular entries by user: */
			$crud->set_rules('aaiId', 'AAI UniqueID', 'required|is_Unique[users.aaiId]');
			$crud->set_rules('firstname', 'Vorname', 'required');
			$crud->set_rules('lastname', 'Nachname', 'required');
			$crud->set_rules('email', 'E-Mail', 'required');

            // execute: 
			$output = $crud->render();
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		return $output;
	}

	public function getUserRequestsCrud() {
		$crud = $this->_getCrud();
		$output = '';

		try{
		    /** config */
			$crud->set_table('user_requests');
            $crud->set_subject('Zugriffsanfragen');
            $crud->unset_add()
				 ->unset_edit();
            
			/** columns: */
			$crud->columns('aaiId', 'firstname', 'lastname', 'email', 'created');

			/** fields: */
			$crud->field_type('created', 'readonly');

			/** Field / column aliases: */
			$crud->display_as('aaiId', 'AAI UniqueID')
				  ->display_as('firstname', 'Vorname')
				  ->display_as('lastname', 'Nachname')
				  ->display_as('email', 'E-Mail-Adresse')
				  ->display_as('created', 'eingegangen am');

			/** actions: */
			// add custom action to accept request
			$crud->add_action('Accept', '', 'admin/user_requests/accept','ui-icon-plus');

			// execute:
			$output = $crud->render();
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		return $output;
	}

    /**
     *   
     */
    public function _callback_checkbox_column($pValue, $pRow){
        $pValue = 'document_' . $pRow->id;
        return '<input type="checkbox" name="selected-rows" value="' . $pValue . '" >';
    }

	public function _callback_fileName_column($pValue, $pRow){
		if ($pValue != '') {
			return '<a href="'.site_url('manager/documents_file/'.$pRow->id).'" target="_blank">Datei herunterladen</a>';
		}
		return "";
	}

    public function _callback_upload_field($pValue, $pId) {
        $ci =& get_instance();
        $ci->load->model('document_mapper');
        $lDocument = $ci->document_mapper->get($pId);
        if ($lDocument->getFileName() != '') {
            $file_buttons_display = '';
            $upload_button_display = 'display:none;';
        } else {
            $file_buttons_display = 'display:none;';
            $upload_button_display = '';
        }
        $view_data = array(
            'unique' => uniqid(),
            'assets_url' => base_url('assets/grocery_crud'),
            'upload_button_display' => $upload_button_display,
            'file_buttons_display' => $file_buttons_display,
            'upload_url' => site_url('manager/documents/file/upload/' . $pId),
            'download_url' => site_url('manager/documents/file/'.$pId),
            'delete_url' => site_url('manager/documents/file/delete/' . $pId),
            'upload_success_msg' => 'Die Datei wurde erfolgreich hochgeladen.',
            'upload_error_msg' => 'Beim Hochladen der Datei ist ein Fehler aufgetreten.',
            'confirm_delete_msg' => 'Möchten Sie die Datei wirklich löschen?',
            'delete_success_msg' => 'Die Datei wurde gelöscht.',
            'delete_error_msg' => 'Die Datei konnte nicht gelöscht werden.'
        );
        $upload_input = $this->_getCI()->load->view('crud/upload_field', $view_data, true);
        return $upload_input;
    }
} 
	
	/**
     * Sets creator and admin to current user and creation timestamp to
     * lastUpated timestamp 
	 * 
     * Callback function, is called when a document list is created 
     * to set admin and creator to current user, but not when an 
     * existing document is edited. Additionally sets the timestamp 
     * of creation ("created") to the same value as lastUpdated
     * 
	 * @param	array	$pPostArray Array with POST data (field entries)
     * @param   int     $pId primary key of the inserted values
     * @return	boolean	true on success, false on error
	 * @access	public
	 *
	 */
	public function update_documents_after_insert($pPostArray, $pId){
        return $this->_update_table_after_insert('documentLists', $postArray, $pId);
    }

    /**
     * Sets creator and admin to current user and creation timestamp
     * to lastUpated timestamp 
	 * 
     * Callback function, is called when a document list is created 
     * to set admin and creator to current user, but not when an 
     * existing document is edited. Additionally sets the timestamp 
     * of creation ("created") to the same value as lastUpdated
     * 
	 *
	 * @param	array	$pPostArray Array with POST data (field entries)
     * @param   int     $pId primary key of the inserted values
     * @return	boolean	true on success, false on error
	 * @access	public
	 *
	 */
    public function update_documentlists_after_insert($pPostArray, $pId){
        return $this->_update_table_after_insert('documents', $postArray, $pId);
    }

    /**
     * Sets the columns creator and admin to current user and creation timestamp
     * to lastUpated timestamp 
     * 
     * @param   string  $pTableName name of the table that should be updated.
     * @param	array	$pPostArray Array with POST data (field entries)
     * @param   int     $pId primary key of the inserted values
     * @return	boolean	true on success, false on error
	 * @access  private	
     * 
     */
    private function _update_table_after_insert($pTableName, $pPostArray, $pId) {
        try{
			// Get data of currently logged in user
			$lCi = $this->_getCI();
			$lCi->load->library('Shibboleth_authentication_service', '', 'shib_auth');
			$lUser = $lCi->shib_auth->verify_user();
			$lUserId = $lUser->getId();
			
			$lCi->load->database();
			$lDb = $lCi->db;
			$lQuery = 'UPDATE ' . $lDb->protect_identifiers($pTableName);
			$lQuery .= ' SET ' . $lDb->protect_identifiers('creator') . ' = ? ,';
			$lQuery .= $lDb->protect_identifiers('admin') . ' = ? ,';
			$lQuery .= $lDb->protect_identifiers('created') . ' = ' . $lDb->protect_identifiers('lastUpdated');
			$lQuery .= ' WHERE ' . $lDb->protect_identifiers('id') . ' = ?;';
            
			if($lDb->query($lQuery, array($lUserId, $lUserId, $pId)) === true){
				return $pPostArray;
			}
			else{
				return false;
			}
		}
		catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }

    }
	
	/**
	 * Checks if document is free for editing. 
	 * 
	 * Callback function, is called before a logged in user tries to
	 * update an existing document.
	 * 
	 * @param	array	$pPostArray	Array with POST data (field entries)
	 * @param	int		$pId primary key of the inserted values
	 * @return	array/boolean	Post array on success, false on error
	 * @access	public
	 */
	public function check_edit_state_document($pPostArray, $pId){
		return $this->_manage_edit_state('documents', $pPostArray, $pId);
	}
	
	/**
	 * Checks if document list is free for editing. 
	 * 
	 * Callback function, is called before a logged in user tries to
	 * update an existing document list.
	 * 
	 * @param	array	$pPostArray	Array with POST data (field entries)
	 * @param	int		$pId primary key of the inserted values
	 * @return	array/boolean	Post array on success, false on error
	 * @access	public
	 */
	public function check_edit_state_documentList($pPostArray, $pId){
		return $this->_manage_edit_state('documentLists', $pPostArray, $pId);
	}
	
	/**
	 * Checks if an entry (document or document list) is available for editing or if it is already being edited by another user.
	 * If entry is free for editing, sets the fields currentUserId and editTimestamp in database for logged in user.
	 *
	 * @param	string  $pTableName	Name of the table that should be updated ("documents" or "documentLists")
	 * @param	array	$pPostArray	Array with POST data (field entries)
	 * @param	int     $pId		Primary key of the inserted values
	 * @return	array/boolean	Post array on success, false on error
	 * @access	private
	 */
	private function _manage_edit_state($pTableName, $pPostArray, $pId){
		// Set currentUserId to ID of current user if entry is not being edited by any other user or edit timestamp is older than 60 minutes
		$lEditTimestamp = new DateTime($pPostArray['editTimestamp']);
		$lCurrentTimestamp = new DateTime();
		$lDifference = _getTimeDifference($lEditTimestamp, $lCurrentTimestamp);
		
		// Load database
		$lCi->load->database();
		$lDb = $lCi->db;
		
		// Get edit information from database
		$lQuery = $lDb->get_where($pTableName, array('id' => $pId));
		if($lQuery->num_rows() == 1){
			$lCurrentUserId = $lQuery->row()->currentUserId;
			$lEditTimestamp = $lQuery->row()->editTimestamp;
		}
		
		// Get data of currently logged in user
		$lCi = $this->_getCI();
		$lCi->load->library('Shibboleth_authentication_service', '', 'shib_auth');
		$lUser = $lCi->shib_auth->verify_user();
		$lUserId = $lUser->getId();
		
		// If logged in user was working on entry or no one was working on entry or if edit time has runned up
		if($lCurrentUserId == $lUserId || $lCurrentUserId === 0 || $lDifference >= 60)){
			try{
				// Set currentUserId to logged in user and editTimestamp to current time
				$lQuery = 'UPDATE ' . $lDb->protect_identifiers($pTableName);
				$lQuery .= ' SET ' . $lDb->protect_identifiers('currentUserId') . ' = ? ,';
				$lQuery .= $lDb->protect_identifiers('editTimestamp') . ' = ? ,';
				$lQuery .= ' WHERE ' . $lDb->protect_identifiers('id') . ' = ?;';

				if($lDb->query($lQuery, array($lUserId, $lCurrentTimestamp, $pId)) === true){
					return $pPostArray;
				}
				else{
					return false;
				}
			}
			catch(Exception $e){
				show_error($e->getMessage().' --- '.$e->getTraceAsString());
			}
		}
		// Do not allow editing the entry when any other user is currently working on it
		else{
			return false;
			// throw new Exception('Bearbeitung nicht möglich. ' . (strcmp($pTableName, 'documents') ? 'Das Dokument' : 'Die Liste') . ' wird gerade von einem anderen Benutzer bearbeitet.');
		}
	}
	
	/**
	 * Calculates the absolute time difference of two given timestamps in minutes.
	 * 
	 * @param	DateTime	$pTimestamp1	First timestamp (usually older one)
	 * @param	DateTime	$pTimestamp2	Second timestamp (usually newer one)
	 * @return	integer		Absolute time difference in minutes (always rounded down to next smaller integer (works like floor()))
	 * @access  private
	 */
	private function _getTimeDifference($pTimestamp1, $pTimestamp2){
		$lOldTime = $pTimestamp1->getTimestamp();
		$lNewTime = $pTimestamp2->getTimestamp();
		$lDifference = $lNewTime - $lOldTime; // Difference in seconds
		return abs($lDifference) / 60; // Difference in minutes (absolute value)
	}
}

/* End of file crud_service.php */
/* Location: ./application/library/crud_service.php */
