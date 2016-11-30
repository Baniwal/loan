<?php
class UsersController extends AppController
{
	public $name = "Users";
	public $components = array('Auth','NoHash','Cookie');

	function beforeFilter(){
		// パスワードハッシュ化を解除
		$this->Auth->authenticate = $this->NoHash;

		$this->Auth->allow('logout');
		$this->Auth->allow('login');
		//$this->Auth->allow('add');
		

		$this->Auth->autoRedirect = false;    //リダイレクト機能をオフにする  
		
		// ログインが二度現れるバグ対策
		//$this->Auth->loginRedirect = array('controller' => '',  'action' => '?version='.date("Ymdhis"));

		$this->Session->write('Auth.redirect',"/?version=".date("YmdHis"));
	}
	
	function login(){
	   	Configure::write("debug",0);
		$this->layout = false;
		
		//Configure::write('Security.level', 'high');
		//$this->layout = "zbass";
        if (empty($this->data)) {
            $cookie = $this->Cookie->read('Auth.User');
            if (!is_null($cookie)) {
		//クッキーの情報でログインしてみる。
                if ($this->Auth->login($cookie)) {
		    		$this->redirect($this->Auth->redirect());
                }
    	    }
    	}
	
        if ($this->Auth->user()) {
			$cookie = array();
			$cookie['username'] = $this->data['User']['username'];
			$cookie['password'] = $this->data['User']['password'];//ハッシュ化されている
			$this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');//3つめの'true'で暗号化
        	$this->redirect($this->Auth->redirect());
        }
 	}
	
	function logout(){
        $this->Cookie->destroy();
		//$this->Session->setFlash(‘ログアウトしました。’);
		$this->redirect($this->Auth->logout());//ログアウトし、ログイン画面へリダイレクト
	}
	
	/*
	function add(){
		if(!empty($this->data)){
			if($this->data){
				$this->User->create();
				$this->User->save($this->data);
				$this->redirect(array('action'=>'login'));
			}
		}
	}
	*/
	
	function test(){
		Configure::write("debug",2);
        $cookie = $this->Cookie->read('Auth.User');
		debug($cookie);
		exit();
	}
}
				
			
			

?>
