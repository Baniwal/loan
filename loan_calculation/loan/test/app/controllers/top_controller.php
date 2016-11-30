<?php
class TopController extends AppController {

	var $name = 'Top';
	var $uses = null;
	
	function index(){
		
		$this->redirect(CAKEPHP_URL ."/users/login/");
		
	}
}
?>