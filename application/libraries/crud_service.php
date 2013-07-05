<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Crud_service {

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
			$crud->columns('explicitId', 'title', 'authors', 'publication', 'publishingHouseAndPlace', 'year', 'pages', 'fileName');

			// customize fields:
			//$crud->callback_field('lastUpdated',array($this,'_make_field_lastUpdated_readonly'));	
			// $crud->set_field_upload('fileName', '/files', '/usr/local/ftp/phil_elearning/roselit/files');
            $crud->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 // ->field_type('creator', 'readonly')
				 ->unset_add_fields('creator', 'admin', 'created', 'lastUpdated');

			$crud->callback_field('fileName', array($this, '_callback_upload_field'));
			$crud->callback_column('fileName', array($this, '_callback_file_url'));

			// Field aliases:
			$crud->display_as('title','Titel')
				 ->display_as('authors', 'Autoren')
				 ->display_as('explicitId', 'explizite ID')
				 ->display_as('publication', 'Werk- oder Zeitschriftentitel (mit Vol.)')
				 ->display_as('editors', 'Herausgeber (mit : und (edd.))')
				 ->display_as('year', 'Jahr')
				 ->display_as('publishingHouseAndPlace', 'Ort und Verlag')
				 ->display_as('pages', 'Seiten')
				 ->display_as('fileName', 'Datei')
				 ->display_as('creator', 'erstellt von')
				 ->display_as('admin', 'verwaltet von');
	
			$output = $crud->render();
		} catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
		// hack to correct the file url:
		// $corrected_output = preg_match('', '',$output,)
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
			$crud->set_relation_n_n('Dokumente', 'documents_documentLists', 'documents', 'documentListId', 'documentId', '{authors} ({year}), {title}');
			$crud->columns('title', 'admin', 'creator', 'lastUpdated');

			// edit fields:
			$crud->field_type('created', 'readonly')
				 ->field_type('lastUpdated', 'readonly')
				 ->field_type('creator', 'readonly')
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
			$crud->columns('id', 'aaiId', 'firstname', 'lastname', 'email');

			// edit fields:
			$crud->field_type('created', 'readonly')
				 ->field_type('lastLogin', 'readonly')
				 ->unset_add_fields('creatod', 'lastLogin');

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

	public function _callback_file_url($pValue, $pRow){
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

/* End of file crud_service.php */
/* Location: ./application/library/crud_service.php */
