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
			$crud->set_relation('creator','users','{firstname} {lastname}');
			$crud->set_relation('admin','users','{firstname} {lastname}');
			$crud->set_relation_n_n('Listen', 'documents_documentLists', 'documentLists', 'documentId', 'documentListId', 'title');
			$crud->columns('explicitId', 'title', 'authors', 'publication', 'volume', 'editors', 'places', 'publishingHouse', 'year', 'pages', 'fileName');
			$crud->fields('explicitId', 'title', 'authors', 'publication', 'volume', 'editors', 'places', 'publishingHouse', 'year', 'pages', 'fileName', 'creator', 'admin');

			// customize fields:
			//$crud->callback_field('lastUpdated',array($this,'_make_field_lastUpdated_readonly'));
			$crud->set_field_upload('fileName','assets/uploads/files')
				 ->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 // ->field_type('creator', 'readonly')
				 ->unset_add_fields('creator', 'admin', 'created', 'lastUpdated');
			
			// Creator and admin will be set automatically by callback to current user
			$crud->change_field_type('creator', 'invisible');
			$crud->change_field_type('admin', 'invisible');
			
			// Field aliases:
			$crud->display_as('title','Titel')
				 ->display_as('authors', 'Autoren')
				 ->display_as('explicitId', 'explizite ID')
				 ->display_as('publication', 'Werk- oder Zeitschriftentitel')
				 ->display_as('volume', 'Band/Ausgabe')
				 ->display_as('editors', 'Herausgeber/Buchautor')
				 ->display_as('year', 'Jahr')
				 ->display_as('places', 'Ort')
				 ->display_as('publishingHouse', 'Verlag')
				 ->display_as('pages', 'Seiten')
				 ->display_as('fileName', 'Datei')
				 ->display_as('creator', 'erstellt von')
				 ->display_as('admin', 'verwaltet von');
			
			// Will only be called when adding a new entry
			$crud->callback_before_insert(array($this, 'setCreatorAsAdmin'));
			
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
			$crud->columns('title', 'admin', 'creator', 'lastUpdated', 'published');
			$crud->fields('title', 'admin', 'creator', 'lastUpdated', 'published');

			// edit fields:
			$crud->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 ->field_type('published', 'readonly')
				 // ->field_type('creator', 'readonly')
				 ->unset_add_fields('creator', 'admin', 'created', 'lastUpdated', 'published');

			// Creator and admin will be set automatically by callback to current user
			$crud->change_field_type('creator', 'invisible');
			$crud->change_field_type('admin', 'invisible');
			
			// Field aliases:
			$crud->display_as('title','Titel')
				  ->display_as('creator', 'erstellt von')
				  ->display_as('admin', 'verwaltet von')
				  ->display_as('creator', 'erstellt von')
				  ->display_as('lastUpdated', 'zuletzt aktualisiert am')
				  ->display_as('published', 'bereits veröffentlicht');
			
			// Will only be called when adding a new entry
			$crud->callback_before_insert(array($this, 'setCreatorAsAdmin'));

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
	 * Setting creator and admin to current user and creation timestamp to NULL
	 * 
	 * Callback function, is called when a new document
	 * or a document list is created to set admin to current
	 * user (= creator), but not when an existing document is
	 * edited. Additionally sets the timestamp of creation
	 * ("created") to NULL, so that this value is set by the
	 * database itself
	 *
	 * @param	array	Array with POST data (field entries)
	 * @return	array	Array with modified POST data ("creator", "admin" and "created" added)
	 * @access	public
	 *
	 */
	public function setCreatorAsAdmin($pPostArray){
		
		try{
			// Get user data
			$lCi = $this->_getCI();
			$lCi->load->library('Shibboleth_authentication_service', '', 'shib_auth');
			$lUser = $lCi->shib_auth->verify_user();
			$lUserId = $lUser->getId();
			// Set creator and admin to current user
			$pPostArray['creator'] = $lUserId;
			$pPostArray['admin'] = $lUserId;
			
			
			
			// TODO: "created" cannot be set in database so far,
			// tried with the following non-working approaches:
			
			// Timestamp of creation will be set by database
			
			// $pPostArray['created'] = NULL;
			
			// $pPostArray['created'] = new DateTime('now');
			
			// $lCreationTimestamp = new DateTime('now');
			// $pPostArray['created'] = $lCreationTimestamp->format('Y-m-d H:i:s');
			
			// Just for testing
			// $pPostArray['created'] = '2013-07-08 01:00:00';
			
			// $pPostArray['created'] = date('Y-m-d H:i:s');
			
			
			// Tried with both datatypes "timestamp" and "datetime" in the database
			
		}
		catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		return $pPostArray;
		
	}
}

/* End of file crud_service.php */
