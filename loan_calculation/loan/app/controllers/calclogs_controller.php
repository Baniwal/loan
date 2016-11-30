<?php
class CalclogsController extends AppController
{
	var $name = "Calclogs";
	var $uses = array("Calclog","User");
	//var $scaffold;
	var $helpers = array("Csv","Javascript");
	

	function getcsv(){
		Configure::write('debug', 0); // 警告を出さない
		$start = "";
		$end = "";
		
		debug($this->params['url']);
		
		if(isset($this->params['url']['search'])){
			if($this->params['url']['password'] != "mbfmbf"){
				die("Password is not correct");
			}
			$this->layout = false;
			
			// サーチボタンが推された場合
			if($this->params['url']['start']){
				$start = $this->params['url']['start'];
			}else{
				$start = "2012/08/05";
			}
			if($this->params['url']['starttime']){
				$starttime = $this->params['url']['starttime'];
			}else{
				$starttime = "00:00:00";
			}
			if($this->params['url']['end']){
				$end = $this->params['url']['end'];
			}else{
				$end = date("Y/m/d");
			}
			if($this->params['url']['endtime']){
				$endtime = $this->params['url']['endtime'];
			}else{
				$endtime = "23:59:59";
			}
			if($start!="" && $end!=""){
				// 期間指定の場合
				$where = "Calclog.created >= '".$start." ".$starttime ."' and Calclog.created <= '".$end." ".$endtime ."'";
			}else{
				if($start!=""){
					$where = "Calclog.created >= '".$start. " ".$starttime . "'";
				}
				if($end!=""){
					$where = "Calclog.created <= '".$end. " " . $endtime . "'";
				}
			}
			
			// セールスマンモードの条件指定を追加
			switch($this->params['url']['salesmanmode']){
				case 0:	// 両方
					//条件指定しない
					break;
				case 1:	// セールスマン
					$where .= ' and (Calclog.salesman != "") ';
					break;
				case 2:	// 一般のみ
					$where .= ' and (Calclog.salesman is null) ';
					break;
			}
			
			
			// お勧め演算分を除外する
			if($where){
				$where .= " and (Calclog.mode = 'button' or Calclog.mode = 'init' or Calclog.mode = 'quickchart')";
			}else{
				$where = "(Calclog.mode = 'button' or Calclog.mode = 'init' or Calclog.mode = 'quickchart')";
			}
			
			
			// プランの条件指定を追加
			if($this->params['url']['plan']){
				$where .= " and (";
				foreach($this->params['url']['plan'] as $key=>$plan){
					if($key==0){
						$where .= "Calclog.plan = '".$plan."'";
					}else{
						$where .= " or Calclog.plan = '".$plan."'";
					}
				}
				$where .= ") ";
			}
			
			debug($where);
			
			// CSV表示をするフィールドをここで定義
			$fields = array(
							'Calclog.id',
							'Calclog.parentid',
							'Calclog.mode',
							'Calclog.plan',
							'Calclog.bmst',
							'Calclog.carname',
							'Calclog.price',
							'Calclog.classname',
							'Calclog.rate',
							'Calclog.normalrate',
							'Calclog.milage',
							'Calclog.installments',
							'Calclog.loanprincipal',
							'Calclog.pricetax',
							'Calclog.optiontotal',
							'Calclog.sonota',
							'Calclog.dealeraccessories',
							'Calclog.mmmaintenance',
							'Calclog.mmsupport',
							'Calclog.salesexpenses',
							'Calclog.mbinsureance',
							'Calclog.dealeroption',
							'Calclog.discount',
							'Calclog.mmmprice',
							'Calclog.mmsprice',
							'Calclog.accessoryprice',
							'Calclog.taxtotal',
							'Calclog.downpayment',
							'Calclog.bonuspayment',
							'Calclog.lastpayment',
							'Calclog.interest',
							'Calclog.total',
							'Calclog.difference',
							'Calclog.bonustimes',
							'Calclog.monthlypayment',
							'Calclog.firstpayment',
							'Calclog.leasingprice',
							'Calclog.pdf',
							'Calclog.salesman',
							'Calclog.created',
							'Calclog.ip',
							'Calclog.agent'
						);
			$order = array('Calclog.id');
			
			$logArrs = $this->Calclog->find('all',array("fields"=>$fields,"conditions"=>$where,"order"=>$order));
			
			
			// セールスマンを配列に入れる
			foreach($logArrs as $key=>$logArr){
				if($logArr['Calclog']['salesman']){
					$salesmanArr = $this->User->findByUsername($logArr['Calclog']['salesman']);
					$logArrs[$key]['Calclog']['dealername'] = $salesmanArr['User']['dealername'];
					$logArrs[$key]['Calclog']['nw'] = $salesmanArr['User']['nw'];		// 2012.11.01 nwフィールド追加 by morita
					$logArrs[$key]['Calclog']['fullname'] = $salesmanArr['User']['fullname'];
					debug("セールスマンヒット!!!!");
				}else{
					$logArrs[$key]['Calclog']['dealername'] = "";
					$logArrs[$key]['Calclog']['fullname'] = "";
				}
				// 日付のみを入れる
				$logArrs[$key]['Calclog']['date'] = date("Y-m-d",strtotime($logArrs[$key]['Calclog']['created']));
			}
			
			//debug($logArrs);exit();
			
			
			$this->set("logArrs",$logArrs);
			$this->set("search",$this->params['url']['search']);
			$encode = "UTF-8";
			if($this->params['url']['submit'] == "CSV Download(SJIS)"){
				$encode = "SJIS";
			}
			$this->set("encode",$encode);
			//debug($logArrs);
		}else{
			// 初回アクセス
			$this->set("logArrs","");
			$this->set("search","");
			$starttime = "00:00:00";
			$endtime = "23:59:59";
		}
		$this->set("start",$start);
		$this->set("starttime",$starttime);
		$this->set("end",$end);
		$this->set("endtime",$endtime);
	}


	function getcsv2(){
		Configure::write('debug', 0); // 警告を出さない
		$start = "";
		$end = "";
		if(isset($this->params['url']['search'])){
			if($this->params['url']['password'] != "08066704475" && $this->params['url']['password'] != "09026507637"){
				die("Password is not correct");
			}
			$this->layout = false;
			
			/*
			// サーチボタンが推された場合
			if(isset($this->params['url']['start'])){
				$start = $this->params['url']['start'];
			}
			if(!is_null($this->params['url']['starttime'])){
				$starttime = $this->params['url']['starttime'];
			}else{
				$starttime = "00:00:00";
			}
			if(isset($this->params['url']['end'])){
				$end = $this->params['url']['end'];
			}
			if(!is_null($this->params['url']['endtime'])){
				$endtime = $this->params['url']['endtime'];
			}else{
				$endtime = "23:59:59";
			}
			if($start!="" && $end!=""){
				// 期間指定の場合
				$where = "`created` >= '#".$start." ".$starttime ."#' and `created` <= '#".$end." ".$endtime ."#'";
			}else{
				if($start!=""){
					$where = "`Calclog`.`created` >= '#".$start. " ".$starttime . "#'";
				}
				if($end!=""){
					$where = "`Calclog`.`created` <= '#".$end. " " . $endtime . "#'";
				}
			}
			*/
			
			//debug($where);
			
			// お勧め演算分を除外する
			/*
			if($where){
				$where .= " and (`Calclog.mode` = 'button' or `Calclog.mode` = 'init')";
			}else{
				$where = "(`Calclog.mode` = 'button' or `Calclog.mode` = 'init')";
			}
			*/
			
			//debug($where);exit();
			
			//$logArrs = $this->Calclog->findAll($where,null,'Calclog.id DESC');
			//$logArrs = $this->Calclog->findAll("Calclog.salesman != ''",null,'Calclog.id DESC');
			$fields = array(
							'Calclog.id',
							'Calclog.parentid',
							'Calclog.mode',
							'Calclog.plan',
							'Calclog.bmst',
							'Calclog.carname',
							'Calclog.price',
							'Calclog.classname',
							'Calclog.rate',
							'Calclog.normalrate',
							'Calclog.milage',
							'Calclog.installments',
							'Calclog.loanprincipal',
							'Calclog.pricetax',
							'Calclog.optiontotal',
							'Calclog.sonota',
							'Calclog.dealeraccessories',
							'Calclog.mmmaintenance',
							'Calclog.mmsupport',
							'Calclog.salesexpenses',
							'Calclog.mbinsureance',
							'Calclog.dealeroption',
							'Calclog.regfee',
							'Calclog.discount',
							'Calclog.mmmprice',
							'Calclog.mmsprice',
							'Calclog.accessoryprice',
							'Calclog.taxtotal',
							'Calclog.downpayment',
							'Calclog.bonuspayment',
							'Calclog.lastpayment',
							'Calclog.interest',
							'Calclog.total',
							'Calclog.difference',
							'Calclog.bonustimes',
							'Calclog.monthlypayment',
							'Calclog.firstpayment',
							'Calclog.leasingprice',
							'Calclog.pdf',
							'Calclog.salesman',
							'Calclog.created',
							'Calclog.ip',
							'Calclog.agent'
						);
			$order = array('Calclog.id');
			
			$logArrs = $this->Calclog->find('all',array("fields"=>$fields,"conditions"=>"salesman != '' and mode = 'button'","order"=>$order));
			
			//debug($logArrs);exit();
			
			// セールスマンを配列に入れる
			foreach($logArrs as $key=>$logArr){
				/*
				unset($logArrs[$key]['Calclog']['imagepath01']);
				unset($logArrs[$key]['Calclog']['imagepath02']);
				unset($logArrs[$key]['Calclog']['imagepath03']);
				unset($logArrs[$key]['Calclog']['imagepath04']);
				unset($logArrs[$key]['Calclog']['imagepath05']);
				unset($logArrs[$key]['Calclog']['imagepath06']);
				unset($logArrs[$key]['Calclog']['imagepath07']);
				unset($logArrs[$key]['Calclog']['imagepath08']);
				unset($logArrs[$key]['Calclog']['imagepath09']);
				unset($logArrs[$key]['Calclog']['imagepath10']);
				*/
				if($logArr['Calclog']['salesman']){
					$salesmanArr = $this->User->findByUsername($logArr['Calclog']['salesman']);
					$logArrs[$key]['Calclog']['dealername'] = $salesmanArr['User']['dealername'];
					$logArrs[$key]['Calclog']['fullname'] = $salesmanArr['User']['fullname'];
					$logArrs[$key]['Calclog']['mode'] = 'init';
				}else{
					$logArrs[$key]['Calclog']['dealername'] = "";
					$logArrs[$key]['Calclog']['fullname'] = "";
					$logArrs[$key]['Calclog']['mode'] = 'init';
				}
			}
			
			
			
			foreach($logArrs as $key=>$logArr){
				// カレントレコードと同じセールスマンのひとつ前のレコードをまず、抽出する
				//if($key <= 1000) continue;
				$id = $logArr['Calclog']['id'];
				$salesman = $logArr['Calclog']['salesman'];
				$plan = $logArr['Calclog']['plan'];
				$bmst = $logArr['Calclog']['bmst'];
				$monthlypayment = $logArr['Calclog']['monthlypayment'];
				$second = strtotime($logArr['Calclog']['created']);
				
				// idが$idより小さい
				// idがMAX
				// salesmanが$salesmanと等しい
				/*
				$conditions = array('salesman'=>$salesman,'id <'=>$id,'mode'=>'button','plan'=>$plan,'bmst'=>$bmst,'monthlypayment !='=>$monthlypayment);
				$resultArr = $this->Calclog->findAll($conditions,null,'Calclog.id DESC');
				if($resultArr){
					debug($id);
					debug($resultArr);
					$logArrs[$key]['Calclog']['mode'] = 'button';
				}else{
					debug($id);
				}
				*/
				debug($key);
				//if($key <= 1000) continue;
				//if($key > 2000) break;
				
				$i=$key-1;
				while(1){
					if($i<=0) break;
					if(
						isset($logArrs[$i]) && 
						$logArrs[$i]['Calclog']['salesman'] == $salesman && 
						$logArrs[$i]['Calclog']['mode'] == 'init' &&
						$logArrs[$i]['Calclog']['plan'] == $plan &&
						$logArrs[$i]['Calclog']['bmst'] == $bmst && 
						$logArrs[$i]['Calclog']['monthlypayment'] != $monthlypayment
					){
						debug($key);
						debug($i);
						if($second - strtotime($logArrs[$i]['Calclog']['created']) <= 3600){
							// 直前のレコード
							$logArrs[$key]['Calclog']['mode'] = 'button';
							debug("-------------------------------------");
						}
						debug($i);
						break;
					}else{
						$i--;
						continue;
					}
					$i--;
				}
			}
			
			$this->set("logArrs",$logArrs);
			$this->set("search",$this->params['url']['search']);
			$encode = "UTF-8";
			if($this->params['url']['submit'] == "CSV Download(SJIS)"){
				$encode = "SJIS";
			}
			$this->set("encode",$encode);
			//debug($logArrs);
		}else{
			// 初回アクセス
			$this->set("logArrs","");
			$this->set("search","");
			$starttime = "00:00:00";
			$endtime = "23:59:59";
		}
		$this->set("start",$start);
		$this->set("starttime",$starttime);
		$this->set("end",$end);
		$this->set("endtime",$endtime);
	}
	
	/*
	function ini(){
		$logArrs = $this->Calclog->find('all',array('fields'=>array('id','mode','plan','bmst','monthlypayment','salesman','created','agent'));
	
	
	
	
	}
	*/
	
	function chg(){
		Configure::write('debug', 2); // 警告を出さない
$data = array(

array('Calclog'=>array('id'=>'359017','mode'=>'init')),
array('Calclog'=>array('id'=>'359033','mode'=>'init')),
array('Calclog'=>array('id'=>'359034','mode'=>'init')),
array('Calclog'=>array('id'=>'359037','mode'=>'init')),
array('Calclog'=>array('id'=>'359040','mode'=>'init')),
array('Calclog'=>array('id'=>'359046','mode'=>'init')),
array('Calclog'=>array('id'=>'359049','mode'=>'init')),
array('Calclog'=>array('id'=>'359052','mode'=>'init')),
array('Calclog'=>array('id'=>'359067','mode'=>'init')),
array('Calclog'=>array('id'=>'359121','mode'=>'init')),
array('Calclog'=>array('id'=>'359238','mode'=>'init')),
array('Calclog'=>array('id'=>'359244','mode'=>'init')),
array('Calclog'=>array('id'=>'359411','mode'=>'init')),
array('Calclog'=>array('id'=>'359469','mode'=>'init')),
array('Calclog'=>array('id'=>'359470','mode'=>'init')),
array('Calclog'=>array('id'=>'359471','mode'=>'init')),
array('Calclog'=>array('id'=>'359477','mode'=>'init')),
array('Calclog'=>array('id'=>'359728','mode'=>'init')),
array('Calclog'=>array('id'=>'359734','mode'=>'init')),
array('Calclog'=>array('id'=>'359737','mode'=>'init')),
array('Calclog'=>array('id'=>'359740','mode'=>'init')),
array('Calclog'=>array('id'=>'359761','mode'=>'init')),
array('Calclog'=>array('id'=>'359798','mode'=>'init')),
array('Calclog'=>array('id'=>'359801','mode'=>'init')),
array('Calclog'=>array('id'=>'359810','mode'=>'init')),
array('Calclog'=>array('id'=>'359846','mode'=>'init')),
array('Calclog'=>array('id'=>'359870','mode'=>'init')),
array('Calclog'=>array('id'=>'359895','mode'=>'init')),
array('Calclog'=>array('id'=>'359911','mode'=>'init')),
array('Calclog'=>array('id'=>'359914','mode'=>'init')),
array('Calclog'=>array('id'=>'359917','mode'=>'init')),
array('Calclog'=>array('id'=>'359920','mode'=>'init')),
array('Calclog'=>array('id'=>'359923','mode'=>'init')),
array('Calclog'=>array('id'=>'359926','mode'=>'init')),
array('Calclog'=>array('id'=>'359935','mode'=>'init')),
array('Calclog'=>array('id'=>'359941','mode'=>'init')),
array('Calclog'=>array('id'=>'359962','mode'=>'init')),
array('Calclog'=>array('id'=>'360304','mode'=>'init')),
array('Calclog'=>array('id'=>'360307','mode'=>'init')),
array('Calclog'=>array('id'=>'360322','mode'=>'init')),
array('Calclog'=>array('id'=>'360343','mode'=>'init')),
array('Calclog'=>array('id'=>'360391','mode'=>'init')),
array('Calclog'=>array('id'=>'360422','mode'=>'init')),
array('Calclog'=>array('id'=>'360518','mode'=>'init')),
array('Calclog'=>array('id'=>'360538','mode'=>'init')),
array('Calclog'=>array('id'=>'360553','mode'=>'init')),
array('Calclog'=>array('id'=>'360574','mode'=>'init')),
array('Calclog'=>array('id'=>'360676','mode'=>'init')),
array('Calclog'=>array('id'=>'360697','mode'=>'init')),
array('Calclog'=>array('id'=>'360740','mode'=>'init')),
array('Calclog'=>array('id'=>'360748','mode'=>'init')),
array('Calclog'=>array('id'=>'360769','mode'=>'init')),
array('Calclog'=>array('id'=>'360978','mode'=>'init')),
array('Calclog'=>array('id'=>'361249','mode'=>'init')),
array('Calclog'=>array('id'=>'361265','mode'=>'init')),
array('Calclog'=>array('id'=>'361268','mode'=>'init')),
array('Calclog'=>array('id'=>'361601','mode'=>'init')),
array('Calclog'=>array('id'=>'361734','mode'=>'init')),
array('Calclog'=>array('id'=>'361764','mode'=>'init')),
array('Calclog'=>array('id'=>'361843','mode'=>'init')),
array('Calclog'=>array('id'=>'361844','mode'=>'init')),
array('Calclog'=>array('id'=>'361845','mode'=>'init')),
array('Calclog'=>array('id'=>'361962','mode'=>'init')),
array('Calclog'=>array('id'=>'362148','mode'=>'init')),
array('Calclog'=>array('id'=>'364249','mode'=>'init')),
array('Calclog'=>array('id'=>'364294','mode'=>'init')),
array('Calclog'=>array('id'=>'364295','mode'=>'init')),
array('Calclog'=>array('id'=>'364301','mode'=>'init')),
array('Calclog'=>array('id'=>'364310','mode'=>'init')),
array('Calclog'=>array('id'=>'364448','mode'=>'init')),
array('Calclog'=>array('id'=>'364449','mode'=>'init')),
array('Calclog'=>array('id'=>'364517','mode'=>'init')),
array('Calclog'=>array('id'=>'364520','mode'=>'init')),
array('Calclog'=>array('id'=>'364618','mode'=>'init')),
array('Calclog'=>array('id'=>'364621','mode'=>'init')),
array('Calclog'=>array('id'=>'364832','mode'=>'init')),
array('Calclog'=>array('id'=>'364860','mode'=>'init')),
array('Calclog'=>array('id'=>'364936','mode'=>'init')),
array('Calclog'=>array('id'=>'365008','mode'=>'init')),
array('Calclog'=>array('id'=>'365009','mode'=>'init')),
array('Calclog'=>array('id'=>'365257','mode'=>'init')),
array('Calclog'=>array('id'=>'365365','mode'=>'init')),
array('Calclog'=>array('id'=>'365384','mode'=>'init')),
array('Calclog'=>array('id'=>'365423','mode'=>'init')),
array('Calclog'=>array('id'=>'365432','mode'=>'init')),
array('Calclog'=>array('id'=>'365441','mode'=>'init')),
array('Calclog'=>array('id'=>'365449','mode'=>'init')),
array('Calclog'=>array('id'=>'365489','mode'=>'init')),
array('Calclog'=>array('id'=>'365528','mode'=>'init')),
array('Calclog'=>array('id'=>'365573','mode'=>'init')),
array('Calclog'=>array('id'=>'365574','mode'=>'init')),
array('Calclog'=>array('id'=>'365608','mode'=>'init')),
array('Calclog'=>array('id'=>'366296','mode'=>'init')),
array('Calclog'=>array('id'=>'366299','mode'=>'init')),
array('Calclog'=>array('id'=>'366447','mode'=>'init')),
array('Calclog'=>array('id'=>'366467','mode'=>'init')),
array('Calclog'=>array('id'=>'367002','mode'=>'init')),
array('Calclog'=>array('id'=>'367117','mode'=>'init')),
array('Calclog'=>array('id'=>'367136','mode'=>'init')),
array('Calclog'=>array('id'=>'368327','mode'=>'init')),
array('Calclog'=>array('id'=>'368943','mode'=>'init')),
array('Calclog'=>array('id'=>'369027','mode'=>'init')),
array('Calclog'=>array('id'=>'369051','mode'=>'init')),
array('Calclog'=>array('id'=>'369098','mode'=>'init')),
array('Calclog'=>array('id'=>'369102','mode'=>'init')),
array('Calclog'=>array('id'=>'369108','mode'=>'init')),
array('Calclog'=>array('id'=>'369198','mode'=>'init')),
array('Calclog'=>array('id'=>'369204','mode'=>'init')),
array('Calclog'=>array('id'=>'369278','mode'=>'init')),
array('Calclog'=>array('id'=>'369280','mode'=>'init')),
array('Calclog'=>array('id'=>'369369','mode'=>'init')),
array('Calclog'=>array('id'=>'369372','mode'=>'init')),
array('Calclog'=>array('id'=>'369374','mode'=>'init')),
array('Calclog'=>array('id'=>'369375','mode'=>'init')),
array('Calclog'=>array('id'=>'369387','mode'=>'init')),
array('Calclog'=>array('id'=>'369389','mode'=>'init')),
array('Calclog'=>array('id'=>'369395','mode'=>'init')),
array('Calclog'=>array('id'=>'369402','mode'=>'init')),
array('Calclog'=>array('id'=>'369405','mode'=>'init')),
array('Calclog'=>array('id'=>'369414','mode'=>'init')),
array('Calclog'=>array('id'=>'369419','mode'=>'init')),
array('Calclog'=>array('id'=>'369422','mode'=>'init')),
array('Calclog'=>array('id'=>'369423','mode'=>'init')),
array('Calclog'=>array('id'=>'369429','mode'=>'init')),
array('Calclog'=>array('id'=>'369441','mode'=>'init')),
array('Calclog'=>array('id'=>'369444','mode'=>'init')),
array('Calclog'=>array('id'=>'369447','mode'=>'init')),
array('Calclog'=>array('id'=>'369462','mode'=>'init')),
array('Calclog'=>array('id'=>'369471','mode'=>'init')),
array('Calclog'=>array('id'=>'369474','mode'=>'init')),
array('Calclog'=>array('id'=>'369486','mode'=>'init')),
array('Calclog'=>array('id'=>'369498','mode'=>'init')),
array('Calclog'=>array('id'=>'369499','mode'=>'init')),
array('Calclog'=>array('id'=>'369511','mode'=>'init')),
array('Calclog'=>array('id'=>'369512','mode'=>'init')),
array('Calclog'=>array('id'=>'369543','mode'=>'init')),
array('Calclog'=>array('id'=>'369576','mode'=>'init')),
array('Calclog'=>array('id'=>'369609','mode'=>'init')),
array('Calclog'=>array('id'=>'369702','mode'=>'init')),
array('Calclog'=>array('id'=>'369720','mode'=>'init')),
array('Calclog'=>array('id'=>'369751','mode'=>'init')),
array('Calclog'=>array('id'=>'369845','mode'=>'init')),
array('Calclog'=>array('id'=>'369856','mode'=>'init')),
array('Calclog'=>array('id'=>'369877','mode'=>'init')),
array('Calclog'=>array('id'=>'369880','mode'=>'init')),
array('Calclog'=>array('id'=>'369894','mode'=>'init')),
array('Calclog'=>array('id'=>'369920','mode'=>'init')),
array('Calclog'=>array('id'=>'369924','mode'=>'init')),
array('Calclog'=>array('id'=>'369927','mode'=>'init')),
array('Calclog'=>array('id'=>'370013','mode'=>'init')),
array('Calclog'=>array('id'=>'370065','mode'=>'init')),
array('Calclog'=>array('id'=>'370066','mode'=>'init')),
array('Calclog'=>array('id'=>'370086','mode'=>'init')),
array('Calclog'=>array('id'=>'370107','mode'=>'init')),
array('Calclog'=>array('id'=>'370278','mode'=>'init')),
array('Calclog'=>array('id'=>'370281','mode'=>'init')),
array('Calclog'=>array('id'=>'370329','mode'=>'init')),
array('Calclog'=>array('id'=>'370333','mode'=>'init')),
array('Calclog'=>array('id'=>'370338','mode'=>'init')),
array('Calclog'=>array('id'=>'370398','mode'=>'init')),
array('Calclog'=>array('id'=>'370441','mode'=>'init')),
array('Calclog'=>array('id'=>'370459','mode'=>'init')),
array('Calclog'=>array('id'=>'370462','mode'=>'init')),
array('Calclog'=>array('id'=>'370528','mode'=>'init')),
array('Calclog'=>array('id'=>'370544','mode'=>'init')),
array('Calclog'=>array('id'=>'370556','mode'=>'init')),
array('Calclog'=>array('id'=>'370589','mode'=>'init')),
array('Calclog'=>array('id'=>'370605','mode'=>'init')),
array('Calclog'=>array('id'=>'370615','mode'=>'init')),
array('Calclog'=>array('id'=>'370624','mode'=>'init')),
array('Calclog'=>array('id'=>'370633','mode'=>'init')),
array('Calclog'=>array('id'=>'370636','mode'=>'init')),
array('Calclog'=>array('id'=>'370639','mode'=>'init')),
array('Calclog'=>array('id'=>'371413','mode'=>'init')),
array('Calclog'=>array('id'=>'371416','mode'=>'init')),
array('Calclog'=>array('id'=>'371784','mode'=>'init')),
array('Calclog'=>array('id'=>'371812','mode'=>'init')),
array('Calclog'=>array('id'=>'371818','mode'=>'init')),
array('Calclog'=>array('id'=>'371821','mode'=>'init')),
array('Calclog'=>array('id'=>'371824','mode'=>'init')),
array('Calclog'=>array('id'=>'371827','mode'=>'init')),
array('Calclog'=>array('id'=>'371887','mode'=>'init')),
array('Calclog'=>array('id'=>'371897','mode'=>'init')),
array('Calclog'=>array('id'=>'371927','mode'=>'init')),
array('Calclog'=>array('id'=>'372903','mode'=>'init')),
array('Calclog'=>array('id'=>'372906','mode'=>'init')),
array('Calclog'=>array('id'=>'372924','mode'=>'init')),
array('Calclog'=>array('id'=>'372928','mode'=>'init')),
array('Calclog'=>array('id'=>'372935','mode'=>'init')),
array('Calclog'=>array('id'=>'373786','mode'=>'init')),
array('Calclog'=>array('id'=>'375447','mode'=>'init')),
array('Calclog'=>array('id'=>'375451','mode'=>'init')),
array('Calclog'=>array('id'=>'375559','mode'=>'init')),
array('Calclog'=>array('id'=>'375577','mode'=>'init')),
array('Calclog'=>array('id'=>'375580','mode'=>'init')),
array('Calclog'=>array('id'=>'375586','mode'=>'init')),
array('Calclog'=>array('id'=>'376014','mode'=>'init')),
array('Calclog'=>array('id'=>'376017','mode'=>'init')),
array('Calclog'=>array('id'=>'376020','mode'=>'init')),
array('Calclog'=>array('id'=>'376023','mode'=>'init')),
array('Calclog'=>array('id'=>'376271','mode'=>'init')),
array('Calclog'=>array('id'=>'376475','mode'=>'init')),
array('Calclog'=>array('id'=>'376484','mode'=>'init')),
array('Calclog'=>array('id'=>'376493','mode'=>'init')),
array('Calclog'=>array('id'=>'376782','mode'=>'init')),
array('Calclog'=>array('id'=>'376809','mode'=>'init')),
array('Calclog'=>array('id'=>'376812','mode'=>'init')),
array('Calclog'=>array('id'=>'377068','mode'=>'init')),
array('Calclog'=>array('id'=>'377137','mode'=>'init')),
array('Calclog'=>array('id'=>'377228','mode'=>'init')),
array('Calclog'=>array('id'=>'377665','mode'=>'init')),
array('Calclog'=>array('id'=>'379908','mode'=>'init')),
array('Calclog'=>array('id'=>'379935','mode'=>'init')),
);

		
		debug($data);
		
		if($this->Calclog->saveAll($data)){
			die("OK");
		}else{
			die("NG");
		}
		
		
		
	}

	
	
}
				
			
			

?>
