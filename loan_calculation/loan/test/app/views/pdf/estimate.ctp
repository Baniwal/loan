<?php
App::import('Vendor', 'fpdf');
$fpdf->AddSJISFont('MS-Mincho','SJIS');
$fpdf->AddSJISFont('MS-Gothic','SJIS2');
//$fpdf->AddMBFont(GOTHIC,'SJIS2');
$fpdf->setPageNoOption('suppress', true);
$fpdf->SetAutoPageBreak(true ,3);
$fpdf->Open();
$fpdf->AddPage();

// 上マージン
$topmargin = 15;


//////////////////////////////////////////////////////////////////////////////
// 背景画像・ロゴ画像セット
//////////////////////////////////////////////////////////////////////////////
// ベース画像
$fpdf->Image(CAKEPHP_URL."/img/pdf/".$basefilename,10,15+$topmargin,190);

$fpdf->SetFont('SJIS2','',10);

// USED時は、NORMALにかぶせてテンプレを作る
// if($logArr['Calclog']['classname'] == "smart" || $mode == "USED"){
if($mode == "USED"){	
	$fpdf->Image(CAKEPHP_URL."/img/pdf/used_smart.png",10,13+$topmargin,190);
}


if($logArr['Calclog']['classname'] == "smart"){
	// smartロゴ
	$fpdf->Image(CAKEPHP_URL."/img/sm_logo.png",177,7,16);
	
	$fpdf->SetXY(135,22); // 2014.07.01 smart新CIを右寄せにしたため日付表示位置を下げる
	$fpdf->Cell(65,8+$topmargin,date("Y年m月d日",strtotime($logArr['Calclog']['created'])),"0",2,"R");		


	// ディーラー名・担当者名レイアウト
	$fpdf->SetFont('SJIS2','',12);
	$fpdf->SetXY(80,25+$topmargin);
	$fpdf->Cell(120,8,$dealer_name,"",2,"R");
	$fpdf->SetFont('SJIS2','',10);
	$fpdf->SetX(145);
	$fpdf->Cell(55,6,"担当者名：　　".$salesman_name,"",2,"C");
}else{
	// Mercedes-Benzロゴ
	$fpdf->Image(CAKEPHP_URL."/img/logo.gif",80,7,60);
	
	$fpdf->SetXY(135,5+$topmargin);		
	$fpdf->Cell(65,8,date("Y年m月d日",strtotime($logArr['Calclog']['created'])),"0",2,"R");		


	// ディーラー名・担当者名レイアウト
	$fpdf->SetFont('SJIS2','',12);
	$fpdf->SetXY(80,25+$topmargin);
	$fpdf->Cell(120,8,$dealer_name,"",2,"R");
	$fpdf->SetFont('SJIS2','',10);
	$fpdf->SetX(145);
	$fpdf->Cell(55,6,"担当者名：　　".$salesman_name,"",2,"C");

}

// 2016.08.31 お客様氏名の入力がない場合は"様"と下線を表示しない
$fpdf->SetFont('SJIS2','',14);
$fpdf->SetLineWidth(0.5);
if(!empty($user_name)){
// 2016.09.01 下線の長さをお客様氏名に合わせて伸ばす
$str_len1 = strlen($user_name);
$fpdf->Text(10,14+$topmargin,$user_name." 様");
$fpdf->Line(10,15.2+$topmargin,$str_len1*1.7+22,15.2+$topmargin);
}else{
// $fpdf->Text(48,14+$topmargin,"様");
}

// 最下段のMBFロゴ
$fpdf->Image(CAKEPHP_URL."/img/mbf.gif",10,284,40);

// ブランク時の文字定義（※Cell2の0円時ブランク文字はfpdf.phpにて定義する必要がある）
$blank = "---------";


//  表の高さ・ブロック間マージンを設定（全ブロック同じ行高さが前提）
$height = 6.27;
$margin1 = 2.0;
$margin2 = 7.3;
$margin3 = 2.2;
$margin4 = 2.2;

//////////////////////////////////////////////////////////////////////////////
// プラン名文字列セット
//////////////////////////////////////////////////////////////////////////////
switch($mode){
	case "CMP":
			$planArr = array(
						"wp"=>"ｳｪﾙｶﾑﾌﾟﾗﾝ",
						"swp"=>"ｽｰﾊﾟｰｳｪﾙｶﾑﾌﾟﾗﾝ",
						"noswp"=>"スーパーウェルカムプラン",		// swp非対応時
						"std"=>"ｽﾀﾝﾀﾞｰﾄﾞﾛｰﾝ",					
						"sup"=>"スタートアッププラン",
						"als"=>"オートリース",
						"cls"=>"オートリース",
						"svc"=>"サービスローン",
						"-"=>"-"
					 );
			break;
	case "USED":
			$planArr = array(
						"wp"=>"ユーズドカーウェルカムプラン（残価設定型ローン）",
						"swp"=>"スーパーウェルカムプラン（残価保証型ローン）",
						"noswp"=>"スーパーウェルカムプラン（残価保証型ローン）",		// swp非対応時
						"std"=>"ユーズドカースタンダードローン",					
						"sup"=>"スタートアッププラン",
						"als"=>"オートリース",
						"cls"=>"オートリース",
						"-"=>"-"
					 );
			break;
	case "PLS":
			$planArr = array(
						"wp"=>"ウェルカムプランプラス（残価設定型ローン）",
						"swp"=>"スーパーウェルカムプランプラス（残価保証型ローン）",
						"noswp"=>"スーパーウェルカムプランプラス（残価保証型ローン）",		// swp非対応時
						"std"=>"スタンダードローン",					
						"sup"=>"スタートアッププラン",
						"als"=>"オートリース",
						"cls"=>"オートリース",
						"-"=>"-"
					 );
			break;
	default:
			$planArr = array(
						"wp"=>"ウェルカムプラン（残価設定型ローン）",
						"swp"=>"スーパーウェルカムプラン（残価保証型ローン）",
						"noswp"=>"スーパーウェルカムプラン（残価保証型ローン）",		// swp非対応時
						"std"=>"スタンダードローン",					
						"sup"=>"スタートアッププラン",
						"als"=>"オートリース",
						"cls"=>"オートリース",
						"svc"=>"サービスローン",
						"-"=>"-"
					 );
			break;
}



switch($mode){
case "NORMAL":
case "USED":
case "CMP":
	$model_width = 89;
	
	$height1 = $height;
	//$starty1 = 63;
	//$startx1 = 109;
	$starty1 = 50.5+$topmargin;
	$startx1 = 104;
	$width1 = 57;
	
	$height2 = $height1;
	//$starty2 = 134.8;
	//$startx2 = 109;
	$width2 = 57;
	
	
	// パラメーターをすべて名前に割り当てる
	
	
	//////////////////////////////////////////////////////////////////////////////
	// １ブロック
	//////////////////////////////////////////////////////////////////////////////
	
	$fpdf->setXY($startx1,$starty1);
	$fpdf->SetFont('Arial','',10);
	
	// クラス名：中古車の場合はクラス名を表示しない
	if($mode == "USED"){
	
		$fpdf->Cell($model_width,$height1,"",0,2,"C");
	}else{
		$fpdf->Cell($model_width,$height1,$logArr['Calclog']['classname'],0,2,"C");
	}
	
	
	
	
	// モデル名は、文字列サイズを考慮
	if($mode == "USED"){
		// USED時は、手入力したモデル名を表示
		$modelname = str_replace("　"," ",$logArr['Calclog']['carname']);
	}else{
		// それ以外では、クイックチャート用のモデル名を表示
		$modelname = str_replace("　"," ",$carArr['Car']['qc_carname']);
	}
	if(strlen($modelname)>25){
		$en_haba = 2.3*0.9;		// 半角1文字の幅
		$jp_haba = 2.1*0.9;		// 半角1文字の幅
		$fsize = 10*0.9;
	}else{
		$en_haba = 2.3;		// 半角1文字の幅
		$jp_haba = 2.1;		// 半角1文字の幅
		$fsize = 10;
	}
	if(strlen($modelname) == mb_strlen($modelname)){
		debug("NO Japanese");
		// 日本語を含まない
		if(strlen($modelname)>20){
			$fpdf->SetFont('Arial','',8);
		}else{
			$fpdf->SetFont('Arial','',10);
		}
		$fpdf->Cell($model_width,$height1,$modelname,0,2,"C");
	}else{
		debug("Japanese");
		// 日本語を含む
		$fpdf->SetFont('SJIS2','',10);
		// 半角に変換
		
		// Text()を使うため、Cellで行送り
		$fpdf->Cell($model_width,$height1,"",0,2,"C");
		/*
			１. 文字列をスペースに区切る
			２．区切られた文字列が全角の場合は日本語フォントでText描画半角の場合は欧文フォントでText描画
		*/
		$dakutenArr = array("ガ","ギ","グ","ゲ","ゴ","ザ","ジ","ズ","ゼ","ゾ","ダ","ジ","ヅ","デ","ド","バ","ビ","ブ","ベ","ボ","パ","ピ","プ","ペ","ポ");
	
		$tok = strtok($modelname," ");
		
		$current_x = $startx1-15;	// 現在の文字位置
		$current_y = $starty1+$height1+4;
		
		debug($tok);
		while ($tok !== false) {
			debug($tok);
			if(strlen($tok) == mb_strlen($tok)){
				// 半角
				//$fpdf->SetFont('Courier','',10);
				$fpdf->SetFont('Arial','',$fsize);
				$fpdf->Text($current_x,$current_y,$tok);
				
				// 現在地の更新
				$current_x += $en_haba*strlen($tok)+$en_haba; 
			}else{
				// 2バイト文字
				$fpdf->SetFont('SJIS2','',$fsize);
				
				// 濁点がいくつ入っているか数える
				$num = 0;
				for($i=0;$i<mb_strlen($tok);$i++){
					//if(in_array(mb_substr($tok,$i,1),$dakutenArr)) $num++;
				}
	
				$fpdf->Text($current_x,$current_y,mb_convert_kana($tok,"k"));
				//$fpdf->Text($current_x,$current_y,$tok);
				
				// 現在地の更新
				$current_x += $jp_haba*(mb_strlen($tok)+$num)+$en_haba; 
			}
			
			$tok = strtok(" ");
		}
	}
	
	// 2バイト文字
	$fpdf->SetFont('SJIS2','',10);
	
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['pricetax'],0,2,"R");
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['makeroption'],0,2,"R");
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['dealeroption'],0,2,"R");
	if($logArr['Calclog']['discount']>0){
		$fpdf->Cell2($width1,$height1,$logArr['Calclog']['discount'],0,2,"R");
	}
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['sonota'],0,2,"R");
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mbinsureance'],0,2,"R");
	
	if($mode == "USED"){
		// サービスプログラム用の3行はブランク
		$fpdf->setX(26);
		$fpdf->Cell($width1,$height1,"",0,0,"L");	
		$fpdf->setX($startx1);
		$fpdf->Cell($width1,$height1,"",0,2,"R");
		$fpdf->setX(26);
		$fpdf->Cell($width1,$height1,"",0,0,"L");
		$fpdf->setX($startx1);
		$fpdf->Cell($width1,$height1,"",0,2,"R");
		$fpdf->setX(26);
		$fpdf->Cell($width1,$height1,"",0,0,"L");
		$fpdf->setX($startx1);
		$fpdf->Cell($width1,$height1,"",0,2,"R");
			}else{
		if($logArr['Calclog']['classname'] == "smart"){
			/************************************************************************
			  mmm/mms/evの表記：
			  	smartの場合かつevprice>0の場合のみ、EVプランのみの表記とする
				smartかつevpriceが未入力もしくはゼロの場合は、smartのプランを表記
				
				2016.07 EV車両対応廃止、smartメンテナンス、smartメンテナンスプラス、保証プラス（旧EV専用プラン欄）
				smartの場合はsmartメンテナンス、smartメンテナンスプラス、保証プラスの表記
				
				smart以外は、mmm/mmsの表記とする
			*************************************************************************/
				// タイトルも書く
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"smartメンテナンス",0,0,"L");			
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mmmprice'],0,2,"R");
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"smartメンテナンスプラス",0,0,"L");
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mmsprice'],0,2,"R");
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"保証プラス",0,0,"L");			
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,$logArr['Calclog']['evprice'],0,2,"R");
		}else{
			// MB
				// タイトルも書く
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"メンテナンスプラス",0,0,"L");
				
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mmmprice'],0,2,"R");
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"保証プラス",0,0,"L");
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mmsprice'],0,2,"R");
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"",0,0,"L");
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,"",0,2,"R");
		}
	}
	
	$fpdf->setX($startx1);
	
	// 現金販売価格合計を計算
	$total1 = $logArr['Calclog']['pricetax']+$logArr['Calclog']['makeroption']+$logArr['Calclog']['dealeroption']-$logArr['Calclog']['discount']+$logArr['Calclog']['sonota']+$logArr['Calclog']['mbinsureance']+$logArr['Calclog']['mmmprice']+$logArr['Calclog']['mmsprice']+$logArr['Calclog']['evprice'];
	
	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->Cell2($width1,$height1,$total1,0,2,"R");
	
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	
	//////////////////////////////////////////////////////////////////////////////
	// ２ブロック
	//////////////////////////////////////////////////////////////////////////////
	//$fpdf->setXY($startx2,$starty2);
	
	$fpdf->Cell(0,$margin1,"",0,2);
	
	if($mode == "CMP"){
		$width2 = 38;
		$startx2 = 82;
		
		// 3行に分ける
		$fpdf->SetX($startx2);
		$fpdf->Cell2($width2,$height2,$logArrs[0]['Calclog']['genkin'],0,0,"R");
		$fpdf->Cell2($width2,$height2,$logArrs[1]['Calclog']['genkin'],0,0,"R");
		$fpdf->Cell2($width2,$height2,$logArrs[2]['Calclog']['genkin'],0,2,"R");
		
		$fpdf->SetX($startx2);
		$fpdf->Cell2($width2,$height2,$logArrs[0]['Calclog']['shitadori'],0,0,"R");
		$fpdf->Cell2($width2,$height2,$logArrs[1]['Calclog']['shitadori'],0,0,"R");
		$fpdf->Cell2($width2,$height2,$logArrs[2]['Calclog']['shitadori'],0,2,"R");
		
		if($logArr['Calclog']['zansai']){
			$fpdf->SetX($startx2);
			$fpdf->Cell2($width2,$height2,$logArrs[0]['Calclog']['zansai'],0,0,"R");
			$fpdf->Cell2($width2,$height2,$logArrs[1]['Calclog']['zansai'],0,0,"R");
			$fpdf->Cell2($width2,$height2,$logArrs[2]['Calclog']['zansai'],0,2,"R");
		}
		
		if($logArrs[0]['Calclog']['zansai']>$logArrs[0]['Calclog']['genkin']+$logArrs[0]['Calclog']['shitadori']){
			// 残債差額がない場合
			$total20 = $logArrs[0]['Calclog']['zansai']-($logArrs[0]['Calclog']['genkin']+$logArrs[0]['Calclog']['shitadori']);
		}else{
			// 残債差額ある場合（必ずプラス計算とは限らない！ zansai=0の場合は、単なる頭金になる）
			$total20 = ($logArrs[0]['Calclog']['genkin']+$logArrs[0]['Calclog']['shitadori'])-$logArrs[0]['Calclog']['zansai'];
		}
		if($logArrs[1]['Calclog']['zansai']>$logArrs[1]['Calclog']['genkin']+$logArrs[1]['Calclog']['shitadori']){
			// 残債差額がない場合
			$total21 = $logArrs[1]['Calclog']['zansai']-($logArrs[1]['Calclog']['genkin']+$logArrs[1]['Calclog']['shitadori']);
		}else{
			// 残債差額ある場合（必ずプラス計算とは限らない！ zansai=0の場合は、単なる頭金になる）
			$total21 = ($logArrs[1]['Calclog']['genkin']+$logArrs[1]['Calclog']['shitadori'])-$logArrs[1]['Calclog']['zansai'];
		}
		if($logArrs[2]['Calclog']['zansai']>$logArrs[2]['Calclog']['genkin']+$logArrs[2]['Calclog']['shitadori']){
			// 残債差額がない場合
			$total22 = $logArrs[2]['Calclog']['zansai']-($logArrs[2]['Calclog']['genkin']+$logArrs[2]['Calclog']['shitadori']);
		}else{
			// 残債差額ある場合（必ずプラス計算とは限らない！ zansai=0の場合は、単なる頭金になる）
			$total22 = ($logArrs[2]['Calclog']['genkin']+$logArrs[2]['Calclog']['shitadori'])-$logArrs[2]['Calclog']['zansai'];
		}
		
		// 太字
		$fpdf->SetFont('SJIS2','B',10);
		$fpdf->SetX($startx2);
		$fpdf->Cell2($width2,$height2,$total20,0,0,"R");
		$fpdf->Cell2($width2,$height2,$total21,0,0,"R");
		$fpdf->Cell2($width2,$height2,$total22,0,2,"R");
			
		// 通常に戻す
		$fpdf->SetFont('SJIS2','',10);
	}else{
		$fpdf->Cell2($width2,$height2,$logArr['Calclog']['genkin'],0,2,"R");
		$fpdf->Cell2($width2,$height2,$logArr['Calclog']['shitadori'],0,2,"R");
		
		// 残債がある場合は残債を表示。なければスキップ
		if($logArr['Calclog']['zansai']>0){
			$fpdf->Cell2($width2,$height2,$logArr['Calclog']['zansai'],0,2,"R");
		}
		
		
		if($logArr['Calclog']['zansai']>$logArr['Calclog']['genkin']+$logArr['Calclog']['shitadori']){
			// 残債差額がない場合
			$total2 = $logArr['Calclog']['zansai']-($logArr['Calclog']['genkin']+$logArr['Calclog']['shitadori']);
		}else{
			// 残債差額ある場合（必ずプラス計算とは限らない！ zansai=0の場合は、単なる頭金になる）
			$total2 = ($logArr['Calclog']['genkin']+$logArr['Calclog']['shitadori'])-$logArr['Calclog']['zansai'];
		}
		
		// 太字
		$fpdf->SetFont('SJIS2','B',10);
		$fpdf->Cell2($width2,$height2,$total2,0,2,"R");
			
		// 通常に戻す
		$fpdf->SetFont('SJIS2','',10);
	}
break;
case "PLS":
	$model_width = 89;
	
	$height1 = $height;
	//$starty1 = 63;
	//$startx1 = 109;
	$starty1 = 50+$topmargin;
	$startx1 = 104;
	$width1 = 57;
	
	$height2 = $height1;
	//$starty2 = 134.8;
	//$startx2 = 109;
	$width2 = 57;
	
	
	// パラメーターをすべて名前に割り当てる
	
	
	//////////////////////////////////////////////////////////////////////////////
	// １ブロック
	//////////////////////////////////////////////////////////////////////////////
	
	$fpdf->setXY($startx1,$starty1);
	$fpdf->SetFont('Arial','',10);
	
	// クラス
	if($logArr['Calclog']['classname'] == "smart" || $mode == "USED"){
	
		$fpdf->Cell($model_width,$height1,"",0,2,"C");
	}else{
		$fpdf->Cell($model_width,$height1,$logArr['Calclog']['classname'],0,2,"C");
	}
	
	
	
	
	// モデル名は、文字列サイズを考慮
	if($mode == "USED"){
		// USED時は、手入力したモデル名を表示
		$modelname = str_replace("　"," ",$logArr['Calclog']['carname']);
	}else{
		// それ以外では、クイックチャート用のモデル名を表示
		$modelname = str_replace("　"," ",$carArr['Car']['qc_carname']);
	}
	if(strlen($modelname)>25){
		$en_haba = 2.3*0.9;		// 半角1文字の幅
		$jp_haba = 2.1*0.9;		// 半角1文字の幅
		$fsize = 10*0.9;
	}else{
		$en_haba = 2.3;		// 半角1文字の幅
		$jp_haba = 2.1;		// 半角1文字の幅
		$fsize = 10;
	}
	if(strlen($modelname) == mb_strlen($modelname)){
		debug("NO Japanese");
		// 日本語を含まない
		if(strlen($modelname)>20){
			$fpdf->SetFont('Arial','',8);
		}else{
			$fpdf->SetFont('Arial','',10);
		}
		$fpdf->Cell($model_width,$height1,$modelname,0,2,"C");
	}else{
		debug("Japanese");
		// 日本語を含む
		$fpdf->SetFont('SJIS2','',10);
		// 半角に変換
		
		// Text()を使うため、Cellで行送り
		$fpdf->Cell($model_width,$height1,"",0,2,"C");
		/*
			１. 文字列をスペースに区切る
			２．区切られた文字列が全角の場合は日本語フォントでText描画半角の場合は欧文フォントでText描画
		*/
		$dakutenArr = array("ガ","ギ","グ","ゲ","ゴ","ザ","ジ","ズ","ゼ","ゾ","ダ","ジ","ヅ","デ","ド","バ","ビ","ブ","ベ","ボ","パ","ピ","プ","ペ","ポ");
	
		$tok = strtok($modelname," ");
		
		$current_x = $startx1-15;	// 現在の文字位置
		$current_y = $starty1+$height1+4;
		
		debug($tok);
		while ($tok !== false) {
			debug($tok);
			if(strlen($tok) == mb_strlen($tok)){
				// 半角
				//$fpdf->SetFont('Courier','',10);
				$fpdf->SetFont('Arial','',$fsize);
				$fpdf->Text($current_x,$current_y,$tok);
				
				// 現在地の更新
				$current_x += $en_haba*strlen($tok)+$en_haba; 
			}else{
				// 2バイト文字
				$fpdf->SetFont('SJIS2','',$fsize);
				
				// 濁点がいくつ入っているか数える
				$num = 0;
				for($i=0;$i<mb_strlen($tok);$i++){
					//if(in_array(mb_substr($tok,$i,1),$dakutenArr)) $num++;
				}
	
				$fpdf->Text($current_x,$current_y,mb_convert_kana($tok,"k"));
				//$fpdf->Text($current_x,$current_y,$tok);
				
				// 現在地の更新
				$current_x += $jp_haba*(mb_strlen($tok)+$num)+$en_haba; 
			}
			
			$tok = strtok(" ");
		}
	}
	
	// 2バイト文字
	$fpdf->SetFont('SJIS2','',10);
	
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['pricetax'],0,2,"R");
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['makeroption'],0,2,"R");
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['dealeroption'],0,2,"R");
	if($logArr['Calclog']['discount']>0){
		$fpdf->Cell2($width1,$height1,$logArr['Calclog']['discount'],0,2,"R");
	}
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['sonota'],0,2,"R");
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mbinsureance'],0,2,"R");
	
	if($mode == "USED"){
		// mmm/mmsはブランク
		$fpdf->setX(26);
		$fpdf->Cell($width1,$height1,"",0,0,"L");
		
		$fpdf->setX($startx1);
		$fpdf->Cell($width1,$height1,"",0,2,"R");
		$fpdf->setX(26);
		$fpdf->Cell($width1,$height1,"",0,0,"L");
		$fpdf->setX($startx1);
		$fpdf->Cell($width1,$height1,"",0,2,"R");
	}else{
		if($logArr['Calclog']['classname'] == "smart"){
			/************************************************************************
			  mmm/mms/evの表記：
			  	smartの場合かつevprice>0の場合のみ、EVプランのみの表記とする
				smartかつevpriceが未入力もしくはゼロの場合は、smartのプランを表記
				
				smart以外は、mmm/mmsの表記とする
			*************************************************************************/
			if($logArr['Calclog']['evprice']>0){
				// EVの場合
				// タイトルも書く
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"保証プラス",0,0,"L");
				
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,$logArr['Calclog']['evprice'],0,2,"R");
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"",0,0,"L");
				$fpdf->setX($startx1);
				$fpdf->Cell($width1,$height1,"",0,2,"R");
			}else{
				// タイトルも書く
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"smartメンテナンス",0,0,"L");
				
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mmmprice'],0,2,"R");
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"smartメンテナンスプラス",0,0,"L");
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mmsprice'],0,2,"R");
			}
		}else{
			// MB
				// タイトルも書く
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"メンテナンスプラス",0,0,"L");				
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mmmprice'],0,2,"R");
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"保証プラス",0,0,"L");
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mmsprice'],0,2,"R");
				$fpdf->setX(26);
				$fpdf->Cell($width1,$height1,"",0,0,"L");
				$fpdf->setX($startx1);
				$fpdf->Cell2($width1,$height1,"",0,2,"R");
		}
	}
	
	$fpdf->setX($startx1);
	
	// 現金販売価格合計を計算
	$total1 = $logArr['Calclog']['pricetax']+$logArr['Calclog']['makeroption']+$logArr['Calclog']['dealeroption']-$logArr['Calclog']['discount']+$logArr['Calclog']['sonota']+$logArr['Calclog']['mbinsureance']+$logArr['Calclog']['mmmprice']+$logArr['Calclog']['mmsprice']+$logArr['Calclog']['evprice'];
	
	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->Cell2($width1,$height1,$total1,0,2,"R");
	
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	
	//////////////////////////////////////////////////////////////////////////////
	// ２ブロック
	//////////////////////////////////////////////////////////////////////////////
	//$fpdf->setXY($startx2,$starty2);
	
	$fpdf->Cell(0,$margin1,"",0,2);
	
	$fpdf->Cell2($width2,$height2,$logArr['Calclog']['genkin'],0,2,"R");
	$fpdf->Cell2($width2,$height2,$logArr['Calclog']['shitadori'],0,2,"R");
	
	// 残債がある場合は残債を表示。なければスキップ
	if($logArr['Calclog']['zansai']>0){
		$fpdf->Cell2($width2,$height2,$logArr['Calclog']['zansai'],0,2,"R");
	}
	
	
	if($logArr['Calclog']['zansai']>$logArr['Calclog']['genkin']+$logArr['Calclog']['shitadori']){
		// 残債差額がない場合
		$total2 = $logArr['Calclog']['zansai']-($logArr['Calclog']['genkin']+$logArr['Calclog']['shitadori']);
	}else{
		// 残債差額ある場合（必ずプラス計算）
		$total2 = $logArr['Calclog']['zansai']-($logArr['Calclog']['genkin']+$logArr['Calclog']['shitadori']);
	}
	
	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->Cell2($width2,$height2,$total2,0,2,"R");
		
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
break;
case "SVC":
	$model_width = 89;
	
	$height1 = $height;
	//$starty1 = 63;
	//$startx1 = 109;
	$starty1 = 52+$topmargin;
	$startx1 = 109;
	$width1 = 57;
	
	$height2 = $height1;
	//$starty2 = 134.8;
	//$startx2 = 109;
	$width2 = 57;
	
	
	// パラメーターをすべて名前に割り当てる
	
	
	
	
	
	//////////////////////////////////////////////////////////////////////////////
	// １ブロック
	//////////////////////////////////////////////////////////////////////////////
	
	$fpdf->setXY($startx1,$starty1);
	$fpdf->SetFont('Arial','',10);
	
	// 2バイト文字
	$fpdf->SetFont('SJIS2','',10);
	
	// 車検費用
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['svicost'],0,2,"R");
	
	// 修理代
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['srepair'],0,2,"R");
	
	// 整備・点検費用
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['smaintenance'],0,2,"R");
	
	// オプション代金
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['soption'],0,2,"R");

	// その他
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['sonota'],0,2,"R");
	
	// メンテナンスプラス
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mmmprice'],0,2,"R");

	// 保証プラス
	$fpdf->Cell2($width1,$height1,$logArr['Calclog']['mmsprice'],0,2,"R");

	// お値引
	if($logArr['Calclog']['discount']>0){
		$fpdf->Cell2($width1,$height1,$logArr['Calclog']['discount'],0,2,"R");
	}
	
	// タイトルも書く
	
	$fpdf->setX($startx1);
	
	// 現金販売価格合計を計算
	$total1 = $logArr['Calclog']['svicost']+$logArr['Calclog']['srepair']+$logArr['Calclog']['smaintenance']+$logArr['Calclog']['soption']+$logArr['Calclog']['sonota']-$logArr['Calclog']['discount']+$logArr['Calclog']['mmmprice']+$logArr['Calclog']['mmsprice']+$logArr['Calclog']['evprice'];
	
	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->Cell2($width1,$height1,$total1,0,2,"R");
	
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	
	//////////////////////////////////////////////////////////////////////////////
	// ２ブロック
	//////////////////////////////////////////////////////////////////////////////
	//$fpdf->setXY($startx2,$starty2);
	
	$fpdf->Cell(0,$margin1,"",0,2);
	
	/*
	
	$fpdf->Cell2($width2,$height2,$logArr['Calclog']['genkin'],0,2,"R");
	$fpdf->Cell2($width2,$height2,$logArr['Calclog']['shitadori'],0,2,"R");
	
	// 残債がある場合は残債を表示。なければスキップ
	if($logArr['Calclog']['zansai']>0){
		$fpdf->Cell2($width2,$height2,$logArr['Calclog']['zansai'],0,2,"R");
	}
	
	
	if($logArr['Calclog']['zansai']>$logArr['Calclog']['genkin']+$logArr['Calclog']['shitadori']){
		// 残債差額がない場合
		$total2 = $logArr['Calclog']['zansai']-($logArr['Calclog']['genkin']+$logArr['Calclog']['shitadori']);
	}else{
		// 残債差額ある場合（必ずプラス計算）
		$total2 = $logArr['Calclog']['zansai']-($logArr['Calclog']['genkin']+$logArr['Calclog']['shitadori']);
	}
	*/
	
	$total2 = $logArr['Calclog']['genkin'];
	
	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->Cell2($width2,$height2,$total2,0,2,"R");
		
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
break;
}


switch($mode){
case "NORMAL":
case "USED":
case "SVC":
/******************************************************************************************************************/
// 通常レイアウト
/******************************************************************************************************************/
	$height3 = $height1;
	//$starty3 = 167;
	//$startx3 = 109;
	$width3 = 62;
	
	$height4 = $height1;
	//$starty4 = 167;
	//$startx4 = 109;
	$width4 = 57;
	
	$height5 = $height1;
	//$starty5 = 167;
	//$startx5 = 109;
	$width5 = 57;
	//////////////////////////////////////////////////////////////////////////////
	// ３ブロック
	//////////////////////////////////////////////////////////////////////////////
	$fpdf->Cell(0,$margin2,"",0,2);
	
	$fpdf->Cell($model_width,$height3,$planArr[$logArr['Calclog']['plan']],0,2,"C");
	
	$fpdf->Cell($width3,$height3,$logArr['Calclog']['installments']+($logArr['Calclog']['lastpayment']>0?1:0)." 回",0,2,"R");
	$fpdf->Cell($width3,$height3,$logArr['Calclog']['rate']." ％",0,2,"R");
	
	//////////////////////////////////////////////////////////////////////////////
	// ４ブロック
	//////////////////////////////////////////////////////////////////////////////
	$fpdf->Cell(0,$margin3,"",0,2);
	
	$total3 = $total1 - $total2;
	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->Cell2($width4,$height4,$total3,0,2,"R");
	
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	if($logArr['Calclog']['lastpayment']>0){
		$fpdf->Cell2($width4,$height4,$logArr['Calclog']['loanprincipal']-$logArr['Calclog']['lastpayment'],0,2,"R");
		$fpdf->Cell2($width4,$height4,$total3 - $logArr['Calclog']['loanprincipal'] + $logArr['Calclog']['lastpayment'],0,2,"R");
	}
	
	$total4 = $logArr['Calclog']['interest'];
	$fpdf->Cell2($width4,$height4,$total4,0,2,"R");
	
	$total5 = $total3 + $total4;
	$fpdf->Cell2($width4,$height4,$total5,0,2,"R");
	
	$total6 = $total2 + $total5;
	$fpdf->Cell2($width4,$height4,$total6,0,2,"R");
	
	
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	
	//////////////////////////////////////////////////////////////////////////////
	// ５ブロック
	//////////////////////////////////////////////////////////////////////////////
	$fpdf->Cell(0,$margin4,"",0,2);
	
	$fpdf->Cell2($width5,$height5,$logArr['Calclog']['firstpayment'],0,0,"R");
	$fpdf->Cell(20,$height5,"× 1",0,2,"L");
	$fpdf->SetX($startx1);
	
	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->Cell2($width5,$height5,$logArr['Calclog']['monthlypayment'],0,0,"R");
	if($logArr['Calclog']['lastpayment']>0){ //2015.02.09 2回目以降お支払い回数の誤り修正
		$fpdf->Cell(20,$height5,"× ".($logArr['Calclog']['installments']-($logArr['Calclog']['lastpayment']>0?1:0)),0,2,"L");
	}else{
		$fpdf->Cell(20,$height5,"× ".($logArr['Calclog']['installments']-1-($logArr['Calclog']['lastpayment']>0?1:0)),0,2,"L");
	}
	$fpdf->SetX($startx1);

	if($logArr['Calclog']['lastpayment']>0){
		$fpdf->Cell2($width5,$height5,$logArr['Calclog']['lastpayment'],0,0,"R");
		$fpdf->Cell(20,$height5,"× 1",0,2,"L");
		$fpdf->SetX($startx1);
	}
	
	if($logArr['Calclog']['bonuspayment']>0){
		$fpdf->Cell2($width5,$height5,$logArr['Calclog']['bonuspayment'],0,0,"R");
		$fpdf->Cell(20,$height5,"× ".$logArr['Calclog']['bonustimes'],0,1,"L");
		$fpdf->SetX($startx1);
	}else{
		$fpdf->Cell2($width5,$height5,$logArr['Calclog']['bonuspayment'],0,2,"R");
	}
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	if($logArr['Calclog']['bonuspayment']>0){
		$fpdf->Cell($width5,$height5,$logArr['Calclog']['bonusmonth1']."月/".$logArr['Calclog']['bonusmonth2']."月",0,2,"R");
	}else{
		$fpdf->Cell($width5,$height5,$blank,0,2,"R");
	}
break;
case "CMP":
/******************************************************************************************************************/
// 比較レイアウト
/******************************************************************************************************************/
	$cmp_startx = 82;
	
	$height3 = $height1;
	$width3 = 38;
	
	$height4 = $height1;
	$width4 = 38;
	
	$height5 = $height1;
	$width5 = 38;

	//////////////////////////////////////////////////////////////////////////////
	// ３ブロック
	//////////////////////////////////////////////////////////////////////////////
	$fpdf->Cell(0,$margin2,"",0,2);
	
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell($width3,$height3,$planArr[$logArrs[0]['Calclog']['plan']],0,0,"C");
	$fpdf->Cell($width3,$height3,$planArr[$logArrs[1]['Calclog']['plan']],0,0,"C");
	$fpdf->Cell($width3,$height3,$planArr[$logArrs[2]['Calclog']['plan']],0,2,"C");
	
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell($width3,$height3,$logArrs[0]['Calclog']['installments']+($logArrs[0]['Calclog']['lastpayment']>0?1:0)." 回",0,0,"R");
	if($logArrs[1]['Calclog']['installments']){
		$fpdf->Cell($width3,$height3,$logArrs[1]['Calclog']['installments']+($logArrs[1]['Calclog']['lastpayment']>0?1:0)." 回",0,0,"R");
	}else{
		$fpdf->Cell($width3,$height3,$blank,0,0,"R");
	}
	if($logArrs[2]['Calclog']['installments']){
		$fpdf->Cell($width3,$height3,$logArrs[2]['Calclog']['installments']+($logArrs[2]['Calclog']['lastpayment']>0?1:0)." 回",0,2,"R");
	}else{
		$fpdf->Cell($width3,$height3,$blank,0,2,"R");
	}

	$fpdf->SetX($cmp_startx);
	$fpdf->Cell($width3,$height3,$logArrs[0]['Calclog']['rate']." ％",0,0,"R");
	if($logArrs[1]['Calclog']['rate']){
		$fpdf->Cell($width3,$height3,$logArrs[1]['Calclog']['rate']." ％",0,0,"R");
	}else{
		$fpdf->Cell($width3,$height3,$blank,0,0,"R");
	}
	if($logArrs[2]['Calclog']['rate']){
		$fpdf->Cell($width3,$height3,$logArrs[2]['Calclog']['rate']." ％",0,2,"R");
	}else{
		$fpdf->Cell($width3,$height3,$blank,0,2,"R");
	}
	
	//////////////////////////////////////////////////////////////////////////////
	// ４ブロック
	//////////////////////////////////////////////////////////////////////////////
	$fpdf->Cell(0,$margin3,"",0,2);
	
	$total30 = $total1 - $total20;
	$total31 = $total1*($logArrs[1]['Calclog']['installments']>0 ? 1:0) - $total21;
	$total32 = $total1*($logArrs[2]['Calclog']['installments']>0 ? 1:0) - $total22;
	
	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width4,$height4,$total30,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total31,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total32,0,2,"R");
	
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	
	if($logArrs[0]['Calclog']['lastpayment']>0 || $logArrs[1]['Calclog']['lastpayment']>0 || $logArrs[2]['Calclog']['lastpayment']>0){
		$fpdf->SetX($cmp_startx);
		$fpdf->Cell2($width4,$height4,$logArrs[0]['Calclog']['loanprincipal']-$logArrs[0]['Calclog']['lastpayment'],0,0,"R");
		$fpdf->Cell2($width4,$height4,$logArrs[1]['Calclog']['loanprincipal']-$logArrs[1]['Calclog']['lastpayment'],0,0,"R");
		$fpdf->Cell2($width4,$height4,$logArrs[2]['Calclog']['loanprincipal']-$logArrs[2]['Calclog']['lastpayment'],0,2,"R");
	
	
		$fpdf->SetX($cmp_startx);
		$fpdf->Cell2($width4,$height4,$total30 - $logArrs[0]['Calclog']['loanprincipal'] + $logArrs[0]['Calclog']['lastpayment'],0,0,"R");
		$fpdf->Cell2($width4,$height4,$total31 - $logArrs[1]['Calclog']['loanprincipal'] + $logArrs[1]['Calclog']['lastpayment'],0,0,"R");
		$fpdf->Cell2($width4,$height4,$total32 - $logArrs[2]['Calclog']['loanprincipal'] + $logArrs[2]['Calclog']['lastpayment'],0,2,"R");
	}
	
	$total40 = $logArrs[0]['Calclog']['interest'];
	$total41 = $logArrs[1]['Calclog']['interest'];
	$total42 = $logArrs[2]['Calclog']['interest'];
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width4,$height4,$total40,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total41,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total42,0,2,"R");
	
	$total50 = $total30 + $total40;
	$total51 = $total31 + $total41;
	$total52 = $total32 + $total42;
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width4,$height4,$total50,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total51,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total52,0,2,"R");
	
	$total60 = $total20 + $total50;
	$total61 = $total21 + $total51;
	$total62 = $total22 + $total52;
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width4,$height4,$total60,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total61,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total62,0,2,"R");
	
	
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	
	//////////////////////////////////////////////////////////////////////////////
	// ５ブロック
	//////////////////////////////////////////////////////////////////////////////
	$fpdf->Cell(0,$margin4,"",0,2);
	
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width5,$height5,$logArrs[0]['Calclog']['firstpayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArrs[1]['Calclog']['firstpayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArrs[2]['Calclog']['firstpayment'],0,2,"R");

	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width5,$height5,$logArrs[0]['Calclog']['monthlypayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArrs[1]['Calclog']['monthlypayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArrs[2]['Calclog']['monthlypayment'],0,2,"R");

	if($logArrs[0]['Calclog']['lastpayment']>0 || $logArrs[1]['Calclog']['lastpayment']>0 || $logArrs[2]['Calclog']['lastpayment']>0){
		$fpdf->SetFont('SJIS2','B',10);
		$fpdf->SetX($cmp_startx);
		$fpdf->Cell2($width5,$height5,$logArrs[0]['Calclog']['lastpayment'],0,0,"R");
		$fpdf->Cell2($width5,$height5,$logArrs[1]['Calclog']['lastpayment'],0,0,"R");
		$fpdf->Cell2($width5,$height5,$logArrs[2]['Calclog']['lastpayment'],0,2,"R");
	}
	
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width5,$height5,$logArrs[0]['Calclog']['bonuspayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArrs[1]['Calclog']['bonuspayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArrs[2]['Calclog']['bonuspayment'],0,2,"R");

	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	$fpdf->SetX($cmp_startx);
	if($logArrs[0]['Calclog']['bonuspayment']>0){
		$fpdf->Cell($width5,$height5,$logArrs[0]['Calclog']['bonusmonth1']."月/".$logArrs[0]['Calclog']['bonusmonth2']."月",0,0,"R");
	}else{
		$fpdf->Cell($width5,$height5,$blank,0,0,"R");
	}
	if($logArrs[1]['Calclog']['bonuspayment']>0){
		$fpdf->Cell($width5,$height5,$logArrs[1]['Calclog']['bonusmonth1']."月/".$logArrs[1]['Calclog']['bonusmonth2']."月",0,0,"R");
	}else{
		$fpdf->Cell($width5,$height5,$blank,0,0,"R");
	}
	if($logArrs[2]['Calclog']['bonuspayment']>0){
		$fpdf->Cell($width5,$height5,$logArrs[2]['Calclog']['bonusmonth1']."月/".$logArrs[2]['Calclog']['bonusmonth2']."月",0,2,"R");
	}else{
		$fpdf->Cell($width5,$height5,$blank,0,2,"R");
	}
break;
case "PLS":
/******************************************************************************************************************/
// プラス計算レイアウト
/******************************************************************************************************************/
	$cmp_startx = 80;
	
	$height3 = $height1;
	$width3 = 38;
	
	$height4 = $height1;
	$width4 = 38;
	
	$height5 = $height1;
	$width5 = 38;

	//////////////////////////////////////////////////////////////////////////////
	// ３ブロック
	//////////////////////////////////////////////////////////////////////////////
	$fpdf->Cell(0,$margin2,"",0,2);
	
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell($width3*3,$height3,$planArr[$logArr['Calclog']['plan']],0,2,"C");
	
	// 行送り
	$fpdf->Cell($width3,$height3,"",0,2,"R");

	$fpdf->SetX($cmp_startx);
	$fpdf->Cell($width3,$height3,$logArr['Calclog']['installments']+($logArr['Calclog']['lastpayment']>0 ? 1:0)." 回",0,0,"R");
	$fpdf->Cell($width3,$height3,$logArr['Calclog']['installments']." 回",0,0,"R");
	$fpdf->Cell($width3,$height3,$logArr['Calclog']['installments']+($logArr['Calclog']['lastpayment']>0 ? 1:0)." 回",0,2,"R");

	$fpdf->SetX($cmp_startx);
	$fpdf->Cell($width3,$height3,$logArr['Calclog']['rate']." ％",0,0,"R");
	$fpdf->Cell($width3,$height3,$logArr['Calclog']['prate']." ％",0,0,"R");
	$fpdf->Cell($width3,$height3,"",0,2,"R");
	
	//////////////////////////////////////////////////////////////////////////////
	// ４ブロック
	//////////////////////////////////////////////////////////////////////////////
	$fpdf->Cell(0,$margin3,"",0,2);
	
	$total3new = $total1;
	$total3pls = $total2;
	$total3 = $total3new+$total3pls;
	
	
	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width4,$height4,$total3new,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total3pls,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total3,0,2,"R");
	
	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width4,$height4,$logArr['Calclog']['monthlypayment'],0,0,"R");
	$fpdf->Cell2($width4,$height4,$logArr['Calclog']['pmonthlypayment'],0,0,"R");
	$fpdf->Cell2($width4,$height4,$logArr['Calclog']['monthlypayment']+$logArr['Calclog']['pmonthlypayment'],0,2,"R");

	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width4,$height4,$logArr['Calclog']['lastpayment'],0,0,"R");
	$fpdf->Cell2($width4,$height4,0,0,0,"R");
	$fpdf->Cell2($width4,$height4,$logArr['Calclog']['lastpayment'],0,2,"R");

	$total4new = $logArr['Calclog']['interest'];
	$total4pls = $logArr['Calclog']['pinterest'];
	$total4 = $total4new+$total4pls;
	
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width4,$height4,$total4new,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total4pls,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total4,0,2,"R");

	$total5new = $total3new+$total4new;
	$total5pls = $total3pls+$total4pls;
	$total5 = $total5new+$total5pls;

	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width4,$height4,$total5new,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total5pls,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total5,0,2,"R");

	/*
	$total6new = $total1+$total5new;
	$total6pls = $total2+$total5pls;
	$total6 = $total6new+$total6pls;

	$total6new = $total5new+$logArr['Calclog']['genkin']+$logArr['Calclog']['shitadori'];
	$total6pls = $total5pls;
	$total6 = $total6new+$total6pls;

	*/

	$total6new = $total5new;
	$total6pls = $total5pls;
	$total6 = $total5;

	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width4,$height4,$total6new,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total6pls,0,0,"R");
	$fpdf->Cell2($width4,$height4,$total6,0,2,"R");

	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	
	//////////////////////////////////////////////////////////////////////////////
	// ５ブロック
	//////////////////////////////////////////////////////////////////////////////
	$fpdf->Cell(0,$margin4,"",0,2);
	
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width5,$height5,$logArr['Calclog']['firstpayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArr['Calclog']['pfirstpayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArr['Calclog']['firstpayment']+$logArr['Calclog']['pfirstpayment'],0,2,"R");

	// 太字
	$fpdf->SetFont('SJIS2','B',10);
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width5,$height5,$logArr['Calclog']['monthlypayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArr['Calclog']['pmonthlypayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArr['Calclog']['monthlypayment']+$logArr['Calclog']['pmonthlypayment'],0,2,"R");

	if($logArr['Calclog']['lastpayment']>0){
		$fpdf->SetFont('SJIS2','B',10);
		$fpdf->SetX($cmp_startx);
		$fpdf->Cell2($width5,$height5,$logArr['Calclog']['lastpayment'],0,0,"R");
		$fpdf->Cell2($width5,$height5,0,0,0,"R");
		$fpdf->Cell2($width5,$height5,$logArr['Calclog']['lastpayment'],0,2,"R");
	}
	
	$fpdf->SetX($cmp_startx);
	$fpdf->Cell2($width5,$height5,$logArr['Calclog']['bonuspayment'],0,0,"R");
	$fpdf->Cell2($width5,$height5,0,0,0,"R");
	$fpdf->Cell2($width5,$height5,$logArr['Calclog']['bonuspayment'],0,2,"R");

	// 通常に戻す
	$fpdf->SetFont('SJIS2','',10);
	$fpdf->SetX($cmp_startx);
	if($logArr['Calclog']['bonuspayment']>0){
		$fpdf->Cell($width5,$height5,$logArr['Calclog']['bonusmonth1']."月/".$logArr['Calclog']['bonusmonth2']."月",0,0,"R");
	}else{
		$fpdf->Cell($width5,$height5,$blank,0,0,"R");
	}
	
	$fpdf->Cell($width5,$height5,$blank,0,0,"R");
		
	if($logArr['Calclog']['bonuspayment']>0){
		$fpdf->SetX($cmp_startx);
		$fpdf->Cell($width5,$height5,$logArr['Calclog']['bonusmonth1']."月/".$logArr['Calclog']['bonusmonth2']."月",0,2,"R");
	}else{
		$fpdf->SetX($cmp_startx);
		$fpdf->Cell($width5,$height5,$blank,0,2,"R");
	}
break;
}

//////////////////////////////////////////////////////////////////////////////
// 備考
//////////////////////////////////////////////////////////////////////////////

$bikouArr = array();
// 注意書き
switch($mode){
	case "NORMAL":
	case "PLS":
	case "CMP":
	case "SVC":
		// WPP低金利対応 2014.06.19
		if($logArrs[0]['Calclog']['prate'] == "2.50"){
			$bikouArr[] = "○このお見積書の適用金利はMB/Smartブランド以外の他ブランド車からの代替えの場合に有効となります。";
			$bikouArr[] = "　MB/Smartブランドからのお乗換えの場合にはご利用になれません。";
		}
		if($logArrs[0]['Calclog']['tax'] == "0.05"){
			$bikouArr[] = "○頭金（下取車含む）の設定によりお支払い内容が異なります。";
			$bikouArr[] = "○下取車残債差額とは、下取車両に係るクレジット等の残債務額から下取車充当額および現金お支払額を除いた金額です。";
			$bikouArr[] = "○ボーナス加算額総額は、月々支払い部分ローン元金の65％以内で設定願います。ローン対象金額によっては65%のボーナス加算ができない場合があります。";
			$bikouArr[] = "○メルセデス・ベンツ・ファイナンス（株）のご利用が必要です。";
			$bikouArr[] = "○表示の価格は消費税（5%）込みの価格です。車両の登録が4月以降になる場合は新税率（8%）適用の価格となります。予めご了承ください。";
			$bikouArr[] = "○ファイナンスプラン、対象期間、適用条件など、詳しくはセールススタッフまでお問い合わせください。";
		}else{
			$bikouArr[] = "○頭金（下取車含む）の設定によりお支払い内容が異なります。";
			$bikouArr[] = "○下取車残債差額とは、下取車両に係るクレジット等の残債務額から下取車充当額および現金お支払額を除いた金額です。";
			$bikouArr[] = "○ボーナス加算額総額は、月々支払い部分ローン元金の65％以内で設定願います。ローン対象金額によっては65%のボーナス加算ができない場合があります。";
			$bikouArr[] = "○メルセデス・ベンツ・ファイナンス（株）のご利用が必要です。";
			$bikouArr[] = "○表示の価格は消費税（8%）込みの価格ですが、内容が変更となる場合がございます。予めご了承ください。";	
			$bikouArr[] = "○ファイナンスプラン、対象期間、適用条件など、詳しくはセールススタッフまでお問い合わせください。";
		}
		break;
	case "USED":	// 中古車
		if($logArrs[0]['Calclog']['tax'] == "0.05"){
			$bikouArr[] = "○頭金（下取車含む）の設定によりお支払い内容が異なります。";
			$bikouArr[] = "○下取車残債差額とは、下取車両に係るクレジット等の残債務額から下取車充当額および現金お支払額を除いた金額です。";
			$bikouArr[] = "○ボーナス加算額総額は、月々支払い部分ローン元金の65％以内で設定願います。ローン対象金額によっては65%のボーナス加算ができない場合があります。";
			$bikouArr[] = "○メルセデス・ベンツ・ファイナンス（株）のご利用が必要です。";
			$bikouArr[] = "○表示の価格は消費税（5%）込みの価格です。車両の登録が4月以降になる場合は新税率（8%）適用の価格となります。予めご了承ください。";
			$bikouArr[] = "○ファイナンスプラン、対象期間、適用条件など、詳しくはセールススタッフまでお問い合わせください。";
		}else{
			$bikouArr[] = "○頭金（下取車含む）の設定によりお支払い内容が異なります。";
			$bikouArr[] = "○下取車残債差額とは、下取車両に係るクレジット等の残債務額から下取車充当額および現金お支払額を除いた金額です。";
			$bikouArr[] = "○ボーナス加算額総額は、月々支払い部分ローン元金の65％以内で設定願います。ローン対象金額によっては65%のボーナス加算ができない場合があります。";
			$bikouArr[] = "○メルセデス・ベンツ・ファイナンス（株）のご利用が必要です。";
			$bikouArr[] = "○表示の価格は消費税（8%）込みの価格ですが、内容が変更となる場合がございます。予めご了承ください。";	
			$bikouArr[] = "○ファイナンスプラン、対象期間、適用条件など、詳しくはセールススタッフまでお問い合わせください。";
		}
		break;
}


$fpdf->SetY($fpdf->GetY()+2);
//$fpdf->Cell(0,10,"",0,2);

$fpdf->SetX(8.5);
$width = 150;
$height = 2.8;

// 備考描画
foreach($bikouArr as $key=>$bikou){
	//if($key==1){
	if(0){
		$fpdf->SetFont('SJIS2', 'B', 8);
	}else{
		$fpdf->SetFont('SJIS2', '', 6.5);
	}

	$fpdf->Cell($width,$height,$bikou,0,2);
}


echo $fpdf->fpdfOutput();

?>