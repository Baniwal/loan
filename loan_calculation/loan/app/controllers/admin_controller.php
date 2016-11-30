<?php
class AdminController extends AppController
{
	public $name = "Admin";
	public $components = array('Auth','NoHash','Cookie');

	function beforeFilter(){
		// パスワードハッシュ化を解除
		$this->Auth->authenticate = $this->NoHash;
		$this->Auth->allow('logout');
		$this->Auth->allow('index');
		$this->Auth->allow('dashboard');
		$this->Auth->autoRedirect = false;    //リダイレクト機能をオフにする  
		
		// ログインが二度現れるバグ対策
		//$this->Auth->loginRedirect = array('controller' => '',  'action' => '?version='.date("Ymdhis"));

		//$this->Session->write('Auth.redirect',"/?version=".date("YmdHis"));
	}
	
	function index(){
	   	Configure::write("debug",1);
		$this->layout = false;
		$msg = '';
		if (empty($this->data)) {
           $cookie = $this->Cookie->read('Auth.Admin');
            if (!is_null($cookie)) {
				//クッキーの情報でログインしてみる。
				$this->redirect(CAKEPHP_URL.'/admin/dashboard');
    	    }
    	}
        
		if (!empty( $this->data )) {
				$this->loadmodel('admin');
				$sql = $this->admin->find('all',array('conditions' => array("username" => $this->data['Admin']['username'],"password"=>$this->data['Admin']['password'],"status"=>'Y'),'fields'=>array('id','username')));
				if($sql)
				{
					$cookie = array();
					$admin_username = $this->data['Admin']['username'];
					$cookie['admin_username'] = $this->data['Admin']['username'];
					$cookie['admin_password'] = $this->data['Admin']['password'];//ハッシュ化されている
					$this->Cookie->write('Auth.Admin', $cookie, true,'+2 weeks');//3つめの'true'で暗号化
					$this->redirect(CAKEPHP_URL.'/admin/dashboard');
					$msg = 'Login Successfully';	
				}
				else
				{
					$msg = 'Invalid username or password, try again';
				}
		}
		$this->set(compact('msg'));
 	}
	
	function dashboard() {
		Configure::write("debug",1);
		
	}
	
	function logout(){
        $this->Cookie->destroy();
		//$this->Session->setFlash(‘ログアウトしました。’);
		$this->redirect(CAKEPHP_URL.'/admin/index');//ログアウトし、ログイン画面へリダイレクト
	}
	
}
				
			
			

?>
