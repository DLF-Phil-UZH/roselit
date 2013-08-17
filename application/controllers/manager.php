<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Manager extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		
		$this->load->library('Shibboleth_authentication_service', NULL, 'shib_auth');
		$user = $this->shib_auth->verify_user();
		if ($user == false) {
			redirect('auth');
		} 
		if ($user !== false && $user->getRole() == 'new') {
			show_error('404');
		}
	}
	
	/**
	 * Renders CRUD output on crud view.
	 * 
	 * @param	string	$pPage		Name of displayed page ("lists" or "documents")
	 * @param			$pOutput	CRUD output
	 * @access	private
	 */
	private function _render_output($pPage, $pOutput = null){
		$this->load->view('header', array('title' => 'RoSeLit',
										  'page' => $pPage,
										  'width' => 'normal',
										  'access' => ($this->shib_auth->verify_user() !== false)));
		$this->load->view('crud', $pOutput);
		$this->load->view('footer');
	}

			
	public function index()
	{
        redirect(site_url('/manager/documents'));
	}
	
    /**
     * Display a CRUD table for Document_models
     */
	public function documents()
	{
		$this->load->library('Crud_service');			
		try{
			$crudOutput = $this->crud_service->getDocumentsCrud();
			$this->_render_output("documents", $crudOutput);
		}catch(Exception $e){
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
    }

	/**
	 * Send the requested file.
	 */
	public function documents_file($pId) {
		$this->load->model('document_mapper');
		$lDocument = $this->document_mapper->get($pId);
		if (!$lDocument) {
			// TODO: return some error message
			return;
		}
		$l_file = '/usr/local/ftp/phil_elearning/roselit/files/' . $lDocument->getFileName();
		$l_documentname = $lDocument->getExplicitId() . '.pdf';

		if (file_exists($l_file)) {
			header('Content-Description: File Transfer');
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename='.$l_documentname);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($l_file));
			ob_clean();
			flush();
			readfile($l_file);
			exit;
		}
    }

   	/**
	 *
	 */
    public function documents_file_upload($pId) {
		$this->load->model('document_mapper');
		$lDocument = $this->document_mapper->get($pId);
		if (!$lDocument) {
            // TODO: return some error message
            echo "Document not found";
            return;
        }
        $this->config->load('pdf_upload', TRUE);
        $global_upload_config = $this->config->item('pdf_upload');
        $upload_config = $global_upload_config;
        $upload_config['file_name'] = uniqid();

        $this->load->library('upload', $upload_config);

        // TODO: test if filetype is pdf
		$lUploadStatus = $this->upload->do_upload();
		if (!$lUploadStatus) {
            // return error message from upload library
            $errors_string = $this->upload->display_errors('', '//');
            $errors = explode('//', $errors_string);
            $this->output
                ->set_status_header('500')
                ->set_content_type('application/json')
                ->set_output(json_encode(array('errors' => $errors)));

			return;
		}
		$file_data = $this->upload->data();
		// set the fileName in the Document_model:
		$lDocument->setFileName($file_data['file_name']);
		$this->document_mapper->save($lDocument);
 
        // TODO: return a JSON
        $file_data = $this->upload->data();
        $json_data = array(
                        "files" => array(
                            "name" => $file_data['file_name'],
                            "size" => $file_data['file_size'],
                            "url" => base_url('files/') . $file_data['file_name'],
                            "delete_url" => base_url('files/delete/'.$file_data['file_name']),
                            "delete_type" => "DELETE"
                            )
                        );
       $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($json_data));
    }

    /**
     * Delete the PDF file associated with the Document_model.
     */
    public function documents_file_delete($pId) {
        $this->load->model('document_mapper');
		$lDocument = $this->document_mapper->get($pId);
		if (!$lDocument) {
            // TODO: return some error message
            $this->output->set_status_header(500);
            return;
        }
        $filePath = $lDocument->getFilePath();
        if (file_exists($filePath)) {
            // TODO: check if file really has been unlinked.
            $lDocument->setFileName('');
            // TODO: check if $lDocument really has been saved.
            $this->document_mapper->save($lDocument);
            $status = unlink($filePath);
            $this->output->set_status_header(200);
        } else {
            $this->output->set_status_header(404);
        }
    }


	public function lists()
	{
		$this->load->library('Crud_service');			
		try {
			$crudOutput = $this->crud_service->getDocumentListsCrud();
			$this->_render_output("lists", $crudOutput);
		} catch(Exception $e) {
			show_error($e->getMessage().' --- '.$e->getTraceAsString());
		}
	}
}

