<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//require_once('Grocery_CRUD.php');

class Crud_service {

    private $user = NULL;

    protected function _getCI() {
        return get_instance();
    }
	protected function _getCrud() {
        $user = $this->_get_user();
		$this->_getCI()->load->library('Grocery_CRUD');
		$crud = new Grocery_CRUD($user);
		$crud->set_theme('datatables');
		return $crud;
	}

    protected function _get_user() {
        if (is_null($this->user)) {
            $lCi = $this->_getCI();
            $lCi->load->library('Shibboleth_authentication_service', '', 'shib_auth');
            $lUser = $lCi->shib_auth->verify_user();
            $this->user = $lUser;
        }
        return $this->user;
    }
	
	public function getDocumentsCrud() {
		$crud = $this->_getCrud();
		$output = '';

		try{
		    /** config: */
			$crud->set_table('documents');
			$crud->set_subject('Dokument');
			$crud->set_relation('creator','users','{firstname} {lastname} ({aaiId})');
			$crud->set_relation_n_n('Verwalter','documents_admins', 'users', 'documentId', 'userId', '{firstname} {lastname} ({aaiId})');
			$crud->set_relation_n_n('Listen', 'documents_documentLists', 'documentLists', 'documentId', 'documentListId', 'title');

            /** columns: */
			$crud->columns('checkbox', 'explicitId','authors', 'title', 'publication', 'volume', 'year', 'pages', 'fileName');
			$crud->callback_column('checkbox', array($this, 'callback_checkbox_column'));
            $crud->callback_column('fileName', array($this, 'callback_fileName_column'));
            $crud->order_by('explicitId');
    
            /** fields: */
            
            // fields for add / edit / read form:
            $fields = array(
                'explicitId',
                'authors',
                'title',
                'editors',
                'publication',
                'volume',
                'places',
                'publishingHouse',
                'year',
                'pages',
                'fileName',
                'preview'
            );
            // additional fields that should be displayed in edit / read form:
            $only_edit_fields = array('creator', 'Verwalter', 'lastUpdated');

            $crud->edit_fields(array_merge($fields, $only_edit_fields));
            $crud->add_fields($fields);
			$crud->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 ->field_type('creator', 'readonly');
			
            // Use special fields only in edit and add, not in read!
            $state = $crud->getState();
            if ($state == 'read') {
                // FIXME: this fails if readForm is displayed in edit action because another
                // user is editing the record!
                $crud->callback_field('fileName', array($this, 'callback_upload_field_read'));
                // 
            } else {
			    $crud->callback_field('explicitId', array($this, 'callback_explicit_id_field'));
                $crud->callback_field('fileName', array($this, 'callback_upload_field'));
                $crud->callback_field('preview', array($this, 'callback_preview_field'));
            }

            /** field / column aliases: */
            $crud->display_as('checkbox', '')
                 ->display_as('title','Titel')
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
				 ->display_as('lastUpdated', 'zuletzt aktualisiert am')
                 ->display_as('preview', 'Vorschau');
			
            /** Callbacks for actions: */
            
            // Will only be called when updating an existing entry
		    $crud->callback_can_edit(array($this, 'check_edit_permissions_document'));
			
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

		try{
		    /** config: */
			$crud->set_table('documentLists');
			$crud->set_subject('Liste');
			$crud->set_relation('creator','users','{firstname} {lastname} ({aaiId})');
			$crud->set_relation_n_n('Verwalter','documentLists_admins', 'users', 'documentListId', 'userId', '{firstname} {lastname} ({aaiId})');
			$crud->set_relation_n_n('Dokumente', 'documents_documentLists', 'documents', 'documentListId', 'documentId', '{authors} ({year}), {title}');

            /** columns: */
			$crud->columns('checkbox', 'title', 'admin', 'lastUpdated', 'published');
			$crud->callback_column('checkbox', array($this, 'callback_checkbox_column'));            
            $crud->order_by('title');

            /** fields: */
            $fields = array('title', 'published', 'Dokumente', 'creator', 'Verwalter', 'lastUpdated');
			$crud->edit_fields($fields);
            $crud->add_fields('title', 'published', 'Dokumente');
			$crud->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 ->field_type('published', 'readonly')
				 ->field_type('creator', 'readonly');

			// Field / column aliases:
            $crud->display_as('checkbox', '')
			     ->display_as('title','Titel')
				 ->display_as('creator', 'erstellt von')
				 ->display_as('admin', 'verwaltet von')
				 ->display_as('creator', 'erstellt von')
				 ->display_as('lastUpdated', 'zuletzt aktualisiert am')
				 ->display_as('published', 'bereits veröffentlicht');
			

            /** Callbacks for actions: */

			// Will only be called when updating an existing entry
			$crud->callback_can_edit(array($this, 'check_edit_permissions_documentlist'));
			
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

		try{
		    /** config */
			$crud->set_table('users');
            $crud->set_subject('Benutzer');

            /** columns: */
			$crud->columns('id', 'aaiId', 'firstname', 'lastname', 'email');

            /** fields: */
			$crud->field_type('created', 'readonly')
				 ->field_type('lastLogin', 'readonly');
			$crud->unset_add_fields('created', 'lastLogin');

            /** Field / column aliases: */
			$crud->display_as('id','ID')
				  ->display_as('aaiId', 'AAI UniqueID')
				  ->display_as('firstname', 'Vorname')
				  ->display_as('lastname', 'Nachname')
				  ->display_as('email', 'E-Mail')
				  ->display_as('lastLogin', 'letzter Login am')
				  ->display_as('created', 'registriert seit');

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
                 ->unset_read()
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
    public function callback_checkbox_column($pValue, $pRow){
        $pValue = 'document_' . $pRow->id;
        return '<input type="checkbox" name="selected-rows" value="' . $pValue . '" >';
    }

	public function callback_fileName_column($pValue, $pRow){
		if ($pValue != '') {
			return '<a href="'.site_url('manager/documents_file/'.$pRow->id).'" target="_blank">Datei herunterladen</a>';
		}
		return '';
	}

    public function callback_explicit_id_field($pValue, $pId) {
        $view_data = array(
            'value' => $pValue,
            'button_title' => 'Explizite ID generieren' // FIXME: add message support
        );
        $explicit_id_input = $this->_getCI()->load->view('crud/explicit_id_field', $view_data, true);
        return $explicit_id_input;
    }

    public function callback_upload_field_read($pValue, $pId) {
        if ($pValue != '') {
            $download_url = site_url('manager/documents/file/'.$pId);
            $unique = uniqid();
            $html = '<a id="download-pdf-' . $unique .'" href="' . $download_url . '" target="_blank">PDF herunterladen</a>';
            $html .= '<script type="text/javascript">$(function(){ $("#download-pdf-' . $unique . '").button();});</script>';
        } else {
            $html = '<div id="field-fileName" class="readonly_label">Kein PDF verfügbar.</div>';
        }
        return $html;
    }

    public function callback_upload_field($pValue, $pId) {
        if ($pId) {
            if ($pValue != '') {
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
       } else {
            return 'Sie können erst ein PDF hochladen, wenn Sie das Dokument gespeichert haben. Klicken Sie zuerst auf speichern.';
       }
    }

    public function callback_preview_field($pValue, $pId) {
        $pValue = '';
        if ($pId) {
            // load model and generate preview:
            $lCi = $this->_getCI();
            $lCi->load->model('document_mapper');
            $document_model = $lCi->document_mapper->get($pId);
            $pValue = $document_model->toFormattedString();
        }
        $view_data = array('unique' => uniqid(), 'value' => $pValue);
        return $this->_getCI()->load->view('crud/preview_field', $view_data, true);
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
            // load database:
            $lCi = $this->_getCI();
            $lCi->load->database();
			$lDb = $lCi->db;

			// Get data of currently logged in user
			$lUser = $this->_get_user(); 
			$lUserId = $lUser->getId();
			
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
	 * @param	int		$pId primary key of the inserted values
     * @param   boolean $pLock_row  If the row should be locked
	 * @return	array/boolean	Post array on success, false on error
	 * @access	public
	 */
    public function check_edit_permissions_document($pId){
        			
		// Load database
        $lCi = $this->_getCI();
		$lCi->load->database();
        $lDb = $lCi->db;
    
        // Get data of currently logged in user
		$lUser = $this->_get_user();

        if ($lUser->isAdmin()) {
            return true;
        }

        $lUserId = $lUser->getId();
		
		// Get edit information from database
        $table_name = $lDb->dbprefix('documents_admins');
		$lQuery = $lDb->get_where($table_name, array('documentId' => $pId, 'userId' => '$lUserId'));
		if($lQuery->num_rows() == 1){
            return true;
        }
        
        return false;
	}
	
	/**
	 * Checks if document list is free for editing. 
	 * 
	 * Callback function, is called before a logged in user tries to
	 * update an existing document list.
	 * 
	 * @param	int		$pId primary key of the inserted values
     * @param   boolean $pLock_row  If the row should be locked
	 * @return	array/boolean	Post array on success, false on error
	 * @access	public
	 */
	public function check_edit_permissions_documentlist($pId){
        // Load database
        $lCi &= $this->_getCI();
		$lCi->load->database();
        $lDb = $lCi->db;
    
        // Get data of currently logged in user
		$lUser = $this->_get_user();
        if ($lUser->isAdmin()) {
            return true;
        }
		$lUserId = $lUser->getId();
		
		// Get edit information from database
        $table_name = $lDb->dbprefix('documentLists_admins');
		$lQuery = $lDb->get_where($table_name, array('documentListId' => $pId, 'userId' => '$lUserId'));
		if($lQuery->num_rows() == 1){
            return true;
        }
        
        return false;
    }

}

/* End of file crud_service.php */
/* Location: ./application/library/crud_service.php */
