<?php
if($mode == "new" || $mode == "smart"){
	$planArr = array(
				"wp"=>"WELCOME PLAN",
				"swp"=>"SUPER WELCOME PLAN",
				"std"=>"STANDARD LOAN",					
				"sup"=>"STARTUP PLAN",
				"-"=>"-"
			 );
}else{
	$planArr = array(
				"wp"=>"Used Car WELCOME PLAN",
				"swp"=>"SUPER WELCOME PLAN",
				"std"=>"STANDARD LOAN",					
				"sup"=>"STARTUP PLAN",
				"-"=>"-"
			 );
}
$m_planArr = array("smart"=>array(
									"mmm"=>"smartメンテナンス",
									"mms"=>"smartメンテナンスプラス",
									"ev"=>"保証プラス"
								),
					"mbj"=>array(
									"mmm"=>"メンテナンスプラス",
									"mms"=>"保証プラス",
									"ev"=>""
								)
							);

App::import('Vendor', 'fpdf');
$fpdf->AddSJISFont('MS-Mincho','SJIS');
$fpdf->AddSJISFont('MS-Gothic','SJIS2');
//$fpdf->AddSJISFont();
$fpdf->setPageNoOption('suppress', true);
$fpdf->Open();
$fpdf->AddPage();


//  配列を全部展開
//extract($logArr['Calclog']);
	

$fpdf->SetFont('SJIS', '', 12);
// 自動改ページ(利用,下マージン)
$fpdf->SetAutoPageBreak(true, 10.0);


//if($mode == "smart"){
/*
if(1){
	// smartロゴ
	$fpdf->Image(CAKEPHP_URL."/img/sm_logo.png",90,5,30);
}else{
	// ベースの画像
	$fpdf->Image("http://mbfj.co-site.jp/loan/img/logo.gif",90,3,70,"gif");
}
*/


if($mode == "smart"){
	// smartロゴ
	$fpdf->Image(CAKEPHP_URL."/img/sm_logo.png",180,5,16);
}else{
	// Mercedes-Benzロゴ
	$fpdf->Image(CAKEPHP_URL."/img/logo.gif",90,3,70,"gif");
}




//ここから描画開始！！
$fpdf->SetFont('SJIS2','',20);
$fpdf->SetXY(10,20);
$fpdf->Cell(145,11,"モデル:","0",2,"L");
//$fpdf->SetFont('Arial','',20);
if($mode == "smarta"){
	$fpdf->SetFont('SJIS2','',18);
	$fpdf->Cell(0,12,mb_convert_kana($logArr['Calclog']['carname'],"k"),"1",2,"L");
}else{
	$fpdf->SetFont('SJIS2','',20);
	//$fpdf->Cell(0,12,$logArr['Calclog']['carname'],"1",2,"L");
	
	$width = 0;
	$height = 12;
	$fontsize = 30;
	
	
	
	
	// モデル名は、文字列サイズを考慮
	$modelname = str_replace("　"," ",$logArr['Calclog']['carname']);
	if(strlen($modelname) == mb_strlen($modelname)){
		if(strlen($modelname) > 20){
			$fontsize = 20;
		}
		debug("NO Japanese");
		// 日本語を含まない
		$fpdf->SetFont('Arial','',$fontsize);
		$fpdf->Cell($width,$height,$modelname,1,1,"L");
	}else{
		if(mb_strlen($modelname) > 20){
			$fontsize = 30*0.6;
			$en_haba = 7*0.6;		// 半角1文字の幅
			$jp_haba = 5*0.6;		// 半角1文字の幅
		}else{
			$fontsize = 30;
			$en_haba = 7;		// 半角1文字の幅
			$jp_haba = 5;		// 半角1文字の幅
		}
		debug("Japanese");
		// 日本語を含む
		$fpdf->SetFont('SJIS2','',$fontsize);
		// 半角に変換
		$fpdf->Cell($width,$height,"",1,1,"L");
		/*
			１. 文字列をスペースに区切る
			２．区切られた文字列が全角の場合は日本語フォントでText描画半角の場合は欧文フォントでText描画
		*/
		$dakutenArr = array("ガ","ギ","グ","ゲ","ゴ","ザ","ジ","ズ","ゼ","ゾ","ダ","ジ","ヅ","デ","ド","バ","ビ","ブ","ベ","ボ","パ","ピ","プ","ペ","ポ");

		$tok = strtok($modelname," ");
		
		$current_x = 15;	// 現在の文字幅
		$current_y = 40;
		
		debug($tok);
		while ($tok !== false) {
			debug($tok);
			if(strlen($tok) == mb_strlen($tok)){
				// 半角
				//$fpdf->SetFont('Courier','',10);
				$fpdf->SetFont('Arial','',$fontsize);
				$fpdf->Text($current_x,$current_y,$tok);
				
				// 現在地の更新
				$current_x += $en_haba*strlen($tok)+$en_haba; 
			}else{
				// 全角
				$fpdf->SetFont('SJIS2','',$fontsize);
				
				// 濁点がいくつ入っているか数える
				$num = 0;
				for($i=0;$i<mb_strlen($tok);$i++){
					if(in_array(mb_substr($tok,$i,1),$dakutenArr)) $num++;
				}

				$fpdf->Text($current_x,$current_y,mb_convert_kana($tok,"k"));
				
				// 現在地の更新
				$current_x += $jp_haba*(mb_strlen($tok)+$num)+$jp_haba; 
			}
			
			$tok = strtok(" ");
		}
	}
	
	
	
	
}
$fpdf->SetFont('Arial','B',28);
$fpdf->Cell(0,18,$planArr[$logArr['Calclog']['plan']],"0",2,"L");
$fpdf->SetFont('SJIS2','',26);
$fpdf->Cell(0,18,"月々のお支払い例","0",2,"L");
$fpdf->SetFont('Arial','',140);
$fpdf->Cell(0,55,number_format($logArr['Calclog']['monthlypayment']),"TB",2,"C");

$fpdf->SetXY(10,150);
$fpdf->SetFont('Arial','B',24);
$fpdf->Cell(0,16,$planArr[$logArr['Calclog']['plan']],"0",2,"L");
$fpdf->SetFont('SJIS2','',24);
$fpdf->Cell(70,20,"月々のお支払い","0",0,"L");
$fpdf->SetFont('Arial','',60);
$fpdf->Cell(100,20,number_format($logArr['Calclog']['monthlypayment']),"0",0,"C");



$tableArr1 = array();

$tableArr1[] = array("title"=>"車両本体価格(消費税込み)","price"=>$logArr['Calclog']['pricetax']);
if($logArr['Calclog']['makeroption']>0){
	$tableArr1[] = array("title"=>"メーカーオプション(消費税込み)","price"=>$logArr['Calclog']['makeroption']);
}
if($logArr['Calclog']['dealeroption']>0){
	$tableArr1[] = array("title"=>"販売店オプション(消費税込み)","price"=>$logArr['Calclog']['dealeroption']);
}
if($logArr['Calclog']['sonota']>0){
	$tableArr1[] = array("title"=>"税金／販売諸費用","price"=>$logArr['Calclog']['sonota']);
}
if($mode == "smart"){
	if($logArr['Calclog']['evprice']>0){
		$tableArr1[] = array("title"=>$m_planArr["smart"]["mmm"],"price"=>$logArr['Calclog']['mmmprice']);
		$tableArr1[] = array("title"=>$m_planArr["smart"]["mms"],"price"=>$logArr['Calclog']['mmsprice']);
		$tableArr1[] = array("title"=>$m_planArr["smart"]["ev"],"price"=>$logArr['Calclog']['evprice']);
	}else{
		$tableArr1[] = array("title"=>$blank,"price"=>0);
		$tableArr1[] = array("title"=>$m_planArr["smart"]["mmm"],"price"=>$logArr['Calclog']['mmmprice']);
		$tableArr1[] = array("title"=>$m_planArr["smart"]["mms"],"price"=>$logArr['Calclog']['mmsprice']);
	}
}else{
	$tableArr1[] = array("title"=>$m_planArr["mbj"]["mmm"],"price"=>$logArr['Calclog']['mmmprice']);
	$tableArr1[] = array("title"=>$m_planArr["mbj"]["mms"],"price"=>$logArr['Calclog']['mmsprice']);
}
if($logArr['Calclog']['mbinsureance']>0){
	$tableArr1[] = array("title"=>"自動車任意保険料","price"=>$logArr['Calclog']['mbinsureance']);
}
$tableArr1[] = array("title"=>"支払金額合計","price"=>$logArr['Calclog']['loanprincipal']+$logArr['Calclog']['downpayment']);
$tableArr1[] = array("title"=>"頭金","price"=>$logArr['Calclog']['downpayment']);
$tableArr1[] = array("title"=>"ローン元金","price"=>$logArr['Calclog']['loanprincipal']);
$tableArr1[] = array("title"=>"実質年率","price"=>$logArr['Calclog']['rate'],"unit"=>"％");
if($logArr['Calclog']['plan'] == "wp" || $logArr['Calclog']['plan'] == "swp"){
	$tableArr1[] = array("title"=>"お支払い回数","price"=>$logArr['Calclog']['installments']+1,"unit"=>"回");
	$tableArr1[] = array("title"=>" (".$logArr['Calclog']['installments']."回+最終回お支払い金額)","price"=>"","unit"=>"");
}else{
	$tableArr1[] = array("title"=>"お支払い回数","price"=>$logArr['Calclog']['installments'],"unit"=>"回");
}
/* $tableArr1[] = array("title"=>"＊保険料、税金（消費税除く）、登録に伴う諸費用は含まれておりません。","price"=>"","unit"=>""); */

$tableArr2[] = array("title"=>"初回お支払い金額","price"=>$logArr['Calclog']['firstpayment']);
if($logArr['Calclog']['plan'] == "wp" || $logArr['Calclog']['plan'] == "swp"){
	$tableArr2[] = array("title"=>"最終回お支払い金額","price"=>$logArr['Calclog']['lastpayment']);
}
if($logArr['Calclog']['bonuspayment']>0){
	$tableArr2[] = array("title"=>"ボーナス月加算金額","price"=>$logArr['Calclog']['bonuspayment']);
}else{
	$tableArr2[] = array("title"=>"ボーナス月加算金額","price"=>"-----","unit"=>"");
}
$tableArr2[] = array("title"=>"分割払い手数料","price"=>$logArr['Calclog']['interest']);
$tableArr2[] = array("title"=>"分割払いお支払い総額","price"=>$logArr['Calclog']['loantotal']);
$tableArr2[] = array("title"=>"お支払い総額","price"=>$logArr['Calclog']['loantotal']+$logArr['Calclog']['downpayment']);

$height = 4.5;
$width1 = 60;
$width2 = 20;
$width3 = 8;

$fontsize = 10;

$fpdf->SetY(185);
foreach($tableArr1 as $arr){
	
	$fpdf->SetX(15);
	
	if($arr["title"] == ""){
		// 行送り
		$fpdf->Cell($width1,$height,"","0",1,"L");
	}else{
		$fpdf->SetFont('SJIS2','',$fontsize);
		$fpdf->Cell($width1,$height,$arr['title'],"0",0,"L");
		
		$fpdf->SetFont('Arial','',$fontsize);
		
		if(isset($arr['unit'])){
			// 単位指定がある場合は、カンマつけない
			$fpdf->Cell($width2,$height,$arr['price'],"0",0,"R");
			$fpdf->SetFont('SJIS2','',$fontsize);
			$fpdf->Cell($width5,$height,$arr['unit'],"0",1,"L");
		}else{
			// 単位指定がない場合は円にする
			$fpdf->Cell($width2,$height,number_format($arr['price']),"0",0,"R");
			$fpdf->SetFont('SJIS2','',$fontsize);
			$fpdf->Cell($width3,$height,"円","0",1,"L");
		}
		
	}
}

$height = 5;
$width1 = 60;
$width2 = 20;
$width3 = 8;

$fontsize = 11;

$fpdf->SetY(190);
foreach($tableArr2 as $arr){
	
	$fpdf->SetX(110);
	
	if($arr["title"] == ""){
		// 行送り
		$fpdf->Cell($width1,$height,"","0",1,"L");
	}else{
		$fpdf->SetFont('SJIS2','',$fontsize);
		$fpdf->Cell($width1,$height,$arr['title'],"0",0,"L");
		
		$fpdf->SetFont('Arial','',$fontsize);
		
		if(isset($arr['unit'])){
			// 単位指定がある場合は、カンマつけない
			$fpdf->Cell($width2,$height,$arr['price'],"0",0,"R");
			$fpdf->SetFont('SJIS2','',$fontsize);
			$fpdf->Cell($width5,$height,$arr['unit'],"0",1,"L");
		}else{
			// 単位指定がない場合は円にする
			$fpdf->Cell($width2,$height,number_format($arr['price']),"0",0,"R");
			$fpdf->SetFont('SJIS2','',$fontsize);
			$fpdf->Cell($width3,$height,"円","0",1,"L");
		}
		
	}
	
}

switch("mode"){
	case "smart":
$comment = array(
"○このお支払い例はご参考例です。",
"○メルセデス・ベンツ・ファイナンス（株）のご利用が必要です。",
"○価格及びファイナンスプランの金利は予告なく変更することがあります。",
"○ファイナンスプラン、対象期間、適用条件など、詳しくはセールススタッフまでお問い合わせください。"
);
		break;
	case "new":
$comment = array(
"○このお支払い例はご参考例です。",
"○メルセデス・ベンツ・ファイナンス（株）のご利用が必要です。",
"○価格及びファイナンスプランの金利は予告なく変更することがあります。",
"○ファイナンスプラン、対象期間、適用条件など、詳しくはセールススタッフまでお問い合わせください。"
);
		break;
	case "used":
		break;
}
$comment = array(
"○このお支払い例はご参考例です。",
"○メルセデス・ベンツ・ファイナンス（株）のご利用が必要です。",
"○価格及びファイナンスプランの金利は予告なく変更することがあります。",
"○ファイナンスプラン、対象期間、適用条件など、詳しくはセールススタッフまでお問い合わせください。"
);

$fpdf->SetFont('SJIS2','',10);
$fpdf->SetY(252);
foreach($comment as $str){
	$fpdf->Cell(0,7,$str,"0",1,"L");
}

$fpdf->Rect(8,250,192,37);


$fpdf->SetFont('SJIS2','',10);
$fpdf->Text(138,47,"(車両本体価格　　　".number_format($logArr['Calclog']['pricetax'])."円)");

$fpdf->SetFont('SJIS2','',14);
$fpdf->Text(120,75,"実質年率　　　　　".$logArr['Calclog']['rate']."　％");

$fpdf->SetFont('SJIS2','',24);
$fpdf->Text(195,125,"円");
$fpdf->Text(175,182,"円");


/*
$fpdf->SetFont('SJIS','B',12);
$fpdf->SetXY(4,120);
$fpdf->Cell(200,10,"例えば ".$logArr['Calclog']['carname']."が","0",2,"L");

$fpdf->SetFont('SJIS','B',18);
$fpdf->Cell(15,12,"月々           円から","0",2,"L");

$fpdf->SetXY(15,125);
$fpdf->SetFont('Times','B',30);
$fpdf->Cell(40,20,number_format($logArr['Calclog']['monthlypayment']),"0",2,"C");

// 計算条件
$fpdf->SetFont('SJIS','',6);
$fpdf->SetXY(96,128);
$fpdf->Cell(120,3,$rate."% ".$planArr[$plan]." ".$installments."回でお支払いの場合","0",2,"L");
$fpdf->Cell(120,3,"■車両本体価格：".number_format($pricetax)."円(消費税込み) ■メンテナンス プラス：".number_format($mmmprice)."円","0",2,"L");
$fpdf->Cell(120,3,"■頭金：".number_format($downpayment)."円 ■ローン元金：".number_format($loanprincipal)."円 ■お支払い回数：61回(60回＋最終回お支払い金額)","0",2,"L");
$fpdf->Cell(120,3,"■初回お支払い額：".number_format($firstpayment)."円 ■2回目以降お支払い額 (59回)：".number_format($monthlypayment)."円 ■ボーナス月加算金額 (10回)：".number_format($bonuspayment)."円","0",2,"L");
$fpdf->Cell(120,3,"■最終回お支払い額(残価)：".number_format($lastpayment)."円 ■分割払い手数料：".number_format($interest)."円","0",2,"L");
$fpdf->Cell(120,3,"■ローンお支払い総額：".number_format($loantotal)."円 ■お支払い総額(消費税込み)：".number_format($total)."円","0",2,"L");

// 文字色を黒に
$fpdf->SetTextColor(0,0,0);
$fpdf->SetFont('SJIS','B',20);
$fpdf->SetXY(10,160);
$fpdf->Cell(200,30,"５年間の安心をパッケージ。「まるごとプラン」","0",2,"L");
*/

			
echo $fpdf->fpdfOutput();

?>