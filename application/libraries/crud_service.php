<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crud_service {

	protected function _getCrud()
	{
		$ci = &get_instance();
		$ci->load->library('grocery_Crud');
		$crud = new grocery_Crud();
		$crud->set_theme('datatables');
		return $crud;
	}
	
	protected function _getCI(){
		$lCi = &get_instance();
		return $lCi;
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
			//$crud->callback_field('lastUpdated',array($this,'_make_field_lastUpdated_readonly'));
			$crud->set_field_upload('fileName','assets/uploads/files')
				 ->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 ->field_type('creator', 'readonly')
				 ->unset_add_fields('creator', 'admin', 'created', 'lastUpdated');
			
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
				 ->display_as('admin', 'verwaltet von');
			
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
			$crud->set_relation('creator','users','{firstname} {lastname} - {email}');
			$crud->set_relation('admin','users','{firstname} {lastname} - {email}');
			$crud->set_relation_n_n('Dokumente', 'documents_documentLists', 'documents', 'documentListId', 'documentId', 'title');
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
				  ->display_as('creator', 'erstellt von')
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

			// TODO: add custom action to accept request
			$crud->add_action('Accept', '', 'admin/user_requests/accept','ui-icon-plus');

			
			$output = $crud->render();
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		return $output;
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
