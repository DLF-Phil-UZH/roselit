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

	public function getDocumentsCrud() {
		$crud = $this->_getCrud();
		$output = '';

		/* config */
		try{
			$crud->set_table('documents');
			$crud->set_subject('Dokument');
			$crud->set_relation('creator','users','{username} - {firstName} {lastName}');
			$crud->set_relation('admin','users','{username} - {firstName} {lastName}');		
			$crud->set_relation_n_n('Listen', 'documents_documentLists', 'documentLists', 'documentId', 'documentListId', 'title');
			$crud->columns('explicitId', 'title', 'authors', 'publication', 'year', 'place', 'startPage', 'endPage', 'fileName');

			// customize fields:
			//$crud->callback_field('lastUpdated',array($this,'_make_field_lastUpdated_readonly'));	
			$crud->set_field_upload('fileName','assets/uploads/files')
				 ->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 // ->field_type('creator', 'readonly')				 
				 ->unset_add_fields('creator', 'admin', 'created', 'lastUpdated');

			// Field aliases:
			$crud->display_as('title','Titel')
				 ->display_as('authors', 'Autoren')
				 ->display_as('explicitId', 'explizite ID')
				 ->display_as('publication', 'Werk- oder Zeitschriftentitel (mit Vol.)')
				 ->display_as('editors', 'Herausgeber (mit : und (edd.))')
				 ->display_as('year', 'Jahr')
				 ->display_as('place', 'Ort')
				 ->display_as('startPage', 'von (Seite)')
				 ->display_as('endPage', 'bis (Seite)')
				 ->display_as('fileName', 'Datei')
				 ->display_as('publisher', 'Verlag')
				 ->display_as('creator', 'erstellt von')
				 ->display_as('admin', 'verwaltet von');
		
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
			$crud->set_relation('creator','users','{username} - {firstName} {lastName}');
			$crud->set_relation('admin','users','{username} - {firstName} {lastName}');						
			$crud->set_relation_n_n('Dokumente', 'documents_documentLists', 'documents', 'documentListId', 'documentId', 'title');
			$crud->columns('title', 'admin', 'creator', 'lastUpdated');

			// edit fields:
			$crud->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 // ->field_type('creator', 'readonly')				 				 
				 ->unset_add_fields('creator', 'admin', 'created', 'lastUpdated');

			// Field aliases:
			$crud->display_as('title','Titel')
				  ->display_as('creator', 'erstellt von')
				  ->display_as('admin', 'verwaltet von')
				  ->display_as('creator', 'erstellt von')
				  ->display_as('lastUpdated', 'zuletzt aktualisiert am');

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
			$crud->columns('id', 'aaiId', 'username', 'firstName', 'lastName');

			// edit fields:
			$crud->field_type('created', 'readonly')
				 ->field_type('lastLogin', 'readonly')
				 ->unset_add_fields('creatod', 'lastLogin');

			// Field aliases:
			$crud->display_as('id','ID')
				  ->display_as('aaiId', 'AAI UniqueID')
				  ->display_as('username', 'Benutzername')
				  ->display_as('firstName', 'Vorname')
				  ->display_as('lastName', 'Nachname')
				  ->display_as('lastLogin', 'letzter Login am')
				  ->display_as('created', 'registriert seit');

			$output = $crud->render();
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		return $output;
	}
}

/* End of file CrudService.php */
