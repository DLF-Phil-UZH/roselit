<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crud_service {

    protected function _getCI() {
        return get_instance();
    }
	protected function _getCrud()
	{
		$this->_getCI()->load->library('grocery_CRUD');
		$crud = new grocery_Crud();
		$crud->set_theme('datatables');
		return $crud;
	}
	
	public function getDocumentsCrud() {
		$crud = $this->_getCrud();
		$output = '';

		/* config */
		try{
			$crud->set_table('documents');
			$crud->set_subject('Dokument');
			$crud->set_relation('creator','users','{firstname} {lastname} ({aaiId})');
			$crud->set_relation('admin','users','{firstname} {lastname} ({aaiId})');
			$crud->set_relation_n_n('Listen', 'documents_documentLists', 'documentLists', 'documentId', 'documentListId', 'title');
			$crud->columns('explicitId','authors', 'title', 'publication', 'volume', 'year', 'pages', 'fileName');
			$crud->fields('explicitId', 'authors', 'title', 'editors', 'publication', 'volume', 'places', 'publishingHouse', 'year', 'pages', 'fileName', 'creator', 'admin', 'lastUpdated');

			// customize fields:
			$crud->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 ->field_type('creator', 'readonly')
				 ->unset_add_fields('fileName', 'creator', 'admin', 'created', 'lastUpdated');
			
			//$crud->callback_field('fileName', array($this, '_callback_upload_field'));
			$crud->callback_column('fileName', array($this, '_callback_fileName_column'));

			// Field aliases:
			$crud->display_as('title','Titel')
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
            
			
			// Will only be called when adding a new entry
			$crud->callback_after_insert(array($this, 'update_documents_after_insert'));

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

		/* config */
		try{
			$crud->set_table('documentLists');
			$crud->set_subject('Liste');
			$crud->set_relation('creator','users','firstname} {lastname} ({aaiId})');
			$crud->set_relation('admin','users','{firstname} {lastname} ({aaiId})');						
			$crud->set_relation_n_n('Dokumente', 'documents_documentLists', 'documents', 'documentListId', 'documentId', '{authors} ({year}), {title}');
			$crud->columns('title', 'admin', 'lastUpdated', 'published');
			$crud->fields('title', 'creator', 'admin', 'lastUpdated', 'published', 'Dokumente');

			// edit fields:
			$crud->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 ->field_type('published', 'readonly')
				 ->field_type('creator', 'readonly')
				 ->unset_add_fields('creator', 'admin', 'created', 'lastUpdated', 'published');

			// Field aliases:
			$crud->display_as('title','Titel')
				  ->display_as('creator', 'erstellt von')
				  ->display_as('admin', 'verwaltet von')
				  ->display_as('lastUpdated', 'zuletzt aktualisiert am')
				  ->display_as('published', 'bereits veröffentlicht');
			
			// Will only be called when adding a new entry
			$crud->callback_after_insert(array($this, 'update_documentlists_after_insert'));

			$output = $crud->render();
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		return $output;
	}

	public function getUsersCrud() {
		$crud = $this->_getCrud();
		$output = '';

		/* config */
		try{
			$crud->set_table('users');
			$crud->set_subject('Benutzer');
			$crud->columns('id', 'aaiId', 'firstname', 'lastname', 'email');

			// edit fields:
			$crud->field_type('created', 'readonly')
				 ->field_type('lastLogin', 'readonly')
				 ->unset_add_fields('created', 'lastLogin');

			// Field aliases:
			$crud->display_as('id','ID')
				  ->display_as('aaiId', 'AAI UniqueID')
				  ->display_as('firstname', 'Vorname')
				  ->display_as('lastname', 'Nachname')
				  ->display_as('email', 'E-Mail')
				  ->display_as('lastLogin', 'letzter Login am')
				  ->display_as('created', 'registriert seit');

			$output = $crud->render();
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		return $output;
	}

	public function getUserRequestsCrud() {
		$crud = $this->_getCrud();
		$output = '';

		/* config */
		try{
			$crud->set_table('user_requests');
			$crud->set_subject('Zugriffsanfragen');
			$crud->columns('aaiId', 'firstname', 'lastname', 'email', 'created');

			// Field aliases:
			$crud->display_as('aaiId', 'AAI UniqueID')
				  ->display_as('firstname', 'Vorname')
				  ->display_as('lastname', 'Nachname')
				  ->display_as('email', 'E-Mail-Adresse')
				  ->display_as('created', 'eingegangen am');

			// edit fields:
			$crud->field_type('created', 'readonly');
			
			$crud->unset_add()
				 ->unset_edit();

			// add custom action to accept request
			$crud->add_action('Accept', '', 'admin/user_requests/accept','ui-icon-plus');

			$output = $crud->render();
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		return $output;
	}

	public function _callback_fileName_column($pValue, $pRow){
		if ($pValue != '') {
			return '<a href="'.site_url('manager/documents_file/'.$pRow->id).'" target="_blank">Datei herunterladen</a>';
		}
		return '';
	}

    public function _callback_upload_field($pValue, $pId) {
        if ($pId) {
            $ci =& get_instance();
            // TODO: do we really need to load the Document_model?
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
        // TODO: what should be returned, if no id is specified? How can we upload something before the document is specified?
        return '';
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
			// Get user data
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
            
            return $lDb->query($lQuery, array($lUserId, $lUserId, $pId));
		}
		catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
        }

    }
}

/* End of file crud_service.php */
/* Location: ./application/library/crud_service.php */
