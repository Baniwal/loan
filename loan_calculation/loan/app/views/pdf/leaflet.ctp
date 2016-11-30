<?php
$planArr = array(
			"wp"=>"ウェルカムプラン（残価設定型ローン）",
			"swp"=>"スーパーウェルカムプラン",
			"noswp"=>"スーパーウェルカムプラン",		// swp非対応時
			"std"=>"スタンダードローン",					
			"sup"=>"スタートアッププラン",
			"als"=>"オートリース",
			"cls"=>"オートリース",
			"-"=>"-"
		 );
		 
$copyArr = array(
		"marugoto"=>array("string"=>"「まるごとプラン」で安心のメルセデスライフを","size"=>22),
		"anshin"=>array("string"=>"","size"=>18),
		"anshin2"=>array("string"=>"メルセデスならではの安心のお支払いプラン","size"=>15),
		"marugoto2"=>array("string"=>"","size"=>15),
		"anshinplus"=>array("string"=>"「5年間の安心パッケージ」にさらなる安心をプラス","size"=>15)
);

// 背景画像のデータ配列
$data = array();

$data['A-Class'] = array('image'=>'A-Class.jpg','r1'=>0xff,'g1'=>0xff,'b1'=>0xff,'r2'=>0x00,'g2'=>0x00,'b2'=>0x00);
$data['B-Class'] = array('image'=>'B-Class.jpg','r1'=>0x00,'g1'=>0x00,'b1'=>0x00,'r2'=>0xff,'g2'=>0xff,'b2'=>0xff);
$data['CLA-Class'] = array('image'=>'CLA-Class.jpg','r1'=>0x00,'g1'=>0x00,'b1'=>0x00,'r2'=>0x00,'g2'=>0x00,'b2'=>0x00);
$data['CLA-Class Shooting Brake'] = array('image'=>'CLA-Class.jpg','r1'=>0x00,'g1'=>0x00,'b1'=>0x00,'r2'=>0x00,'g2'=>0x00,'b2'=>0x00);
$data['GLC'] = array('image'=>'GLC-Class.jpg','r1'=>0x00,'g1'=>0x00,'b1'=>0x00,'r2'=>0xff,'g2'=>0xff,'b2'=>0xff);
$data['C-Class Sedan'] = array('image'=>'C-Class.jpg','r1'=>0x00,'g1'=>0x00,'b1'=>0x00,'r2'=>0x00,'g2'=>0x00,'b2'=>0x00);
$data['C-Class Stationwagon'] = array('image'=>'C-Class_SW.jpg','r1'=>0xff,'g1'=>0xff,'b1'=>0xff,'r2'=>0x00,'g2'=>0x00,'b2'=>0x00);
$data['C-Class Coupe'] = array('image'=>'C-Class_CP.jpg','r1'=>0xff,'g1'=>0xff,'b1'=>0xff,'r2'=>0xff,'g2'=>0xff,'b2'=>0xff);
$data['E-Class Sedan'] = array('image'=>'E-Class.jpg','r1'=>0xff,'g1'=>0xff,'b1'=>0xff,'r2'=>0xff,'g2'=>0xff,'b2'=>0xff);
$data['E-Class Stationwagon'] = array('image'=>'E-Class_SW.jpg','r1'=>0xff,'g1'=>0xff,'b1'=>0xff,'r2'=>0xff,'g2'=>0xff,'b2'=>0xff);
$data['E-Class Coupe'] = array('image'=>'E-Class_CP.jpg','r1'=>0xff,'g1'=>0xff,'b1'=>0xff,'r2'=>0xff,'g2'=>0xff,'b2'=>0xff);
$data['E-Class Cabriolet'] = array('image'=>'E-Class_CA.jpg','r1'=>0xff,'g1'=>0xff,'b1'=>0xff,'r2'=>0xff,'g2'=>0xff,'b2'=>0xff);
$data['S-Class'] = array('image'=>'S-Class.jpg','r1'=>0xff,'g1'=>0xff,'b1'=>0xff,'r2'=>0xff,'g2'=>0xff,'b2'=>0xff);
// 2014.06.09 add by morita
$data['GLA-Class'] = array('image'=>'GLA-Class.jpg','r1'=>0x00,'g1'=>0x00,'b1'=>0x00,'r2'=>0xff,'g2'=>0xff,'b2'=>0xff);





App::import('Vendor', 'fpdf');
$fpdf->AddSJISFont('MS-Mincho','SJIS');
$fpdf->AddSJISFont('MS-Gothic','SJIS2');
//$fpdf->AddSJISFont();
$fpdf->setPageNoOption('suppress', true);
//$fpdf->SetMargins(1,1,1);
$fpdf->Open();
$fpdf->AddPage();


//  配列を全部展開
extract($logArr['Calclog']);
	

$fpdf->SetFont('SJIS', '', 12);
// 自動改ページ(利用,下マージン)
$fpdf->SetAutoPageBreak(true, 10.0);

// 下半分をグレーに
// 塗りつぶし色設定
$fpdf->SetFillColor(220,220,220);
$fpdf->rect(0,145,220,180,"F");

// フッターを黒帯に
// 塗りつぶし色設定
// $fpdf->SetFillColor(0,0,0);
// $fpdf->rect(0,254,220,50,"F");

// 左下MBFロゴ画像
// $fpdf->Image(CAKEPHP_URL."/img/mbflogo.jpg",6,277,80,"jpeg");
// 右下スリーポインテッドスター画像
// $fpdf->Image(CAKEPHP_URL."/img/tpslogo.jpg",165,262,24.5,"jpeg");

// 20160210 MBFロゴ・スリーポインテッドスター・背景色セットの画像
$fpdf->Image(CAKEPHP_URL."/img/footer.jpg",0,254,214,"jpeg");

// ベースの画像
$fpdf->Image(CAKEPHP_URL."/img/".$data[$logArr['Calclog']['classname']]['image'],0,0,220,"jpeg");

// プラン説明画像
$fpdf->Image(CAKEPHP_URL."/img/".$logArr['Calclog']['leafletimage'].".jpg",10,168,190,"jpeg");
//$fpdf->Image(CAKEPHP_URL."/img/logo.gif",120,270,80,"gif");


// 文字色1番目に
$fpdf->SetTextColor($data[$logArr['Calclog']['classname']]['r1'],$data[$logArr['Calclog']['classname']]['g1'],$data[$logArr['Calclog']['classname']]['b1'],255,255);


// 宛名
// 白抜き
//$fpdf->SetFillColor(160,160,160);

//$fpdf->Rect(10,10,30,10,'F');
//$fpdf->Image(CAKEPHP_URL."/img/name.gif",10,10,"gif"); // 2015.02.10 画像内に宛名欄を埋め込みしたためコードを削除
//$fpdf->Image(CAKEPHP_URL."/img/name.gif",17,10,"gif");

//$fpdf->SetFont('SJIS','',34);
//$fpdf->SetXY(105,12);
//$fpdf->Cell(145,12,"様へのご提案です。","0",2,"L");

//$fpdf->SetFont('Times','',16);
//$fpdf->Cell(90,6,"Payment Simulation","0",1,"R");

//2016.08.26 NexusWeb copy & change add
if(!empty($user_name)){
//フォントサイズを変更する場合は、直下のSetFont('SJIS','',30)の30の数値を変更してください
$fpdf->SetFont('SJIS','',30);
$fpdf->SetXY(10,5);
$fpdf->Cell(145,12,$user_name . " 様へのご提案です。","0",2,"L");
}

// 文字色2番目に
$fpdf->SetTextColor($data[$logArr['Calclog']['classname']]['r2'],$data[$logArr['Calclog']['classname']]['g2'],$data[$logArr['Calclog']['classname']]['b2'],255,255);



/*
$fpdf->SetFont('SJIS','B',12);
$fpdf->SetXY(4,120);
$fpdf->Cell(200,10,"例えば ".$logArr['Calclog']['carname']."が","0",2,"L");
*/


$fpdf->SetFont('SJIS','',12);
//$fpdf->Cell(0,12,$logArr['Calclog']['carname'],"1",2,"L");

$width = 0;
$height = 12;
$fontsize = 18;

$current_x = 5;	// 現在の文字幅
$current_y = 123;

$fpdf->SetFont('SJIS','',16);
$fpdf->Text($current_x,$current_y,"(例)");

$current_x += 12;

// モデル名は、文字列サイズを考慮
$modelname = str_replace("　"," ",$logArr['Calclog']['carname']);
if(strlen($modelname) == mb_strlen($modelname)){
	debug("NO Japanese");
	// 日本語を含まない
	$fpdf->SetFont('Times','',$fontsize);
	$fpdf->Text($current_x,$current_y,$modelname);
}else{
	debug("Japanese");
	// 日本語を含む
	$fpdf->SetFont('SJIS','',$fontsize);
	// 半角に変換
	//$fpdf->Cell($width,$height,"",1,1,"L");
	/*
		１. 文字列をスペースに区切る
		２．区切られた文字列が全角の場合は日本語フォントでText描画半角の場合は欧文フォントでText描画
	*/
	$dakutenArr = array("ガ","ギ","グ","ゲ","ゴ","ザ","ジ","ズ","ゼ","ゾ","ダ","ジ","ヅ","デ","ド","バ","ビ","ブ","ベ","ボ","パ","ピ","プ","ペ","ポ");

	$tok = strtok($modelname," ");
	
	
	$en_haba = 3;		// 半角1文字の幅
	$jp_haba = 5;		// 半角1文字の幅
	$fontsize = 13;
	
	debug($tok);
	while ($tok !== false) {
		debug($tok);
		if(strlen($tok) == mb_strlen($tok)){
			// 半角
			//$fpdf->SetFont('Courier','',10);
			$fpdf->SetFont('Times','',$fontsize);
			$fpdf->Text($current_x,$current_y,$tok);
			
			// 現在地の更新
			$current_x += $en_haba*strlen($tok)+$en_haba; 
		}else{
			// 全角
			$fpdf->SetFont('SJIS','',$fontsize);
			
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
	





$fpdf->SetXY(4,130);
$fpdf->SetFont('SJIS','B',22);
// 2014.12.05 からを削除 morita
// 2015.01.01 からを再追加
$fpdf->Cell(15,12,"月々         円から","0",2,"L");

$fpdf->SetXY(17,125);
$fpdf->SetFont('Times','',30);
$fpdf->Cell(40,20,number_format($logArr['Calclog']['monthlypayment']),"0",2,"C");

// 計算条件
$fpdf->SetFont('SJIS','',6.2);
$fpdf->SetXY(85,125);
$fpdf->Cell(120,3,$rate."% ".$planArr[$plan]." ".($installments+($lastpayment==0 ? 0 : 1))."回でお支払いの場合","0",2,"L");
$fpdf->Cell(120,3,"■車両本体価格(消費税込み)：".number_format($pricetax)."円","0",2,"L");
$fpdf->Cell(120,3,"■メンテナンスプラス：".number_format($mmmprice)."円 ■保証プラス".number_format($mmsprice)."円 ■支払金額合計".number_format($loanprincipal+$downpayment)."円","0",2,"L");
$fpdf->Cell(120,3,"■頭金：".number_format($downpayment)."円 ■ローン元金：".number_format($loanprincipal)."円 ■お支払い回数：".($installments+($lastpayment==0 ? 0 : 1))."回 (".($lastpayment==0 ? "" :($installments."回＋最終回お支払い金額)")),"0",2,"L");
$fpdf->Cell(120,3,"■初回お支払い額：".number_format($firstpayment)."円 ■2回目以降お支払い額 (".($installments-1)."回)：".number_format($monthlypayment)."円".($bonuspayment>0 ? " ■ボーナス月加算金額 (".$bonustimes."回)：".number_format($bonuspayment)."円" : ""),"0",2,"L");
$fpdf->Cell(120,3,($lastpayment==0 ? "" : "■最終回お支払い額(残価)：".number_format($lastpayment)."円 ")."■分割払い手数料：".number_format($interest)."円","0",2,"L");
$fpdf->Cell(120,3,"■分割払いお支払い総額：".number_format($loantotal)."円 ■お支払い総額：".number_format($loantotal+$downpayment)."円（消費税込み）","0",2,"L");

// キャッチコピー　文字色を黒に  
$fpdf->SetTextColor(0,0,0);
$fpdf->SetFont('SJIS','B',$copyArr[$logArr['Calclog']['leafletimage']]['size']);
$fpdf->SetXY(10,145);
$fpdf->Cell(200,30,$copyArr[$logArr['Calclog']['leafletimage']]['string'],"0",2,"L");


// コメントを記載
// 文字色を黒に
$fpdf->SetTextColor(0,0,0);
$fpdf->SetFont('SJIS','B',10);
$fpdf->SetXY(13,210);
$fpdf->MultiCell(280,6,$comment,0,"L");

// ディーラー名・担当者名レイアウト
$fpdf->SetFont('SJIS2','',12);
$fpdf->SetXY(80,232);
$fpdf->Cell(120,8,$dealer_name,"",2,"R");
$fpdf->SetFont('SJIS2','',10);
$fpdf->SetX(145);
$fpdf->Cell(55,6,"担当者名：　　".$salesman_name,"B",2,"C");


			
echo $fpdf->fpdfOutput();

?>