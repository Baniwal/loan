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
		$this->Auth->allow('add');
		$this->Auth->allow('singup');
		$this->Auth->allow('verify');
		$this->Auth->allow('getdealername');
		
		

		$this->Auth->autoRedirect = false;    //リダイレクト機能をオフにする  
		
		// ログインが二度現れるバグ対策
		//$this->Auth->loginRedirect = array('controller' => '',  'action' => '?version='.date("Ymdhis"));

		$this->Session->write('Auth.redirect',"/?version=".date("YmdHis"));
	}
	
	function login(){
	   	Configure::write("debug",1);
		$this->layout = false;
		$msg = '';
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
		if (!empty( $this->data )) {
			if ($this->Auth->user()) {
				$cookie = array();
				$cookie['username'] = $this->data['User']['username'];
				$cookie['password'] = $this->data['User']['password'];//ハッシュ化されている
				$this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');//3つめの'true'で暗号化
				$this->redirect($this->Auth->redirect());
			}
			else
			{
				$msg = 'Invalid username or password, try again';
			}
		}
		$this->set(compact('msg'));
 	}
	
	function logout(){
        $this->Cookie->destroy();
		//$this->Session->setFlash(‘ログアウトしました。’);
		$this->redirect($this->Auth->logout());//ログアウトし、ログイン画面へリダイレクト
	}
	
	
	function add(){
		if(!empty($this->data)){
		Configure::write("debug",0);
		//$this->data['User']['username'];
		//$carArr = $this->Car->find('all',array('conditions'=>array('classname'=>$classname,'activemodel'=>1)));
		
			if($this->data){
				$this->User->create();
				$this->User->save($this->data);
				$this->redirect(array('action'=>'login'));
			}
		}
		
		$dealername =$this->User->find('all',array('fields'=>array('DISTINCT dealername'), 'order'=>'dealername DESC'));
		$this->set('dealername', $dealername);
	}
	
	
	function test(){
		Configure::write("debug",2);
		$this->Auth->authenticate = $this->NoHash;
        $cookie = $this->Cookie->read('Auth.User');
		debug($cookie);
		exit();
	}
	
	
	
	function singup() {
		Configure::write("debug",1);
		$msg = '';
		if(!empty($this->data)){
		//$this->data['User']['dealername'];
		//echo $this->data['User']['fullname'];
		$sirname = $this->data['User']['sirname'];
		$firstname = $this->data['User']['firstname'];
		$name = $firstname." ".$sirname;
		$sirnamekana = $this->data['User']['sirnamekana'];
		$firstnamekana = $this->data['User']['firstnamekana'];
		$kana = $firstnamekana." ".$sirnamekana;
		$email = $this->data['User']['email'];
		$telnumber = $this->data['User']['telnumber'];
		$this->data['User']['fullname'] = $name;
		$this->data['User']['kana'] = $kana;
		$username = $this->data['User']['username'];
		//$carArr = $this->Car->find('all',array('conditions'=>array('classname'=>$classname,'activemodel'=>1)));
		
			if($this->data){
				$this->User->create();
				if($this->User->save($this->data)){
					$to = $email;
					$subject = "User Registration email";
					$ur = CAKEPHP_URL."/users/verify?action=verify&ur=".base64_encode($email);
					include_once('../views/template/register.php');
					// Always set content-type when sending HTML email
					$headers = "MIME-Version: 1.0" . "\r\n";
					$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
					
					// More headers
					$headers .= 'From: <ajay@baniwalinfotech.com>' . "\r\n";
					
					mail($to,$subject,$message,$headers);
					//$this->Session->setFlash(__('User registered successfully'));
					//$this->redirect(array('action'=>'login'));
					
					$msg = "<strong>Registered Successfully!</strong> Please check your email and verify";

                	}
                	else {
						$msg = "<strong>Already Registered!</strong> Please try again";
            		}
			}
			
		}
		
		$dealername =$this->User->find('all',array('fields'=>array('DISTINCT dealername'), 'order'=>'dealername DESC'));
		$this->loadmodel('Dealer');
		$dealerarea =$this->Dealer->find('all',array('fields'=>array('DISTINCT Area'), 'group'=>'Area'));
		
		$page=$subpage=$title_for_layout='ログイン｜ローン見積り提案システム';
		$this->set(compact('title_for_layout','dealername','dealerarea','msg'));

	}
	
	
	
	function verify() {
		Configure::write("debug",0);
		$error = '';
		$action = $this->params['url']['action'];
		$ur = base64_decode($this->params['url']['ur']);
		$password = $this->data['User']['password'];
		if($action=='verify')
		{
			$sql = $this->User->updateAll(array("User.verifyemail" => "1","User.usedwp" => "1","User.active" => "1"),array("User.email" => $ur));
			$error = "Your Email has been Verified successfully.Please set your passowrd";
		}
		else if($action == 'updatepassword')
		{
			$sql = $this->User->updateAll(array("User.password" => $password),array("User.email" => $ur));
			$error = "Your password has been saved successfully.<a href='login'>Please login here</a>";
		}
		else
		{
			$error = "Please Verify your email.";
		}
		
		$this->set(compact('error'));
		
	}
	
	function getdealername()
	{
		Configure::write("debug",0);
		$dealers = '';
		$this->layout = 'ajax';
		$dealerarea = $_GET['dealerarea'];
		$this->loadmodel('Dealer');
		$dealers = $this->Dealer->find('all',array('conditions' => array("Area" => $dealerarea),'fields'=>array('DISTINCT dealer_name','dealer_code')));
		$this->set(compact('dealers'));
	}
	
}
				
			
			

?>
