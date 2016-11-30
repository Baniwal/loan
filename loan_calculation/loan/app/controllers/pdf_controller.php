<?php
/**
 * PDFコントローラー
 */
class PdfController extends AppController {
	var $name = 'Pdf';
	var $uses = array('Calclog','Car','User');
	var $helpers = array('Fpdf');
	var $dummyArr = array(
				'id' =>'',
				'parentid' => '',
				'mode' =>'',
				'plan' =>'-',
				'bmst' => '',
				'carname' => '',
				'price' => '-',
				'classname' => '-',
				'rate' => '-',
				'normalrate' => '-',
				'milage' => '-',
				'installments' => '-',
				'loanprincipal' => '-',
				'pricetax' => '-',
				'optiontotal' => '-',
				'sonota' => '-',
				//'dealeraccessories' => '-',
				'mmmaintenance' => '-',
				'mmsupport' => '-',
				'salesexpenses' => '-',
				'mbinsureance' => '-',
				'dealeroption' => '-',
				'discount' => '-',
				'mmmprice' => '-',
				'mmsprice' => '-',
				'accessoryprice' => '-',
				'taxtotal' => '-',
				'downpayment' => '-',
				'bonuspayment' => '-',
				'lastpayment' => '-',
				'interest' => '-',
				'total' => '-',
				'difference' => '-',
				'bonustimes' => '-',
				'monthlypayment' => '-',
				'firstpayment' => '-',
				'leasingprice' => '-',
				'pdf' => '-',
				'salesman' => '-'
				);

	/**
	 * 見積書発行
	 * @param string $code: (e.g.: EA00000)
	 */
	function estimateold($string,$logid1=0,$logid2=0,$logid3=0) {
		// デバックモードを0にする。
		Configure::write('debug',0);
		
		// PDFレイアウトを使用。
		$this->layout = 'pdf';
		
		// パラメーターを解析する
		// hikaku: 2012.12.06 by morita
		
		if($logid1 == 0){
			/* タブと改行をトークンの区切りとして使用します */
			$arg = array();
			
			$tok = strtok($string, "_");
			
			while ($tok !== false) {
				$arg[] = $tok;
				$tok = strtok("_");
			}
			
			if(count($arg) == 5){
				$date = $arg[0];
				$time = $arg[1];
				$plan = $arg[2];
				$classname = $arg[3];
				$code = $arg[4];
			}else{
				die("estimate pdf not found!!");
			}
			
			
			//$logArrs = $this->Calclog->findAll(array("id"=>$code,"plan"=>strtolower($plan),"classname"=>$classname,"created"=>$date.$time));
			$logArrs = $this->Calclog->find('all',array("conditions"=>array("id"=>$code,"plan"=>strtolower($plan),"created"=>$date.$time)));
			$logArrs2 = $this->Calclog->findAllByMode($code);
			
			// logArrにlogArr2を統合
			$cnt=1;
			foreach($logArrs2 as $logArr2){
				$logArrs[$cnt] = $logArr2;
				$cnt++;
			}
		}else{
			// hikaku
			$logArrs[0] = $this->Calclog->findById($logid1);
			
			if($logid2 != 0){
				$logArrs[] = $this->Calclog->findById($logid2);
			}else{
				$logArrs[]['Calclog'] = $this->dummyArr;
			}
			if($logid3 != 0){
				$logArrs[] = $this->Calclog->findById($logid3);
			}else{
				//$this->dummyArr['plan'] = $logArrs[0]['plan'];
				$logArrs[]['Calclog'] = $this->dummyArr;
			}
			
		}
		debug($logid1);
		debug($logid2);
		debug($logid3);
		
		
		if($logArrs){
			$logArr = $logArrs[0];
			$bmst = $logArr['Calclog']['bmst'];
			
			// dummyArrを作成
			foreach($logArr as $key=>$value){
				$this->dummyArr[$key] = "";
			}
		}else{
			die("ご指定のお見積書が見つかりませんでした。見積書番号をお確かめください。");
		}
		

		// basic認証対策
		
		//$string = str_replace("http://","http://mbjtest:qwerty@",$string);
		$string = str_replace("http://","http://mbjtest:qwerty@",$string);
		
		$url = CAKEPHP_URL."/makeimg/car.php?".$string;
		
		
		$this->set("string",$url);
		
		//debug($url);exit();
		
		$carArr = $this->Car->findByBmst($logArr['Calclog']['bmst']);
		//$this->set('logArr', $logArr);
		$this->set('logArrs', $logArrs);
		$this->set('carArr',$carArr);
		
		
		// pdf発行ログ対応 2011.11.24 by morita
		$this->Calclog->id = $code;
		$this->Calclog->saveField('pdf', 1); 		
		
		
		// その他項目の演算をコントローラーに移動
		
		
		//車両本体価格（消費税込み）	pricetax	pricetax
		$pricetax =  $logArr['Calclog']['pricetax'];
		$this->set("pricetax",$pricetax);
		debug($pricetax);
		
		// オプション・アクセサリ
		$options = $logArr['Calclog']['makeroption'] + $logArr['Calclog']['dealeroption'];
		$this->set("options",$options);
		debug($options);

		if($logArr['Calclog']['salesman']){
			// 新仕様かつセールスマンモード
			// 付属品
			//$huzokuhin = $logArr['Calclog']['dealeraccessories'];
			//$this->set("huzokuhin",$huzokuhin);
		}
		
		// mmm mms acc 追加 2012.07.20 by morita
		//(サービスプログラム小計（消費税込み） ○○ 円)	serviceprograms=mmmaintenance+mmsupport
		$serviceprograms = $logArr['Calclog']['mmmprice'] + $logArr['Calclog']['mmsprice'];
		$this->set("serviceprograms",$serviceprograms);
		debug($serviceprograms);
		
		//その他費用
		$sonotahiyou = $logArr['Calclog']['sonota'] - $serviceprograms;
		$this->set("sonotahiyou",$sonotahiyou);
		debug($sonotahiyou);
		
		//(サービスプログラム小計（消費税込み） ○○ 円)	serviceprograms=mmmaintenance+mmsupport
		//$serviceprograms = $logArr['Calclog']['mmmaintenance'] + $logArr['Calclog']['mmsupport'];
		//$this->set("serviceprograms",$serviceprograms);
		//debug($serviceprograms);

		
		//(諸経費小計　　　　　　　　　　　　　　　○○○円)	taxtotal	taxtotal
		$taxtotal = $logArr['Calclog']['taxtotal'];
		$this->set("taxtotal",$taxtotal);
		debug($taxtotal);
		
		//(販売諸費用小計（消費税込み）　　　　　　 ○○○円)	salesexpenses	salesexpenses
		$salesexpenses = $logArr['Calclog']['salesexpenses'];
		$this->set("salesexpenses",$salesexpenses);
		debug($salesexpenses);
		
		//(値引き　　　　　　　　　　　　　　　　　 ○○○円)	discount	discount
		$discount = $logArr['Calclog']['discount'];
		$this->set("discount",$discount);
		debug($discount);
		
		//(メルセデス・ベンツ自動車保険プログラム　 ○○○円)	mbinsureance	mbinsureance
		$mbinsureance = $logArr['Calclog']['mbinsureance'];
		$this->set("mbinsureance",$mbinsureance);
		debug($mbinsureance);
		
		/*
		//その他項目
		$sonota = $logArr['Calclog']['sonota'];
		$this->set("sonota",$sonota);
		debug($sonota);
		*/
		
		// 合計金額（消費税込み）
		// $cartotal = $pricetax + $options + $serviceprograms + $taxtotal + $salesexpenses - $discount + $mbinsureance;
		$cartotal = $logArr['Calclog']['pricetax'] +  $logArr['Calclog']['makeroption'] + $logArr['Calclog']['taxtotal'] + $logArr['Calclog']['sonota'];
		$this->set("cartotal",$cartotal);
		debug($cartotal);
		
		// セールスマン情報取得
		$salesmanArr = $this->User->findById($logArr['Calclog']['salesman']);
		$salesman_name = $salesmanArr['User']['fullname'];
		$dealer_name = $salesmanArr['User']['dealername'];
		
		$this->set("salesman_name",$salesman_name);
		$this->set("dealer_name",$dealer_name);
		
		// 中古車か新車か
		$bmst=$logArr['Calclog']['bmst'];
		
		//if(strpos($bmst,'used') === false){
		switch($logArr['Calclog']['classname']){
			case "used":
				// 中古車
				$this->set("mode","used");
				break;
			case "smart":
				// smart
				$this->set("mode","smart");
				break;
			default:
				// 新車
				$this->set("mode","new");
		}
	}
	
	/**
	 * 見積書発行
	 * @param string $code: (e.g.: EA00000)
	 */
	/*
	function estimate3($code) {
		// デバックモードを0にする。
		Configure::write('debug', 0);
		
		// PDFレイアウトを使用。
		$this->layout = 'pdf';
		
		$logArrs = $this->Calclog->findAll(array("id"=>$code));
		$logArrs2 = $this->Calclog->findAll(array("mode"=>$code));
		
		// logArrにlogArr2を統合
		$cnt=1;
		foreach($logArrs2 as $logArr2){
			$logArrs[$cnt] = $logArr2;
			$cnt++;
		}
		
		
		if($logArrs){
			$logArr = $logArrs[0];
			$bmst = $logArr['Calclog']['bmst'];
		}else{
			die("ご指定のお見積書が見つかりませんでした。見積書番号をお確かめください。");
		}
		
		$string  = "w=480&h=360";
		$string .= "&imagepath01=".$logArr['Calclog']['imagepath01'];
		$string .= "&imagepath02=".$logArr['Calclog']['imagepath02'];
		$string .= "&imagepath03=".$logArr['Calclog']['imagepath03'];
		$string .= "&imagepath04=".$logArr['Calclog']['imagepath04'];
		$string .= "&imagepath05=".$logArr['Calclog']['imagepath05'];
		$string .= "&imagepath06=".$logArr['Calclog']['imagepath06'];
		$string .= "&imagepath07=".$logArr['Calclog']['imagepath07'];
		$string .= "&imagepath08=".$logArr['Calclog']['imagepath08'];
		$string .= "&imagepath09=".$logArr['Calclog']['imagepath09'];
		$string .= "&imagepath10=".$logArr['Calclog']['imagepath10'];
		
		
		$url = CAKEPHP_URL."/makeimg/car.php?".$string;
		
		
		$this->set("string",$url);
		
		//debug($url);exit();
		
		$carArr = $this->Car->findByBmst($logArr['Calclog']['bmst']);
		//$this->set('logArr', $logArr);
		$this->set('logArrs', $logArrs);
		$this->set('carArr',$carArr);
		
		// pdf発行ログ対応 2011.11.24 by morita
		$this->Calclog->id = $code;
		$this->Calclog->saveField('pdf', 1); 		
	}
	*/
	
	/*
		個別提案書
	*/
	function leaflet($string){
		// デバックモードを0にする。
		Configure::write('debug',0);
		
		// PDFレイアウトを使用。
		$this->layout = 'pdf';
		
		
		/* タブと改行をトークンの区切りとして使用します */
		$arg = array();
		
		$tok = strtok($string, "_");
		
		while ($tok !== false) {
			$arg[] = $tok;
			$tok = strtok("_");
		}
		
		if(count($arg) == 6){
			$date = $arg[1];
			$time = $arg[2];
			$plan = $arg[3];
			$classname = $arg[4];
			$code = $arg[5];
		}else{
			die("leaflet pdf not found!!");
		}
		
		
		$logArr = $this->Calclog->findById($code);
		
		debug($logArr);
		
		$this->set("logArr",$logArr);
		$this->set("comment",$this->params['url']['comment']);
		//2016.08.26 add
		$this->set("user_name",$this->params['url']['user_name']);
		
		// セールスマン情報取得
		$salesmanArr = $this->User->findById($logArr['Calclog']['salesman']);
		$salesman_name = $salesmanArr['User']['fullname'];
		$dealer_name = $salesmanArr['User']['dealername'];
		
		$this->set("salesman_name",$salesman_name);
		$this->set("dealer_name",$dealer_name);
		
		switch($logArr['Calclog']['classname']){
			case "used":
				// 中古車
				$this->set("mode","used");
				break;
			case "smart":
				// smart
				$this->set("mode","smart");
				break;
			default:
				// 新車
				$this->set("mode","new");
		}
		
	}
	/*
		ディスプレーシート
	*/
	function display($string){
		// デバックモードを0にする。
		Configure::write('debug',0);
		
		// PDFレイアウトを使用。
		$this->layout = 'pdf';
		
		
		/* タブと改行をトークンの区切りとして使用します */
		$arg = array();
		
		$tok = strtok($string, "_");
		
		while ($tok !== false) {
			$arg[] = $tok;
			$tok = strtok("_");
		}
		
		if(count($arg) == 6){
			$date = $arg[1];
			$time = $arg[2];
			$plan = $arg[3];
			$classname = $arg[4];
			$code = $arg[5];
		}else{
			die("display pdf not found!!");
		}
		
		
		
		$logArr = $this->Calclog->findById($code);
		
		debug($logArr);
		
		$this->set("logArr",$logArr);
		
		// 中古車か新車か
		$bmst=$logArr['Calclog']['bmst'];
		
		//if(strpos($bmst,'used') === false){
		switch($logArr['Calclog']['classname']){
			case "used":
				// 中古車
				$this->set("mode","used");
				break;
			case "smart":
				// smart
				$this->set("mode","smart");
				break;
			default:
				// 新車
				$this->set("mode","new");
		}
	}
	
	function estimate($string,$logid1=0,$logid2=0,$logid3=0) {
		// デバックモードを0にする。
		Configure::write('debug',0);
		
		// PDFレイアウトを使用。
		$this->layout = 'pdf';
		
		// パラメーターを解析する
		// hikaku: 2012.12.06 by morita
		
		if($logid1 == 0){
			/* タブと改行をトークンの区切りとして使用します */
			$arg = array();
			
			$tok = strtok($string, "_");
			
			while ($tok !== false) {
				$arg[] = $tok;
				$tok = strtok("_");
			}
			
			if(count($arg) == 5){
				$date = $arg[0];
				$time = $arg[1];
				$plan = $arg[2];
				$classname = $arg[3];
				$code = $arg[4];
			}else{
				die("estimate pdf not found!!");
			}
			
			
			//$logArrs = $this->Calclog->findAll(array("id"=>$code,"plan"=>strtolower($plan),"classname"=>$classname,"created"=>$date.$time));
			$logArrs = $this->Calclog->find('all',array("conditions"=>array("id"=>$code,"plan"=>strtolower($plan),"created"=>$date.$time)));
			$logArrs2 = $this->Calclog->findAllByMode($code);
			
			// logArrにlogArr2を統合
			$cnt=1;
			foreach($logArrs2 as $logArr2){
				$logArrs[$cnt] = $logArr2;
				$cnt++;
			}
		}else{
			// hikaku
			$logArrs[0] = $this->Calclog->findById($logid1);
			
			if($logid2 != 0){
				$logArrs[] = $this->Calclog->findById($logid2);
			}
			if($logid3 != 0){
				$logArrs[] = $this->Calclog->findById($logid3);
			}else{
				//$this->dummyArr['plan'] = $logArrs[0]['plan'];
				$logArrs[]['Calclog'] = $this->dummyArr;
			}
			
		}
		debug($logid1);
		debug($logid2);
		debug($logid3);
		
		
		if($logArrs){
			$logArr = $logArrs[0];
			$bmst = $logArr['Calclog']['bmst'];
		}else{
			die("ご指定のお見積書が見つかりませんでした。見積書番号をお確かめください。");
		}
		

		
		//debug($url);exit();
		
		$carArr = $this->Car->findByBmst($logArr['Calclog']['bmst']);
		$this->set('logArr', $logArr);
		$this->set('logArrs', $logArrs);
		$this->set('carArr',$carArr);
		
		
		
		// pdf発行ログ対応 2011.11.24 by morita
		$this->Calclog->id = $code;
		$this->Calclog->saveField('pdf', 1); 		
		
		
		// その他項目の演算をコントローラーに移動

		
		// セールスマン情報取得
		$salesmanArr = $this->User->findById($logArr['Calclog']['salesman']);
		$salesman_name = $salesmanArr['User']['fullname'];
		$dealer_name = $salesmanArr['User']['dealername'];
		
		$this->set("salesman_name",$salesman_name);
		$this->set("dealer_name",$dealer_name);
		
		
		//2016.08.26 add
		$this->set("user_name",$this->params['url']['user_name']);
		
		
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// テンプレートファイル名を取得
		/////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// 中古車か新車か
		$bmst=$logArr['Calclog']['bmst'];
		
		// プラス計算か
		($logArr['Calclog']['ploantotal'] > 0) ? $plusemode = 1 : $plusemode = 0;
		
		// 比較計算か？
		(count($logArrs) > 1) ? $hikakumode = 1: $hikakumode = 0;
		
		// サービスローン？
		($logArr['Calclog']['plan'] == "svc") ? $servicemode = 1: $servicemode = 0;
		
		//if(strpos($bmst,'used') === false){
		// ８つのモードを特定する
		switch($logArr['Calclog']['classname']){
			case "used":
				// 中古車
				$mode = "USED";
				break;
			case "smart":
				// smart
				if($plusemode){
					//$mode = "SMPLS";
					$mode = "PLS";
				}else{
					if($hikakumode){
						//$mode = "SMCMP";
						$mode = "CMP";
					}else{
						//$mode = "SM";
						$mode = "NORMAL";
					}
				}
				break;
			default:
				if($servicemode){
					$mode = "SVC";
				}else{
					// 新車
					if($plusemode){
						//$mode = "MBPLS";
						$mode = "PLS";
					}else{
						if($hikakumode){
							//$mode = "MBCMP";
							$mode = "CMP";
						}else{
							//$mode = "MB";
							$mode = "NORMAL";
						}
					}
				}
		}
		
		// 値引きがあるか？
		($logArr['Calclog']['discount']>0) ? $di = 1: $di = 0;
		
		// 残債きがあるか？
		($logArr['Calclog']['zansai']>0) ? $de = 1: $de = 0;
		
		// 残価きがあるか？
		($logArrs[0]['Calclog']['lastpayment']>0||$logArrs[1]['Calclog']['lastpayment']>0||$logArrs[2]['Calclog']['lastpayment']>0) ? $lp = 1: $lp = 0;
		
		$this->set("mode",$mode);
		
		
		// USEDには、NORMALのテンプレを強引に使用
		if($mode == "USED") $mode = "NORMAL";
		$this->set("basefilename",$mode."_di".$di."_de".$de."_lp".$lp.".png");
	}
}
