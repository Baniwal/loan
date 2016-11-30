/*  リリースノート


2014.02.02 ボーナス加算額上限を100円単位に変更

2014.02.06 addon2の計算で、0.00001をプラスしているのを消去



2014.02.10

ボーナス回数無効時に残価算出修正

金利選択していないときのjsエラー回避

金利選択していないときの_checkルーチンエラー回避

2014.04.14 元本充当額チェックをスルーしてしまう不具合修正
2014.04.24 ボーナス加算額MAXが負の場合に、元本充当額チェックをスルーする問題を修正→ボーナス加算MAXが負の場合は、直接NGにする
2014.05.07 ボーナス加算MAXに月額3000円ガードを盛り込む
2014.05.08 残債を考慮するように修正
2014.06.19 YearDiffを月に対応。
2014.07.31 parseIntを Math.floor(1.0*trimFixed())に変更し、jsによる誤差を修正
2014.09.19 元本充当額計算ループを高速化
2015.08.05 車検到来機能 by morita
*/







// 中古車ローン計算時の、残価および、ローン元金の計算基準になる料率。支払い回数や、年式によって変わる
// 2016.11 中古車WPのローン元金下限ガードは一定パーセンテージが撤廃になり、元本充当額ロジックのみ
/*
	中古車WPの新料率
	
　　12　-　24回　　　　残価上限　50%　（下限　5%）
　　25　-　36回　　　　　　　　　40%　（下限　5%）
　　37　-　48回　　　　　　　　　30%　（下限　5%）
　　48　-　60回　　　　　　　　　20%　（下限　5%）
*/
var useddata = new Array();
useddata[24] = {"yeardiff":5,"maxlptrate":50,"minlptrate":5,"minlpprate":0};
useddata[36] = {"yeardiff":5,"maxlptrate":40,"minlptrate":5,"minlpprate":0};
useddata[48] = {"yeardiff":4,"maxlptrate":30,"minlptrate":5,"minlpprate":0};
useddata[60] = {"yeardiff":3,"maxlptrate":20,"minlptrate":5,"minlpprate":0};
for(i=13;i<=23;i++){
	useddata[i] = useddata[24];
	useddata[i+12] = useddata[36];
	useddata[i+24] = useddata[48];
}

// 2013.04.04 残価ロジック変更 by morita
// 2016.03.31 一部モデル1年タイプ追加
var lptrateArr6 ={
				"12":12,
				"24":9,
				"36":0,
				"48":-6,
				"60":-12
				};
var lptrateArr7 ={
				"12":12,
				"24":9,
				"36":0,
				"48":-7,
				"60":-14
				};
var lptrateArr9 ={
				"12":12,
				"24":9,
				"36":0,
				"48":-9,
				"60":-18
				};
				
// 残価データ
var lptratedataArr9 ={"12":12,"24":9,"36":0,"48":-9,"60":-18,"72":-27};
var lptratedataArr7 ={"12":12,"24":9,"36":0,"48":-7,"60":-14,"72":-21};
var lptratedataArr6 ={"12":12,"24":9,"36":0,"48":-6,"60":-12,"72":-18};

// プラス計算の金利を保持している。金利は、支払い回数に依存する
var tsuikakinriArr = {12:5.59,24:5.59,36:5.59,48:5.59,60:5.59,72:5.59};

// WPP低金利対応 2014.06.09
var tsuikakinriArr2 = {12:2.90,24:2.90,36:2.90,48:2.90,60:2.90,72:2.90};
			
// WPPキャンペーン　低金利インナーのみに仕様変更 2014.06.28
// WPPキャンペーン　金利変更 2.50%→2.90% 2014.09.30
var tsuikakinriArr3 = {12:2.90,24:2.90,36:2.90,48:2.90,60:2.90,72:2.90};
			
// 20150701 スーパーウェルカムプランプラスでは49回以上の追加売買代金金利が5.89%
var tsuikakinriArr4 = {12:5.59,24:5.59,36:5.59,48:5.89,60:5.89,72:5.89};
							


/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// ローン計算本体
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
function loancalc(plan,conditions){
	var result = [];
	
	var rate 				= trimFixed(1.0*conditions['rate']/100.0);
	var installments 		= 1*conditions['installments'];
	var pricetax			= 1*conditions['pricetax'];
	var optiontotal 		= 1*conditions['optiontotal'];
	var taxtotal 			= 1*conditions['taxtotal'];
	var downpayment 		= 1*conditions['downpayment'];
	var bonuspayment 		= 1*conditions['bonuspayment'];
	var lastpayment 		= 1*conditions['lastpayment'];
	var sonota		 		= 1*conditions['sonota'];

	var loanprincipal 		= pricetax + optiontotal + taxtotal + sonota - downpayment;
	
	var bonustimes;
	


	var test = 1.0-1.0/Math.pow((1.0+rate/12.0),installments);

	var addon1 = (Math.round(((rate/12.0/test)*(1.0*installments)-1.0)*10000))/10000;
	
	//小数点第4位に切り上げ
	// 2014.02.06 addon2の計算で、0.00001をプラスしているのを消去
	//var addon2 = (Math.round((rate*(installments+1)/12+0.00001)*10000))/10000;
	var addon2 = (Math.round((rate*(installments+1)/12)*10000))/10000;
	
	
	//alert("test:"+test+"addon1:"+addon1+"addon2:"+addon2);
	
	//loanprincipal = pricetax + optiontotal + taxtotal - downpayment;
	var interest = Math.floor(1.0*trimFixed(((loanprincipal - lastpayment) * addon1) + lastpayment * addon2));
	var total = Math.round((loanprincipal + interest)*100)/100;
	if(conditions['bonustimes']){
		bonustimes = conditions['bonustimes'];
	}else{
		bonustimes = 1.0*trimFixed(installments / 12 * 2);
		bonustimes = Math.round(bonustimes,0);
	}
	var monthlypayment = (Math.floor(1.0*trimFixed(((loanprincipal - lastpayment + interest - bonuspayment * bonustimes )/installments)/100)))*100;
	var firstpayment = Math.floor(1.0*trimFixed(loanprincipal - lastpayment + interest - bonuspayment * bonustimes - monthlypayment * (installments - 1)));
	
	result['interest'] = interest;
	result['total'] = total;		// mbjから来る総額totalとは別物なので注意！！！
	result['bonustimes'] = bonustimes;
	result['monthlypayment'] = monthlypayment;
	result['firstpayment'] = firstpayment;
	result['leasingprice'] = 0;
	result['loanprincipal'] = loanprincipal;
	
	
	if(0){
	
		total				 = conditions['loanprincipal'];
		pricetax			 = conditions['pricetax'];
		optiontotal		 = conditions['optiontotal'];
		automobiletax		 = conditions['automobiletax'];
		acquisitiontax		 = conditions['acquisitiontax'];
		tonnagetax			 = conditions['tonnagetax'];
		acquisitiontax		 = conditions['acquisitiontax'];
		insurance			 = conditions['insurance'];
		recycle			 = conditions['recycle'];
		taxtotal			 = conditions['taxtotal'];
		installments		 = conditions['installments'];
		rate				 = conditions['rate']/100;
		downpayment		 = conditions['downpayment'];
		bonuspayment		 = conditions['bonuspayment'];
		price				 = conditions['price'];
		accessory			 = conditions['accessory'];
		lastpayment		 = conditions['lastpayment'];
		//regmonth			 = 4;
		management_fee		 = 0;
		// 2012.01.28 翌年度自動車税50%減税　by pc-otasuke.jp
		reduce_automobiletax = conditions['reduce_automobiletax'];
		
		debug(conditions);
		
		debug("合計金額 " + total + "円<br />");
		debug("税込み車両本体価格 " + pricetax + "円<br />");
		
		/*
		For cnt = 0 To 9
			optionid(cnt) = Request.Form("optionid(" & cnt & ")")
			Response.Write("オプションID" & (cnt + 1) & " = " & optionid(cnt) & "<br />")
		Next
		
		For cnt = 0 To 9
			optiontotal(cnt) = Request.Form("optiontotal(" & cnt & ")")
			Response.Write("オプション代金" & (cnt + 1) & " = " & optiontotal(cnt) & "<br />")
		Next
		*/
		
		debug("税込みオプション金額合計 " + optiontotal + "円<br />");
		debug("自動車税 " + automobiletax + "円<br />");
		debug("自動車取得税 " + acquisitiontax + "円<br />");
		debug("自動車重量税 " + tonnagetax + "円<br />");
		debug("自賠責保険料 " + insurance + "円<br />");
		debug("リサイクル料金 " + recycle + "円<br />");
		debug("諸経費小計 " + taxtotal + "円<br />");
		
		
		debug("お支払い年数 " + installments + "回<br />");
		
		debug("実質年率 " + rate * 100 + "%<br />");
		debug("頭金 " + downpayment + "円<br />");
		debug("ボーナス月加算金額 " + bonuspayment + "円<br />");
		
		debug("税抜き車両本体価格 " + price + "円<br />");
		debug("付属品価格 " + accessory + "円<br />");
		debug("残価 " + lastpayment + "円<br />");
		debug("登録月 " + regmonth + "月<br />");
		debug("契約管理手数料 " + management_fee + "円<br />");
		
		
		if( pricetax == "") pricetax = 7800000;
		if( optiontotal == "") optiontotal = 0;
		if( downpayment == "") downpayment = 0;
		if( bonuspayment == "") bonuspayment = 0;
		if( lastpayment == "") lastpayment = 1410000;
		if( price == "") price = 7428571;
		
		
		// リース計算
		
		// 登録諸費用は一律80,000円で計算
		regist = 80000;
		// 車両登録月は4月固定で計算
		regmonth = 7;	//※検証用にコメントアウト。本番システムでは元に戻す。
		
		// 自動車税の算出
		
			// 登録予定月が1未満または13以上の場合、登録予定月を当月にする
			// 課税月数 = 15 ー 登録予定月
			// 課税月数が12以上の場合、課税月数 = 課税月数 - 12
			// 登録地域が北海道の場合、自動車税年額 ÷ 1.081 (100円未満切り捨て)
			// 初回自動車税 = 自動車税年額 × 課税月数 ÷ 12 (100円未満切り捨て)
			// 継続自動車税 = 自動車税年額 × リース年数 － 自動車税年額 × 課税月数 ÷ 12 (100円未満切り捨て)
		
		
				// if( regmonth < 1 or regmonth > 12 ) regmonth = month(now);
		
		
				kazei = 15 - regmonth;
		
				if( kazei > 11 ){
					kazei = kazei - 12;
				}else if( kazei < 3 ){
					kazei = 11;
				}
				
				
				
		
		
		
				automobile1 = (Int(automobiletax * kazei / 12 / 100))*100;
		
				tomobile2 = Int((automobiletax * installments / 12 - automobiletax * kazei / 12) / 100)*100;
				// 2012.01.28 翌年度自動車税50%減税　by pc-otasuke.jp
				/*
				if(reduce_automobiletax){
					automobile2 -= automobiletax * 0.5;
				}
				*/
				
				// 2013.07.03 翌年度自動車税減税（特別オプション）対応 by morita
				automobile2 -= automobiletax * getGenzeiRate()/100;
				
				log(getGenzeiRate()+"％減税！！！");
				
				
				
				// add by morita
				Temp7 = automobile1;
				
				debug ("☆初回自動車税 " + automobile1 + "円<br />");
				debug ("☆継続自動車税 " + automobile2 + "円<br />");
				
		
		// 償却費の計算
		
		// 取得金額 ＝ 車両本体価格 － 値引額 ＋ オプション代金（取得税課税） ＋ 付属品価格（取得税非課税） ＋ 旧付属品価格（hidden） ＋ 登録諸費用
		
		// 償却費 ＝ 取得金額 － 残価額
			
				//acq_price = price + Int(optiontotal / 1.05) + accessory + regist;
				// その他項目も追加（非課税？？？）
				//acq_price = price + Int((optiontotal + sonota) / 1.05) + accessory + regist;
				acq_price = price + Int((optiontotal + sonota) / g_tax);
				
		
				repay_price = acq_price - lastpayment;
		
		// 検収時元本 (362円はリサイクル料金の資金管理料380円の消費税抜き金額）
								Ganpon1st = repay_price + tonnagetax + acquisitiontax + automobile1 + insurance + 362;
		
				debug("☆repay_price " + repay_price + "円<br />");
				debug("☆tonnagetax " + tonnagetax + "円<br />");
				debug("☆acquisitiontax " + acquisitiontax + "円<br />");
				debug("☆automobile1 " + automobile1 + "円<br />");
				debug("☆insurance " + insurance + "円<br />");
				debug("☆検収時元本 " + Ganpon1st + "円<br />");
				debug("☆取得金額 " + acq_price + "円<br />");
				debug("☆償却費 " + repay_price + "円<br />");
		
		
		
		// 継続元本
						GanponLeft = automobile2;
		
		// 06/09/26 社内金利＝実行金利
				rate1 = rate;
				
						Geturi_Tekiyou = fRound(rate1 / 12.0, 9);
						Geturi_Jikkou = fRound(rate / 12.0, 9);
		
		// 平均元本率
		
		Temp1 = fRuijou((1+Geturi_Tekiyou),installments,2);
		Temp2 = fRound(1/fRound((Geturi_Tekiyou * installments),9),9);
		SyanaiHeiganritu = fRound(fRound(Temp1/(Temp1-1),9)-Temp2,3);
		
		debug("☆社内平元率 " + SyanaiHeiganritu + "<br />");
		
		// ===============================================================
		// 変則回収でない場合（※頭金とボーナス加算額がどちらもゼロの場合）
		intMaeukeKaisu = 0;		// by morita
		
		if( downpayment == 0 && bonuspayment == 0 ){
		
		// 支払利息　均等払い分
				Temp2 = fRound(installments / 12, 9);
		
				Temp1 = fRound(1 - fRound(intMaeukeKaisu / (installments), 9), 9);
		
				Temp1 = fRound(Ganpon1st * Temp1, 1);
		
				Temp1 = fRound(Temp1 * rate1, 1); // 適用金利
						
		
		// 		recyclerate = Temp1 * (recycle - 380) * installments / 12
		
		
				Temp1 = fRound(Temp1 * SyanaiHeiganritu, 1); // 
		
		
				Temp3 = fRound(lastpayment * rate1, 1); // 適用金利
				Temp3 = Temp3 * fRound(installments / 12, 9);
		
		
		
		// リサイクル料
				Temp4 = fRound((recycle - 380) * rate1, 1); // 適用金利
				Temp4 = Temp4 * fRound(installments / 12, 9);
		// 支払利息
				pay_interest = Clng(fRound(Temp1 * Temp2, 0) + fRound(Temp3, 0) + fRound(Temp4, 0));
				debug("☆Temp4 " + Temp4 + "<br />");
				debug("☆支払利息 " + pay_interest + "<br />");
		
		
		// 契約管理手数料（クローズエンドリースの場合のみ算入）
				// 2012.02.07 add by morita
				if(plan == "cls"){
					management_fee = carArr['Car']['managementfee'];
				}
				management_fee_int = management_fee * rate1 * fRound(installments / 12, 9);
				pay_interest = Clng(fRound(Temp1 * Temp2, 0) + fRound(Temp3, 0));		
					
				
		// 総費用
				pay_interest_sum = pay_interest + management_fee_int;
				// del by morita
				//leasetotal =  pay_interest + automobile2 + Ganpon1st + 362;
				
		
		
		
		// リース原価
				//leasing_cost = leasetotal + management_fee + management_fee_int;
				leasing_cost = fRound(pay_interest + automobile2 + Ganpon1st,0) + automobile1 - Temp7 + 362;
				
				//debug ("☆総費用 " + leasetotal + "円<br>");
				debug ("☆リース原価 " + leasing_cost + "円<br>");
			
		
		
		
		// 月額リース料
				Temp1 = fRound(rate - rate1 , 9);
				Temp1 = fRound(pay_interest_sum * Temp1, 1);
				Temp1 = fRound(Temp1 / rate1, 0);
				Temp1 = leasing_cost + Temp1;
				leasingprice = fRoundUp(Temp1 / installments, -2);
		
				contract = leasingprice * installments;

		// ボーナス回数はゼロ by morita
				bonustimes = 0;
		
		
		}else{
		// ===============================================================
		// 変則回収の場合
		
		// 頭金の消費税対応
		// 頭金 ＝ 頭金（税込） ÷ this->tax （小数点第1位以下切り上げ）
			downpayment_without_tax = Int(downpayment/g_tax+0.99999);
		
		// ボーナス月の設定
		// ボーナス月は1月と7月固定
		// LMSではボーナス月を1月7月と設定すると登録予定月が2月～7月の場合、
		// 最初の半年にボーナス加算月がなく、ボーナス回数が１回減ってしまう。
		// 登録予定月が2月～7月の場合は7月1月と入れ替えることにより解消する。
		
		//regmonth = CInt(regmonth);
		intBonusMonth1_input = 1;
		intBonusMonth2_input = 7;
		
		
		if( regmonth > 1 && regmonth < 8 ){
				intBonusMonth1 = 7;
				intBonusMonth2 = 1;
		}else{
				intBonusMonth1 = 1;
				intBonusMonth2 = 7;
		}
		
		
		// ボーナス回収回数の取得
								intBonusKaisu = 0;
								intBonusSueoki = 0;
								m = regmonth;
		
								for( t = 1; t <= installments; t++){
										if( m == intBonusMonth1_input || m == intBonusMonth2_input ){
												intBonusKaisu = intBonusKaisu + 1;
												if( intBonusSueoki == 0 && m == intBonusMonth1_input ){
														intBonusSueoki = t;
														intBonusKaisu = 1;
												}
										}
										if( m  < 12 ){
												m = m + 1;
										}else{
												m = 1;
										}
								}
		
					bonustimes = intBonusKaisu;
					bonustimes = installments / 6;		// 固定 by morita
					
					debug("ボーナス回数:"+bonustimes+"回");
		
					// 通常資本回収係数（実行）
								Temp1 = fRuijou(1 + Geturi_Jikkou, installments, 1);
								Temp2 = fRoundDown(Geturi_Jikkou * Temp1, 9);
								SKK_Tsuujou_Jikkou = fRound(fRound(Temp2 / (Temp1 - 1), 9), 6);
		
					// ボーナス資本回収係数（実行）
								Temp3 = fRound(rate / 2, 9);
								Temp1 = fRuijou((1 + Temp3), (bonustimes -1), 1);
								Temp1 = fRound(Temp3 * Temp1, 9);
								Temp2 = fRuijou((1 + Temp3), bonustimes, 1) - 1;
								SKK_Bonus_Jikkou = fRound(fRound(Temp1 / Temp2, 9), 6);
		
					// 据置分補正（実行）
								SueokiHosei_Jikkou = 1 + fRoundDown(Geturi_Jikkou * intBonusSueoki, 9);
		
					// 通常資本回収係数（適用）
								Temp1 = fRuijou(1 + Geturi_Tekiyou, installments, 1);
								Temp2 = fRound(Geturi_Tekiyou * Temp1, 9);
								SKK_Tsuujou_Tekiyou = fRound(fRound(Temp2 / (Temp1 - 1), 9), 6);
		
					// ボーナス資本回収係数（適用）
								Temp3 = fRound(rate1 /2, 9);
								Temp1 = fRuijou((1 + Temp3), (bonustimes - 1), 1);
								Temp1 = fRound(Temp3 * Temp1, 9);
								Temp2 = fRuijou((1 + Temp3), bonustimes, 1) - 1;
								SKK_Bonus_Tekiyou = fRound(fRound(Temp1 / Temp2, 9), 6);
		
					// 据置分補正（適用）
								SueokiHosei_Tekiyou = 1 + fRoundDown(Geturi_Tekiyou * intBonusSueoki, 9);
		
					// 月額最低支払額
								Temp1 = automobile2;
								Temp2 = fRoundDown(lastpayment * Geturi_Jikkou, 9);
								Temp2 = fRound(Temp2 * installments, 0);
								Temp1 = Clng(Temp2) + Temp1;
								leasingprice_min = Clng(fRound(Temp1 / installments, 0));
		
					// 通常月額
								Temp2 = fRound(bonuspayment / SKK_Bonus_Jikkou, 1);
								Temp2 = fRound(Temp2 / SueokiHosei_Jikkou, 1);
								Temp1 = Ganpon1st - (downpayment_without_tax + Temp2);
								Temp1 = fRound(Temp1 * SKK_Tsuujou_Jikkou, 1);
								leasingprice = Clng(fRoundDown(Temp1, 0)) + leasingprice_min;
		
		
					// 通常月額、ボーナス月加算額の調整
								leasingprice = fRound(leasingprice, -2);
								bonuspayment = fRound(bonuspayment, -2);
		
					// 支払利息
		
		
								Temp2 = fRound(bonuspayment / SKK_Bonus_Jikkou, 1);
								Temp2 = fRound(Temp2 / SueokiHosei_Jikkou, 1);
								Temp1 = fRound(Temp2 * SKK_Bonus_Tekiyou, 1);
								Temp1 = fRound(Temp1 * SueokiHosei_Tekiyou, 1);
								Temp1 = fRound(Temp1 * bonustimes, 1);
								Temp1 = fRoundDown((Temp1 - Temp2), 0);
								Temp4 = Ganpon1st - downpayment_without_tax;
								Temp4 = fRoundDown((Ganpon1st - downpayment_without_tax), 0);
								Temp4 = fRoundDown((Temp4 - Temp2), 0);
								Temp3 = fRound(Temp4 * SKK_Tsuujou_Tekiyou, 1);
								Temp3 = fRound(Temp3 * installments, 1);
								Temp3 = fRound((Temp3 - Temp4) , 1);
								Temp1 = fRoundDown((Temp1 + Temp3), 0);
								Temp2 = 0;
								Temp2 = fRoundDown(lastpayment * Geturi_Tekiyou, 9);
								Temp2 = fRound(Temp2 * installments, 0);
								pay_interest = fRoundDown((Temp1 + Temp2), 0);
		
		// リース原価
				//leasetotal =  pay_interest + automobile2 + Ganpon1st;
				// chg by morita
				leasing_cost = fRound(pay_interest + automobile2 + Ganpon1st,0) + automobile1 - Temp7 + 362;
		
					// 契約額
								Temp1 = leasingprice * installments;
								Temp2 = bonuspayment * bonustimes;
								contract = Temp1 + downpayment_without_tax + Temp2;
		
		}
		
		// ===============================================================
		// 採算関連
		
				 // 当社手数料
		//                 charge = contract - leasetotal
						charge = contract - leasing_cost + pay_interest;
						debug ("☆当社手数料 " + charge + "円<br>");
		
		
		
		
				 // 総荒利額
						//gross_profit = contract - leasetotal;
						// chg by morita
						gross_profit = contract - leasing_cost;
		
				 // 年荒利額
						if( installments > 12 ){
								Temp1 = fRound(gross_profit / installments, 9);
								annual_profit = fRound(Temp1 * 12, 0);
						}else{
								annual_profit = gross_profit;
						}
		
		
		
				  // 総荒利率
						gross_profit_rate = fRound(fRound(gross_profit / acq_price, 9), 5);
		
				  // 年荒利率
						if( installments > 12 ){
								Temp1 = fRound(gross_profit / acq_price, 9);
								Temp1 = fRound(Temp1 / installments, 9);
								annual_profit_rate = fRound(Temp1 * 12, 5);
						}else{
								annual_profit_rate = gross_profit_rate;
						}
		
		
				 // 年利回り
						Temp1 = fRound(gross_profit / pay_interest, 9);
						annual_interest_rate = fRound(fRound(Temp1 * rate1, 9), 5);
		
				 // 運用利回り
						interest_rate = rate1 + annual_interest_rate;
		
						debug ("☆運用利回り1 " + interest_rate + 100 + "<br>");
		
		
		// ===============================================================
		// 運用利回りが実行金利を下回った場合の処理

		// 運用利回りが実行金利を下回った場合、月額リース料 ＝ 月額リース料 ＋ 100
		
		if( interest_rate * 1 < rate * 1 ){
		
						debug("☆リース料調整あり(+100円)<br />");
						debug ("☆運用利回り(比較)interest_rate " & interest_rate * 100 & "<br>");
						debug ("☆実行金利(比較)rate " + rate * 100 + "<br>");
		
		
			// 契約額
						leasingprice = leasingprice + 100;
						Temp1 = leasingprice * installments;
						Temp2 = bonuspayment * bonustimes;
						contract = Temp1 + downpayment_without_tax + Temp2;
		
			// 月額リース料
						leasingprice = fRoundUp(fRound(Temp1 / installments, 0), -2);
		
						Genka = Ganpon1st + automobile2 + pay_interest + 362;
		
		
			// 当社手数料
						charge = contract - Genka;
		
			// 総荒利額
						gross_profit = charge;
		
			// 年荒利額
						if( installments > 12 ){
								Temp1 = fRound(gross_profit / installments, 9);
								annual_profit = fRound(Temp1 * 12, 0);
		
						}else{
								annual_profit = gross_profit;
						}
		
			// 総荒利率
						gross_profit_rate = fRound(fRound(gross_profit / acq_price, 9), 5);
		
			// 年荒利率
						if( installments > 12 ){
								Temp1 = fRound(gross_profit / acq_price, 9);
								Temp1 = fRound(Temp1 / installments, 9);

								annual_profit_rate = fRound(Temp1 * 12, 5);
						}else{
								annual_profit_rate = gross_profit_rate;
						}
		
			// 年利回り
						Temp1 = fRound(gross_profit / pay_interest, 9);
						annual_interest_rate = fRound(fRound(Temp1 * rate1, 9), 5);
		
			// 運用利回り
						interest_rate = rate1 + annual_interest_rate;
		
		
		}
		
		debug("<hr>月額リース料 " + leasingprice + "円<br />");
		debug("消費税込み月額リース料 " + Int(leasingprice * g_tax) + "円<br />");
		debug("リース料総額 " + Int(leasingprice * g_tax) * installments + "円<br />");
		debug("☆契約金額 " + contract + "円<br />");
						
		result['leasingprice'] = leasingprice;
		result['bonuspayment'] = bonuspayment;
		result['total'] = Int(leasingprice * g_tax) * installments + bonuspayment*bonustimes;
		result['interest'] = result['total'];				// 特別金利時の差のためにここに格納
		result['bonustimes'] = installments / 12 * 2;
		result['monthlypayment'] = Int(leasingprice * g_tax) ;
		result['firstpayment'] = 0;
		
	}
	
	return result;
}



function _check(plan,conditionArr,_newused){
	
	var mode = conditionArr['mode'];
	var bmst = conditionArr['bmst'];
	//plan = conditionArr['plan'];						//プラン名
	var total = 1*conditionArr['total'];						//tax optionなどを含めた総額(resultのtotalとは違うので注意)
	var installments = 1*conditionArr['installments'];		//支払い回数
	//loanprincipal = conditionArr['loanprincipal'];		//ローン元金
	var pricetax = 1*conditionArr['pricetax'];				//税込み車両本体価格
	var optiontotal = 1*conditionArr['optiontotal'];			//税込みオプション金額合計
	var taxtotal = 1*conditionArr['taxtotal'];						//税金等の諸経費合計
	var downpayment = 1*conditionArr['downpayment'];			//頭金
	var sonota = 1*conditionArr['sonota'];					// その他オプション
	
	var zansai = 0;
	
	var rateArr = [];
	var LptrateArr = [];
	var BptrateArr = [];
	var LpprateArr = [];
	
	var dptmin = 0;
	var dptmax = 0;
	var lppmin = 0;
	var lppmax = 0;
	var bptmin = 0;
	var bptmax = 0;
	
	var jsonArr = [];
	
	
	// ボーナス加算額が引数として入っている場合はそれを採用
	var bonuscnt;
	if(conditionArr['bonustimes'] == undefined){
		bonuscnt = kirisute(1,installments / 6);
	}else{
		bonuscnt = conditionArr['bonustimes'];
	}
	// 2014.04.14 元本充当額チェックをスルーしてしまう不具合修正
	//if(conditionArr['bonuspayment'] == 0) bonuscnt = 0;
	
	if(plan=="swp" && _newused=="used"){
		jsonArr['selectedrate'] = "-";
		jsonArr['lowrate'] = "-";
		jsonArr['normalrate'] = "-";
		jsonArr['innerrate'] = "-";
		jsonArr['message'] = "中古車はスーパーウェルカムプランには対応しておりません";
		return jsonArr;
	}

	// swp対応モデル？？？？
	if(_newused=="new" && carArr['swpmodel']==0 && plan=="swp"){
		jsonArr['selectedrate'] = "-";
		jsonArr['lowrate'] = "-";
		jsonArr['normalrate'] = "-";
		jsonArr['innerrate'] = "-";
		jsonArr['message'] = "このモデルはswp非対応です";
		return jsonArr;
	}
			
	
	// ローン元金をPHPで算出 2012.02.25 by morita
	//loanprincipal = total - downpayment + sonota;
	var loanprincipal = pricetax + taxtotal + optiontotal - downpayment + sonota;
	

	// mbjからの総額ではなく、本当の総額を求める
	var total2 = total;
	
	//debug(conditionArr);
	
	
	// 2012.02.07 add by morita
	if(1){	// 常にセールスマンモード
		var milage = 1*conditionArr['milage'];				//走行距離
	}
	// ボーナス加算額を100円で切り捨てる 2011.11.29 by morita
	//bonuspayment = conditionArr['bonuspayment'];		//ボーナス月加算額
	var bonuspayment = kirisute(100,conditionArr['bonuspayment']);		//ボーナス月加算額
	//debug(conditionArr['lastpayment']);
	var lastpayment = kirisute(10000,conditionArr['lastpayment']);			//残価
	
	//debug(conditionArr);
	
	//carArr = this->Car->findByBmst(bmst);
	//initrateArr = this->Initrate->find(array("patternid"=>carArr['Car']['initratepattern']));
	
	var price = 1*carArr['price'];				//税抜き車両本体価格
	
	// 万が一変な値が入っていた場合は、ここで調整 2011.11.06 by morita
	if(!is_numeric(downpayment)) downpayment = 0;
	if(!is_numeric(bonuspayment)) bonuspayment = 0;
	if(!is_numeric(lastpayment)) lastpayment = 0;
	if(!is_numeric(loanprincipal)) loanprincipal = 0;
	
	// 2014.05.08 残債を考慮するように修正
	if(conditionArr['zansai']){
		zansai = 1*conditionArr['zansai'];
	}
		
	
	// 低金利期間対応 2011.11.23 by morita
	//lowratemodel = is_lowratemodel(bmst,installments);

	// 金利ルーチンを統合 2012.02.22 by morita
	/*
	rateArr = getRateArr(plan,bmst,installments);
	lowrate = rateArr['lowrate'];
	normalrate = rateArr['normalrate'];
	innerrate = rateArr['innerrate'];
	*/
	//tempArr = getRateArr(plan,bmst,installments);
	
	// 金利設定していない場合のエラー対策
	var lowrate = 0;
	var normalrate = 0;
	var innerrate = 0;

	rateArr =findFromDBArr(Rates,{"patternid":carArr[plan+"ratepattern"],"installments":installments});
	
	if(rateArr[0]){
		lowrate = 1.0*rateArr[0]['lowrate'];
		normalrate = 1.0*rateArr[0]['rate'];
		innerrate = 1.0*rateArr[0]['innerrate'];
	}
	
	//debug(tempArr);

	
	//list(lowrate,normalrate,innerrate,rateArr) = tempArr;
	
	// 一般モード時にselectedrateが初期値のままになる不具合修正 2012.05.11 by morita
	if(1){ // 常にセールスマンモード
		// セールスマンモード時は何もしなくてよい
	}else{
		// 金利パターン大幅変更 2012.05.26 by morita
		conditionArr['selectedrate'] = lowrate;
		/*
		// 一般モード時は、installmentsに対応した金利をここで設定してあげる
		if(lowratemodel){
			conditionArr['selectedrate'] = lowrate;
		}else{
			conditionArr['selectedrate'] = normalrate;
		}
		*/
	}
	
	
	var message = "";
	
	switch(plan){
		case "wp":
			// WPの頭金、残価、ボーナス加算額、金利データを取得する
			//rateArr = this->Rate->find(array("patternid"=>carArr['Car']['wpratepattern'],"installments"=>installments));
			bptrateArr = findFromDBArr(Bptrates,{patternid:carArr['wpbptpattern'],installments:chg_installments(installments)});
			lptrateArr = findFromDBArr(Lptrates,{patternid:carArr['wplptpattern'],installments:chg_installments(installments)});
			lpprateArr = findFromDBArr(Lpprates,{patternid:carArr['wplpppattern'],installments:chg_installments(installments)});
			
			//初期表示の場合
			if(mode==false){
			//初期表示の場合はメッセージ無効
				message = "";
			}						
			
			//実質年率
			//normalrate = rateArr['Rate']['rate'];
			//lowrate = rateArr['Rate']['lowrate'];
			//innerrate = rateArr['Rate']['innerrate'];		// セールスマン向けの金利 2012.02.09 by morita
			
			if(_newused == "new"){
				// 新車の場合				
				// 残価　lastpayment
				// 残価上限は、SWP残価＋5%に変更 2012.12.13 by morita
				//lptmax = kirisute(10000,lptrateArr['Lptrate']['maxrate'] / 100.0 * price);
				lptmax = getmaxwplpt(installments,"new");

				//lptmin = kiriage(10000,lptrateArr[0]['minrate']*1.0 / 100.0 * (price+1*get_makeroptiontotal()));
				//lptmin = kiriage(10000,lptrateArr[0]['minrate']*1.0 / 100.0 * price);
				lptmin = getminwplpt(installments,"new");
				
				// 支払い回数変更時は、残価をMAXにする
				if(mode == "installments_change"){
					lastpayment = lptmax;
				}

				// max,minでフィルタリング
				if(lastpayment > lptmax){
					if(lptmax <= 0){
						lptmax = 0;
					}
					message += "残価の上限は￥"+number_format(lptmax)+"です.";
					lastpayment = lptmax;
				}
				if(lastpayment < lptmin){
					if(lptmin <= 0){
						lptmin = 0;
					}else{
						message += "残価の下限は￥"+number_format(lptmin)+"です.";
					}
					lastpayment = lptmin;
				}
				
/*
				// ローン元金 loanprincipal
				lppmax = kirisute(1,lpprateArr[0]['maxrate'] / 100.0 * pricetax + taxtotal + optiontotal + sonota);
				//lppmin = kiriage(10000,lpprateArr[0]['minrate'] / 100.0 * price);
				lppmin = 0;		// 元本充当額でガード
				
				// max,minでフィルタリング
				if(loanprincipal > lppmax){
					loanprincipal = lppmax;
				}
				if(loanprincipal < lppmin){
					loanprincipal = lppmin;
				}
*/
				
				// ボーナス加算額 bonuspayment
				//bonuscnt = installments / 6;
				if(bonuscnt > 0){
					if(bptrateArr[0]){
						bptmax = kirisute(100,(loanprincipal - lastpayment) * bptrateArr[0]['maxrate'] / 100.0 / bonuscnt);
					}else{
						bptmax = 20000000;
					}
					
					
					// bptmax時に、ローン元金が下限になっていないかを確認する
					var resultArr = [];
					var guard_flg = 0;		// 0:ガードなし 1:ガード引っかかった
					var guardArr = [];
					
					// 演算条件を再設定
					var tempArr = [];
					tempArr['rate'] =  conditionArr['selectedrate'];
					
					var cnt=0;
					
					while(1){
						cnt++;
						tempArr['installments'] = installments;
						tempArr['pricetax'] = pricetax;
						tempArr['optiontotal'] = optiontotal;
						tempArr['downpayment'] = downpayment;
						tempArr['lastpayment'] = lastpayment;
						tempArr['sonota'] = sonota;
						tempArr['taxtotal'] = taxtotal;
						
						
						tempArr['bonuspayment'] = bptmax;
						
						resultArr = loancalc(plan,tempArr);
	
						// さらにガードをする
						// 全てのファイナンスプラン　→　月額3000円未満ガード
						/*
						if(resultArr['monthlypayment'] < 3000){
							guard_flg = 1;		// 計算NG
							bptmax = 0;
							break;
						}
						*/
						
						// 新車WP・SWP　→　据置検証シート（元本充当額）によるガード
						// 元本充当額ガード
						
						guardArr['rate'] = conditionArr['rate'];
						guardArr['loanprincipal'] = resultArr['loanprincipal'];
						guardArr['monthlypayment'] = resultArr['monthlypayment'];
						guardArr['firstpayment'] = resultArr['firstpayment'];
						guardArr['bonuspayment'] = conditionArr['bonuspayment'];
						guardArr['installments'] = conditionArr['installments'];
						guardArr['lastpayment'] = conditionArr['lastpayment'];
						
						guard_flg = downpayment_check(guardArr);
						
						if(guard_flg == 0 && resultArr['monthlypayment'] >= 3000){
							// ガード通った場合は終了！
							break;
						}else{
							bptmax -= 100;
							if(bptmax < 0){
								break;
							}
						}
					}
					bptmin = 0;
				}else{
					bptmax = 0;
					bptmin = 0;
				}
				//alert(cnt);
				// max,minでフィルタリング
				if(bptmax < 0){
					// 元本充当額計算で通らない場合は、必ずbptmax<0になる！
					// 頭金をゼロにしてチェック強制終了
					downpayment = 0;
					loanprincipal = total2 - downpayment;
					
					// あいまいエラーメッセージを格納
					message += "ローン元金下限を下回っています。入力条件をご確認ください.";
				}else{
					if(bonuspayment > bptmax){
						bonuspayment = bptmax;
						// ボーナス加算が負にならないようにガード
						if(bptmax <= 0){
							message += "ボーナス月加算は無効です.";
						}else{
							message += "ボーナス加算の上限は￥"+number_format(bptmax)+"です.";
						}
					}
					if(bonuspayment < bptmin){
						// 下限がゼロの場合は、メッセージ非表示
						if(bptmin <= 0){
							bptmin = 0;
						}else{
							message += "ボーナス加算の下限は￥"+number_format(bptmin)+"です.";
						}
						bonuspayment = bptmin;
					}
					// 最終的な値を格納
					conditionArr['bonuspayment'] = bonuspayment;
					
					
					//////////////////////////////////////////////////////////////////////////////
					/*
						ローン元金ガードについて
						
						全てのファイナンスプラン　→　月額3000円未満ガード
						新車WP・SWP　→　据置検証シート（元本充当額）によるガード
						新車・中古車STD　→　ローン元金30万円未満ガード
						中古車WP　→　元本充当額ガード	
					*/
					//////////////////////////////////////////////////////////////////////////////
					// 演算条件を再設定
					conditionArr['rate'] = conditionArr['selectedrate'];
					var resultArr = loancalc(plan,conditionArr);
					var guard_flg = 0;		// 0:ガードなし 1:ガード引っかかった
	
					// さらにガードをする
					// 全てのファイナンスプラン　→　月額3000円未満ガード
					if(resultArr['monthlypayment'] < 3000){
						guard_flg = 1;		// 計算NG
					}else{
					
						// 新車WP・SWP　→　据置検証シート（元本充当額）によるガード
						// 元本充当額ガード
						var guardArr = new Array();
						
						guardArr['rate'] = conditionArr['rate'];
						guardArr['loanprincipal'] = resultArr['loanprincipal'];
						guardArr['monthlypayment'] = resultArr['monthlypayment'];
						guardArr['firstpayment'] = resultArr['firstpayment'];
						guardArr['bonuspayment'] = conditionArr['bonuspayment'];
						guardArr['installments'] = conditionArr['installments'];
						guardArr['lastpayment'] = conditionArr['lastpayment'];
						
						guard_flg = downpayment_check(guardArr);
					}
					
					
					if(guard_flg){
						// ローン元金下限ガードで引っかかっているので、メッセージを表示して、頭金をデフォルト(ゼロ）にする
						// 頭金・ローン元金を強制的に変更する
						downpayment = 0;
						loanprincipal = total2 - downpayment;
						
						// あいまいエラーメッセージを格納
						message += "ローン元金下限を下回っています。入力条件をご確認ください.";
					}
	
	
					// 頭金 downpayment
					dptmin = 0;
					if(downpayment < dptmin){
						if(dptmin <= 0){
							dptmin = 0;
						}
						message += "頭金の下限は￥"+number_format(dptmin+zansai)+"です.";
						downpayment = dptmin;
					}
				}

				
				//初期表示または、支払い回数変更の場合はメッセージ無効
				if(mode=="nomessage" || mode=="installments_change"){
					message = "";
				}
			}else{
				// 中古車の場合
				// yeardiff:経過年数
				// useddatea: 何年未満を取扱いするか？
				if(useddata[chg_installments(installments)]['yeardiff'] > getYearDiff($("#tourokuyear").val(),$("#tourokumonth").val(),$("#usedcar_year").val(),$("#usedcar_month").val())){
					// 残価
					//lptmax = kirisute(10000,useddata[installments]['maxlptrate']/100.0 * price);
					lptmax = getmaxwplpt(installments,"used");
					//lptmin = kiriage(10000,useddata[installments]['minlptrate']/100.0 * price);
					lptmin = getminwplpt(installments,"used");
					
					// max,minでフィルタリング
					if(lastpayment > lptmax){
						if(lptmax <= 0){
							lptmax = 0;
						}
						message += "残価の上限は￥"+number_format(lptmax)+"です.";
						lastpayment = lptmax;
					}
					if(lastpayment < lptmin){
						if(lptmin <= 0){
							lptmin = 0;
						}else{
							message += "残価の下限は￥"+number_format(lptmin)+"です.";
						}
						lastpayment = lptmin;
					}
					
					// ボーナス加算額 bonuspayment
					//bonuscnt = installments / 6;
					//bonuscnt = installments / 6;
					if(bonuscnt > 0){
						bptmax = kirisute(100,(loanprincipal - lastpayment) * bptrateArr[0]['maxrate'] / 100.0 / bonuscnt);
					
						// bptmax時に、ローン元金が下限になっていないかを確認する
						var resultArr = [];
						var guard_flg = 0;		// 0:ガードなし 1:ガード引っかかった
						var guardArr = [];
						
						// 演算条件を再設定
						var tempArr = [];
						tempArr['rate'] =  conditionArr['selectedrate'];
						
						while(1){
							tempArr['installments'] = installments;
							tempArr['pricetax'] = pricetax;
							tempArr['optiontotal'] = optiontotal;
							tempArr['downpayment'] = downpayment;
							tempArr['lastpayment'] = lastpayment;
							tempArr['sonota'] = sonota;
							tempArr['taxtotal'] = taxtotal;
							
							
							tempArr['bonuspayment'] = bptmax;
							
							resultArr = loancalc(plan,tempArr);
		
							// さらにガードをする
							// 全てのファイナンスプラン　→　月額3000円未満ガード
							/*
							if(resultArr['monthlypayment'] < 3000){
								guard_flg = 1;		// 計算NG
								bptmax = 0;
								break;
							}
							*/
							
							// 新車WP・SWP　→　据置検証シート（元本充当額）によるガード
							// 元本充当額ガード
							
							guardArr['rate'] = conditionArr['rate'];
							guardArr['loanprincipal'] = resultArr['loanprincipal'];
							guardArr['monthlypayment'] = resultArr['monthlypayment'];
							guardArr['firstpayment'] = resultArr['firstpayment'];
							guardArr['bonuspayment'] = conditionArr['bonuspayment'];
							guardArr['installments'] = conditionArr['installments'];
							guardArr['lastpayment'] = conditionArr['lastpayment'];
							
							guard_flg = downpayment_check(guardArr);
							
							if(guard_flg == 0 && resultArr['monthlypayment'] >= 3000){
								// ガード通った場合は終了！
								break;
							}else{
								bptmax -= 100;
							}
						}
						bptmin = 0;
					}else{
						bptmax = 0;
						bptmin = 0;
					}
					if(bptmax < 0){
						// 元本充当額計算で通らない場合は、必ずbptmax<0になる！
						// 頭金をゼロにしてチェック強制終了
						downpayment = 0;
						loanprincipal = total2 - downpayment;
						
						// あいまいエラーメッセージを格納
						message += "ローン元金下限を下回っています。入力条件をご確認ください.";
					}else{
						// max,minでフィルタリング
						if(bonuspayment > bptmax){
							bonuspayment = bptmax;
							// ボーナス加算が負にならないようにガード
							if(bptmax == 0){
								message += "ボーナス月加算は無効です.";
							}else{
								message += "ボーナス加算の上限は￥"+number_format(bptmax)+"です.";
							}
						}
						if(bonuspayment < bptmin){
							// 下限がゼロの場合は、メッセージ非表示
							if(bptmin <= 0){
								bptmin = 0;
							}else{
								message += "ボーナス加算の下限は￥"+number_format(bptmin)+"です.";
							}
							bonuspayment = bptmin;
						}
						
						conditionArr['bonuspayment'] = bonuspayment;
							
							
						//////////////////////////////////////////////////////////////////////////////
						/*
							ローン元金ガードについて
							
							全てのファイナンスプラン　→　月額3000円未満ガード
							新車WP・SWP　→　据置検証シート（元本充当額）によるガード
							新車・中古車STD　→　ローン元金30万円未満ガード
							中古車WP　→　元本充当額ガード									
						
						*/
						//////////////////////////////////////////////////////////////////////////////
						// 演算条件を再設定
						conditionArr['rate'] = conditionArr['selectedrate'];
						var resultArr = loancalc(plan,conditionArr);
						var guard_flg = 0;		// 0:ガードなし 1:ガード引っかかった
						// var lppmin = kiriage(10000,useddata[conditionArr['installments']]['minlpprate']*g_u_carArr['price']/100); 2015/4/10 1万円単位へ切り上げ→1円未満切り捨て
						var lppmin = kiriage(1,useddata[chg_installments(conditionArr['installments'])]['minlpprate']*g_u_carArr['price']/100);		
						// さらにガードをする
						// 全てのファイナンスプラン　→　月額3000円未満ガード
						if(resultArr['monthlypayment'] < 3000){
							guard_flg = 1;		// 計算NG
						}else{
						
							// 新車WP・SWP　→　据置検証シート（元本充当額）によるガード
							// 元本充当額ガード
							var guardArr = new Array();
							
							guardArr['rate'] = conditionArr['rate'];
							guardArr['loanprincipal'] = resultArr['loanprincipal'];
							guardArr['monthlypayment'] = resultArr['monthlypayment'];
							guardArr['firstpayment'] = resultArr['firstpayment'];
							guardArr['bonuspayment'] = conditionArr['bonuspayment'];
							guardArr['installments'] = conditionArr['installments'];
							guardArr['lastpayment'] = conditionArr['lastpayment'];
							
							guard_flg = downpayment_check(guardArr);
						}
						
						
						if(guard_flg){
							// ローン元金下限ガードで引っかかっているので、メッセージを表示して、頭金をデフォルト(ゼロ）にする
							// 頭金・ローン元金を強制的に変更する
							downpayment = 0;
							loanprincipal = total2 - downpayment;
							
							// あいまいエラーメッセージを格納
							message += "ローン元金下限を下回っています。入力条件をご確認ください.";
						}
					}
						
					// 頭金のガードは、中古車は不要
					//if(_newused=="new"){
					
						// 頭金 downpayment
						dptmin = 0;
						dptmax = total2 - lppmin;
		
						// max,minでフィルタリング
						if(downpayment > dptmax){
							if(dptmax <= 0){
								dptmax = 0;
							}
							message += "頭金の上限は￥"+number_format(dptmax+zansai)+"です.";
							downpayment = dptmax;
						}
						if(downpayment < dptmin){
							if(dptmin <= 0){
								dptmin = 0;
							}else{
								message += "頭金の下限は￥"+number_format(dptmin+zansai)+"です.";
							}
							downpayment = dptmin;
						}
					//}
					
				}else{
					message = "その年式の中古車は取り扱いできません";
					bptmax = 0;
					bptmin = 0;
					lptmax = 0;
					lptmin = 0;
					dptmax = 0;
					dptmin = 0;
				}
			}						
				
			//JSON文字列作成
			jsonArr = {
							"selectedrate":conditionArr['selectedrate'],
							"normalrate":normalrate,
							"lowrate":lowrate,
							"innerrate":innerrate,			// rate for salesman mode 2012.02.08 add by morita
							"bptmax":bptmax,
							"bptmin":bptmin,
							"bonuspayment":bonuspayment,
							"lptmax":lptmax,
							"lptmin":lptmin,
							"lastpayment":lastpayment,
							"dptmax":dptmax,
							"dptmin":dptmin,
							"downpayment":downpayment,
							"loanprincipal":loanprincipal,	// 2014.01.10 add by morita
							"installments":installments,
							"maxmile":0,					// 2012.02.07 add by morita
							"milage":0,					// 2012.02.07 add by morita
							"message":message
						};
		break;
		case "swp":
			// SWPの頭金、ボーナス加算額、金利データを取得する（残価は入力欄なし）
			//rateArr = this->Rate->find(array("patternid":carArr['Car']['swpratepattern'],"installments":installments));
			bptrateArr = findFromDBArr(Bptrates,{patternid:carArr['swpbptpattern'],installments:installments});
			lpprateArr = findFromDBArr(Lpprates,{patternid:carArr['swplpppattern'],installments:installments});

			
			// メッセージ初期化 by morita 2011.11.05
			message = "";
			
			
			// lastpaymentを更新 by morita 2011.11.05
			var lastpayment = getswplpt(installments)*1;
			
			/*
			
			//実質年率
			//rate = rateArr['Rate']['rate'];
			//lowrate = rateArr['Rate']['lowrate'];
			//innerrate = rateArr['Rate']['innerrate'];		// セールスマン向けの金利 2012.02.09 by morita
							
			// ローン元金 loanprincipal
			lppmax = kirisute(1,lpprateArr[0]['maxrate'] / 100.0 * pricetax + taxtotal + optiontotal);
			//lppmin = kiriage(10000,lpprateArr[0]['minrate'] / 100.0 * price);
			lppmin = 0;		// 元本充当額でガード

			// max,minでフィルタリング
			if(loanprincipal > lppmax){
				loanprincipal = lppmax;
			}
			if(loanprincipal < lppmin){
				loanprincipal = lppmin;
			}
			
			// ボーナス加算額 bonuspayment
			//bonuscnt = installments / 6;
			if(bonuscnt > 0){
				bptmax = kirisute(100,(loanprincipal - lastpayment) * bptrateArr[0]['maxrate'] / 100.0 / bonuscnt);
			}else{
				bptmax = 0;
			}
			bptmin = 0;

			// max,minでフィルタリング
			if(bonuspayment > bptmax){
				bonuspayment = bptmax;
				// ボーナス加算が負にならないようにガード
				if(bptmax < 0){
					bptmax = 0;
				}
				message += "ボーナス加算の上限は￥"+number_format(bptmax)+"です.";
			}
			if(bonuspayment < bptmin){
				bonuspayment = bptmin;
				// 下限がゼロの場合は、メッセージ非表示
				if(bptmin <= 0){
					bptmin = 0;
				}else{
					message += "ボーナス加算の下限は￥"+number_format(bptmin)+"です.";
				}
			}
			*/
			// ボーナス加算額 bonuspayment
			//bonuscnt = installments / 6;
			if(bonuscnt > 0){
				if(bptrateArr[0]){
					bptmax = kirisute(100,(loanprincipal - lastpayment) * bptrateArr[0]['maxrate'] / 100.0 / bonuscnt);
				}else{
					bptmax = 99999999;
				}
				
				
				// bptmax時に、ローン元金が下限になっていないかを確認する
				var resultArr = [];
				var guard_flg = 0;		// 0:ガードなし 1:ガード引っかかった
				var guardArr = [];
				
				// 演算条件を再設定
				var tempArr = [];
				tempArr['rate'] =  conditionArr['selectedrate'];
				
				while(1){
					tempArr['installments'] = installments;
					tempArr['pricetax'] = pricetax;
					tempArr['optiontotal'] = optiontotal;
					tempArr['downpayment'] = downpayment;
					tempArr['lastpayment'] = lastpayment;
					tempArr['sonota'] = sonota;
					tempArr['taxtotal'] = taxtotal;
					
					
					tempArr['bonuspayment'] = bptmax;
					
					resultArr = loancalc(plan,tempArr);

					// さらにガードをする
					// 全てのファイナンスプラン　→　月額3000円未満ガード
					if(guard_flg == 0 && resultArr['monthlypayment'] >= 3000){
						guard_flg = 1;		// 計算NG
						//bptmax = 0;
						//break;
					}
					
					// 新車WP・SWP　→　据置検証シート（元本充当額）によるガード
					// 元本充当額ガード
					
					guardArr['rate'] = conditionArr['rate'];
					guardArr['loanprincipal'] = resultArr['loanprincipal'];
					guardArr['monthlypayment'] = resultArr['monthlypayment'];
					guardArr['firstpayment'] = resultArr['firstpayment'];
					guardArr['bonuspayment'] = conditionArr['bonuspayment'];
					guardArr['installments'] = conditionArr['installments'];
					guardArr['lastpayment'] = conditionArr['lastpayment'];
					
					guard_flg = downpayment_check(guardArr);
					
					if(guard_flg == 0){
						// ガード通った場合は終了！
						break;
					}else{
						bptmax -= 100;
					}
				}
				bptmin = 0;
			}else{
				bptmax = 0;
				bptmin = 0;
			}
			
			if(bptmax < 0){
				// 元本充当額計算で通らない場合は、必ずbptmax<0になる！
				// 頭金をゼロにしてチェック強制終了
				downpayment = 0;
				loanprincipal = total2 - downpayment;
				
				// あいまいエラーメッセージを格納
				message += "ローン元金下限を下回っています。入力条件をご確認ください.";
			}else{
				// max,minでフィルタリング
				if(bonuspayment > bptmax){
					if(bptmax<0){
						bonuspayment = 0;
					}else{
						bonuspayment = bptmax;
					}
					// ボーナス加算が負にならないようにガード
					if(bptmax <= 0){
						message += "ボーナス月加算は無効です.";
					}else{
						message += "ボーナス加算の上限は￥"+number_format(bptmax)+"です.";
					}
				}
				if(bonuspayment < bptmin){
					// 下限がゼロの場合は、メッセージ非表示
					if(bptmin <= 0){
						bptmin = 0;
					}else{
						message += "ボーナス加算の下限は￥"+number_format(bptmin)+"です.";
					}
					bonuspayment = bptmin;
				}
				
				conditionArr['bonuspayment'] = bonuspayment;
				
				
				//////////////////////////////////////////////////////////////////////////////
				/*
					ローン元金ガードについて
					
					全てのファイナンスプラン　→　月額3000円未満ガード
					新車WP・SWP　→　据置検証シート（元本充当額）によるガード
					新車・中古車STD　→　ローン元金30万円未満ガード
					中古車WP　→　元本充当額ガード
				*/
				//////////////////////////////////////////////////////////////////////////////
				// 演算条件を再設定
				conditionArr['rate'] = conditionArr['selectedrate'];
				var resultArr = loancalc(plan,conditionArr);
				var guard_flg = 0;		// 0:ガードなし 1:ガード引っかかった
	
				// さらにガードをする
				// 全てのファイナンスプラン　→　月額3000円未満ガード
				if(resultArr['monthlypayment'] < 3000){
					guard_flg = 1;		// 計算NG
				}else{
				
					// 新車WP・SWP　→　据置検証シート（元本充当額）によるガード
					// 元本充当額ガード
					var guardArr = new Array();
					
					guardArr['rate'] = conditionArr['rate'];
					guardArr['loanprincipal'] = resultArr['loanprincipal'];
					guardArr['monthlypayment'] = resultArr['monthlypayment'];
					guardArr['firstpayment'] = resultArr['firstpayment'];
					guardArr['bonuspayment'] = conditionArr['bonuspayment'];
					guardArr['installments'] = conditionArr['installments'];
					guardArr['lastpayment'] = conditionArr['lastpayment'];
					
					guard_flg = downpayment_check(guardArr);
				}
				
				
				if(guard_flg){
					// ローン元金下限ガードで引っかかっているので、メッセージを表示して、頭金をデフォルト(ゼロ）にする
					// 頭金・ローン元金を強制的に変更する
					downpayment = 0;
					loanprincipal = total2 - downpayment;
					
					// あいまいエラーメッセージを格納
					message += "ローン元金下限を下回っています。入力条件をご確認ください.";
				}
	
				// 頭金 downpayment
				dptmin = 0;
				dptmax = total2 - lppmin;
	
				// max,minでフィルタリング
				if(downpayment > dptmax){
					if(dptmax <= 0){
						dptmax = 0;
					}
					message += "頭金の上限は￥"+number_format(dptmax+zansai)+"です.";
					downpayment = dptmax;
				}
				if(downpayment < dptmin){
					if(dptmin <= 0){
						dptmin = 0;
					}else{
						message += "頭金の下限は￥"+number_format(dptmin+zansai)+"です.";
					}
					downpayment = dptmin;
				}
			}
			
			//初期表示の場合
			if(mode=="nomessage"){
			//初期表示の場合はメッセージ無効
				message = "";
			}						
			
			//JSON文字列作成
			jsonArr = {
							"selectedrate":conditionArr['selectedrate'],
							"normalrate":normalrate,
							"lowrate":lowrate,
							"innerrate":innerrate,			// rate for salesman mode 2012.02.08 add by morita
							"bptmin":bptmin,
							"bptmax":bptmax,
							"bonuspayment":bonuspayment,
							"lptmax":lastpayment,
							"lptmin":lastpayment,
							"lastpayment":lastpayment,
							"dptmax":dptmax,
							"dptmin":dptmin,
							"downpayment":downpayment,
							"loanprincipal":loanprincipal,	// 2014.01.10 add by morita
							"installments":installments,
							"maxmile":0,					// 2012.02.07 add by morita
							"milage":0,					// 2012.02.07 add by morita
							"message":message
						};
		break;
		case "std":
			// STDの頭金、ボーナス加算額、金利データを取得する（残価は入力欄なし）
			//rateArr = this->Rate->find(array("patternid"=>carArr['Car']['stdratepattern'],"installments"=>installments));
			bptrateArr = findFromDBArr(Bptrates,{patternid:carArr['stdbptpattern'],installments:installments});
			lpprateArr = findFromDBArr(Lpprates,{patternid:carArr['stdlpppattern'],installments:installments});
			
			//初期表示の場合
			if(mode==false){
			//初期表示の場合はメッセージ無効
				message = "";
			}
			
			if(plusmode && _newused=="new"){
				// プラスは非対応
				message = "このプランは対応していません";
				conditionArr['selectedrate'] = "-";
				bptmax = 0;
				bptmin = 0;
				lptmax = 0;
				lptmin = 0;
				dptmax = 0;
				dptmin = 0;
			}else{					
		
				// ローン元金 loanprincipal
				if(lpprateArr[0]){
					lppmax = kirisute(1,1*lpprateArr[0]['maxrate'] / 100.0 * pricetax + taxtotal + optiontotal + sonota);
					lppmin = kiriage(10000,1*lpprateArr[0]['minrate'] / 100.0 * price);
				}else{
					lppmax = 99999999;
					lppmin = 0;
				}
				
				if(mode != "new"){
					// ローン元金下限は30万円
					if(lppmin < 300000){
						lppmin = 300000;
					}
				}

				// minrateが0の場合は、priceを下限とする 2011.11.06 by morita
				if(lpprateArr[0]){
					if(lpprateArr[0]['minrate'] == 0){
						lppmin = 1*lpprateArr[0]['price'];
					}
				}else{
					lppmin = 0;
				}


				// max,minでフィルタリング
				if(loanprincipal > lppmax){
					loanprincipal = lppmax;
				}
				if(loanprincipal < lppmin){
					loanprincipal = lppmin;
				}
				
				
				// 頭金のガードは、中古車は不要
				//if(_newused=="new"){
					// 頭金 downpayment
					dptmin = 0;
					dptmax = total2 - lppmin;
					
					// max,minでフィルタリング
					if(downpayment > dptmax){
						if(dptmax <= 0){
							dptmax = 0;
						}
						message += "頭金の上限は￥"+number_format(dptmax+zansai)+"です.";
						downpayment = dptmax;
					}
					if(downpayment < dptmin){
						if(dptmin <= 0){
							dptmin = 0;
						}else{
							message += "頭金の下限は￥"+number_format(dptmin+zansai)+"です.";
						}
						downpayment = dptmin;
					}
				//}
				
				// 残価はゼロ
				lastpayment = 0;
				
				
				/* 2014.05.07 ボーナス加算MAXに月額3000円ガードを盛り込む
				// ボーナス加算額 bonuspayment
				//bonuscnt = installments / 6;
				if(bonuscnt > 0){
					// 3回払いの場合は、Webシミュレーションでは、ボーナス加算無しだが、ローン計算シートでは、65%になる
					if(installments == 3){
						bptmax = kirisute(100,(loanprincipal - lastpayment) * 65 / 100.0 / bonuscnt);
					}else{
						if(bptrateArr[0]){
							bptmax = kirisute(100,(loanprincipal - lastpayment) * 1*bptrateArr[0]['maxrate'] / 100.0 / bonuscnt);
						}else{
							bptmax = 99999999;
						}
					}
				}else{
					bptmax = 0;
				}
				bptmin = 0;
				*/
				
				
				
				// ボーナス加算額 bonuspayment
				//bonuscnt = installments / 6;
				if(bonuscnt > 0){
					// 3回払いの場合は、Webシミュレーションでは、ボーナス加算無しだが、ローン計算シートでは、65%になる
					if(installments == 3){
						bptmax = kirisute(100,(loanprincipal - lastpayment) * 65 / 100.0 / bonuscnt);
					}else{
						if(bptrateArr[0]){
							bptmax = kirisute(100,(loanprincipal - lastpayment) * 1*bptrateArr[0]['maxrate'] / 100.0 / bonuscnt);
						}else{
							bptmax = 99999999;
						}
					}
					
					
					// bptmax時に、ローン元金が下限になっていないかを確認する
					var resultArr = [];
					var guard_flg = 0;		// 0:ガードなし 1:ガード引っかかった
					var guardArr = [];
					
					// 演算条件を再設定
					var tempArr = [];
					tempArr['rate'] =  conditionArr['selectedrate'];
					
					while(1){
						tempArr['installments'] = installments;
						tempArr['pricetax'] = pricetax;
						tempArr['optiontotal'] = optiontotal;
						tempArr['downpayment'] = downpayment;
						tempArr['lastpayment'] = lastpayment;
						tempArr['sonota'] = sonota;
						tempArr['taxtotal'] = taxtotal;
						
						
						tempArr['bonuspayment'] = bptmax;
						
						resultArr = loancalc(plan,tempArr);
	
						
						if(resultArr['monthlypayment'] >= 3000){
							// ガード通った場合は終了！
							break;
						}else{
							bptmax -= 100;
						}
					}
					bptmin = 0;
				}else{
					bptmax = 0;
					bptmin = 0;
				}
				
				
				if(bptmax < 0){
					// 元本充当額計算で通らない場合は、必ずbptmax<0になる！
					// 頭金をゼロにしてチェック強制終了
					downpayment = 0;
					loanprincipal = total2 - downpayment;
					
					// あいまいエラーメッセージを格納
					message += "ローン元金下限を下回っています。入力条件をご確認ください.";
				}else{
					// max,minでフィルタリング
					if(bonuspayment > bptmax){
						if(bptmax<0){
							bonuspayment = 0;
						}else{
							bonuspayment = bptmax;
						}
						// ボーナス加算が負にならないようにガード
						if(bptmax <= 0){
							message += "ボーナス月加算は無効です.";
						}else{
							message += "ボーナス加算の上限は￥"+number_format(bptmax)+"です.";
						}
					}
					if(bonuspayment < bptmin){
						// 下限がゼロの場合は、メッセージ非表示
						if(bptmin <= 0){
							bptmin = 0;
						}else{
							message += "ボーナス加算の下限は￥"+number_format(bptmin)+"です.";
						}
						bonuspayment = bptmin;
					}
					
					//conditionArr['bonuspayment'] = bonuspayment;
					
					
					//////////////////////////////////////////////////////////////////////////////
					/*
						ローン元金ガードについて
						
						全てのファイナンスプラン　→　月額3000円未満ガード
						新車WP・SWP　→　据置検証シート（元本充当額）によるガード
						新車・中古車STD　→　ローン元金30万円未満ガード
						中古車WP　→　元本充当額ガード
					*/
					//////////////////////////////////////////////////////////////////////////////
					// 演算条件を再設定
					conditionArr['rate'] = conditionArr['selectedrate'];
					var resultArr = loancalc(plan,conditionArr);
					var guard_flg = 0;		// 0:ガードなし 1:ガード引っかかった
		
					// さらにガードをする
					// 全てのファイナンスプラン　→　月額3000円未満ガード
					if(resultArr['monthlypayment'] < 3000){
						guard_flg = 1;		// 計算NG
					}else{
					
						/*
						// 新車WP・SWP　→　据置検証シート（元本充当額）によるガード
						// 元本充当額ガード
						var guardArr = new Array();
						
						guardArr['rate'] = conditionArr['rate'];
						guardArr['loanprincipal'] = resultArr['loanprincipal'];
						guardArr['monthlypayment'] = resultArr['monthlypayment'];
						guardArr['firstpayment'] = resultArr['firstpayment'];
						guardArr['bonuspayment'] = conditionArr['bonuspayment'];
						guardArr['installments'] = conditionArr['installments'];
						guardArr['lastpayment'] = conditionArr['lastpayment'];
						
						guard_flg = downpayment_check(guardArr);
						*/
					}
					
					
					if(guard_flg){
						// ローン元金下限ガードで引っかかっているので、メッセージを表示して、頭金をデフォルト(ゼロ）にする
						// 頭金・ローン元金を強制的に変更する
						downpayment = 0;
						loanprincipal = total2 - downpayment;
						
						// あいまいエラーメッセージを格納
						message += "ローン元金下限を下回っています。入力条件をご確認ください.";
					}
		
					// 頭金 downpayment
					dptmin = 0;
					dptmax = total2 - lppmin;
		
					// max,minでフィルタリング
					if(downpayment > dptmax){
						if(dptmax <= 0){
							dptmax = 0;
						}
						message += "頭金の上限は￥"+number_format(dptmax+zansai)+"です.";
						downpayment = dptmax;
					}
					if(downpayment < dptmin){
						if(dptmin <= 0){
							dptmin = 0;
						}else{
							message += "頭金の下限は￥"+number_format(dptmin+zansai)+"です.";
						}
						downpayment = dptmin;
					}
				}





			}
			
			
			//初期表示の場合
			if(mode=="nomessage"){
			//初期表示の場合はメッセージ無効
				message = "";
			}						
			
			//JSON文字列作成
			jsonArr = {
							"selectedrate":conditionArr['selectedrate'],
							"normalrate":normalrate,
							"lowrate":lowrate,
							"innerrate":innerrate,			// rate for salesman mode 2012.02.08 add by morita
							"bptmin":bptmin,
							"bptmax":bptmax,
							"bonuspayment":bonuspayment,
							//"lptmax":lptmax,
							//"lptmin":lptmin,
							"lastpayment":lastpayment,
							"dptmax":dptmax,
							"dptmin":dptmin,
							"downpayment":downpayment,
							"loanprincipal":loanprincipal,	// 2014.01.10 add by morita
							"installments":installments,
							"maxmile":0,					// 2012.02.07 add by morita
							"milage":0,					// 2012.02.07 add by morita
							"message":message
						};
		break;
		case "sup":
			// SUPの頭金、ボーナス加算額、金利データを取得する（残価は入力欄なし）
			//rateArr = this->Rate->find(array("patternid"=>carArr['Car']['stdratepattern'],"installments"=>installments));
			bptrateArr = findFromDBArr(Bptrates,{patternid:carArr['supbptpattern'],installments:installments});
			lpprateArr = findFromDBArr(Lpprates,{patternid:carArr['suplpppattern'],installments:installments});
			
			//初期表示の場合
			if(mode==false){
			//初期表示の場合はメッセージ無効
				message = "";
			}
			
			if(plusmode && _newused=="new"){
				// プラスは非対応
				message = "このプランは対応していません";
				conditionArr['selectedrate'] = "-";
				bptmax = 0;
				bptmin = 0;
				lptmax = 0;
				lptmin = 0;
				dptmax = 0;
				dptmin = 0;
			}else{					
		
				// ローン元金 loanprincipal
				if(lpprateArr[0]){
					lppmax = kirisute(1,1*lpprateArr[0]['maxrate'] / 100.0 * pricetax + taxtotal + optiontotal + sonota);
					lppmin = kiriage(10000,1*lpprateArr[0]['minrate'] / 100.0 * price);
				}else{
					lppmax = 99999999;
					lppmin = 0;
				}
				
				if(mode != "new"){
					// ローン元金下限は30万円
					if(lppmin < 300000){
						lppmin = 300000;
					}
				}

				// minrateが0の場合は、priceを下限とする 2011.11.06 by morita
				if(lpprateArr[0]){
					if(lpprateArr[0]['minrate'] == 0){
						lppmin = 1*lpprateArr[0]['price'];
					}
				}else{
					lppmin = 0;
				}


				// max,minでフィルタリング
				if(loanprincipal > lppmax){
					loanprincipal = lppmax;
				}
				if(loanprincipal < lppmin){
					loanprincipal = lppmin;
				}
				
				
				// 頭金のガードは、中古車は不要
				//if(_newused=="new"){
					// 頭金 downpayment
					dptmin = 0;
					dptmax = total2 - lppmin;
					
					// max,minでフィルタリング
					if(downpayment > dptmax){
						if(dptmax <= 0){
							dptmax = 0;
						}
						message += "頭金の上限は￥"+number_format(dptmax+zansai)+"です.";
						downpayment = dptmax;
					}
					if(downpayment < dptmin){
						if(dptmin <= 0){
							dptmin = 0;
						}else{
							message += "頭金の下限は￥"+number_format(dptmin+zansai)+"です.";
						}
						downpayment = dptmin;
					}
				//}
				
				// 残価はゼロ
				lastpayment = 0;
				
				
				/* 2014.05.07 ボーナス加算MAXに月額3000円ガードを盛り込む
				// ボーナス加算額 bonuspayment
				//bonuscnt = installments / 6;
				if(bonuscnt > 0){
					// 3回払いの場合は、Webシミュレーションでは、ボーナス加算無しだが、ローン計算シートでは、65%になる
					if(installments == 3){
						bptmax = kirisute(100,(loanprincipal - lastpayment) * 65 / 100.0 / bonuscnt);
					}else{
						if(bptrateArr[0]){
							bptmax = kirisute(100,(loanprincipal - lastpayment) * 1*bptrateArr[0]['maxrate'] / 100.0 / bonuscnt);
						}else{
							bptmax = 99999999;
						}
					}
				}else{
					bptmax = 0;
				}
				bptmin = 0;
				*/
				
				
				
				// ボーナス加算額 bonuspayment
				//bonuscnt = installments / 6;
				if(bonuscnt > 0){
					// 3回払いの場合は、Webシミュレーションでは、ボーナス加算無しだが、ローン計算シートでは、65%になる
					if(installments == 3){
						bptmax = kirisute(100,(loanprincipal - lastpayment) * 65 / 100.0 / bonuscnt);
					}else{
						if(bptrateArr[0]){
							bptmax = kirisute(100,(loanprincipal - lastpayment) * 1*bptrateArr[0]['maxrate'] / 100.0 / bonuscnt);
						}else{
							bptmax = 99999999;
						}
					}
					
					
					// bptmax時に、ローン元金が下限になっていないかを確認する
					var resultArr = [];
					var guard_flg = 0;		// 0:ガードなし 1:ガード引っかかった
					var guardArr = [];
					
					// 演算条件を再設定
					var tempArr = [];
					tempArr['rate'] =  conditionArr['selectedrate'];
					
					while(1){
						tempArr['installments'] = installments;
						tempArr['pricetax'] = pricetax;
						tempArr['optiontotal'] = optiontotal;
						tempArr['downpayment'] = downpayment;
						tempArr['lastpayment'] = lastpayment;
						tempArr['sonota'] = sonota;
						tempArr['taxtotal'] = taxtotal;
						
						
						tempArr['bonuspayment'] = bptmax;
						
						resultArr = loancalc(plan,tempArr);
	
						
						if(resultArr['monthlypayment'] >= 3000){
							// ガード通った場合は終了！
							break;
						}else{
							bptmax -= 100;
						}
					}
					bptmin = 0;
				}else{
					bptmax = 0;
					bptmin = 0;
				}
				
				
				if(bptmax < 0){
					// 元本充当額計算で通らない場合は、必ずbptmax<0になる！
					// 頭金をゼロにしてチェック強制終了
					downpayment = 0;
					loanprincipal = total2 - downpayment;
					
					// あいまいエラーメッセージを格納
					message += "ローン元金下限を下回っています。入力条件をご確認ください.";
				}else{
					// max,minでフィルタリング
					if(bonuspayment > bptmax){
						if(bptmax<0){
							bonuspayment = 0;
						}else{
							bonuspayment = bptmax;
						}
						// ボーナス加算が負にならないようにガード
						if(bptmax <= 0){
							message += "ボーナス月加算は無効です.";
						}else{
							message += "ボーナス加算の上限は￥"+number_format(bptmax)+"です.";
						}
					}
					if(bonuspayment < bptmin){
						// 下限がゼロの場合は、メッセージ非表示
						if(bptmin <= 0){
							bptmin = 0;
						}else{
							message += "ボーナス加算の下限は￥"+number_format(bptmin)+"です.";
						}
						bonuspayment = bptmin;
					}
					
					//conditionArr['bonuspayment'] = bonuspayment;
					
					
					//////////////////////////////////////////////////////////////////////////////
					/*
						ローン元金ガードについて
						
						全てのファイナンスプラン　→　月額3000円未満ガード
						新車WP・SWP　→　据置検証シート（元本充当額）によるガード
						新車・中古車STD　→　ローン元金30万円未満ガード
						中古車WP　→　元本充当額ガード
					*/
					//////////////////////////////////////////////////////////////////////////////
					// 演算条件を再設定
					conditionArr['rate'] = conditionArr['selectedrate'];
					var resultArr = loancalc(plan,conditionArr);
					var guard_flg = 0;		// 0:ガードなし 1:ガード引っかかった
		
					// さらにガードをする
					// 全てのファイナンスプラン　→　月額3000円未満ガード
					if(resultArr['monthlypayment'] < 3000){
						guard_flg = 1;		// 計算NG
					}else{
					
						/*
						// 新車WP・SWP　→　据置検証シート（元本充当額）によるガード
						// 元本充当額ガード
						var guardArr = new Array();
						
						guardArr['rate'] = conditionArr['rate'];
						guardArr['loanprincipal'] = resultArr['loanprincipal'];
						guardArr['monthlypayment'] = resultArr['monthlypayment'];
						guardArr['firstpayment'] = resultArr['firstpayment'];
						guardArr['bonuspayment'] = conditionArr['bonuspayment'];
						guardArr['installments'] = conditionArr['installments'];
						guardArr['lastpayment'] = conditionArr['lastpayment'];
						
						guard_flg = downpayment_check(guardArr);
						*/
					}
					
					
					if(guard_flg){
						// ローン元金下限ガードで引っかかっているので、メッセージを表示して、頭金をデフォルト(ゼロ）にする
						// 頭金・ローン元金を強制的に変更する
						downpayment = 0;
						loanprincipal = total2 - downpayment;
						
						// あいまいエラーメッセージを格納
						message += "ローン元金下限を下回っています。入力条件をご確認ください.";
					}
		
					// 頭金 downpayment
					dptmin = 0;
					dptmax = total2 - lppmin;
		
					// max,minでフィルタリング
					if(downpayment > dptmax){
						if(dptmax <= 0){
							dptmax = 0;
						}
						message += "頭金の上限は￥"+number_format(dptmax+zansai)+"です.";
						downpayment = dptmax;
					}
					if(downpayment < dptmin){
						if(dptmin <= 0){
							dptmin = 0;
						}else{
							message += "頭金の下限は￥"+number_format(dptmin+zansai)+"です.";
						}
						downpayment = dptmin;
					}
				}





			}
			
			
			//初期表示の場合
			if(mode=="nomessage"){
			//初期表示の場合はメッセージ無効
				message = "";
			}						
			
			//JSON文字列作成
			jsonArr = {
							"selectedrate":conditionArr['selectedrate'],
							"normalrate":normalrate,
							"lowrate":lowrate,
							"innerrate":innerrate,			// rate for salesman mode 2012.02.08 add by morita
							"bptmin":bptmin,
							"bptmax":bptmax,
							"bonuspayment":bonuspayment,
							//"lptmax":lptmax,
							//"lptmin":lptmin,
							"lastpayment":lastpayment,
							"dptmax":dptmax,
							"dptmin":dptmin,
							"downpayment":downpayment,
							"loanprincipal":loanprincipal,	// 2014.01.10 add by morita
							"installments":installments,
							"maxmile":0,					// 2012.02.07 add by morita
							"milage":0,					// 2012.02.07 add by morita
							"message":message
						};
		break;
		case "als":
			// ALSの頭金、残価、ボーナス加算額、金利データを取得する
			//rateArr = this->Rate->find(array("patternid"=>carArr['Car']['stdratepattern'],"installments"=>installments));
			bptrateArr = findFromDBArr(Bptrates,{patternid:carArr['alsbptpattern'],installments:installments});
			lpprateArr = findFromDBArr(Lpprates,{patternid:carArr['alslpppattern'],installments:installments});
			
			//初期表示の場合
			if(mode==false){
			//初期表示の場合はメッセージ無効
				message = "";
			}						
			
	
			// 残価　lastpayment
			if(1){
				// セールスマンモードでは、残価MAX=固定残価、MIN＝残価の90%
				// 残価　lastpayment
				lptmax = kirisute(10000,getalslpt(bmst,installments));
				lptmin = kiriage(10000,lptmax*0.9);
				
				// 支払い回数変更時は、残価をMAXにする
				if(mode == "installments_change"){
					lastpayment = lptmax;
				}
			
				// max,minでフィルタリング
				if(lastpayment > lptmax){
					lastpayment = lptmax;
					message += "残価の上限は￥"+number_format(lptmax)+"です.";
				}
				if(lastpayment < lptmin){
					lastpayment = lptmin;
					message += "残価の下限は￥"+number_format(lptmin)+"です.";
				}
			}else{
				// 通常モードでは、固定値
				lastpayment = getalslpt(bmst,installments);
				lptmin = lastpayment;
				lptmax = lastpayment;
			}

			
			// 税込み車両本体価格×料率＋オプション小計＋諸経費小計
			lppmax = lpprateArr['Lpprate']['maxrate'] / 100.0 * pricetax + taxtotal + optiontotal + sonota;
			
			// 税抜き車両本体価格×料率（１万円単位切り上げ）
			lppmin = kiriage(10000,lpprateArr['Lpprate']['minrate'] / 100.0 * price);
			// ローン元金 loanprincipal
			lppmax = lpprateArr['Lpprate']['maxrate'] / 100.0 * pricetax + taxtotal + optiontotal + sonota;
			//lppmin = kiriage(10000,lpprateArr['Lpprate']['minrate'] / 100.0 * price);
			
			// minrateが0の場合は、priceを下限とする 2011.12.14 by morita
			if(lpprateArr['Lpprate']['minrate'] == 0){
				lppmin = lpprateArr['Lpprate']['price'];
			}else{
				lppmin = kiriage(10000,lpprateArr['Lpprate']['minrate'] / 100.0 * price);
			}

			// ローン元金 loanprincipal
			
			// max,minでフィルタリング
			if(loanprincipal > lppmax){
				loanprincipal = lppmax;
			}
			if(loanprincipal < lppmin){
				loanprincipal = lppmin;
			}
			
			
			// ボーナス加算額 bonuspayment
			bonuscnt = installments / 6;
			bptmax = kirisute(1000,(loanprincipal - lastpayment) * bptrateArr['Bptrate']['maxrate'] / 100.0 / bonuscnt);
			// ボーナス加算額がマイナスになる不具合修正 2011.12.14 by morita
			if(bptmax < 0){
				bptmax = 0;
			}
			bptmin = 0;
			
			// 月額が10000円未満にならないようにボーナス加算額上限を調整する 2011.12.21 by morita
			while(1){
				
				conditions = {
						'installments'		: installments,
						'loanprincipal'		: loanprincipal,
						'pricetax'			: conditionArr['pricetax'],
						'price'				: carArr['Car']['price'],
						'automobiletax'		: conditionArr['automobiletax'],
						'acquisitiontax'	: conditionArr['acquisitiontax'],
						'tonnagetax'		: conditionArr['tonnagetax'],
						'insurance'			: conditionArr['insurance'],
						'recycle'			: conditionArr['recycle'],
						'optiontotal'		: conditionArr['optiontotal'],
						'accessory'			: conditionArr['accessory'],
						'taxtotal'			: conditionArr['taxtotal'],
						'reduce_automobiletax'	: carArr['Car']['reduce_automobiletax'],
						'downpayment'		: downpayment,
						'bonuspayment'		: bptmax,
						'lastpayment'		: lastpayment,
						'sonota'			: sonota
					};
				// 金利パターン大幅変更 2012.05.26 by morita
				// 金利初期値を決める
				if(1){
					conditions['rate'] = innerrate;
				}else{
					conditions['rate'] = lowrate;
				}
				
				resultArr = array();
				resultArr = loancalc("als",conditions);
				
				if(resultArr['leasingprice']>= 10000){
					break;
				}else{
					if(bptmax <= 1000){
						bptmax = 0;
						break;
					}else{
						bptmax -= 1000;
						//debug(resultArr['leasingprice']);
						//debug(bptmax);
						
					}
				}
			}
			
			// max,minでフィルタリング
			if(bonuspayment > bptmax){
				bonuspayment = bptmax;
				// ボーナス加算が負にならないようにガード
				if(bptmax < 0){
					bptmax = 0;
				}
				message += "ボーナス加算の上限は￥"+number_format(bptmax)+"です.";
			}
			if(bonuspayment < bptmin){
				bonuspayment = bptmin;
				// 下限がゼロの場合は、メッセージ非表示
				if(bptmin <= 0){
					bptmin = 0;
				}else{
					message += "ボーナス加算の下限は￥"+number_format(bptmin)+"です.";
				}
			}
			
			// 頭金 downpayment
			// 残価MAX,ボーナス加算額MAXにて、月額が3000円になる頭金をあらかじめ算出して、月額が3000円を割らないようにガードをかける
			dptmin = 0;
			dptmax = total2 - lppmin - lastpayment;
			// max,minでフィルタリング
			if(downpayment > dptmax){
				downpayment = dptmax;
				message += "頭金の上限は￥"+number_format(dptmax+zansai)+"です.";
			}
			if(downpayment < dptmin){
				downpayment = dptmin;
				message += "頭金の下限は￥"+number_format(dptmin+zansai)+"です.";
			}
			
			
			//初期表示の場合
			if(mode=="nomessage"){
			//初期表示の場合はメッセージ無効
				message = "";
			}						
			
			//JSON文字列作成
			jsonArr = {
							"selectedrate":conditionArr['selectedrate'],
							"normalrate":normalrate,
							"lowrate":lowrate,
							"innerrate":innerrate,			// rate for salesman mode 2012.02.08 add by morita
							"bptmax":bptmax,
							"bptmin":bptmin,
							"bonuspayment":bonuspayment,
							"lptmax":lptmax,
							"lptmin":lptmin,
							"lppmax":lppmax,
							"lppmin":lppmin,
							"lastpayment":lastpayment,
							"dptmax":dptmax,
							"dptmin":dptmin,
							"downpayment":downpayment,
							"installments":installments,
							"maxmile":0,					// 2012.02.07 add by morita
							"milage":0,					// 2012.02.07 add by morita
							"message":message
						};
			//debug(jsonArr);
		break;
		// 2012.02.06 add by morita
		case "cls":
			// ALSの頭金、残価、ボーナス加算額、金利データを取得する
			bptrateArr = findFromDBArr(Bptrates,{patternid:carArr['clsbptpattern'],installments:installments});
			lpprateArr = findFromDBArr(Lpprates,{patternid:carArr['clslpppattern'],installments:installments});
			
			
			//初期表示の場合
			if(mode==false){
			//初期表示の場合はメッセージ無効
				message = "";
			}						
			
			// 年間走行距離 2012.02.07 add by morita
			maxmile = get_maxmilage(bmst,installments);
			
			if(maxmile < milage){
				milage = maxmile;
				message += "年間走行距離の上限は"+maxmile+"万kmです.";
			}
			
			
			// 残価　lastpayment
			lastpayment = getclslpt(bmst,installments, milage);
			
		
			// 税込み車両本体価格×料率＋オプション小計＋諸経費小計
			lppmax = lpprateArr['Lpprate']['maxrate'] / 100.0 * pricetax + taxtotal + optiontotal + sonota;
			
			// 税抜き車両本体価格×料率（１万円単位切り上げ）
			lppmin = kiriage(10000,lpprateArr['Lpprate']['minrate'] / 100.0 * price);
			// ローン元金 loanprincipal
			lppmax = lpprateArr['Lpprate']['maxrate'] / 100.0 * pricetax + taxtotal + optiontotal + sonota;
			//lppmin = kiriage(10000,lpprateArr['Lpprate']['minrate'] / 100.0 * price);
			
			// minrateが0の場合は、priceを下限とする 2011.12.14 by morita
			if(lpprateArr['Lpprate']['minrate'] == 0){
				lppmin = lpprateArr['Lpprate']['price'];
			}else{
				lppmin = kiriage(10000,lpprateArr['Lpprate']['minrate'] / 100.0 * price);
			}

			// ローン元金 loanprincipal
			
			// max,minでフィルタリング
			if(loanprincipal > lppmax){
				loanprincipal = lppmax;
			}
			if(loanprincipal < lppmin){
				loanprincipal = lppmin;
			}
			
			
			// ボーナス加算額 bonuspayment
			bonuscnt = installments / 6;
			bptmax = kirisute(1000,(loanprincipal - lastpayment) * bptrateArr['Bptrate']['maxrate'] / 100.0 / bonuscnt);
			// ボーナス加算額がマイナスになる不具合修正 2011.12.14 by morita
			if(bptmax < 0){
				bptmax = 0;
			}
			bptmin = 0;
			
			// 月額が10000円未満にならないようにボーナス加算額上限を調整する 2011.12.21 by morita
			while(1){
				
				conditions = {
						'installments'		: installments,
						'loanprincipal'		: loanprincipal,
						'pricetax'			: conditionArr['pricetax'],
						'price'				: carArr['Car']['price'],
						'automobiletax'		: conditionArr['automobiletax'],
						'acquisitiontax'	: conditionArr['acquisitiontax'],
						'tonnagetax'		: conditionArr['tonnagetax'],
						'insurance'			: conditionArr['insurance'],
						'recycle'			: conditionArr['recycle'],
						'optiontotal'		: conditionArr['optiontotal'],
						'accessory'			: conditionArr['accessory'],
						'taxtotal'			: conditionArr['taxtotal'],
						'reduce_automobiletax'	: carArr['Car']['reduce_automobiletax'],
						'downpayment'		: downpayment,
						'bonuspayment'		: bptmax,
						'lastpayment'		: lastpayment,
						'sonota'			: sonota
					};
				// 金利パターン大幅変更 2012.05.26 by morita
				// 金利初期値を決める
				if(1){
					conditions['rate'] = innerrate;
				}else{
					conditions['rate'] = lowrate;
				}

				
				// rate for salesman mode 2012.02.09 by morita
				conditions['rate'] = conditionArr['selectedrate'];
				
				resultArr = array();
				resultArr = loancalc("cls",conditions);
				
				if(resultArr['leasingprice']>= 10000){
					break;
				}else{
					if(bptmax <= 1000){
						bptmax = 0;
						break;
					}else{
						bptmax -= 1000;
						//debug(resultArr['leasingprice']);
						//debug(bptmax);
						
					}
				}
			}
			
			// max,minでフィルタリング
			if(bonuspayment > bptmax){
				bonuspayment = bptmax;
				// ボーナス加算が負にならないようにガード
				if(bptmax < 0){
					bptmax = 0;
				}
				message += "ボーナス加算の上限は￥"+number_format(bptmax)+"です.";
			}
			if(bonuspayment < bptmin){
				bonuspayment = bptmin;
				// 下限がゼロの場合は、メッセージ非表示
				if(bptmin <= 0){
					bptmin = 0;
				}else{
					message += "ボーナス加算の下限は￥"+number_format(bptmin)+"です.";
				}
			}
			
			// 頭金 downpayment
			// 残価MAX,ボーナス加算額MAXにて、月額が3000円になる頭金をあらかじめ算出して、月額が3000円を割らないようにガードをかける
			dptmin = 0;
			dptmax = total2 - lppmin - lastpayment;
			// max,minでフィルタリング
			if(downpayment > dptmax){
				downpayment = dptmax;
				message += "頭金の上限は￥"+number_format(dptmax+zansai)+"です.";
			}
			if(downpayment < dptmin){
				downpayment = dptmin;
				message += "頭金の下限は￥"+number_format(dptmin+zansai)+"です.";
			}
			
			
			//初期表示の場合
			if(mode=="nomessage"){
			//初期表示の場合はメッセージ無効
				message = "";
			}						
			
			//JSON文字列作成
			jsonArr = {
							"selectedrate":conditionArr['selectedrate'],
							"normalrate":normalrate,
							"lowrate":lowrate,
							"innerrate":innerrate,			// rate for salesman mode 2012.02.08 add by morita
							"bptmax":bptmax,
							"bptmin":bptmin,
							"bonuspayment":bonuspayment,
							"lptmax":lastpayment,
							"lptmin":lastpayment,
							"lppmax":lppmax,
							"lppmin":lppmin,
							"lastpayment":lastpayment,
							"dptmax":dptmax,
							"dptmin":dptmin,
							"downpayment":downpayment,
							"installments":installments,
							"maxmile":maxmile,					// 2012.02.07 add by morita
							"milage":milage,					// 2012.02.07 add by morita
							"message":message
						};
			//debug(jsonArr);
		break;
	}
	
	// プラスモード時は、低金利対応かどうかをきちんと判定する
	//if(plusmode && _newused=="new" && (plan=="wp" || plan=="swp")){
	/*
	if(plusmode && _newused=="new"){
		var selectedmode = $("#rate").val();
		
		if(selectedmode == "lowrate"){
			// 低金利時
			if(lowrate == normalrate){
				jsonArr['message'] += "低金利には対応していないプランです";
				jsonArr['selectedrate'] = "-";
				//planNameArr[plan] = "";
			}
		}
		if(selectedmode == "innerrate"){
			// 低金利時
			if(innerrate == lowrate){
				jsonArr['message'] += "インナー金利には対応していないプランです";
				jsonArr['selectedrate'] = "-";
				//planNameArr[plan] = "";
			}
		}
	}
	*/
		
			
	
	return jsonArr;
}

/***********************************************************************************

	作成者	pc-otasuke.jp morita
	
	関数名	_getmaxwplpt()
	
	引数	bmst:bmst値
			installments:支払い回数
			
			
	戻り値	残価
	
	機能	
		WP残価算出方法 （2014年1月〜）
		
		①SWP対象モデル（swpmodelフラグが1または2）
		
		　WP3年残価上限＝SWP3年残価＋5%　（swplptrate+5%）
		 　WP2年、4年、5年上限の階段パーセンテージは＋9、ー７、ー14（WP3年上限を基準として）
		
		②SWP対象外モデル（swpmodelフラグが0）
		
		　WP3年残価上限＝OP（オープンエンドリース）3年残価＋5%　（swplptrate+alslptpattern+5%）
		 　WP2年、4年、5年上限の階段パーセンテージは＋9、ー７、ー14（WP3年上限を基準として）

************************************************************************************/
function getmaxwplpt(installments,_mode){

	var price = carArr['price']*1;
	
	var rate = getmaxwplptrate(installments,_mode);
	
	var lastpayment;
	
	if(_mode=="new"){
		
		lastpayment = kirisute(10000,rate * (price+1*(get_makeroptiontotal())) / 100.0);
	}else{
		// 中古の場合
		// 残価
		lastpayment = kirisute(10000,rate/100.0 * price);
	}
	
	return lastpayment;
}

function getmaxwplptrate(installments,_mode){

	var price = carArr['price']*1;
	
	var rate;
	
	
	if(_mode=="new"){
		
/*
		// 変則のクラス名
		var classArr = ['A-Class','B-Class','C-Class Sedan','C-Class Stationwagon','C-Class Coupe','CLA-Class'];
		
		var tempArr = [];
		
		if(classArr.indexOf(carArr['classname'],0) != -1){
			// 変則
			p = 10;
			tempArr = lptratedataArr9;
		}else{
			// 通常
			p = 5;
			tempArr = lptratedataArr7;
		}
		
		
		// 残価　lastpayment
		// まず、swpのベース料率を取得する
		var swplptrate = carArr['swplptrate']*1;
		
		
		// 残価は支払い回数のみによる固定値+5
		var lptrate = swplptrate + tempArr[installments] +p*1;
		// 2013.02.04 by morita 残価にメーカーオプション反映
		//lastpayment = this->_kirisute(10000,lptrate * price / 100.0);
*/

		// swpを確認
		var swpmodel = carArr['swpmodel'];
		
		var p;
		var lptrate;
		var lptrateArr_tmp = [];
		
	   // swpのベース料率を取得する
	   var swplptrate = carArr['swplptrate']*1;
	   var alslptpattern = carArr['alslptpattern']*1;
		
		if(swpmodel != 0){
			//　WP3年残価上限＝SWP3年残価＋5%　（swplptrate+5%）
			//　WP2年、4年、5年上限の階段パーセンテージは＋9、ー７、ー14（WP3年上限を基準として）
			p = 5;
			lptrateArr_tmp = lptratedataArr7;
			lptrate = swplptrate + p + lptratedataArr7[installments];
		}else{
			// WP3年残価上限＝OP（オープンエンドリース）3年残価＋5%　（swplptrate+alslptpattern+5%）
			// WP2年、4年、5年上限の階段パーセンテージは＋9、ー７、ー14（WP3年上限を基準として）
			//　WP3年残価上限＝SWP3年残価＋5%　（swplptrate+5%）
			//　WP2年、4年、5年上限の階段パーセンテージは＋9、ー７、ー14（WP3年上限を基準として）
			p = 5;
			lptrateArr_tmp = lptratedataArr7;
			lptrate = swplptrate + alslptpattern + p + lptrateArr_tmp[installments];
		}
		
		rate = lptrate;


		var lastpayment = kirisute(10000,lptrate * (price+1*(get_makeroptiontotal())) / 100.0);
	}else{
		// 中古の場合
		if(useddata[chg_installments(installments)]['yeardiff'] > getYearDiff($("#tourokuyear").val(),$("#tourokumonth").val(),$("#usedcar_year").val(),$("#usedcar_month").val())){
			// 残価
			lastpayment = kirisute(10000,useddata[chg_installments(installments)]['maxlptrate']/100.0 * price);
			
			rate = useddata[chg_installments(installments)]['maxlptrate'];
		}else{
			lastpayment = 0;
			
			rate = 0;
		}
	}
	
	return rate;
}


// wp の残価下限を求める（新車・中古車対応）
function getminwplpt(installments,_mode){
	var price = carArr['price']*1;
	
	var lptmin;
	
	var rate = getminwplptrate(installments,_mode);
	
	lptmin = kiriage(10000,rate*1.0 / 100.0 * price);
	
	return lptmin;
	
}


function getminwplptrate(installments,_mode){
	var rate;
	var lptrateArr = findFromDBArr(Lptrates,{patternid:carArr['wplptpattern'],installments:installments});
	
	if(_mode == "new"){
		if(lptrateArr[0]){
			rate = lptrateArr[0]['minrate'];
		}else{
			rate = 0;
		}
	}else{
		rate = useddata[chg_installments(installments)]['minlptrate'];
	}
	
	return rate;
	
}


function getswplpt(installments){

// 残価　lastpayment
// まず、swpのベース料率を取得する
var swplptrate = carArr['swplptrate']*1.0;
var price = carArr['price']*1;

// 残価は支払い回数のみによる固定値
var lptrate = swplptrate + lptratedataArr6[installments]*1;
// 2013.02.04 by morita 残価にメーカーオプション反映
//lastpayment = this->_kirisute(10000,lptrate * price / 100.0);
var lastpayment = kirisute(10000,lptrate * (price+1*(get_makeroptiontotal())) / 100.0);

return lastpayment;
}


/***********************************************************************************

作成者	pc-otasuke.jp morita

関数名	_getalslpt()

引数	bmst:bmst値
installments:支払い回数
(get変数：optionid optionpriceも参照している）

戻り値	残価

機能	オートリース（オープン）の残価を算出


************************************************************************************/
function getalslptrate(installments){
//carArr = this->Car->findByBmst(bmst);


//2013.04.03 残価算出ロジック変更 by morita
// 変則のクラス名
var classArr = [
				'A-Class',
				'B-Class',
				'C-Class Sedan',
				'C-Class Stationwagon',
				'C-Class Coupe',
				'CLA-Class'
				];
				
//carArr = this->Car->findByBmst(bmst);
var lptArr = new Array();

if($.inArray(carArr['classname'],classArr)){
	// ABCクラスの場合
	var lptrateArr_tmp = lptrateArr7;
}else{
	// ほかのクラス
	var lptrateArr_tmp = lptrateArr7;
}

// 残価　lastpayment
// まず、swpのベース料率を取得する(SWP3年)
var swplptrate = carArr['swplptrate'];
var price = carArr['price'];
// alslptpattern 1,2のみ クローズドエンドリースの場合は3?
// この値は、参照先テーブルパターンではなく、数値として使用するので注意！！
var alslptpattern = carArr['alslptpattern'];

// オートリース3年を算出したものに、階段処理


// パーセンテージ算出
//2013.04.03 残価算出ロジック変更 by morita
// lptrate = swplptrate + this->lptrateArr[installments] + alslptpattern;
var lptrate = 1*swplptrate + 1*alslptpattern + 1*lptrateArr_tmp[installments];


// 特殊オプションが入っていないかを確認する
// 2013.02.04 by morita 残価にメーカーオプション反映
/*
alsoptionprice = 0.0;
for(i=1;i<10;i++){
	// optionid01 optionid02 ... optionid10
	idkey = 'optionid'.sprintf("%02d",i);
	// optionprice01 optionprice02 ... optionprice10
	pricekey = 'optionprice'.sprintf("%02d",i);
	
	if(isset(this->params['url'][idkey])){
		// DBから該当がないかチェック！！
		alsoptionArr = this->Alsoption->findByoptionid(this->params['url'][idkey]);
		
		if(alsoptionArr){
			if(alsoptionArr['Alsoption']['special'] == 1){
				// 125万円の税抜き分をプラスする
				alsoptionprice += this->_kirisute(1,1250000.0/1.05);
			}else{
				// 飛んできた値段をプラスする
				alsoptionprice += this->params['url'][pricekey];
			}
		}
	}
}
*/

// 残価は支払い回数のみによる固定値
//lastpayment = this->_kirisute(10000,lptrate * (price + alsoptionprice) / 100.0);
// 2013.02.04 by morita 残価にメーカーオプション反映
var lastpayment = kirisute(10000,lptrate * (price + get_makeroptiontotal()) / 100.0);


//return lastpayment;
return lptrate;
}

/***********************************************************************************

	作成者	pc-otasuke.jp morita
	
	関数名	_getclslpt()
	
	引数	bmst:bmst値
			installments:支払い回数
			milage:年間走行距離万km
			(get変数：optionid optionpriceも参照している）
			
			
	戻り値	残価
	
	機能	オートリース（クローズ）の残価を算出
			

************************************************************************************/
	   // 2012.02.07 add by morita
       function getclslpt(bmst,installments,milage){
			   
			   clslptrateArr = {
			   							1:0,
										2:-10,
										3:-20,
										4:-25
									};


               // 残価　lastpayment
               // まず、swpのベース料率を取得する
               swplptrate = carArr['Car']['swplptrate'];
               price = carArr['Car']['price'];
               // alslptpattern 1,2のみ クローズドエンドリースの場合は3?
               // この値は、参照先テーブルパターンではなく、数値として使用するので注意！！
               alslptpattern = carArr['Car']['alslptpattern'];
			   
			   // パーセンテージ算出
			   //lptrate = swplptrate + this->lptrateArr[installments] + clslptrateArr[milage];
			   // 残価算出ロジック変更 2013.04.03 by morita
			   lptrate = swplptrate + g_lptrateArr6[installments] + clslptrateArr[milage];
			   
			   // パーセンテージが5%を割った場合は、エラーを返す
			   if(lptrate < 5){
			   		return -1;
				}
				
				// 特殊オプションが入っていないかを確認する
			   	// 2013.02.04 by morita 残価にメーカーオプション反映
				/*
				alsoptionprice = 0.0;
				for(i=1;i<10;i++){
					// optionid01 optionid02 ... optionid10
					idkey = 'optionid'.sprintf("%02d",i);
					// optionprice01 optionprice02 ... optionprice10
					pricekey = 'optionprice'.sprintf("%02d",i);
					
					if(isset(this->params['url'][idkey])){
						// DBから該当がないかチェック！！
						alsoptionArr = this->Alsoption->findByoptionid(this->params['url'][idkey]);
						
						if(alsoptionArr){
							if(alsoptionArr['Alsoption']['special'] == 1){
								// 125万円の税抜き分をプラスする
								alsoptionprice += this->_kirisute(1,1250000.0/1.05);
							}else{
								// 飛んできた値段をプラスする
								alsoptionprice += this->params['url'][pricekey];
							}
						}
					}
				}
				*/

               // 残価は支払い回数のみによる固定値
               //lastpayment = this->_kirisute(10000,lptrate * (price + alsoptionprice) / 100.0);
			   // 2013.02.04 by morita 残価にメーカーオプション反映
               lastpayment = kirisute(10000,lptrate * (price + get_makeroptiontotal()) / 100.0);

			   debug(lptrateArr);
			   debug(installments);
			   debug(lptrate);
			   debug(lastpayment);
			   
               return lastpayment;
       }


function downpayment_check(arr){
	var rate = arr['rate']/100;	// 実質年率
	var addon1;
	var addon2;
	var lastpayment = arr['lastpayment']*1;
	var sueoki_risoku;
	var mikeika_risoku_ryouritsu = new Array();
	var loaninterest;
	var monthly_interest;
	var sueoki_interest;
	var firstpayment = arr['firstpayment']*1;	// 初回支払い金額
	var installments = arr['installments']*1;
	var loanprincipal = arr['loanprincipal']*1;
	var monthlypayment = arr['monthlypayment']*1;
	
	var temp1;
	var temp2;
	var temp3;
	
	
	temp1 = rate/12.0;
	temp2 = 1+temp1;
	temp3 = Math.pow(temp2,installments);
	
	
	// アドオン率
	addon1 = Math.round(10000*((rate/12.0/(1.0-1.0/temp3))*installments-1.0))/10000;
	addon2 = Math.round(rate*(installments+1)/12.0*10000)/10000;
	
	// 据え置き利息
	sueoki_risoku = kirisute(1,lastpayment*addon2);
	
	// 未経過利息料率
	for(num=1;num<=5;num++){
		temp1 = (((installments+1)-num)*((installments+1)-num+1))/((installments+1)*((installments+1)+1));
		mikeika_risoku_ryouritsu[num] = Math.floor(temp1*1000)/1000;
	}
	
	// 毎月支払い利息
	monthly_interest = kirisute(1,(loanprincipal - lastpayment)*addon1);
	
	// 据置支払い利息
	sueoki_interest = kirisute(1,lastpayment*addon2);
	
	// ローン利息合計
	loaninterest = monthly_interest + sueoki_interest;
	
	
	var risoku_zandaka_before = loaninterest;
	var risoku_zandaka;
	var risoku;
	var ganpon_jyuutou;
	var rtn_flg = 0;
	for(i=1;i<=5;i++){
		// 利息残高合計
		risoku_zandaka = Math.round(monthly_interest*mikeika_risoku_ryouritsu[i],0) + Math.round(sueoki_interest*mikeika_risoku_ryouritsu[i],0);
		
		risoku = risoku_zandaka_before - risoku_zandaka;
		
		risoku_zandaka_before = risoku_zandaka;
		
		// 元本充当額 = 毎月支払い額 - 利息額
		if(i==1){
			ganpon_jyuutou = firstpayment - risoku;
		}else{
			ganpon_jyuutou = monthlypayment - risoku;
		}
		if(ganpon_jyuutou > 0){
			// 何もしない
		}else{
			rtn_flg = 1;
		}
	}
	
	return rtn_flg;
}


/*
function get_makeroptiontotal(){
	return 0;
}
*/



// 2進数演算特有の誤差を検出して補正する関数
// 数値を文字列化して、99999999999999999などを元に誤差を検出して補正する
function trimFixed(a) {
	var x = "" + a;
	var m = 0;
	var e = x.length;
	for (var i = 0; i < x.length; i++) {
		var c = x.substring(i, i + 1);
		if (c >= "0" && c <= "9") {
			if (m == 0 && c == "0") {
			} else {
				m++;
			}
		} else if (c == " " || c == "+" || c == "-" || c == ".") {
		} else if (c == "E" || c == "e") {
			e = i;
			break;
		} else {
			return a;
		}
	}

	var b = 1.0 / 3.0;
	var y = "" + b;
	var q = y.indexOf(".");
	var n;
	if (q >= 0) {
		n = y.length - (q + 1);
	} else {
		return a;
	}

	if (m < n) {
		return a;
	}

	var p = x.indexOf(".");
	if (p == -1) {
		return a;
	}
	var w = " ";
	for (var i = e - (m - n) - 1; i >= p + 1; i--) {
		var c = x.substring(i, i + 1);
		if (i == e - (m - n) - 1) {
			continue;
		}
		if (i == e - (m - n) - 2) {
			if (c == "0" || c == "9") {
				w = c;
				continue;
			} else {
				return a;
			}
		}
		if (c != w) {
			if (w == "0") {
				var z = (x.substring(0, i + 1) + x.substring(e, x.length)) - 0;
				return z;
			} else if (w == "9") {
				var z = (x.substring(0, i) + ("" + ((c - 0) + 1)) + x.substring(e, x.length)) - 0;
				return z;
			} else {
				return a;
			}
		}
	}
	if (w == "0") {
		var z = (x.substring(0, p) + x.substring(e, x.length)) - 0;
		return z;
	} else if (w == "9") {
		var z = x.substring(0, p) - 0;
		var f;
		if (a > 0) {
			f = 1;
		} else if (a < 0) {
			f = -1;
		} else {
			return a;
		}
		var r = (("" + (z + f)) + x.substring(e, x.length)) - 0;
		return r;
	} else {
		return a;
	}
}

function addZero(n){
	n=n+"";
	if(n != "-"){
		n = n.split(".")[0]+"."+(n.split(".")[1]+"00").substring(0,2);
	}
	return n;
}



function kiriage(num,value){
	// 10000円未満を切り上げる
	return Math.ceil(value/num)*num;
}

function kirisute(num,value){
	// 1000万円未満を切り捨て
	return Math.floor(value/num)*num;
}

// DBArr から、 keyにvalueが入っているものを抽出する
function findFromDBArr(Arr,conditionArr){
	rtnArr = [];
	var j=0;
	for(var i=0;i<Arr.length;i++){
		var findFlg = 1;
		for(var key in conditionArr){
			if(Arr[i][key] != conditionArr[key]){
				findFlg=0;
			}
		}
		if(findFlg){
			rtnArr[j++] = Arr[i];
		}
	}
	return rtnArr;
}

function is_numeric(num){	
	var rtn = true;
	
	if(num == ""){
		rtn = false;
	}
	
	return rtn;				
}

function number_format(num){
	return num2price(num);
}



//カンマ挿入関数
function num2price(sourceStr) {
	sourceStr = String(sourceStr);
	var destStr = toHankakuNum(sourceStr);
	var tmpStr = "";
	
	if(sourceStr == null){
		destStr = "0";
	}else{
	
		// NAN問題対応
		while (destStr != (tmpStr = destStr.replace(/^([+-]?\d+)(\d\d\d)/,"$1,$2"))) {
			destStr = tmpStr;
		}
	}
	
	
	return destStr;
}



//カンマ削除関数
function price2num(w) {
	//var z = w.replace(/[,a-zA-Z]/g,"");
	w = toHankakuNum(w);
	var z = w.replace(/,/g,"");
	z = Number(z);
						// NaN Infinityの場合はゼロにする
	if ( isNaN(z) || z == Number.POSITIVE_INFINITY || z == Number.NEGATIVE_INFINITY || z==null){
		z=0;
	}
	return (z*1);
}

// 年月成形関数 0000-00
function num2date(sourceStr) {
	var destStr = toHankakuNum(String(sourceStr));
	var tmpStr = "";
	var year;
	var month;
	var day;
	
	sourceStr = String(sourceStr);
	
	if(sourceStr.length < 8){
		destStr = sourceStr;
	}else{
		// NAN問題対応
		year = sourceStr.substr(0,4);
		// 平成に直す
		//year -= 1988;
		if(year > 2013) year = 2013;
		if(year < 0){
			destStr = num2date(datemin);
		}else{
			month = sourceStr.substr(4,2);
			if(month*1>12) month=12;
			day = sourceStr.substr(6.2);
			if(day*1>31) day = 31;
			destStr = year+"年"+month+"月"+day+"日";
		}
	}
	return destStr;
}

// 年月を数字に変更する関数
function date2num(w) {
	//var z = w.replace(/[,a-zA-Z]/g,"");
	w = toHankakuNum(w);
	var z = w.replace(/[,a-zA-Z平成年月日]/g,"");
	//z = Number(z);
	if(isNaN(z) || z==null){
		z=datemin+"";
	}
	var year = z.substr(0,4);
	var month = z.substr(4,2);
	var day = z.substr(6,2);
	
	//year *= 1;
	
	//year += 1988;
	//year += "";
	
	return (year+month+day);
}

function correct_year(){
  var date = new Date("2000/1/1");
  if((2000 - date.getYear()) == 0){
	return 0;
  }else{
	return (2000 - date.getYear());
  }
}

//alert(date.getYear() + "/" + (date.getMonth() + 1) + "/" + date.getDate()); //補正前
//alert((date.getYear() + correct_year()) + "/" + (date.getMonth() + 1) + "/" + date.getDate()); //補正後

// 全角を半角に変換
function toHankakuNum(motoText)
{
	han = "0123456789.,-+";
	zen = "０１２３４５６７８９．，－＋";
	str = "";
	if(motoText){
		for (i=0; i<motoText.length; i++)
		{
			c = motoText.charAt(i);
			n = zen.indexOf(c,0);
			if (n >= 0) c = han.charAt(n);
			str += c;
		}
	}
	return str;
}



// 重複配列要素を取り除く
function array_unique(Arr){
	var storeArr = new Array;
	var ret = new Array;
	i=0;
	f=0;
	while(Arr[i] != null){
		if(Arr[i] != ""){
			if(storeArr[String(Arr[i])]){
			}else{
				storeArr[String(Arr[i])] = 1;
				ret[f]=Arr[i];
				f++;
			}
		}
		i++;
	}
	return ret;
}


// ajaxでキャッシュを防ぐためのタイムスタンプ生成関数
function stamp(){
	var date = new Date();
	
	return date.getTime();
}
				
// 年式文字列から、経過年数を求める
// 例：2013年のとき、2013年式・・・・=3年未満
// 例：2013年のとき、2012年式・・・・=3年未満
// 例：2013年のとき、2011年式・・・・=3年未満
// 例：2013年のとき、2010年式・・・・=3年未満
// 例：2013年のとき、2009年式・・・・=4年未満
// 例：2013年のとき、2008年式・・・・=5年未満
// 
function getYearDiff(year1,month1,year2,month2){
	var today = new Date();
	var year = today.getFullYear();
	var month = today.getMonth()+1;
	var diff;
	
	if(year2 == undefined) year2 = year;
	if(month2 == undefined) month2 = month;
	
	
	// 中古車年式を月まで対応 2014.06.16
	// 文字列をすべて数値に変換
	year1 = year1*1;			// 中古車登録年
	month1 = month1*1;			// 中古車登録月
	year2 = year2*1;			// 初度車登録年
	month2 = month2*1;			// 初度車登録月
	
	
	// 年式文字列の最初はスペースなので要注意！！！！
	diff = year2 - year1 - (month2<month1 ? 1:0);
	
	// 3年以下は3年とする
	//if(diff < 3){
	//	diff = 3;
	//}
	
	if(diff<0) diff=0;
	
	/*
	var year5 = new Date((year*1-5)+"/"+month+"/"+day);
	var year4 = new Date((year*1-4)+"/"+month+"/"+day);
	var year3 = new Date((year*1-3)+"/"+month+"/"+day);
	
	var tourokuday = String(targetdate);
	var temp =tourokuday.substr(0,4)+"/"+tourokuday.substr(4,2)+"/"+tourokuday.substr(6,2);

	
	var registday = new Date(temp);
	
	if(year3<registday){
		diff = 3;
	}else if(year4<registday){
		diff = 4;
	}else if(year5<registday){
		diff = 5;
	}else{
		diff = 6;
	}
	*/
	
	//alert(diff);
	
	return diff;
}

// 2015.08.05 車検到来機能 by morita
function chg_installments(installments){
/*
　　12         ->24（例外処理）
	13　-　24回->24  1...1 1...2 1...3 ...  1...23  2 切り上げ
　　25　-　36回->36
　　37　-　48回->48
　　49　-　60回->60
*/
	
	// 12の場合は、例外的に24になる
	var ins;
	
	if(installments == 12){
		ins = 24;
	}else{
		// 12で割って切り上げる
		ins = 12*Math.ceil(installments/12);
	}
	
	return ins;
}




