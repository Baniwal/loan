<?php
/*


リリースノート



*/
class CarsController extends AppController
{
    var $name = 'Cars';
	//var $uses = array("Car","Rate","Initrate","Bptrate","Lptrate","Lpprate","Accesslog","Alsoption","Popdata","Quickchart","User","Taxoption","Calclog");
	var $uses = array("Car","Rate","Initrate","Bptrate","Lptrate","Lpprate","Accesslog","Alsoption","User","Taxoption","Calclog","Quickchart");
	
	var $components = array('Auth','Cookie');
	//var $helpers = array('Javascript');
	var $mbjarr = array();
	
	var $sonotaArr = array();
	
	var	$plannameArr = array(
			'wp'=>'ウェルカムプラン',
			'swp'=>'スーパーウェルカムプラン',
			'std'=>'スタンダードローン',
			'sup'=>'スタートアッププラン',
		);
	
	var	$u_plannameArr = array(
			'wp'=>'ユーズドカーウェルカムプラン',
			'std'=>'通常ローン',
		);
	

	function beforeFilter(){
		$this->Auth->allow('carjson');
		$this->Auth->allow('carnamejson');
		$this->Auth->allow('ratejson');
		$this->Auth->allow('gettable');
		$this->Auth->allow('hikakuhtml');
		$this->Auth->allow('insertlog');
		$this->Auth->allow('ajaxrate');
		$this->Auth->allow('ajaxinstallments');
		$this->Auth->allow('version');
		
		
		// ログイン状態を取得
		//$this->login = $this->Auth->isAuthorized();
		// セールスマン名を取得
		/*
		$this->salesman = $this->Auth->user('username');
		$this->login = $this->salesman;
		
		*/
	
		$this->set("login",1);
	}
	
	
	
				
/***********************************************************************************

	作成者	pc-otasuke.jp morita
	
	関数名	index()
	引数	なし
	戻り値	なし
	
	機能
	
		ローン計算シート基本画面
		
		
************************************************************************************/
	function index(){
		$this->layout = false;
		Configure::write("debug",0);
		
		
		$arr = Configure::read("leafletArrs");
		
		debug($arr);
		
		// クラス名のjsonデータを作る
		//$carArrs = $this->Car->find("all",array("conditions"=>"Car.bmst not like 'used%'","group"=>array("qc_classname"),"fields"=>array("classname","qc_classname","min('id') as id"),"order"=>"id DESC"));
		//$carArrs = $this->Car->find("all",array("conditions"=>array("Car.bmst not like"=>"used%","Car.activemodel"=>1),"group"=>array("qc_classname"),"fields"=>array("classname","qc_classname")));
		$carArrs = $this->Car->find("all",array("conditions"=>array("Car.bmst not like"=>"used%","Car.activemodel"=>"1"),"group"=>array("qc_classname"),"fields"=>array("id","classname","qc_classname"),"order"=>"qc_classorder"));
		
		$classjson = '{';
		foreach($carArrs as $key=>$arr){
			if(end($carArrs)==$arr){
				$classjson .= '"'.$key.'":"'.$arr['Car']['qc_classname'].'"';
			}else{
				$classjson .= '"'.$key.'":"'.$arr['Car']['qc_classname'].'",';
			}
		}
		
		// クラス名のjsonデータを作る
		//$carArrs = $this->Car->find("all",array("conditions"=>"Car.bmst not like 'used%'","group"=>array("qc_classname"),"fields"=>array("classname","qc_classname","min('id') as id"),"order"=>"id DESC"));
		$u_carArrs = $this->Car->find("all",array("conditions"=>array("Car.bmst like"=>"used%","Car.activemodel"=>1)));
		
		$u_classjson = '{';
		foreach($u_carArrs as $key=>$arr){
			if(end($u_carArrs)==$arr){
				$u_classjson .= '"'.$key.'":"'.$arr['Car']['qc_classname'].'"';
			}else{
				$u_classjson .= '"'.$key.'":"'.$arr['Car']['qc_classname'].'",';
			}
		}
		
		// プラン名称
		$this->set("planArr",$this->plannameArr);
		$this->set("u_planArr",$this->u_plannameArr);
		
		$this->set("classjson",$classjson);
		$this->set("u_classjson",$u_classjson);
		
		$this->set("carArrs",$carArrs);
		$this->set("u_carArrs",$u_carArrs);
		
		$this->set("salesman",$this->Auth->user('id'));
	}

function compare2(){
		$this->layout = false;
		Configure::write("debug",0);
}
	
/***********************************************************************************

	作成者	pc-otasuke.jp morita
	
	関数名	carjson()
	引数	mbst
	戻り値	なし
	
	機能
	
		bmstに該当するCarレコードを取得し、JSON形式にて出力する
		
		
************************************************************************************/
	function carjson($bmst){
		Configure::write("debug",0);
		
		$this->layout = "ajax";
		
		$carArr = $this->Car->findByBmst($bmst);
		
		debug(count($carArr['Car']));
		
		// jsonを作る
		$json = '{';
		
		$i=1;
		foreach($carArr['Car'] as $key=>$value){
			if(count($carArr['Car'])==$i++){
				$json.= '"'.$key.'":"'.$value.'"';
			}else{
				$json.= '"'.$key.'":"'.$value.'",';
			}
		}
		$json .= '}';
		
		debug($json);
		
		$this->set("json",$json);
	}
/***********************************************************************************

	作成者	pc-otasuke.jp morita
	
	関数名	carnamejson()
	引数	mbst
	戻り値	なし
	
	機能
	
		bmstに該当するCarレコードを取得し、JSON形式にて出力する
		
		
************************************************************************************/
	function carnamejson($classname){
		Configure::write("debug",0);
		
		$this->layout = "ajax";
		
		//$carArr = $this->Car->findAllByClassname($classname);
		$carArr = $this->Car->find('all',array('conditions'=>array('classname'=>$classname,'activemodel'=>1)));
		
		// jsonを作る
		$json = '{';
		
		foreach($carArr as $key=>$arr){
			if(end($carArr)==$arr){
				$json.= '"'.$key.'":{"qc_carname":"'.$arr['Car']['qc_carname'].'","bmst":"'.$arr['Car']['bmst'].'"}';
			}else{
				$json.= '"'.$key.'":{"qc_carname":"'.$arr['Car']['qc_carname'].'","bmst":"'.$arr['Car']['bmst'].'"},';
			}
		}
		$json .= '}';
		
		debug($json);
		
		$this->set("json",$json);
	}
	
/***********************************************************************************

	作成者	pc-otasuke.jp morita
	
	関数名	ratejson()
	引数	$patternid
	戻り値	なし
	
	機能
	
		patternidに該当するCarレコードを取得し、JSON形式にて出力する
		
		
************************************************************************************/
	function ratejson($patternid){
		Configure::write("debug",0);
		
		$this->layout = "ajax";
		
		$rateArr = $this->Rate->findAllBypatternid($patternid);
		
		// jsonを作る
		$json = '{';
		
		foreach($rateArr as $key=>$arr){
			if(end($rateArr)==$arr){
				$json.= '"'.$arr['Rate']['installments'].'":{"rate":"'.$arr['Rate']['rate'].'","lowrate":"'.$arr['Rate']['lowrate'].'","innerrate":"'.$arr['Rate']['innerrate'].'"}';
			}else{
				$json.= '"'.$arr['Rate']['installments'].'":{"rate":"'.$arr['Rate']['rate'].'","lowrate":"'.$arr['Rate']['lowrate'].'","innerrate":"'.$arr['Rate']['innerrate'].'"},';
			}
		}
		$json .= '}';
		
		debug($json);
		
		$this->set("json",$json);
	}
	
	function gettable($tablename){
		Configure::write("debug",0);
		
		// User Tableは念のためブロック
		if($tablename == "User"){
			die("");
		}
		
		$this->layout = "ajax";
		
		$tableArrs = $this->{$tablename}->find('all');
		
		// jsonを作る
		$json = '[';
		
		$c1 = count($tableArrs);
		foreach($tableArrs as $key=>$arr){
			$json .= '{';
			$c2 = count($arr[$tablename]);
			foreach($arr[$tablename] as $field=>$value){
				$json .= '"'.$field.'":"'.$value.'"';
				if(--$c2){
					$json.= ',';
				}else{
					// 何もしない
				}
			}
			$json .= '}';
			if(--$c1){
				$json .=",";
			}else{
				// 何もしない
			}
		}
		$json .= ']';
		
		debug($json);
		
		$this->set("json",$json);
	}
	
	function insertlog(){
		Configure::write("debug",0);
		$this->layout = 'ajax';
		
		$data['Calclog'] = $this->params['url'];
		
		// datetimeを入れる
		$datetime = date("Y-m-d H:i:s");
		$data['Calclog']['created'] = $datetime;
		
		
		// PDF用のコードを作る
		$date = date("Ymd",strtotime($datetime));
		$time = date("His",strtotime($datetime));
		
		// クラス名を取得
		$classname = $data['Calclog']['classname'];
		
		// プラン
		$plan = $data['Calclog']['plan'];
		
		
		$this->Calclog->create();
		
		$this->Calclog->save($data);
		
		// インサートしたIDを取得
		$id = $this->Calclog->getLastInsertID();
		
		// コードを算出
		$code = $date."_".$time."_".strtoupper($plan)."_".$classname."_".$id;

		$this->set("code",$code);
		$this->set("id",$id);
	}
/***********************************************************************************

	作成者	pc-otasuke.jp morita
	
	関数名	compare()
	引数	なし
	戻り値	なし
	
	機能
	
	支払例比較タブ（ポップアップ）
	

************************************************************************************/
	function compare($logid){
		$this->layout = false;
		Configure::write("debug",0);
		
		// microtime
		$this->set("microtime",microtime());
		
				
		
		// urlの変数を直接参照
		$this->set("get",$this->params['url']);
		
		$logArr = $this->Calclog->findById($logid);
		
		$this->set("logid",$logid);
			
		// ログに記録されているパラメーターを直接ctpにて参照
		debug($logArr);
		foreach($logArr['Calclog'] as $key=>$value){
			$this->set($key,$value);
		}
		
		debug($logArr);
		
		// Car テーブルを参照
		$carArr = $this->Car->findByBmst($logArr['Calclog']['bmst']);
		
		$plan = $logArr['Calclog']['plan'];
		
		// 金利の配列を得る→金利リストボックス作成のため
		$this->set("rateArr",$this->Rate->findAllByPatternid($carArr['Car'][$plan.'ratepattern'],null,'Rate.installments'));
		
		$this->set("accessory",0);
		
		
		$this->set("plannameArr",$this->plannameArr);
		//$this->set("planArr",$planArr);
		
		if(isset($this->params['url']['milage'])){
			$this->set("milage",$this->params['url']['milage']);
		}else{
			$this->set("milage",1);
		}
		
		$this->set("carname",$carArr['Car']['carname']);
		$this->set("classname",$carArr['Car']['classname']);
		
		// タイトル設定
		$this->pageTitle = $carArr['Car']['carname']."[".$carArr['Car']['carname']."] 支払比較";
				
		
		// PDF用のコードを作る
		$datetime = $logArr['Calclog']['created'];
		$date = date("Ymd",strtotime($datetime));
		$time = date("His",strtotime($datetime));
		
		// コードを算出
		$code = $date."_".$time."_".strtoupper($plan)."_".$logArr['Calclog']['classname']."_".$logid;

		$this->set("code",$code);
		$this->set("leaflet",$logArr['Calclog']['leafletimage']);

	}
	
	function hikakuhtml($plan,$num,$bmst){
		Configure::write("debug",0);
		$this->layout = 'ajax';
		
		/*
		$rate = array();
		if($this->login){
			// Car テーブルを参照
			$carArr = $this->Car->findByBmst($this->params['url']['bmst']);
			
			// 金利の配列を得る→金利リストボックス作成のため
			$rateArr = $this->Rate->findAllByPatternid($carArr['Car'][$plan.'ratepattern'],null,'Rate.installments');
			
			$rate = array($rateArr[0]['Rate']['innnerrate'],$rateArr[0]['Rate']['lowrate'],$rateArr[0]['Rate']['rate']);
			$rate = array_unique($rate);
		}
		
		$this->set("rateArr",$rate);
		*/
		
		// Car テーブルを参照
		$carArr = $this->Car->findByBmst($bmst);
		
		$this->set("swpmodel",$carArr['Car']['swpmodel']);
		
		$this->set("plan",$plan);
		$this->set("num",$num);
		$this->set("plannameArr",$this->plannameArr);
	}
	
	function ajaxinstallments(){
		Configure::write("debug",0);
		$this->layout = 'ajax';

		$bmst = $this->params['url']['bmst'];
		$plan = $this->params['url']['plan'];
		
		if($plan){
			// Car テーブルを参照
			$carArr = $this->Car->findByBmst($bmst);
			
			// 金利の配列を得る→金利リストボックス作成のため
			$rateArr = $this->Rate->findAllByPatternid($carArr['Car'][$plan.'ratepattern'],null,'Rate.installments');
			
			// リース系の場合は、アクセサリをDBから持ってくる
			
			
			debug($rateArr);
			
			$string = "{";
			
			foreach($rateArr as $key=>$arr){
				if($key==0){
					$string .= '"'.$arr['Rate']['installments'].'":'.$arr['Rate']['installments'];
				}else{
					$string .= ',"'.$arr['Rate']['installments'].'":'.$arr['Rate']['installments'];
				}
			}
			$string .= "}";
		}else{
			$string = '{"回数選択":"回数選択"}';
		}
		
		$this->set("json",$string);
	}
	
	
	function ajaxrate(){
		Configure::write("debug",0);
		$this->layout = 'ajax';

		$bmst = $this->params['url']['bmst'];
		$plan = $this->params['url']['plan'];
		$installments = $this->params['url']['installments'];
		
		$carArr = $this->Car->findByBmst($bmst);
		$rateArr = $this->Rate->find("all",array("conditions"=>array("patternid"=>$carArr['Car'][$plan.'ratepattern'],"installments"=>$installments)));
		
		$this->set("rateArr",$rateArr);
	}
	
	
	// DBのバージョンを返す
	function version(){
		$this->layout = "ajax";
		
		// 日付が一番遅いものを持ってくる
		$versionArr = $this->Quickchart->find("first",array('order'=>"date desc"));
		
		$this->set("version",$versionArr['Quickchart']['version']);
	}
		
	
	
}

?>
