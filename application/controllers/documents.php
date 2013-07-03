<?php

class Documents extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->load->model('document_mapper');
	}

	public function file($pId){
		$lDocument = $this->document_mapper->get($pId);
		if(!$lDocument){
			// TODO: return some error message
			return;
		}
		$lFile = $lDocument->getFilePath();
		$lDocumentname = $lDocument->getExplicitId() . '.pdf';

		if($lFile === false){
		// TODO: return some error message
			return;
        }
		else{
			header('Content-Description: File Transfer');
			header('Content-Type: application/pdf');
			header('Content-Disposition: attachment; filename='.$lDocumentname);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($lFile));
			ob_clean();
			flush();
			readfile($lFile);
			// exit;
		}
	
	}
	
}