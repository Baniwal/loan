// 2014.02.18 中古車名なしでも計算できるように変更
// 2014.02.18 u_pricetax のfocusoutイベントトリガーを汎用トリガーに統合
// 2014.03.14 モデル選択時にボーナス加算額をゼロに
// 2014.03.25 テキストボックス選択時に、カーソルが必ず右に行く対策実施
// 2014.04.01 演算開始時に消費税率デフォルトを変更するように
// 2014.04.29 サービスローンボーナス加算額MAX計算にて、元本充当額ガードを復活・サービスローン頭金MAXの演算を修正
// 2014.06.19 変更点多々・・・
// 2014.06.20 残債が頭金より大きい場合の頭金ガード時に頭金修正方法を変更
// 2014.06.28 WPPキャンペーン　低金利インナーのみに仕様変更
// 2014.08.05 社員用低金利に対応
// 2014.09.26 ボーナスを100円、残価を10000円で丸める処理を追加
// 2015.03.11 車両本体価格が低い場合フリーズするので、ローン元金ガードを追加
// 2015.03.12 中古車のローン元金下限ガードを修正（不等式が逆）
// 2015.05.15 smart EV時、WPPの73回払いをブロック 2015.04.27 by morita
// 2015.09.05 車検到来機能 by morita
// 2016.01.08 次回車検到来月を一か月前に
// 2016.05.25 支払い回数が6の倍数でない場合のボーナス回数不具合修正




/************************************************************************

	ローン計算シートjsファイル







*************************************************************************/
// urlパラメーターを受け取る
var g_param = GetScriptParams();

// グローバル変数
var g_time = getUnixTime();

var g_site_url = "http://mbfj.co-site.jp/loan";

var g_logoutsec = 3600;					// 強制ログアウトするまでの秒数

var g_mode = "new";						// 計算モード new/used/service
var g_rateArr = new Array();			// 現在選択されているプラン・モデルでの支払い回数・金利の配列 新車のみ
var g_u_rateArr = new Array();			// 現在選択されているプラン・モデルでの支払い回数・金利の配列 新車のみ

// DB配列
// ローカルDBの値参照は、sqlがよいが、コールバックが多いので、極力配列参照にした
// 配列の名前＝テーブル名としている ex: Initrates=>MB_initrates
var Rates = [];
var Bptrates = [];
var Lptrates = [];
var Lpprates = [];
var Initrates = [];

var g_rate;		// 現在の金利
var g_carname;	// 現在のモデル名
var g_bptmax = 0;		// ボーナス加算MAX
var g_u_bptmax = 0;		// ボーナス加算MAX
var g_s_bptmax = 0;		// ボーナス加算MAX

var carArr = [];		// 現在のcarテーブル情報配列（loan_common.jsとの互換性のため）
var g_carArr = []		// 現在の新車carテーブル情報配列
var g_u_carArr = [];	// 現在の中古車carテーブル情報配列
var g_s_carArr = [];	// 現在のサースローンcarテーブル情報配列（互換性保つためのダミー）

var g_tax = 0.08;		// 消費税（デフォルトでは5%）新車
var g_u_tax = 0.08;		// 消費税（デフォルトでは5%）中古車
var g_s_tax = 0.08;		// 消費税（デフォルトでは5%）サービスローン

// var g_price = 201503;	// 車両価格 2015年3月31日までは旧価格(201503)、2015年4月1日からは新価格(201504)
var g_price = 201605;	// 車両価格 2016年5月31日までは旧価格(201605)、2016年6月1日からは新価格(201606)

// 年月アラート後戻し用グローバル
var g_year;
var g_month;

var g_u_year;
var g_u_month;

var g_s_year;
var g_s_month;


// 初度登録年月のデフォルト＝中古車登録年月の1か月前にする
var g_tourokuyear;
var g_tourokumonth;

var g_calc_ok = true;		// 計算ボタンを押せるフラグ（値変更して、チェックが通らないときに計算されることを防ぐ）




var usedCarParams = [];

var plusmode = 0;			// プラスモード時は、1になる

var plannameArr = {
						"wp":"ウェルカムプラン",
						"swp":"スーパーウェルカムプラン",
						"std":"スタンダードローン"
//						"sup":"スタートアッププラン"
					};
var u_plannameArr = {
						"wp":"ユーズドカーウェルカムプラン",
						"swp":"スーパーウェルカムプラン",
						"std":"スタンダードローン"
//						"sup":"スタートアッププラン"
					};
					
// WPPキャンペーン　低金利インナーのみに仕様変更 2014.06.28
var g_wpplowrate;		// 1:WPPキャンペーン低金利対象 0:非対象

					
$(function() {
	// キャッシュ対策：
	// bodyのクリックイベントが一定時間発生しなければ、強制ログアウト
	//var timeid = setTimeout(forcelogout,10*60*1000);
	var timeid = setInterval(checklogout,3*1000);
	
	$("body").on("mouseup",function(){
		if(checklogout()){
			// ログアウト
		}else{
			// 時刻をリセット
			g_time = getUnixTime();
		}
	});
	
	// ログアウト
	$("#logout").on("click",function(){
		forcelogout();
	});
		
	// テキストボックスパラメーターを変更（focusout)
	//$("#discount,#lastpayment,#makeroption,#dealeroption,#sonota,#mmmprice,#mms2price,#zansai,#downpayment,#bonuspayment").on("focusout",function(){
	$(document).on("focusout",'input[type="text"],input[type="tel"]',function(){
		// リアルタイムカンマ処理をなくす（フォーカスアウト時に変更）
		// 入力値の種類によっては、まるめ処理を入れる
		var id = $(this).attr("id");
		var temp = price2num($(this).val());
		switch(id){
		case "u_carname":
		//2016.08.26 add
		case "user_name":
		case "estimate_user_name":
			// なにもしない
			break;
		case "u_bonuspayment":
		case "bonuspayment":
		case "s_bonuspayment":
			// 100円を切り捨て
			$(this).val(num2price(kirisute(100,temp)));
			break;
		case "lastpayment":
		case "u_lastpayment":
			// 10000円を切り捨て
			$(this).val(num2price(kirisute(10000,temp)));
			break;
		case "n_inspection_y":
		case "n_inspection_m":
		case "nn_inspection_y":
		case "nn_inspection_m":
			// 何もしない
			break;
		default:
			$(this).val(num2price(temp));
		}
		
		/*
		if($(this).attr("id")=="u_carname"){
			// 何もしない
		}else{
			var temp = price2num($(this).val());
			
			$(this).val(num2price(temp));
		}
		*/
		// usedかどうか判断
		var id = $(this).attr("id");
		
		switch(id.substring(0,2)){
			case "u_":
				if(id == "u_pricetax"){
					// 車両データを変更
					g_u_carArr['pricetax'] = textbox2num("u_pricetax");
					//carArr['pricetax'] = g_u_carArr['pricetax'];
					
					g_u_carArr['price'] = kirisute(1,textbox2num("u_pricetax")/(1.0+g_u_tax));
					//carArr['price'] = g_u_carArr['price'];
				}
				usedcar_check();
				break;
			case "s_":
				service_check();
				break;
			default:
				newcar_check();
		}
	});
	
	// ボーナス関連パラメーター変更時
	$(	"input[name='bonusmonth1']:radio,input[name='bonusmonth2']:radio,input[name='u_bonusmonth1']:radio,input[name='u_bonusmonth2']:radio,input[name='s_bonusmonth1']:radio,input[name='s_bonusmonth2']:radio").on("change",function(){
		// usedかどうか判断
		var id = $(this).attr("name");
		
		switch(id.substring(0,2)){
			case "u_":
				usedcar_check();
				break;
			case "s_":
				service_check();
				break;
			default:
				newcar_check();
		}
	});
	
	// テキストボックスにリアルタイムにカンマを入れる処理
	/*
	$('input[type=tel],input[type=text]').on("keyup",function(){
		if($(this).attr("id")=="u_carname"){
			// 何もしない
		}else{
			var temp = price2num($(this).val());
			
			//if(temp*1 > 100000000){
			if(0){
				temp = 0;
			}else{
				$(this).val(num2price(temp));
			}
		}
	});
	*/
	
	$(document).on("focusin",'input[type=tel]',function(){
		//if($(':focus').attr('id')!=$(this).attr('id')){
			$(this).val(price2num($(this).val()));
			if($(this).val() == 0){
				//$(this).select();
			}
		//}
	});
	$(document).on("mouseup",'input[type=text],input[type=tel]',function(){
		if($(this).val() == 0){
			$(this).select();
		}
	});
	
	
	// 新車タブをクリック
	$("a.new-tab").on("click",function(){
		g_mode = "new";
		// 新車タブがクリックされたときの処理
		carArr = g_carArr;
		$("#usedcar, #serviceloan").hide(0,function(){
			$("#newcar").show();
		});
		return false;
	});
	
	// 新車の計算ボタンクリック
	$("#newcar_calc").on("click",function(){
		var message = "";
		
		// プラスモードでwp swp以外は、ダイアログを出すだけ
		if(plusmode == 1 && ($("#plan").val()=="std" || $("#plan").val()=="sup")){
			alert2("ウェルカムプランまたは、スーパーウェルカムプランにて計算してください");
		}else{
			if(g_calc_ok){
				$("#newcar_input").hide(0,function(){
					
					if(plusmode == 0){
							// 通常モード
						// 計算をする
						var conditions = [];
						conditions['rate'] = g_rate;
						conditions['installments'] = $('#installments').val();
						conditions['pricetax'] = textbox2num('pricetax');
						conditions['optiontotal'] = textbox2num('makeroption')+textbox2num('dealeroption');
						conditions['taxtotal'] = 0;
						conditions['downpayment'] = textbox2num('genkin') + textbox2num('shitadori')-textbox2num('zansai');
						conditions['bonuspayment'] = textbox2num('bonuspayment');
						conditions['lastpayment'] = textbox2num('lastpayment');
						conditions['sonota'] = textbox2num('sonota')+textbox2num('mmmprice')+textbox2num('mmsprice')+textbox2num('evprice')+textbox2num('mbinsureance')-textbox2num('discount');
						
						// ボーナス回数を厳密に計算
						conditions['bonustimes'] = get_bonustimes($("input[name='bonusmonth1']:checked").val(),$("input[name='bonusmonth2']:checked").val(),nextMonth($("#newcar_month").val()),$("#installments").val());
	
						var leaflet = "";
						
						// 個別提案書のデータを確認する
						leaflet = get_leaflet_data($("#plan").val(),$("#classname").val(),$("#installments").val());
						
						
						var resultArr = loancalc($("#plan").val(),conditions);
						$("#monthlypayment").text(num2price(resultArr["monthlypayment"]));
						
						// 計算結果をサーバーに送信し、DBのIDを受け取る
						$.getJSON(g_site_url+"/cars/insertlog/",{
										salesman:$("#salesman").val(),
										plan:$("#plan").val(),
										classname:$("#classname").val(),
										bmst:g_carArr['bmst'],
										carname:g_carname,
										pricetax:textbox2num('pricetax'),
										makeroption:textbox2num('makeroption'),
										dealeroption:textbox2num('dealeroption'),
										discount:textbox2num('discount'),
										mbinsureance:textbox2num('mbinsureance'),
										sonota:textbox2num('sonota'),
										mmmprice:textbox2num('mmmprice'),
										mmsprice:textbox2num('mmsprice'),
										evprice:textbox2num('evprice'),
										cartotal:textbox2num('cartotal'),
										totalpayment:textbox2num('totalpayment'),
										downpayment:textbox2num('genkin') + textbox2num('shitadori') - textbox2num('zansai'),
										zansai:textbox2num('zansai'),
										shitadori:textbox2num('shitadori'),
										genkin:textbox2num('genkin'),
										loanprincipal:textbox2num('loanprincipal'),
										loantotal:resultArr['total'],
										installments:$('#installments').val(),
										firstpayment:resultArr['firstpayment'],
										monthlypayment:resultArr["monthlypayment"],
										bonuspayment:textbox2num('bonuspayment'),
										bonustimes:resultArr['bonustimes'],
										registyear:$("#newcar_year").val(),
										registmonth:$("#newcar_month").val(),
										bonusmonth1:$("input[name='bonusmonth1']:checked").val(),
										bonusmonth2:$("input[name='bonusmonth2']:checked").val(),
										rate:addZero(g_rate),
										lastpayment:textbox2num('lastpayment'),
										interest:resultArr['interest'],
										leafletimage:leaflet,
										tax:g_tax,
										tm:stamp()
								},function(data){
							// DBのIDを受信
							//$("#newcar_pdf").attr("href",g_site_url+"/pdf/estimate/"+data.code);
							//2016.08.26 change & add
							$("#newcar_pdf").attr("href",g_site_url+"/pdf/estimate/"+data.code+"?");
							$("#newcar_leaflet").attr("href",g_site_url+"/pdf/leaflet/l_"+data.code);
							//$("#newcar_leaflet2").attr("href","./pdf/leaflet/"+data.id);
							$("#newcar_display").attr("href",g_site_url+"/pdf/display/d_"+data.code);
							$("#newcar_compare").attr("href",g_site_url+"/cars/compare/"+data.id);
							
							// 結果表示
							$("#newcar_result_classname").text($("#classname").val());
							$("#newcar_result_carname").text(g_carname);
							$("#newcar_result_plan").text(plannameArr[$("#plan").val()]);
							$("#newcar_result_pricetax").text($('#pricetax').val()+"円");
							$("#newcar_result_totalpayment").text($('#totalpayment').val()+"円");
							$("#newcar_result_zansai").text($('#zansai').val()+"円");
							$("#newcar_result_tsuika").text("0円"),
							$("#newcar_result_monthlypayment").text(num2price(resultArr['monthlypayment'])+"円");
							$("#newcar_result_loanprincipal").text($('#loanprincipal').val()+"円");
							
							// 残価欄の制御
							if(textbox2num("lastpayment") > 0){
								// 残価欄出現
								$("#tr_lastpayment").show();
							}else{
								// 残価欄消去
								$("#tr_lastpayment").hide();
							}
							$("#newcar_result_lastpayment").text($('#lastpayment').val()+"円");
							$("#newcar_result_bonuspayment").text($('#bonuspayment').val()+"円");
							$("#newcar_result_genkin").text($('#genkin').val()+"円");
							$("#newcar_result_shitadori").text($('#shitadori').val()+"円");
							$("#newcar_result_zansai").text($('#zansai').val()+"円");
							$("#newcar_result_installments").text($('#installments').val()*1+1*(($("#plan").val()=="wp" ||$("#plan").val()=="swp") ? 1 : 0 )+"回");
							$("#newcar_result_bonustimes").text(textbox2num('bonustimes')+"円");
							$("#newcar_result_rate").text(addZero(g_rate)+"％");
							$("#newcar_result_loantotal").text(num2price(resultArr['total'])+"円");
							$("#newcar_result_alltotalpayment").text(num2price(resultArr['total']+textbox2num('genkin') + textbox2num('shitadori'))+"円");
							
							
							// 受信後に画面表示
							// 頭金ラベルを変更
							//$("#label_newcar_result_downpayment").empty().append("頭金／下取");
							$("#newcar_result").show();
							if(leaflet){
								$("#newcar_proposal_open").show();
							}else{
								$("#newcar_proposal_open").hide();
							}
							if($("#plan").val() == "sup"){
								$("#newcar_compare").hide();
							}else{
								$("#newcar_compare").show();
							}
							$("#newcar_display").show();
						});
					}else{
						// プラスモード
						// 新車分の計算
						
						var conditions = [];
						conditions['rate'] = g_rate;
						conditions['installments'] = $('#installments').val();
						conditions['pricetax'] = textbox2num('pricetax');
						conditions['optiontotal'] = textbox2num('makeroption')+textbox2num('dealeroption');
						conditions['taxtotal'] = 0;
						conditions['downpayment'] = 0;				// プラス計算時は、新車分頭金は絶対にゼロ！
						conditions['bonuspayment'] = textbox2num('bonuspayment');
						conditions['lastpayment'] = textbox2num('lastpayment');
						conditions['sonota'] = textbox2num('sonota')+textbox2num('mmmprice')+textbox2num('mmsprice')+textbox2num('evprice')+textbox2num('mbinsureance')-textbox2num('discount');
						
						// ボーナス回数を厳密に計算
						conditions['bonustimes'] = get_bonustimes($("input[name='bonusmonth1']:checked").val(),$("input[name='bonusmonth2']:checked").val(),nextMonth($("#newcar_month").val()),$("#installments").val());
						// 個別提案書のデータを確認は不要！
						
						var resultArr = loancalc($("#plan").val(),conditions);
						
						// 追加売買分の計算
						var conditions2 = [];
						// WPP低金利対応 2014.06.09
						if($('#plan option:selected').text() == "ウェルカムプランプラス（キャンペーン用）"){
							// WPPキャンペーン　低金利インナーのみに仕様変更 2014.06.28
							//conditions2['rate'] = tsuikakinriArr2[$('#installments').val()];
							// 2014.12.06 追加売買金利=選択した金利にする
							//conditions2['rate'] = tsuikakinriArr3[$('#installments').val()];
							conditions2['rate'] = conditions['rate'];
						}else if($('#plan option:selected').text() == "スーパーウェルカムプランプラス"){
							// 2015.07.01 通常金利変更 追加売買金利=選択した金利にする
							conditions2['rate'] = tsuikakinriArr4[$('#installments').val()];
						}else{
							conditions2['rate'] = tsuikakinriArr[$('#installments').val()];
						}
						conditions2['installments'] = $('#installments').val();
						conditions2['pricetax'] = textbox2num('zansai')-(textbox2num('genkin') + textbox2num('shitadori'));
						conditions2['optiontotal'] = 0;
						conditions2['taxtotal'] = 0;
						conditions2['downpayment'] = 0;				// プラス計算時は、新車分頭金は絶対にゼロ！
						conditions2['bonuspayment'] = 0;
						conditions2['lastpayment'] = 0;
						conditions2['sonota'] = 0;
						
						// プラス計算は必ずゼロ
						conditions2['bonustimes'] = 0;
		
						var resultArr2 = loancalc("dummy",conditions2);
						
						// 計算結果をサーバーに送信し、DBのIDを受け取る
						$.getJSON(g_site_url+"/cars/insertlog/",{
										salesman:$("#salesman").val(),
										plan:$("#plan").val(),
										classname:$("#classname").val(),
										bmst:g_carArr['bmst'],
										carname:g_carname,
										pricetax:textbox2num('pricetax'),
										makeroption:textbox2num('makeroption'),
										dealeroption:textbox2num('dealeroption'),
										discount:textbox2num('discount'),
										mbinsureance:textbox2num('mbinsureance'),
										sonota:textbox2num('sonota'),
										mmmprice:textbox2num('mmmprice'),
										mmsprice:textbox2num('mmsprice'),
										evprice:textbox2num('evprice'),
										cartotal:textbox2num('cartotal'),
										totalpayment:textbox2num('totalpayment'),
										downpayment:0,
										zansai:textbox2num('zansai'),
										shitadori:textbox2num('shitadori'),
										genkin:textbox2num('genkin'),
										loanprincipal:textbox2num('loanprincipal'),
										loantotal:resultArr['total'],
										installments:$('#installments').val(),
										firstpayment:resultArr['firstpayment'],
										monthlypayment:resultArr["monthlypayment"],
										bonuspayment:textbox2num('bonuspayment'),
										bonustimes:resultArr['bonustimes'],
										registyear:$("#newcar_year").val(),
										registmonth:$("#newcar_month").val(),
										bonusmonth1:$("input[name='bonusmonth1']:checked").val(),
										bonusmonth2:$("input[name='bonusmonth2']:checked").val(),
										rate:addZero(g_rate),
										lastpayment:textbox2num('lastpayment'),
										interest:resultArr['interest'],
										leafletimage:"",
										prate:addZero(conditions2['rate']+""),
										pinterest:resultArr2['interest'],
										ploantotal:resultArr2['total'],
										pfirstpayment:resultArr2['firstpayment'],
										pmonthlypayment:resultArr2["monthlypayment"],
										tax:g_tax,
										tm:stamp()
								},function(data){
							// DBのIDを受信
							//$("#newcar_pdf").attr("href","./pdf/estimate/"+data.code);
							//2016.08.26 change & add
							$("#newcar_pdf").attr("href",g_site_url+"/pdf/estimate/"+data.code+"?");
							$("#newcar_leaflet").attr("href","./pdf/leaflet/l_"+data.code);
							//$("#newcar_leaflet2").attr("href","./pdf/leaflet/"+data.id);
							$("#newcar_display").attr("href","./pdf/display/d_"+data.code);
							
							// 結果表示
							$("#newcar_result_classname").text($("#classname").val());
							$("#newcar_result_carname").text(g_carname);
			
							// WPP低金利対応 2014.06.10
							//$("#newcar_result_plan").text(plannameArr[$("#plan").val()]+"プラス");
							$("#newcar_result_plan").text($('#plan option:selected').text());

							$("#newcar_result_pricetax").text($('#pricetax').val()+"円");
							$("#newcar_result_totalpayment").text($('#totalpayment').val()+"円");
							$("#newcar_result_genkin").text($('#genkin').val()+"円");
							$("#newcar_result_shitadori").text($('#shitadori').val()+"円");
							$("#newcar_result_zansai").text($('#zansai').val()+"円"),
							$("#newcar_result_tsuika").text(num2price(textbox2num('zansai')-(textbox2num('genkin') + textbox2num('shitadori')))+"円"),
							$("#newcar_result_monthlypayment").text(num2price(resultArr['monthlypayment']+resultArr2['monthlypayment'])+"円");
							$("#newcar_result_loanprincipal").text($('#loanprincipal').val()+"円");
							
							// 残価欄の制御
							if(textbox2num("lastpayment") > 0){
								// 残価欄出現
								$("#tr_lastpayment").show();
							}else{
								// 残価欄消去
								$("#tr_lastpayment").hide();
							}
							$("#newcar_result_lastpayment").text($('#lastpayment').val()+"円");
							$("#newcar_result_bonuspayment").text($('#bonuspayment').val()+"円");
							$("#newcar_result_installments").text($('#installments').val()*1+1*(($("#plan").val()=="wp" ||$("#plan").val()=="swp") ? 1 : 0 )+"回");
							$("#newcar_result_bonustimes").text(textbox2num('bonustimes')+"円");
							$("#newcar_result_rate").text(addZero(g_rate)+"％ / "+addZero(conditions2['rate'])+"％");
							$("#newcar_result_loantotal").text(num2price(resultArr['total']+resultArr2['total'])+"円");
							$("#newcar_result_alltotalpayment").text(num2price(resultArr['total']+resultArr2['total']+textbox2num('genkin') + textbox2num('shitadori'))+"円");
							
							
							// 受信後に画面表示
							// 頭金ラベルを変更
							//$("#label_newcar_result_downpayment").empty().append("頭金");
							$("#newcar_result").show();
							$("#newcar_proposal_open").hide();
							$("#newcar_compare").hide();
							$("#newcar_display").hide();
						});
					}
							
					
				});
			}
		}	// if(g_calc_ok)
		return false;
	});
	
	
	// 新車結果閉じる
	$("#newcar_result_close").on("click",function(){
		$("#newcar_result").hide(0,function(){
			$("#newcar_input").show();
		});
		return false;
	});
	
	// クラス名選択後の処理
	$("#classname").on("change",function(){
		// smartが選択されて場合はメンテナンスパッケージのラベル画像を変更する
		if($(this).val() == "smart"){
			$("#mmm_mbj").hide();
			$("#mms_mbj").hide();
			//$("#ev_mbj").hide();
			$("#mmm_smart").show();
			$("#mms_smart").show();
			$("#ev_smart").show();
			// evのテキストボックスを表示
			$("#evprice,#ev_yen").show();
		}else{
			$("#mmm_smart").hide();
			$("#mms_smart").hide();
			$("#ev_smart").hide();
			$("#mmm_mbj").show();
			$("#mms_mbj").show();
			//$("#ev_mbj").show();
			// evのテキストボックスを隠す
			$("#evprice,#ev_yen").hide();
		}
		
		
		// ajaxで、モデル名リストをダウンロードする
		$.getJSON(g_site_url+"/cars/carnamejson/"+$(this).val()+"?tm="+stamp(),function(json){
			// モデル名プルダウンを作成する
			optionTag = "<option value=''>▼選択してください</option>";
			for(var key in json){
				var carname = json[key]["qc_carname"];
				var bmst = json[key]["bmst"];
				
				if(bmst){
					
					// オプションタグ生成
					optionTag += "<option value='"+bmst+"'>"+carname+"</option>";
				}
			}
			// オプションタグ入れ替え
			$("#carname").empty().append(optionTag).val("").custom_selectbox();
			
			newcar_input_disable();
			
			//$("name:bonusmonth1").attr("readonly","readonly");
			
			/*
			$("#total").val("").attr('readonly',true);
			$("#pricetax").val("").attr('readonly',true);
			$("#installments").empty.attr('readonly',true);
			$("#year").empty;
			$("#month").empty;
			$("#lastpayment").val("").attr('readonly',true);
			$("#loanprincipal").val("").attr('readonly',true);
			
			$("#newcar_input input").attr('readonly',true);
			*/
			
			//　コメント欄をクリア
			$("#newcar_comment").val("");
			//2016.08.26 add
			$("#estimate_user_name").val("");

			check_newcar_calc_button();
		});
	});
	
	// モデル名選択後の処理
	$("#carname").on("change",function(){
		var bmst = $(this).val();
		
		
		// ボーナス加算をゼロにする
		$('input[name="bonus"][value="1"]').prop('checked', true).radio_update();
		$("#bonuspayment").val(0);
		bonus_off();
		
		newcar_input_enable();
		// mmm mms 欄を編集不可能にする
		//$("#mmmprice,#mmsprice").attr("readonly",true);
		
		// carレコードの取得
		$.getJSON(g_site_url+"/cars/carjson/"+bmst+"&tm="+stamp(),function(json){
			// グローバルに格納
			g_carArr = json;
			carArr = g_carArr;

// 2015.02.20 2015年3月からの新旧価格対応（3月一杯pricetax→旧価格、4月以降pircetax2→新価格）
// 2016.03.25 2016年4月からの新旧価格対応（5月一杯pricetax→旧価格、6月以降pircetax2→新価格）
// 2016.06.01 クイックチャートでの旧価格表示用にpricetax2に旧価格を格納（提案システムはpricetaxを参照に変更）
			if(g_price == 201606) {
				// 消費税8%
				// 新価格読み込み
				g_carArr['price'] = g_carArr['price'];
				g_carArr['pricetax'] = g_carArr['pricetax'];
				g_carArr['mmm'] = g_carArr['mmm2'];
				g_carArr['mms'] = g_carArr['mms2'];
				g_carArr['ev'] = g_carArr['ev2'];
			}

//			if(g_tax == 0.05) {
//				// 消費税5%
//				// なにもしない
//			}else{
//				// 消費税8%
//				// テーブル読み直し
//				g_carArr['price'] = g_carArr['price2'];
//				g_carArr['pricetax'] = g_carArr['pricetax2'];
//				g_carArr['mmm'] = g_carArr['mmm2'];
//				g_carArr['mms'] = g_carArr['mms2'];
//				g_carArr['ev'] = g_carArr['ev2'];
//			}
			
			// 消費税8％しか対応していない車選択した場合、5％時のpriceはゼロとする。この場合、ポップアップして計算させない
			if(g_carArr['price'] == 0){
				alert2("登録月に誤りがあります。");
				// クラス名変更イベント発火
				$("#classname").trigger("change");
			}else{
				// チェックボックスなくす
				$("#m_plan_check").hide();
				
				//車両本体価格を変更
				$("#pricetax").val(num2price(g_carArr.pricetax));
				
				// gsがデフォルトになる
				$("input[name='m_plan']").val(["gs"]);
				
				// 車両本体価格・保証・メンテナンス代を反映する
				calcNewCarParams();
				
				g_carname = g_carArr.carname;
				
				// WPP低金利対応 2014.06.09 2014.12.1 CSWを追加 2014.12.26 C-Class以外を対象外に変更 2015.03.31 WPP低金利 C-Class, E-Class
				// WPP低金利取り下げ 2015.08.03
				switch($("#classname").val()){
					case "XXX-Class":
					//case "C-Class Sedan":
					//case "C-Class Stationwagon":
					//case "C-Class Coupe":
					//case "E-Class Sedan":
					//case "E-Class Stationwagon":
					//case "E-Class Coupe":
					//case "E-Class Cabriolet":
						// 低金利対象
						g_wpplowrate = 1;
						// プランプルダウンを更新
						if(g_carArr['swpmodel'] == 1 || g_carArr['swpmodel'] == 2){
							optionTag = "<option value=''>▼選択してください</option>";
							optionTag += "<option value='wp'>ウェルカムプラン</option>";
							optionTag += "<option value='wp'>ウェルカムプランプラス</option>";
							optionTag += "<option value='wp'>ウェルカムプランプラス（キャンペーン用）</option>";
							optionTag += "<option value='swp'>スーパーウェルカムプラン</option>";
							optionTag += "<option value='swp'>スーパーウェルカムプランプラス</option>";
							optionTag += "<option value='std'>スタンダードローン</option>";
							if($("#classname").val() != "smart"){
								optionTag += "<option value='sup'>スタートアッププラン</option>";
							}
						}else{
							optionTag = "<option value=''>▼選択してください</option>";
							optionTag += "<option value='wp'>ウェルカムプラン</option>";
							optionTag += "<option value='wp'>ウェルカムプランプラス</option>";
							optionTag += "<option value='wp'>ウェルカムプランプラス（キャンペーン用）</option>";
							optionTag += "<option value='std'>スタンダードローン</option>";
							if($("#classname").val() != "smart"){
								optionTag += "<option value='sup'>スタートアッププラン</option>";
							}
						}
						break;
					default:
						// 低金利非対象
						g_wpplowrate = 0;
						// プランプルダウンを更新
						if(g_carArr['swpmodel'] == 1 || g_carArr['swpmodel'] == 2){
							optionTag = "<option value=''>▼選択してください</option>";
							optionTag += "<option value='wp'>ウェルカムプラン</option>";
							optionTag += "<option value='wp'>ウェルカムプランプラス</option>";
							optionTag += "<option value='swp'>スーパーウェルカムプラン</option>";
							optionTag += "<option value='swp'>スーパーウェルカムプランプラス</option>";
							optionTag += "<option value='std'>スタンダードローン</option>";
							if($("#classname").val() != "smart"){
								optionTag += "<option value='sup'>スタートアッププラン</option>";
							}
						}else{
							optionTag = "<option value=''>▼選択してください</option>";
							optionTag += "<option value='wp'>ウェルカムプラン</option>";
							optionTag += "<option value='wp'>ウェルカムプランプラス</option>";
							optionTag += "<option value='std'>スタンダードローン</option>";
							if($("#classname").val() != "smart"){
								optionTag += "<option value='sup'>スタートアッププラン</option>";
							}
						}
				}
	
				
				$("#plan").empty().append(optionTag).val("").custom_selectbox();
				
				$("#rate,#installments").val("").empty().custom_selectbox();
				
				//　コメント欄をクリア
				$("#newcar_comment").val("");
				//2016.08.26 add
				$("#estimate_user_name").val("");
				
				// メンテサービスのデフォルトをEVからGSに変更（この関数は_checkを実行するため、ここでは最後に配置する！）
				$("#evprice").val(0).attr("readonly",true);
				//m_plan_click("gs");
				
				check_newcar_calc_button();
			}
		});
	});
	
	// プラン選択後の処理
	$("#plan").on("change",function(){
		// rateレコードの取得
		var patternid = g_carArr[$("#plan").val()+"ratepattern"];
		
		// グローバル変数に代入
		g_rateArr = findFromDBArr(Rates,{"patternid":patternid});

		// 支払い回数プルダウン作成
		optionTag = "<option value=''>▼選択</option>";
		var temp = ($("#plan").val() == "wp" || $("#plan").val() == "swp") ? 1:0;
		
		for(var key in g_rateArr){
			var installments = g_rateArr[key]["installments"];
			
			// オプションタグ生成
			
			// smart EV時、WPPの73回払いをブロック 2015.04.27 by morita
			if(g_carArr['ev'] && installments == 72 && $('#plan option:selected').text()=="ウェルカムプランプラス"){
				continue; 
			}
			optionTag += "<option value='"+installments+"'>"+(installments*1+temp*1)+"回</option>";
		}
		
		
		// 金利プルダウンをリセット
		$("#rate").val("").empty().custom_selectbox();
		
		// オプションタグ入れ替え
		$("#installments").empty().append(optionTag).val("").custom_selectbox();
		// mmm mms 欄を編集不可能にする
		$("#mmmprice,#mmsprice").attr("readonly",true);
			
		// 残価ボックスの設定
		if($(this).val()=="wp"){
			$("#lastpayment").removeAttr("readonly");
		}else{
			$("#lastpayment").attr("readonly",true);
		}
		check_newcar_calc_button();
	});
	
	
	// 支払い回数選択時の処理
	$("#installments").on("change",function(){
		// 残価を取得
		var lastpayment;
		switch($("#plan").val()){
			/*
			case "wp":
				lastpayment = getmaxwplpt($("#installments").val(),"new");
				$("#lastpayment").val(num2price(lastpayment));
				break;
			*/
			case "wp":
			case "swp":
				lastpayment = getswplpt($("#installments").val());
				$("#lastpayment").val(num2price(lastpayment));
				break;
			default:
				// 残価はゼロ
				$("#lastpayment").val(0);
		}
											
		// 金利プルダウン作成
		var tempArr = findFromDBArr(g_rateArr,{"installments":$(this).val()});
		
		var rateArr = tempArr[0];
		/*
		$.each(["rate","lowrate","innerrate"],function(){
			// オプションタグ生成
			optionTag += "<option value='"+this+"'>"+rateArr[0][this]+"%</option>";
		});
		*/
		
		var rateArr2 = [];
		
		// WPP低金利対応 2014.06.12
		if($('#plan option:selected').text() == "ウェルカムプランプラス（キャンペーン用）"){
			// 金利プルダウンを数字のみに変更　2014.03.04
			// 重複金利を削除
			// WPPキャンペーン　低金利インナーのみに仕様変更 2014.06.28
			// rateArr2 = array_unique([rateArr.lowrate*1,rateArr.innerrate*1]);
			if(g_wpplowrate){
				// 2014.12.01 CクラスWPP金利 2.9 1.9選択できるように変更
				//rateArr2 = [rateArr.innerrate*1];
				rateArr2 = array_unique([rateArr.lowrate*1,rateArr.innerrate*1]);
			}else{
				rateArr2 = array_unique([rateArr.lowrate*1,rateArr.innerrate*1]);
			}
			
		}else{
			// 2014.08.04 社員用金利
			// プラス計算・スタートアッププラン以外は、通常金利・通常金利-0.5％を表示する
			switch($('#plan option:selected').text()){
				case "ウェルカムプランプラス":
				case "スーパーウェルカムプランプラス":
				case "スタートアッププラン":
					// 今までと変わらない
					rateArr2 = array_unique([rateArr.rate*1,rateArr.lowrate*1,rateArr.innerrate*1]);
					break;
				default:
					rateArr2 = array_unique([rateArr.rate*1,rateArr.rate - 0.5, rateArr.lowrate*1,rateArr.innerrate*1]);
			}
		}
		
		rateArr2 = rateArr2.sort();
		
		var optionTag = "<option value=''>▼選択</option>";
		var num = rateArr2.length;

		for(var i=0;i<num;i++){
			optionTag += "<option value='"+rateArr2[i]+"'>"+addZero(rateArr2[i]+"")+"%</option>";
		}
		
		
		if(0){
		
		

		// 重複金利を削除
		var rateArr2 = array_unique([rateArr.rate*1,rateArr.lowrate*1,rateArr.innerrate*1]);
		var rateNameArr = ["rate","lowrate","innerrate"];
		
		// タグを作成
		/*
		// 金利が1種類の場合： normalrate
		// 金利が2種類の場合： normalrate, lowrate
		// 金利が3種類の場合： normalrate, lowrate, innerrate
		//var optionTag =  "<option value='' selected>金利を選択してください</option>";
		var optionTag =  "";
		var num = rateArr2.length;
		for(var i=0;i<num;i++){
			optionTag += "<option value='"+rateNameArr[i]+"'>"+addZero(rateArr2[i]+"")+"%</option>";
		}
		// ループ最後の金利をデフォルトにする
		rateDefault = rateNameArr[i-1];
		*/
		// 2013.10.18 低金利と通常金利が同じときに、インナーが表示できなくなるバグ修正
		// 金利が1種類の場合： normalrate
		// 金利が2種類の場合： normalrate, innerrate
		// 金利が3種類の場合： normalrate, lowrate, innerrate
		//var optionTag =  "<option value='' selected>金利を選択してください</option>";
		var optionTag = "<option value=''>▼選択</option>";
		var num = rateArr2.length;
		switch(num){
			case 0:
				// ありえない！！！
				break;
			case 1:
				// 通常金利
				rateNameArr = ["rate"];
				break;
			case 2:
				rateNameArr = ["rate","innerrate"];
				break;
			case 3:
				rateNameArr = ["rate","lowrate","innerrate"];
				break;
		}

		for(var i=0;i<num;i++){
			optionTag += "<option value='"+rateNameArr[i]+"'>"+addZero(rateArr2[i]+"")+"%</option>";
		}
		
		}	// if(0)
		
		
		
		// オプションタグ入れ替え
		$("#rate").empty().append(optionTag).custom_selectbox();
		
/*************************************************************************************************************************************
			smart のサービスプログラムについて
			
			EV専用プランはEV車でWP6年を選んだ場合のみアクティブになります。
			それ以外はEV車でもスタートプランとセカンドプランになります。
			
			＝＝
			・3年の場合はスタートプランがデフォルト表記
			・3年の場合はセカンドプランは空白で、自由記入
			・5年の場合（通常ローンの5年以上含む）はスタートプラン、セカンドプランの両方がデフォルト表記
			・6年（EV）の場合はEV専用プランがデフォルト表記（EV以外は6年選択不可のため、EV専用プランも記入等一切不可）
			・6年（EV）の場合、EV専用プランがデフォルトで表記されるが、それを削除してスタートプラン、セカンドプランを入力することも可能。
			　ただ、EV専用プランとメンテパック（スタートプラン、セカンドプランのいずれかまたは両方）の両方に金額を入力することは不可
			・上記デフォルト表記の場合でも、表記削除、金額変更入力は可能
			・上記以外の年数（2年や4年等）はすべて空白で、自由記入
			
			＝＝
			2016.07 smartのサービスプログラム改訂
			・WP37回、STD36回→smartメンテナンスの金額を表示
			・WP60回、STD60〜84回→smartメンテナンス、smartメンテナンスプラス、保証プラスの金額を表示
			
*************************************************************************************************************************************/
				var plan = $("#plan").val();
				var installments = 1*$("#installments").val();
				
				if($("#classname").val() == "smart"){
					
					// サービスプログラム欄を編集可能にする
					$("#mmmprice,#mmsprice,#evprice").removeAttr("readonly");
					
						if(installments == 36){
						// smartメンテナンスのデフォルト値コピー処理
						$("#mmmprice").val(num2price(g_carArr.mmm));
						$("#mmsprice").val("");
						$("#evprice").val("");
						}
						else if(installments >= 60){
						// smartメンテナンス、smartメンテナンスプラス、保証プラスのデフォルト値コピー処理	
						$("#mmmprice").val(num2price(g_carArr.mmm));					
						$("#mmsprice").val(num2price(g_carArr.mms));
						$("#evprice").val(num2price(g_carArr['ev'])).removeAttr("desabled readonly");
						}
						else if(installments < 36){
						$("#mmmprice").val("");
						$("#mmsprice").val("");
						$("#evprice").val("");
						}
						else if((installments > 36) && (installments < 60)){
						$("#mmmprice").val("");
						$("#mmsprice").val("");
						$("#evprice").val("");
						}
	
				}else if($("#classname").val() != "smart"){
/*************************************************************************************************************************************
			
			MBのサービスプログラムについて
			【仕様】
			MB 
			WP61回、SWP61回、STD60?84回、SUP96?120回（お支払い年数が5年以上のもの全て）
			→保証プラスの初期値をmmsの値にする（消費税8%時にはmms2の値）
			
			WP25?49回、SWP25?49回、STD3?54回（お支払い年数が5年未満）
			→保証プラスの初期値をゼロにする
			
			メンテナンスプラスはお支払い回数・年数に関わらずmmmの値を初期表示（8%時はmmm2の値）
*************************************************************************************************************************************/
				// smart以外の場合
				if(g_carArr['ev'] > 0){
					// evを初期化してアクティブに
					// チェックボックス出現
					$("#m_plan_check").show();
					
					// evnがデフォルトになる
					$("inpur[name='m_plan']").val("ev").trigger("change");
					
					// evを初期化してアクティブに
					$("#evprice").val(num2price(g_carArr['ev'])).removeAttr("readonly");
					
					// その他の処理はまだわからない
				}else{
					// 仕様変更：5年以上はmmm/mmsともにデフォルト表示で編集可能。
					//           5年未満はmmm/mmsともにゼロで編集不可能
					// gsがデフォルトになる
					$("inpur[name='m_plan']").val("gs").trigger("change");
					// チェックボックス隠す
					$("#m_plan_check").hide();
					// evを初期化してグレーアウト
					$("#evprice").val(0).attr("readonly",true);
					
					if(installments >= 60){
						// mmm mms欄を編集可能にする
						$("#mmmprice,#mmsprice").removeAttr("readonly");
						
						// mmm mmsのデフォルト値コピー処理
						$("#mmmprice").val(num2price(g_carArr.mmm));
						
						$("#mmsprice").val(num2price(g_carArr.mms));
					}else{
						// 2014.06.18 仕様変更
						// mmm mms欄を編集可能にする
						$("#mmmprice,#mmsprice").removeAttr("readonly");
						
						// 60回以外はゼロリセット
						$("#mmmprice").val(0);
						$("#mmsprice").val(0);
						
						/*
						if($("#mmmprice").val()){
							// 何もしない
						}else{
							$("#mmmprice").val(0);
						}
						
						if($("#mmsprice").val()){
							// 何もしない
						}else{
							$("#mmsprice").val(0);
						}
						*/
						
						/*
						// mmm mms欄を編集可能にする
						$("#mmmprice,#mmsprice").attr("readonly",true);
						
						// mmm mmsのデフォルト値コピー処理
						$("#mmmprice").val(0);
						
						$("#mmsprice").val(0);
						*/
					}
				}
			}
		
		newcar_check();
		check_newcar_calc_button();
	});
	
	// 金利選択時の処理
	$("#rate").on("change",function(){
		var rateArr =findFromDBArr(Rates,{"patternid":g_carArr[$("#plan").val()+"ratepattern"],"installments":$('#installments').val()});
		
		//g_rate = 1.0*rateArr[0][$(this).val()];
		g_rate = 1.0*$(this).val();

		newcar_check();
		check_newcar_calc_button();
	});
	
	// ボーナス加算の有無
	$("input[name='bonus']").on("change",function(){
		if($(this).val()=="1"){
			// 1だったのがゼロになる→無効になる
			bonus_off();
		}else{
			bonus_on();
		}
	});
	$("input[name='u_bonus']").on("change",function(){
		if($(this).val()=="1"){
			// 1だったのがゼロになる→無効になる
			used_bonus_off();
		}else{
			used_bonus_on();
		}
	});
	$("input[name='s_bonus']").on("change",function(){
		if($(this).val()=="1"){
			// 1だったのがゼロになる→無効になる
			service_bonus_off();
		}else{
			service_bonus_on();
		}
	});
	
	
	// 中古車タブをクリック
	$("a.used-tab").on("click",function(){
		g_mode = "used";
		// 中古車タブがクリックされたときの処理
		carArr = g_u_carArr;
		$("#newcar, #serviceloan").hide(0,function(){
			$("#usedcar").show();
		});
		return false;
	});
	
	// 中古車の計算ボタンクリック
	$("#usedcar_calc").on("click",function(){
		$("#usedcar_input").hide(0,function(){
			// 計算をする
			var conditions = [];
			conditions['rate'] = $("#u_rate").val();
			conditions['installments'] = $('#u_installments').val();
			conditions['pricetax'] = textbox2num('u_pricetax');
			conditions['optiontotal'] = textbox2num('u_makeroption')+textbox2num('u_dealeroption');
			conditions['taxtotal'] = 0;
			//conditions['downpayment'] = textbox2num('u_downpayment');
			conditions['downpayment'] = textbox2num('u_shitadori')+textbox2num('u_genkin');
			conditions['bonuspayment'] = textbox2num('u_bonuspayment');
			conditions['lastpayment'] = textbox2num('u_lastpayment');
			conditions['sonota'] = textbox2num('u_sonota')+textbox2num('u_mbinsureance')-textbox2num('u_discount');

			// ボーナス回数を厳密に計算
			conditions['bonustimes'] = get_bonustimes($("input[name='u_bonusmonth1']:checked").val(),$("input[name='u_bonusmonth2']:checked").val(),nextMonth($("#usedcar_month").val()),$("#u_installments").val());
			
			var resultArr = loancalc($("#u_plan").val(),conditions);
			// 計算結果をサーバーに送信し、DBのIDを受け取る
			$.getJSON(g_site_url+"/cars/insertlog/",{
							salesman:$("#salesman").val(),
					  		plan:$("#u_plan").val(),
					  		classname:"used",
							bmst:g_u_carArr['bmst'],
							carname:$("#u_carname").val(),
							pricetax:textbox2num('u_pricetax'),
							makeroption:textbox2num('u_makeroption'),
							dealeroption:textbox2num('u_dealeroption'),
							discount:textbox2num('u_discount'),
							mbinsureance:textbox2num('u_mbinsureance'),
							sonota:textbox2num('u_sonota'),
							mmmprice:textbox2num('u_mmmprice'),
							mmsprice:textbox2num('u_mmsprice'),
							cartotal:textbox2num('u_cartotal'),
							totalpayment:textbox2num('u_totalpayment'),
							//downpayment:textbox2num('u_downpayment'),
							downpayment:textbox2num('u_genkin')+textbox2num('u_shitadori'),
							genkin:textbox2num('u_genkin'),
							shitadori:textbox2num('u_shitadori'),
							zansai:textbox2num('u_zansai'),
							loanprincipal:textbox2num('u_loanprincipal'),
							loantotal:resultArr['total'],
							installments:$('#u_installments').val(),
							firstpayment:resultArr['firstpayment'],
							monthlypayment:resultArr["monthlypayment"],
							bonuspayment:textbox2num('u_bonuspayment'),
							bonustimes:resultArr['bonustimes'],
							bonusmonth1:$("input[name='u_bonusmonth1']:checked").val(),
							bonusmonth2:$("input[name='u_bonusmonth2']:checked").val(),
							rate:$("#u_rate").val(),
							lastpayment:textbox2num('u_lastpayment'),
							interest:resultArr['interest'],
							tax:g_u_tax,
							tm:stamp()
					},function(data){
				// DBのIDを受信
				$("#u_pdf").attr("href","./pdf/estimate/"+data.code);
				//$("#leaflet").attr("href","./pdf/leaflet/"+data.id);
				//$("#leaflet2").attr("href","./pdf/leaflet/"+data.id);
				$("#u_display").attr("href","./pdf/display/d_"+data.code);
				
				
				// 結果表示
				$("#usedcar_result_classname").text($("#u_classname").val());
				$("#usedcar_result_carname").text($("#u_carname").val());

				$("#usedcar_result_plan").text(u_plannameArr[$("#u_plan").val()]);
				$("#usedcar_result_pricetax").text($('#u_pricetax').val()+"円");
				$("#usedcar_result_totalpayment").text($('#u_totalpayment').val()+"円");
				$("#usedcar_result_genkin").text($('#u_genkin').val()+"円");
				$("#usedcar_result_shitadori").text($('#u_shitadori').val()+"円");
				$("#usedcar_result_zansai").text($('#u_zansai').val()+"円");
				$("#usedcar_result_monthlypayment").text(num2price(resultArr['monthlypayment'])+"円");
				$("#usedcar_result_loanprincipal").text($('#u_loanprincipal').val()+"円");
				$("#usedcar_result_lastpayment").text($('#u_lastpayment').val()+"円");
				
				// 残価欄の制御
				if(textbox2num("u_lastpayment") > 0){
					// 残価欄出現
					$("#tr_u_lastpayment").show();
				}else{
					// 残価欄消去
					$("#tr_u_lastpayment").hide();
				}
				
				$("#usedcar_result_bonuspayment").text($('#u_bonuspayment').val()+"円");
				//$("#usedcar_result_downpayment").text($('#u_downpayment').val()+"円");
				$("#usedcar_result_installments").text($('#u_installments').val()*1+1*(($("#u_plan").val()=="wp" ||$("#u_plan").val()=="swp") ? 1 : 0 )+"回");
				$("#usedcar_result_bonustimes").text(textbox2num('bonustimes')+"円");
				$("#usedcar_result_rate").text($("#u_rate").val()+"％");
				$("#usedcar_result_loantotal").text(num2price(resultArr['total'])+"円");
				//$("#usedcar_result_alltotalpayment").text(num2price(resultArr['total']+textbox2num('u_downpayment'))+"円");
				$("#usedcar_result_alltotalpayment").text(num2price(resultArr['total']+textbox2num('u_shitadori')+textbox2num('u_genkin'))+"円");
				
				
				// 受信後に画面表示
				$("#usedcar_result").show();
			});
				
			
		});
		return false;
	});



	// 中古車結果閉じる
	$("#usedcar_result_close").on("click",function(){
		$("#usedcar_result").hide(0,function(){
			$("#usedcar_input").show();
		});
		return false;
	});
	
	
	//サービスローンタブをクリック
	$("a.serviceloan-tab").on("click",function(){
		g_mode = "service";
		//サービスローンタブがクリックされたときの処理
		carArr = g_s_carArr;
		$("#newcar, #usedcar").hide(0,function(){
			$("#serviceloan").show();
		});
		return false;
	});

	//サービスローンの計算ボタンクリック
	$("#serviceloan_calc").on("click",function(){
		
		if( (textbox2num("s_mmmprice")>0 || textbox2num("s_mmsprice")>0) && $("#s_installments").val() > 24){
				alert2("お支払い回数に誤りがあります。");
		}else{
			// 計算をする
			var conditions = [];
			conditions['rate'] = $("#s_rate").val();
			conditions['installments'] = $('#s_installments').val();
			conditions['pricetax'] = g_s_carArr['pricetax'];
			conditions['optiontotal'] = 0;
			conditions['taxtotal'] = 0;
			conditions['downpayment'] = textbox2num('s_genkin');
			conditions['bonuspayment'] = textbox2num('s_bonuspayment');
			conditions['lastpayment'] = 0;
			conditions['sonota'] = 0;
			
			// ボーナス回数を厳密に計算
			conditions['bonustimes'] = get_bonustimes($("input[name='s_bonusmonth1']:checked").val(),$("input[name='s_bonusmonth2']:checked").val(),$("#s_month").val(),$("#s_installments").val());
			
			var resultArr = loancalc("std",conditions);
			
			/* 見積書に載せる項目
				車検費用											vicost
				修理代												repair
				整備・点検費用										maintenance
				オプション代金										option
				その他												sonota
				お値引き											discount
				メンテナンスプラス（有償メンテナンスパッケージ）	mmmprice
				保証プラス（有償アフターサービスサポート）			mmsprice
				支払金額合計										totalpayment
				頭金												downpayment
				ローン元金			
			*/
			
			
			// 月額3000円ガード
			if(resultArr['monthlypayment'] < 3000){
				alert2("入力条件をご確認ください。");
				return false;
			}else{
				$("#serviceloan_input").hide(0,function(){
			
					// 計算結果をサーバーに送信し、DBのIDを受け取る
					$.getJSON(g_site_url+"/cars/insertlog/",{
									salesman:$("#salesman").val(),
									plan:"svc",
									classname:"n",
									svicost:textbox2num('s_vicost'),
									srepair:textbox2num('s_repair'),
									smaintenance:textbox2num('s_maintenance'),
									soption:textbox2num('s_option'),
									discount:textbox2num('s_discount'),
									sonota:textbox2num('s_sonota'),
									mmmprice:textbox2num('s_mmmprice'),
									mmsprice:textbox2num('s_mmsprice'),
									totalpayment:textbox2num('s_totalpayment'),
									genkin:textbox2num('s_genkin'),
									downpayment:textbox2num('s_genkin'),
									loanprincipal:textbox2num('s_loanprincipal'),
									loantotal:resultArr['total'],
									installments:$('#s_installments').val(),
									firstpayment:resultArr['firstpayment'],
									monthlypayment:resultArr["monthlypayment"],
									bonuspayment:textbox2num('s_bonuspayment'),
									bonustimes:resultArr['bonustimes'],
									bonusmonth1:$("input[name='s_bonusmonth1']:checked").val(),
									bonusmonth2:$("input[name='s_bonusmonth2']:checked").val(),
									rate:$("#s_rate").val(),
									lastpayment:0,
									interest:resultArr['interest'],
									tax:g_s_tax,
									tm:stamp()
							},function(data){
						// DBのIDを受信
						$("#s_pdf").attr("href","./pdf/estimate/"+data.code);
						
						// 結果表示
						$("#service_result_totalpayment").text($('#s_totalpayment').val()+"円");
						$("#service_result_genkin").text($('#s_genkin').val()+"円");
		
						$("#service_result_loanprincipal").text($('#s_loanprincipal').val()+"円");
						$("#service_result_plan").text("サービスローン");
						$("#service_result_installments").text($('#s_installments').val()+"回");
						$("#service_result_rate").text($('#s_rate').val()+"％");
						$("#service_result_monthlypayment").text(num2price(resultArr['monthlypayment'])+"円");
						$("#service_result_bonuspayment").text($('#s_bonuspayment').val()+"円");
						$("#service_result_loantotal").text(num2price(resultArr['total'])+"円");
						$("#service_result_alltotalpayment").text(num2price(resultArr['total']+textbox2num('s_genkin'))+"円");
						
						// 受信後に画面表示
						$("#serviceloan_input").hide(0,function(){
							$("#serviceloan_result").show();
						});
						return false;
					});
				});
			}
		}
	});

	//サービスローン結果閉じる
	$("#serviceloan_result_close").on("click",function(){
		$("#serviceloan_result").hide(0,function(){
			$("#serviceloan_input").show();
		});
	});
	
	
	
	
	// ボーナス加算額上限表示ボタンクリック
	$("#getbptmax").on("click",function(){
		if($("#getbptmax").attr('disabled')){
			// 何もしない
		}else{
			var bonustimes = get_bonustimes($("input[name='bonusmonth1']:checked").val(),$("input[name='bonusmonth2']:checked").val(),nextMonth($("#newcar_month").val()),$("#installments").val());
			if(bonustimes == 0){
				alert2("ボーナス加算設定は無効です");
			}else{
				newcar_check(true);
				$("#bptmax").text(num2price(g_bptmax));
			}
		}
		return false;
	});
	// ボーナス加算額上限表示ボタンクリック
	$("#u_getbptmax").on("click",function(){
		if($("#u_getbptmax").attr('disabled')){
			// 何もしない
		}else{
			var bonustimes = get_bonustimes($("input[name='u_bonusmonth1']:checked").val(),$("input[name='u_bonusmonth2']:checked").val(),nextMonth($("#usedcar_month").val()),$("#u_installments").val());
			if(bonustimes == 0){
				alert2("ボーナス加算設定は無効です");
			}else{
				usedcar_check();
				$("#u_bptmax").text(num2price(g_u_bptmax));
			}
		}
		return false;
	});
	// ボーナス加算額上限表示ボタンクリック
	$("#s_getbptmax").on("click",function(){
		if($("#s_getbptmax").attr('disabled')){
			// 何もしない
		}else{
			var bonustimes = get_bonustimes($("input[name='s_bonusmonth1']:checked").val(),$("input[name='s_bonusmonth2']:checked").val(),$("#s_month").val(),$("#s_installments").val());
			if(bonustimes == 0){
				alert2("ボーナス加算設定は無効です");
			}else{
				service_check();
				$("#s_bptmax").text(num2price(g_s_bptmax));
			}
		}
		return false;
	});
	
	// コメント付きの個別提案書ボタンクリック
	/*
	$("#newcar_leaflet2").on("click",function(){
		$(this).attr("href",$(this).attr()+"?comment="+$("#newcar_comment").val());
	});
	*/
	
	// 登録年月変更
	$("#newcar_year,#newcar_month").on("change",function(){
		// 過去年月のガード
		if(!checkYearMonth($("#newcar_year").val(),$("#newcar_month").val())){
			// NGの場合
			alert2("登録年月をご確認ください");
			// 値を戻す
			$("#newcar_year").val(g_year);
			$("#newcar_month").val(g_month);
			
		}else{
			g_year = $("#newcar_year").val();
			g_month = $("#newcar_month").val();
			
			// 消費税切り替え
			var dt1 = new Date(2016, 6 - 1, 1); // 2015.02.17 価格改定対応 2015年4月以降は新価格、2015年3月一杯は旧価格
			// 2015.03.25 価格改定対応 2016年6月以降は新価格、2015年5月一杯は旧価格
			var dt2 = new Date($("#newcar_year").val()*1, $("#newcar_month").val()*1 - 1, 1);
			
			if(dt1.getTime() > dt2.getTime()) {
				// 消費税5% → 2015.02.17 両方共8%に変更
				g_tax = 0.08; // 2015.02.17
				g_price = 201605;
			}else{
				g_tax = 0.08;
				g_price = 201606;
			}
			$("#newcar_zei").empty().append("【消費税率 "+g_tax*100+"％】");
			$("#maker_option_comment").empty().append("※税込み金額をご入力ください【消費税率 "+g_tax*100+"％】<br>※サウンドスイートの場合は残価に算入できないためJPOSをご活用ください</span>");
			
			// モデル選択イベント発火
			if($("#carname").val()){
				$("#carname").trigger("change");
			}
		}
	});
	
	// メンテナンスパッケージ関連
	// ラジオボタンが変化したとき
	$("input[name='m_plan']").on("change",function(){
		m_plan_click($(this).val());
	});
	
	
	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
// 中古車関連


	// 登録年月変更
	$("#usedcar_year,#usedcar_month").on("change",function(){
		if(!checkYearMonth($("#usedcar_year").val(),$("#usedcar_month").val()) || !checkYearMonth2($("#usedcar_year").val(),$("#usedcar_month").val(),$("#tourokuyear").val(),$("#tourokumonth").val())){
			// NGの場合
			alert2("登録年月をご確認ください");
			// 値を戻す
			$("#usedcar_year").val(g_u_year);
			$("#usedcar_month").val(g_u_month);
			
		}else{
			g_u_year = $("#usedcar_year").val();
			g_u_month = $("#usedcar_month").val();
			
			// 消費税切り替え
			var dt1 = new Date(2014, 4 - 1, 1);
			var dt2 = new Date($("#usedcar_year").val()*1, $("#usedcar_month").val()*1 - 1, 1);
			
			if(dt1.getTime() > dt2.getTime()) {
				// 消費税5%
				g_u_tax = 0.05;
				
				// テーブル読み直し
			}else{
				g_u_tax = 0.08;
				// テーブル読み直し
			}
			$("#usedcar_zei").empty().append("【消費税率 "+g_u_tax*100+"％】");
			$("#u_maker_option_comment").empty().append("※税込み金額をご入力ください【消費税率 "+g_u_tax*100+"％】</span>");
			
			
			/* 2014.07.31 登録月変更で、クラスがリセットされる
			// モデル選択前に戻す
			$("#u_classname").val("");
			usedcar_input_disable();
			$("#u_classname").trigger("change");
			*/
			/*
			if($("#u_classname").val()){
				$("#u_classname").trigger("change");
			}
			*/
			// クラスがすでに選択されている場合は、プランを選択できるようにする
			if($("#u_classname").val()){
				$("#tourokuyear").trigger("change");
			}
			
			
			
		}
	});


	// 中古車クラス変更時→新車のモデル名変更時と同様にする
	$("#u_classname").on("change",function(){	
	
		var bmst = $(this).val();
	
		// carレコードの取得
		$.getJSON(g_site_url+"/cars/carjson/"+bmst+"&tm="+stamp(),function(json){
			// グローバルに格納
			g_u_carArr = json;
			carArr = g_u_carArr;
			
		
			// 初度登録年月初期化
			g_tourokuyear = $("#usedcar_year").val()-1;
			
			
			// 年式プルダウンを有効にする
			$("#tourokuyear").val(" 2013").removeAttr('disabled').select_disabled_off().custom_selectbox();
			$("#tourokumonth").val("10").removeAttr('disabled').select_disabled_off().custom_selectbox();
			
			// プランプルダウンを有効にする
			// グローバルを編集
			// 値を戻す
			g_tourokuyear = $("#tourokuyear").val();
			g_tourokumonth = $("#tourokumonth").val();
			
			usedcar_input_enable();
			
			// 5年以上昔のはwpだめ
			var yeardiff = getYearDiff($("#tourokuyear").val(),$("#tourokumonth").val(),$("#usedcar_year").val(),$("#usedcar_month").val());
			
			if( yeardiff < 5){
				// wp OK
			
				// プランプルダウンを更新
				optionTag = "<option value=''>▼選択してください</option>";
				optionTag += "<option value='wp'>ユーズドカーウェルカムプラン</option>";
				optionTag += "<option value='std'>通常ローン</option>";
			}else{
				// プランプルダウンを更新
				optionTag = "<option value=''>▼選択してください</option>";
				optionTag += "<option value='std'>通常ローン</option>";
			}
			$("#u_plan").empty().append(optionTag).val("").custom_selectbox();
			
			$("#u_rate,#u_installments").val("").empty().custom_selectbox();
			
			check_usedcar_calc_button();
		});
		
		usedcar_input_disable();
	});
	
	
	// 次回車検到来月変更時
	$("#n_inspection_y").on("change",function(){
		setNnInspection();
	});
	
	
	// 年式を選択後の処理
	$("#tourokuyear,#tourokumonth").on("change",function(){
		
		// 初度登録年月は、車両登録年月よりも過去でなければならない
		if(checkYearMonth2($("#tourokuyear").val(),$("#tourokumonth").val(),$("#usedcar_year").val(),$("#usedcar_month").val())){
			alert2("初度登録年月をご確認ください");
			// 値を戻す
			$("#tourokuyear").val(g_tourokuyear);
			$("#tourokumonth").val(g_tourokumonth);
		}else{
			// グローバルを編集
			// 値を更新
			g_tourokuyear = $("#tourokuyear").val();
			g_tourokumonth = $("#tourokumonth").val();
			
			usedcar_input_enable();
			
			// 5年以上昔のはwpだめ
			var yeardiff = getYearDiff($("#tourokuyear").val(),$("#tourokumonth").val(),$("#usedcar_year").val(),$("#usedcar_month").val());
			
			if( yeardiff < 5){
				// wp OK
			
				// プランプルダウンを更新
				optionTag = "<option value=''>▼選択してください</option>";
				optionTag += "<option value='wp'>ユーズドカーウェルカムプラン</option>";
				optionTag += "<option value='std'>通常ローン</option>";
			}else{
				// プランプルダウンを更新
				optionTag = "<option value=''>▼選択してください</option>";
				optionTag += "<option value='std'>通常ローン</option>";
			}
			// 車検関連を隠す
			$(".u_inspection").hide();
			
			$("#u_plan").empty().append(optionTag).val("").custom_selectbox();
			
			$("#u_rate,#u_installments").val("").empty().custom_selectbox();
			
			check_usedcar_calc_button();
		}
	});
	
	// 車両本体価格変更後（フォーカスアウト）
	/* 2014.02.18 統合
	$("#u_pricetax").on("focusout",function(){
		// 車両データを変更
		g_u_carArr['pricetax'] = textbox2num("u_pricetax");
		//carArr['pricetax'] = g_u_carArr['pricetax'];
		
		g_u_carArr['price'] = kirisute(1,textbox2num("u_pricetax")/(1.0+g_u_tax));
		//carArr['price'] = g_u_carArr['price'];
		check_usedcar_calc_button();
	});
	*/
		
		
	// プラン選択後の処理
	$("#u_plan").on("change",function(){

		// rateレコードの取得
		var patternid = g_u_carArr[$("#u_plan").val()+"ratepattern"];

		// グローバル変数に代入
		g_u_rateArr = findFromDBArr(Rates,{"patternid":patternid});
		
		// 経過件数・プランによって支払い回数プルダウンが変わる
		var yeardiff = getYearDiff($("#tourokuyear").val(),$("#tourokumonth").val(),$("#usedcar_year").val(),$("#usedcar_month").val());

		
		// 支払い回数プルダウン作成
		optionTag = "<option value=''>▼選択</option>";
		
		if($("#u_plan").val()=="wp"){
			// 2015.08.05 車検到来機能 by morita
			if($("#usedwp").val()=="1"){
				for(var i=12; i<=60;i+=1){
					// オプションタグ生成
					
					// 2015.08.05 車検到来機能 by morita
					if(useddata[chg_installments(i)]["yeardiff"] > yeardiff){
						optionTag += "<option value='"+i+"'>"+(i+1)+"回</option>";
					}
					
				}
				// 次回車検到来月
				enableNextInspection();
				setNnInspection();
			}else{
				for(var i=24; i<=60;i+=12){
					// オプションタグ生成
					if(useddata[i]["yeardiff"] > yeardiff){
						optionTag += "<option value='"+i+"'>"+(i+1)+"回</option>";
					}
				}
			}
		}else{
			// 車検関連OFF
			$(".u_inspection").hide();
			
			//$.each([3,6,10,12,15,18,24,30,36,42,48,54,60,66,72,78,84],function(){
			$.each([3,6,12,18,24,30,36,42,48,54,60,66,72,78,84],function(){
				optionTag += "<option value='"+this+"'>"+this*1+"回</option>";
			});
		}
		

		
		// 金利プルダウンをリセット
		$("#u_rate").val("").empty().custom_selectbox();
		
		// オプションタグ入れ替え
		$("#u_installments").empty().append(optionTag).val("").custom_selectbox();
			
		// 残価ボックスの設定
		if($(this).val()=="wp"){
			$("#u_lastpayment").removeAttr("readonly");
		}else{
			$("#u_lastpayment").attr("readonly",true);
		}

		check_usedcar_calc_button();
	});
	
	// 支払い回数選択時の処理
	$("#u_installments").on("change",function(){
		
		//車両本体価格を変更を発火して、price/pricetaxをグローバルに入れる
		$("#u_pricetax").trigger("focusout");
		
		// mmmの変更
		//$("#mmmprice").val(num2price(g_u_carArr.mmm));
		$("#mmmprice").val(0);
		
		
		// mms2の変更
		//$("#mmsprice").val(num2price(g_u_carArr.mms2));
		$("#mmsprice").val(0);
		
		// 車両本体価格・保証・メンテナンス代を反映する
		calcUsedCarParams();
		
		//g_carname = g_u_carArr.carname;
		
		// 残価を取得
		var lastpayment;
		switch($("#u_plan").val()){
			case "wp":
				// 残価MAXの0.8%にする
				// 2015.08.05 車検到来機能 by morita
				lastpayment = getmaxwplpt(chg_installments($("#u_installments").val()),"used");
				$("#u_lastpayment").val(num2price(lastpayment));
				break;
			default:
				// 残価はゼロ
				$("#u_lastpayment").val(0);
		}
											
		// 金利プルダウン作成

		// 2015.08.05 車検到来機能 by morita
		var tempArr = findFromDBArr(g_u_rateArr,{"installments":chg_installments($(this).val())});
		
		var rateArr = tempArr[0];
		/*
		$.each(["rate","lowrate","innerrate"],function(){
			// オプションタグ生成
			optionTag += "<option value='"+this+"'>"+rateArr[0][this]+"%</option>";
		});
		*/
		
		// 金利プルダウンを数字のみに変更　2014.03.04
		// 重複金利を削除
		
		if(rateArr){
			// 2014.08.05 社員用低金利を追加
			//var rateArr2 = array_unique([rateArr.rate*1,rateArr.lowrate*1,rateArr.innerrate*1]);
			var rateArr2 = array_unique([rateArr.rate*1,rateArr.rate*1-0.5,rateArr.lowrate*1,rateArr.innerrate*1]);
			rateArr2 = rateArr2.sort();
			
			var optionTag = "<option value=''>▼選択</option>";
			var num = rateArr2.length;
		
			for(var i=0;i<num;i++){
				optionTag += "<option value='"+rateArr2[i]+"'>"+addZero(rateArr2[i]+"")+"%</option>";
			}
			
			
			// オプションタグ入れ替え
			$("#u_rate").empty().append(optionTag).custom_selectbox();
		}
		
		usedcar_check();
		check_usedcar_calc_button();
		
	});
	
	// 金利選択時の処理
	$("#u_rate").on("change",function(){
		var rateArr =findFromDBArr(Rates,{"patternid":g_u_carArr[$("#plan").val()+"ratepattern"],"installments":$('#installments').val()});
		
		usedcar_check();
		check_usedcar_calc_button();
	});
	
	// ボーナス加算の有無
	$("input[name='u_bonus']").on("change",function(){
		if($(this).val()=="1"){
			// 1だったのがゼロになる→無効になる
			used_bonus_off();
		}else{
			used_bonus_on();
		}
	});
	
	//  モデル名変更時
	$("#u_carname").on("change",function(){
		check_usedcar_calc_button();
	});
	
	
	// サービスローン　支払い回数変更時
	$("#s_installments").on("change",function(){
		service_check();
	});
	
	// サービスローン登録年月日変更時
	$("#s_year,#s_month").on("change",function(){
		// 過去年月のガード
		var month = 1*$("#s_month").val()-1;
		var year = 1*$("#s_year").val();
		if(month == 0){
			month = 12;
			year = year-1;
		}else{
			// 何もしない
		}
		if(!checkYearMonth(year,month)){
			// NGの場合
			alert2("初回年月をご確認ください");
			// 値を戻す
			$("#s_year").val(g_s_year);
			$("#s_month").val(g_s_month);
			
		}else{
			g_s_year = $("#s_year").val();
			g_s_month = $("#s_month").val();
			
			var dt1 = new Date(2014, 4 - 1, 1);
			var dt2 = new Date($("#s_year").val()*1, $("#s_month").val()*1 - 1, 1);
			
			if(dt1.getTime() > dt2.getTime()) {
				// 消費税5%
				g_s_tax = 0.05;
				
				// テーブル読み直し
			}else{
				g_s_tax = 0.08;
				// テーブル読み直し
			}
			// 値のチェック
			service_check();
		}
	});
	
});


// 画面描画後におこなう初期化関数
function uiInit(){
	// 画面描画後に初期化する
	$("#usedcar").hide();
	$("#newcar_result").hide();
	$("#usedcar_result").hide();
	$("#service_result").hide();
	
	$("#newcar_calc").hide();
	$("#usedcar_calc").hide();
	$("#serviceloan_calc").hide();
	
	// smartのmmm/mms画像を非表示に
	$("#mmm_smart").hide();
	$("#mms_smart").hide();
	$("#ev_smart").hide();
	
	// m_plan のラジオボタンを非表示に
	$("#m_plan_check").hide();
	// evのテキストボックスを隠す
	$("#evprice,#ev_yen").hide();
	
	// 年月保持用
	g_year = $("#newcar_year").val();
	g_month = $("#newcar_month").val();
	
	g_u_year = $("#usedcar_year").val();
	g_u_month = $("#usedcar_month").val();
	
	g_s_year = $("#s_year").val();
	g_s_month = $("#s_month").val();
	

	newcar_input_disable();
	usedcar_input_disable();
	
	service_bonus_off();
}

// パラメーターチェック関数
function newcar_check(bonus_flg){
		// 新車パラメーターを更新
		// パラメーターチェック
		var message = "";
		
		var conditionArr = [];

		conditionArr['mode'] = true;		// メッセージは表示する
		conditionArr['bmst'] = $("#carname").val();
		conditionArr['total'] = textbox2num('totalpayment');						//合計金額
		conditionArr['installments'] = $('#installments').val()*1;	//支払い回数
		conditionArr['pricetax'] = textbox2num('pricetax');			//税込み車両本体価格
		conditionArr['optiontotal'] = textbox2num('makeroption');			//税込みオプション金額合計
		conditionArr['taxtotal'] = textbox2num('dealeroption');						//税金等の諸経費合計
		conditionArr['downpayment'] = textbox2num('genkin') + textbox2num('shitadori')-textbox2num('zansai');			//頭金
		if(conditionArr['downpayment'] < 0) conditionArr['downpayment'] = 0;
		conditionArr['zansai'] = textbox2num('zansai');					// 残債を考慮
		conditionArr['sonota'] = textbox2num('sonota')-textbox2num('discount')+textbox2num('mmmprice')+textbox2num('mmsprice')+textbox2num('evprice')+textbox2num('mbinsureance');					// その他オプション
		conditionArr['loanprincipal'] = textbox2num('loanprincipal');					// その他オプション
		conditionArr['lastpayment'] = textbox2num('lastpayment');					// その他オプション
		conditionArr['bonuspayment'] = textbox2num('bonuspayment');				// その他オプション
		conditionArr['rate'] = g_rate;			// 選択された金利
		conditionArr['selectedrate'] = g_rate;			// 選択された金利　_check()内部では、これも参照されているので
		
		var lptmaxmin_string="　　<br/>  ";
		var zankaritsu;
		
		
		// やっぱり常に厳密にする　2014.01.26
		bonus_flg = 0;
		
		if(bonus_flg){
			// 上限を求めるときは、最大回数にする
			var installments = textbox2num("installments");
			conditionArr['bonustimes'] = kirisute(1,installments / 6)+((installments % 6 == 0) ? 0 : 1);
		}else{
			// ボーナス回数を厳密に計算
			conditionArr['bonustimes'] = get_bonustimes($("input[name='bonusmonth1']:checked").val(),$("input[name='bonusmonth2']:checked").val(),nextMonth($("#newcar_month").val()),$("#installments").val());
		}

		// パラメーターの値チェック
		// 金利が選択されている場合のみ行う
		//if($("#rate").val()){
		if($("#rate").val()){
			var checkArr = [];
			checkArr= _check($("#plan").val(),conditionArr,"new");
			
			g_bptmax = checkArr['bptmax'];
			
			
			// メッセージ表示
			if(checkArr['message']){
				// パラメーター修正
				/*
				
				checkArr['downpayment']が変更されてで帰ってくる場合がある。この場合、残債が入力されている場合は、修正値に注意する
				
				１．残債がゼロの場合：頭金＝補正頭金値
				２．残債が頭金より小さい場合：頭金＝補正頭金値＋残債
				３．残債が頭金より大きい場合：頭金＝補正なし（check内では、常に頭金ゼロなので）
				*/
				$("#lastpayment").val(num2price(checkArr['lastpayment']));
				$("#bonuspayment").val(num2price(checkArr['bonuspayment']));
				
				// 頭金が変更されたか？？
				if(checkArr['downpayment'] != textbox2num('genkin') + textbox2num('shitadori')-textbox2num('zansai')){
					// 頭金が変更されてきた！！！
					
				
					// 残債が頭金より大きい場合は、残債は足さない
					if(textbox2num('genkin') + textbox2num('shitadori')-textbox2num('zansai')>0){
						$("#genkin").val(num2price(checkArr['downpayment']+textbox2num('zansai')));
						$("#shitadori").val(0);
					}else{
						// 補正なし
						//$("#downpayment").val(num2price(checkArr['downpayment']));
					}
				}
				$("#loanprincipal").val(num2price(checkArr['loanprincipal']));
	
				alert2(checkArr['message']);
				newcar_check();
				message = checkArr['message'];
			}
		}else{
			g_bptmax = "";
		}
		
		// 残価率・上限下限を取得
		if($("#installments").val()){
			if($("#plan").val() == "wp"){
				var installments = $("#installments").val();
				
				// 残価がmin/maxの時は、残価率をDBから直接持ってくるように変更 2014.04.16
				// →副作用怖いので、とりあえず新車はそのままにする
				var lptmax = getmaxwplpt(installments,"new");
				var lptmin = getminwplpt(installments,"new");
				var lptmaxrate = getmaxwplptrate(installments,"new");
				var lptminrate = getminwplptrate(installments,"new");
				var lastpayment = textbox2num('lastpayment');
			
				zankaritsu = Math.round(lastpayment/(g_carArr['price']*1+get_makeroptiontotal()*1)*100);
				/*
				if(lastpayment == lptmax){
					zankaritsu = lptmaxrate;
				}
				if(lastpayment == lptmin){
					zankaritsu = lptminrate;
				}
				*/
				lptmaxmin_string = num2price(lptmax)+"円（上限） / "+num2price(lptmin)+"円（下限） 残価率"+zankaritsu+"%<br/>※上限はメーカーオプションを含んだ金額です";
			}
		}
		
		// ボーナス加算MAX取得ボタンイベント発火
		//$("#getbptmax").trigger("click");
		
		// 残価上限下限表示
		$("#lptmaxmin").empty().append(lptmaxmin_string);
		
		
		calcNewCarParams();
		return message;
}

// パラメーターチェック関数
function usedcar_check(){
		// 新車パラメーターを更新
		// パラメーターチェック
		var conditionArr = [];

		conditionArr['mode'] = true;		// メッセージは表示する
		conditionArr['bmst'] = $("#u_classname").val();						// クラス名にbmstが入るので要注意！！！！！！！！！！！！！！！！
		conditionArr['total'] = textbox2num('u_totalpayment');						//合計金額
		conditionArr['installments'] = $('#u_installments').val()*1;	//支払い回数
		conditionArr['pricetax'] = textbox2num('u_pricetax');			//税込み車両本体価格
		conditionArr['optiontotal'] = textbox2num('u_makeroption');			//税込みオプション金額合計
		conditionArr['taxtotal'] = textbox2num('u_dealeroption');						//税金等の諸経費合計
		//conditionArr['downpayment'] = textbox2num('u_downpayment')-textbox2num('u_zansai');			//頭金
		conditionArr['downpayment'] = textbox2num('u_shitadori')+textbox2num('u_genkin');			//頭金
		conditionArr['zansai'] = textbox2num('u_zansai');			//頭金
		conditionArr['sonota'] = +textbox2num('u_sonota')-textbox2num('u_discount')+textbox2num('mbinsureance');					// その他オプション
		conditionArr['loanprincipal'] = textbox2num('u_loanprincipal');					// その他オプション
		conditionArr['lastpayment'] = textbox2num('u_lastpayment');					// その他オプション
		conditionArr['bonuspayment'] = textbox2num('u_bonuspayment');				// その他オプション
		conditionArr['rate'] = $("#u_rate").val()*1.0;			// 選択された金利
		conditionArr['selectedrate'] = $("#u_rate").val()*1.0;		// 選択された金利　_check()内部では、これも参照されているので
		
		var lptmaxmin_string="　　<br/>  ";
		var zankaritsu;


		// ボーナス回数を厳密に計算
		conditionArr['bonustimes'] = get_bonustimes($("input[name='u_bonusmonth1']:checked").val(),$("input[name='u_bonusmonth2']:checked").val(),nextMonth($("#usedcar_month").val()),$("#u_installments").val());

		// パラメーターの値チェック
		// 金利が選択されている場合のみ行う
		if($("#u_rate").val()){
			var checkArr = [];
			var u_pricetax;
			var loanprinipal_min;
			var u_loanprincipal;
			var temp;
			
			switch($("#u_plan").val()){
				case "wp":
					// temp = kiriage(10000,useddata[$("#u_installments").val()]['minlpprate']*g_u_carArr['price']/100); 2015/4/10 1万円単位へ切り上げ→1円未満切り捨て
					// 2015.08.05 車検到来機能 by morita
					temp = kirisute(1,useddata[chg_installments($("#u_installments").val())]['minlpprate']*g_u_carArr['price']/100);
					if(temp > 500000){
						loanprinipal_min = temp*1;
					}else{
						loanprinipal_min = 500000;
					}
					break;
				case "std":
					loanprinipal_min = 300000;
					break;
				default:
					loanprinipal_min = 300000;
			}
			
			// ローン元金の最低限のチェック
			u_loanprincipal = conditionArr['pricetax'] + conditionArr['taxtotal'] + conditionArr['optiontotal'] - conditionArr['downpayment'] + conditionArr['sonota'];
			
			// alert("u_loanprincipal" + u_loanprincipal);
			// alert("loanprinipal_min" + loanprinipal_min);
			
			if(u_loanprincipal<loanprinipal_min){
				// 現金をゼロにてまにあう？
				$("#u_genkin").val(0);
				conditionArr['downpayment'] = textbox2num('u_shitadori')+textbox2num('u_genkin');			//頭金
				
				u_loanprincipal = conditionArr['pricetax'] + conditionArr['taxtotal'] + conditionArr['optiontotal'] - conditionArr['downpayment'] + conditionArr['sonota'];
				
				if(u_loanprincipal<loanprinipal_min){
					// それでもだめなら、車両本体価格を変更
					u_pricetax = loanprinipal_min - (conditionArr['taxtotal'] + conditionArr['optiontotal'] - conditionArr['downpayment'] + conditionArr['sonota']);
					if(pricetax<0){
						u_pricetax = 0;
					}
					// 車両データを変更
					g_u_carArr['pricetax'] = u_pricetax;
					//carArr['pricetax'] = g_u_carArr['pricetax'];
					$("#u_pricetax").val(num2price(u_pricetax));
					
					g_u_carArr['price'] = kirisute(1,u_pricetax/(1.0+g_u_tax));
					//carArr['price'] = g_u_carArr['price'];
				}
				
				// アラートを出して車両本体価格を変更する
				alert2("ローン元金下限を下回っています");
				
			}else{
						
						
			
				checkArr= _check($("#u_plan").val(),conditionArr,"used");
				
				g_u_bptmax = checkArr['bptmax'];
				
				// パラメーター修正
				$("#u_lastpayment").val(num2price(checkArr['lastpayment']));
				$("#u_bonuspayment").val(num2price(checkArr['bonuspayment']));
				//$("#u_downpayment").val(num2price(checkArr['downpayment']+textbox2num('u_zansai')));
				$("#u_genkin").val(num2price(checkArr['downpayment']-textbox2num('u_shitadori')));
				$("#u_loanprincipal").val(num2price(checkArr['loanprincipal']));
	
				
				// メッセージ表示
				if(checkArr['message']){
					alert2(checkArr['message']);
					usedcar_check();
				}
			}
		}else{
			g_u_bptmax = "";
		}
		
		// 残価率・上限下限を取得
		if($("#u_installments").val()){
			if($("#u_plan").val() == "wp"){
				// 2015.08.05 車検到来機能 by morita
				var installments = chg_installments($("#u_installments").val());
				// 残価がmin/maxの時は、残価率をDBから直接持ってくるように変更 2014.04.16
				var lptmax = getmaxwplpt(installments,"used");
				var lptmin = getminwplpt(installments,"used");
				var lptmaxrate = getmaxwplptrate(installments,"used");
				var lptminrate = getminwplptrate(installments,"used");
				var lastpayment = textbox2num('u_lastpayment');
			
				zankaritsu = Math.round(lastpayment/g_u_carArr['price']*100);
				if(lastpayment == lptmax){
					zankaritsu = lptmaxrate;
				}
				if(lastpayment == lptmin){
					zankaritsu = lptminrate;
				}
				lptmaxmin_string = num2price(lptmax)+"円（上限） / "+num2price(lptmin)+"円（下限） 残価率"+zankaritsu+"%<br/>";
			}
		}

		// ボーナス加算MAX取得ボタンイベント発火
		//$("#u_getbptmax").trigger("click");
		
		// 残価上限下限表示
		$("#u_lptmaxmin").empty().append(lptmaxmin_string);
		
		
		calcUsedCarParams();
}


// パラメーターチェック関数
function service_check(){	

	calcServiceCarParams();
	
	if(textbox2num("s_loanprincipal") >= 30000 && textbox2num("s_loanprincipal")<= 3000000){
		conditionArr = [];
		
		conditionArr['installments'] = textbox2num('s_installments');
		conditionArr['bonuspayment'] = textbox2num('s_bonuspayment');
		conditionArr['downpayment'] = textbox2num('s_genkin');
		conditionArr['loanprincipal'] = textbox2num('s_loanprincipal');
		conditionArr['sonota'] = 0;
		conditionArr['optiontotal'] = 0;
		conditionArr['sonota'] = 0;
		conditionArr['automobiletax'] = 0;
		conditionArr['acquisitiontax'] = 0;
		conditionArr['automobiletax'] = 0;
		conditionArr['tonnagetax'] = 0;
		conditionArr['insurance'] = 0;
		conditionArr['recycle'] = 0;
		conditionArr['taxtotal'] = 0;
		conditionArr['accessory'] = 0;
		
		// ボーナス回数を厳密に計算
		conditionArr['bonustimes'] = get_bonustimes($("input[name='s_bonusmonth1']:checked").val(),$("input[name='s_bonusmonth2']:checked").val(),$("#s_month").val(),$("#s_installments").val());
		
		var checkArr = [];
		
		calcServiceCarParams();
		
		checkArr = check_serviceloan(conditionArr);
		
		g_s_bptmax = checkArr['bptmax'];
				
		// パラメーター修正
		$("#s_bonuspayment").val(num2price(checkArr['bonuspayment']));
		$("#s_genkin").val(num2price(checkArr['downpayment']));
		$("#s_loanprincipal").val(num2price(checkArr['loanprincipal']));
	
		// メッセージ表示
		if(checkArr['message']){
			alert2(checkArr['message']);
			service_check();
		}
		calcServiceCarParams();
				
		// ボーナス加算MAX取得ボタンイベント発火
		//$("#u_getbptmax").trigger("click");
	}else{
		alert2("ローン元金の上限・下限をご確認ください.");
	}
	check_service_calc_button();
}


// ロジックの初期化関数
function logicInit(callback){
	$.get(g_site_url+"/cars/version?tm="+stamp(),function(data){
		// バージョン番号を書く
		$(".version").html("ver."+data+" ｜ <a id='logout' href='"+g_site_url+"/users/logout?tm="+stamp()+"' >ログアウト</a>");
	});
	// ajaxにてデータ取得
	$.getJSON(g_site_url+"/cars/gettable/Rate?tm="+stamp(),function(json){
		Rates = json;
		$.getJSON(g_site_url+"/cars/gettable/Bptrate?tm="+stamp(),function(json){
			Bptrates = json;
			$.getJSON(g_site_url+"/cars/gettable/Lpprate?tm="+stamp(),function(json){
				Lpprates = json;
				$.getJSON(g_site_url+"/cars/gettable/Lptrate?tm="+stamp(),function(json){
					Lptrates = json;
					$.getJSON(g_site_url+"/cars/gettable/Initrate?tm="+stamp(),function(json){
						Initrates = json;
						callback();
					});
				});
			});
		});
	});
}

// 新車のパラメーターを計算する
function calcNewCarParams(){
		// cartotal: 最終的な車の値段
		// totalpayment: 車の値段＋税金やメンテナンスパッケージ費用
			
		var cartotal = textbox2num('pricetax') + textbox2num('makeroption') + textbox2num('dealeroption') - textbox2num('discount');
		
		$("#cartotal").val(num2price(cartotal));
		
		var totalpayment = cartotal + textbox2num('mbinsureance') + textbox2num('sonota') + textbox2num('mmmprice') + textbox2num('mmsprice') + textbox2num('evprice');
		
		$("#totalpayment").val(num2price(totalpayment));
		
		var loanprincipal = totalpayment - (textbox2num('genkin') + textbox2num('shitadori')) + textbox2num('zansai');
		$("#loanprincipal").val(num2price(loanprincipal));
		
		// プラスモード判断
		if(textbox2num('genkin') + textbox2num('shitadori') >= textbox2num('zansai')){
			// 通常モード
			plusmode = 0;
		}else{
			// プラスモード
			plusmode = 1;
		}
		
		// 残債がゼロより大きく、かつプランがwp/swpの時は、プラン選択をやり直し
		if((textbox2num("zansai") - (textbox2num("genkin")+textbox2num("shitadori"))) > 0 ){
			if( $("#plan").val() == "std" ||　$("#plan").val() == "sup" || $('#plan option:selected').text() == "ウェルカムプラン" || $('#plan option:selected').text() == "スーパーウェルカムプラン" || (textbox2num("zansai") - (textbox2num("genkin")+textbox2num("shitadori"))) > textbox2num("pricetax") || (textbox2num("zansai") - (textbox2num("genkin")+textbox2num("shitadori"))) > 5000000){
				alert2("ローン元金の上限を上回っています。");
				$("#zansai").val(0);
				// 通常モードに戻す
				plusmode = 0;
				
				// ローン元金再計算
				loanprincipal = totalpayment - (textbox2num('genkin') + textbox2num('shitadori')) + textbox2num('zansai');
				$("#loanprincipal").val(num2price(loanprincipal));
			}
		}
}

// 中古車のパラメーターを計算する
function calcUsedCarParams(){
		// cartotal: 最終的な車の値段
		// totalpayment: 車の値段＋税金やメンテナンスパッケージ費用
			
		var cartotal = textbox2num('u_pricetax') + textbox2num('u_makeroption') + textbox2num('u_dealeroption') - textbox2num('u_discount');
		
		$("#u_cartotal").val(num2price(cartotal));
		
		var totalpayment = cartotal + textbox2num('u_mbinsureance') + textbox2num('u_sonota') + textbox2num('u_mmmprice') + textbox2num('u_mmsprice');
		
		$("#u_totalpayment").val(num2price(totalpayment));
		
		//var loanprincipal = totalpayment - textbox2num('u_downpayment') + textbox2num('u_zansai');
		var loanprincipal = totalpayment - textbox2num('u_genkin') - textbox2num('u_shitadori');
		$("#u_loanprincipal").val(num2price(loanprincipal));
		
		check_usedcar_calc_button();
		
}

// サービスローンのパラメーターを計算する
function calcServiceCarParams(){
		// cartotal: 最終的な車の値段
		// totalpayment: 車の値段＋税金やメンテナンスパッケージ費用
			
		var totalpayment = textbox2num('s_vicost') + textbox2num('s_repair') + textbox2num('s_maintenance') + textbox2num('s_option') + textbox2num('s_sonota') - textbox2num('s_discount') + textbox2num('s_mmmprice') + textbox2num('s_mmsprice');
		
		$("#s_totalpayment").val(num2price(totalpayment));
		
		var loanprincipal = totalpayment - textbox2num('s_genkin');
		$("#s_loanprincipal").val(num2price(loanprincipal));
		
		g_s_carArr['pricetax'] = textbox2num("s_totalpayment");
		g_s_carArr['price'] = kirisute(1,textbox2num("s_totalpayment")/(1.0+g_s_tax));
		
		check_usedcar_calc_button();
		
}

// 新車画面入力を無効にする
function newcar_input_disable(){
	// モデルが選択されていないので、ブランクにしてリードオンリーに
	$("#pricetax,#makeroption,#dealeroption,#discount,#total,#sonota,#mbinsureance,#mmmprice,#mmsprice,#evprice,#totalpayment,#genkin,#shitadori,#zansai,#loanprincipal,#lastpayment,#bonus,#bonusmonth1,#bonusmonth2").val("").attr('readonly',true);
	$("#plan,#rate,#installments").empty().val("").attr("disabled","disabled").select_disabled_on().custom_selectbox();
	
	bonus_off();
	$("input[name='bonus']:radio").attr("disabled",true).radio_disabled_on().custom_selectbox();
	$("#m_plan_check").hide();
	
}

// 新車画面入力を有効にする
function newcar_input_enable(){
	// モデルが選択されていないので、ブランクにしてリードオンリーに
	$("#makeroption,#dealeroption,#discount,#sonota,#mbinsureance,#genkin,#shitadori,#zansai,#bonus,#bonusmonth1,#bonusmonth2").removeAttr('readonly').val(0);
	$("#plan,#rate,#installments").removeAttr('disabled').select_disabled_off().custom_selectbox();
	// 値引き欄はブランクにする
	$("#discount").val("");
	
	//bonus_on();
	$("input[name='bonus']:radio").removeAttr("disabled").radio_disabled_off().custom_selectbox();
}

// 中古車画面入力を無効にする
function usedcar_input_disable(){
	// モデルが選択されていないので、ブランクにしてリードオンリーに
	$("#u_makeroption,#u_dealeroption,#u_discount,#u_cartotal,#u_sonota,#u_mbinsureance,#u_mmmprice,#u_mmsprice,#u_totalpayment,#u_shitadori,#u_genkin,#u_loanprincipal,#u_lastpayment,#u_bonus,#u_bonusmonth1,#u_bonusmonth2").val("").attr('readonly',true);
	$("#u_plan,#u_rate,#u_installments").empty().val("").attr("disabled","disabled").select_disabled_on().custom_selectbox();
	$("#tourokuyear").val("").attr("disabled","disabled").select_disabled_on().custom_selectbox();
	$("#tourokumonth").val("").attr("disabled","disabled").select_disabled_on().custom_selectbox();
	
	used_bonus_off();
	$("input[name='u_bonus']:radio").attr("disabled",true).radio_disabled_on().custom_selectbox();
	
	// 車検関連OFF
	$(".u_inspection").hide();
	
}

// 中古車画面入力を有効にする
function usedcar_input_enable(){
	// モデルが選択されていないので、ブランクにしてリードオンリーに
	$("#u_makeroption,#u_dealeroption,#u_discount,#u_sonota,#u_mbinsureance,#u_mmmprice,#u_mmsprice,#u_shitadori,#u_genkin,#u_bonus,#u_bonusmonth1,#u_bonusmonth2").removeAttr('readonly').val(0);
	$("#u_plan,#u_rate,#u_installments").removeAttr('disabled').select_disabled_off().custom_selectbox();
	// 値引き欄はブランクにする
	$("#u_discount").val("");
	
	//used_bonus_on();
	$("input[name='u_bonus']:radio").removeAttr("disabled").radio_disabled_off().custom_selectbox();
}

function bonus_on(){
	// 有効になったとき
	// ボーナス関連をenableにする
	//$('input[name="bonus"][value="0"]').prop('checked', true).radio_update();
	$("#bonuspayment").removeAttr("readonly").val(0).custom_selectbox();
	$('input[name="bonusmonth1"]').removeAttr("disabled").radio_disabled_off().custom_selectbox();
	$('input[name="bonusmonth2"]').removeAttr("disabled").radio_disabled_off().custom_selectbox();
	$('#getbptmax').removeAttr('disabled');

}

function bonus_off(){
	// 無効になったとき
	$("#bonuspayment").val(0);
	// ボーナス関連をreadonlyにする
	$("#bonuspayment").attr('readonly', true).custom_selectbox();;
	$('input[name="bonusmonth1"]:radio').attr('disabled', true).radio_disabled_on().custom_selectbox();
	$('input[name="bonusmonth2"]:radio').attr('disabled', true).radio_disabled_on().custom_selectbox();
	$('#getbptmax').attr('disabled', true);
	$('#bptmax').empty();
}
function used_bonus_on(){
	// 有効になったとき
	// ボーナス関連をenableにする
	$("#u_bonuspayment").removeAttr("readonly").val(0).custom_selectbox();
	$('input[name="u_bonusmonth1"]').removeAttr("disabled").radio_disabled_off().custom_selectbox();
	$('input[name="u_bonusmonth2"]').removeAttr("disabled").radio_disabled_off().custom_selectbox();
	$('#u_getbptmax').removeAttr('disabled');
}

function used_bonus_off(){
	// 無効になったとき
	$("#u_bonuspayment").val(0);
	//$('input[name="u_bonus"]:radio').val(0).custom_selectbox();
	// ボーナス関連をreadonlyにする
	$("#u_bonuspayment").attr('readonly', true).custom_selectbox();;
	$('input[name="u_bonusmonth1"]:radio').attr('disabled', true).radio_disabled_on().custom_selectbox();
	$('input[name="u_bonusmonth2"]:radio').attr('disabled', true).radio_disabled_on().custom_selectbox();
	$('#u_getbptmax').attr('disabled', true);
	$('#u_bptmax').empty();
}
function service_bonus_on(){
	// 有効になったとき
	// ボーナス関連をenableにする
	$("#s_bonuspayment").removeAttr("readonly").val(0).custom_selectbox();
	$('input[name="s_bonusmonth1"]').removeAttr("disabled").radio_disabled_off().custom_selectbox();
	$('input[name="s_bonusmonth2"]').removeAttr("disabled").radio_disabled_off().custom_selectbox();
	$('#s_getbptmax').removeAttr('disabled');
}

function service_bonus_off(){
	// 無効になったとき
	$("#s_bonuspayment").val(0);
	//$('input[name="s_bonus"]:radio').val(0).custom_selectbox();
	// ボーナス関連をreadonlyにする
	$("#s_bonuspayment").attr('readonly', true).custom_selectbox();;
	$('input[name="s_bonusmonth1"]:radio').attr('disabled', true).radio_disabled_on().custom_selectbox();
	$('input[name="s_bonusmonth2"]:radio').attr('disabled', true).radio_disabled_on().custom_selectbox();
	$('#s_getbptmax').attr('disabled', true);
	$('#s_bptmax').empty();
}

function textbox2num(id){
	var num = price2num($("#"+id).val());
	
	if(!num) num = 0;
	
	return num;
}

function get_makeroptiontotal(){
	switch(g_mode){
		case "new":
			return textbox2num("makeroption")/(1.00+g_tax);
			break;
		case "used":
			return textbox2num("u_makeroption")/(1.00+g_u_tax);
			break;
		case "service":
			return textbox2num("s_makeroption")/(1.00+g_s_tax);
			break;
		default:
			return 0;
	}
}

function newcar_leflet_comment(){
	//2016.08.26 copy & change
//	var url = $("#newcar_leaflet").attr("href")+"?comment="+$("#newcar_comment").val();
	var url = $("#newcar_leaflet").attr("href")+"?comment="+$("#newcar_comment").val()+"&user_name="+$("#user_name").val();

	//alert(url);
	
	$("#newcar_leaflet2").attr("href",encodeURI(url));
	
	return true;
}
//2016.08.26 add
function newcar_estimate(){
	var url = $('#newcar_pdf').attr('href')+"user_name="+$("#estimate_user_name").val();
	$("#newcar_pdf2").attr("href",encodeURI(url));
	return true;
}

function check_newcar_calc_button(){
	if($("#classname").val() && $("#carname").val() && $("#installments").val() && $("#rate").val() && $("#plan").val() && textbox2num("loanprincipal")<100000000){
		$("#newcar_calc").show();
	}else{
		$("#newcar_calc").hide();
	}
}

function check_usedcar_calc_button(){
	// 2014.02.18 中古車名なしでも計算できるように変更
	//if($("#u_classname").val() && $("#u_carname").val() && $("#u_installments").val() && $("#u_rate").val() && $("#u_plan").val() && $("#tourokuyear").val() && ( ( $("#u_plan").val() == "wp" && textbox2num("u_loanprincipal")>=500000) || ( $("#u_plan").val() == "std" && textbox2num("u_loanprincipal")>=300000))){
	if($("#u_classname").val() && $("#u_installments").val() && $("#u_rate").val() && $("#u_plan").val() && $("#tourokuyear").val() && ( ( $("#u_plan").val() == "wp" && textbox2num("u_loanprincipal")>=50000) || ( $("#u_plan").val() == "std" && textbox2num("u_loanprincipal")>=300000))){
		$("#usedcar_calc").show();
	}else{
		$("#usedcar_calc").hide();
	}
}

function check_service_calc_button(){
	if(textbox2num("s_loanprincipal") >= 30000 && textbox2num("s_loanprincipal")<= 3000000){
		$("#serviceloan_calc").show();
	}else{
		$("#serviceloan_calc").hide();
	}
}


// サービスローンのチェックルーチンのみ外に出した。インターフェースは、_check()と同様にする
function check_serviceloan(conditionArr){
	
	// エラーメッセージ初期化
	var message = "";
	
	var loanprincipal = conditionArr['loanprincipal'];
	var downpayment = conditionArr['downpayment'];
	var bonuspayment = conditionArr['bonuspayment'];
	// 残価はゼロ
	var lastpayment = 0;
	var installments = conditionArr['installments'];
	
	var bonustimes;
	
	var bptmax;
	var bptmin;
	var dptmax;
	var dptmin;
	var lppmax;
	var lppmin;
	
	
	if(conditionArr['bonustimes'] == undefined){
		bonustimes = Math.round(installments / 6);
	}else{
		bonustimes = Math.round(conditionArr['bonustimes']);
	}
	

	// ローン元金 loanprincipal
	lppmax = 3000000;
	lppmin = 30000;
	
	// max,minでフィルタリング
	if(loanprincipal > lppmax){
		message += "ローン元金の上限は￥"+number_format(lppmax)+"です.";
		loanprincipal = lppmax;
	}
	if(loanprincipal < lppmin){
		message += "ローン元金の下限は￥"+number_format(lppmin)+"です.";
		loanprincipal = lppmin;
	}
	
	
	// 頭金 downpayment
	dptmin = 0;
	dptmax = loanprincipal + downpayment - lppmin;
	
	// max,minでフィルタリング
	if(downpayment > dptmax){
		if(dptmax <= 0){
			dptmax = 0;
		}
		message += "頭金の上限は￥"+number_format(dptmax)+"です.";
		downpayment = dptmax;
	}
	if(downpayment < dptmin){
		if(dptmin <= 0){
			dptmin = 0;
		}else{
			message += "頭金の下限は￥"+number_format(dptmin)+"です.";
		}
		downpayment = dptmin;
	}
	
	
	
	// ボーナス加算額 bonuspayment
	//bonustimes = installments / 6;
	if(bonustimes > 0){
		bptmax = kirisute(1000,(loanprincipal - lastpayment) * 0.6 / bonustimes);
	}else{
		bptmax = 0;
	}
	if(bptmax < 0){
		bptmax = 0;
	}
	bptmin = 0;
	
	// 月額が3000円未満にならないようにボーナス加算額上限を調整する 2011.11.21 by morita
	/* 月額3000円ガードがあるので、省略 2013.08.22 by morita
	2014.04.28 ボーナス加算上限が誤って表示されるので、コメントアウト解除 */
	while(1){
		
		conditions = {
				'installments'		: installments,
				'loanprincipal'		: loanprincipal,
				'pricetax'			: carArr['pricetax'],
				'price'				: carArr['price'],
				'automobiletax'		: conditionArr['automobiletax'],
				'acquisitiontax'	: conditionArr['acquisitiontax'],
				'tonnagetax'		: conditionArr['tonnagetax'],
				'insurance'			: conditionArr['insurance'],
				'recycle'			: conditionArr['recycle'],
				'optiontotal'		: conditionArr['optiontotal'],
				'accessory'			: conditionArr['accessory'],
				'taxtotal'			: conditionArr['taxtotal'],
				'downpayment'		: downpayment,
				'bonuspayment'		: bptmax,
				'lastpayment'		: lastpayment,
				'sonota'			: conditionArr['sonota'],
				'bonustimes'		: bonustimes
			};
		// 金利パターン大幅変更 2012.05.26 by morita
		// 金利初期値を決める
		conditions['rate'] = 1*$("#s_rate").val();
		/*
		if(1){
			conditions['rate'] = innerrate;
		}else{
			conditions['rate'] = lowrate;
		}
		*/
		
		
		var resultArr = [];
		resultArr = loancalc("std",conditions);
		
		if(resultArr['monthlypayment']>= 3000){
			break;
		}else{
			if(bptmax <= 0){
				break;
			}else{
				bptmax -= 1000;
				//debug(resultArr['monthlypayment']);
				//debug(bptmax);
				
			}
		}
	}
	/* 2014.04.28 ボーナス加算上限が誤って表示されるので、コメントアウト解除 */
	
	
	
	
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
	
	
	//JSON文字列作成
	jsonArr = {
					"bptmin":bptmin,
					"bptmax":bptmax,
					"bonuspayment":bonuspayment,
					"dptmax":dptmax,
					"dptmin":dptmin,
					"downpayment":downpayment,
					"loanprincipal":loanprincipal,	// 2014.01.10 add by morita
					"installments":installments,
					"message":message
				};

	return jsonArr;
}

function alert2(string){
	var string2;
	
	// アラートが出た直後は、計算を効かなくする
	g_calc_ok = false;
	setTimeout(function(){
		g_calc_ok = true;
	},500);
	
	string2 = string.replace(".", "。\n");
	string3 = string2.replace(".", "。");
	
	// iosのバグ対策
	// https://discussionsjapan.apple.com/thread/10139305
	setTimeout(function(){
		alert(string3);
	},200);
}





// ボーナス加算の回数を算出する
function get_bonustimes(summer,winter,start,installments){
	var month_flg = [];
	var month_data = [];
	var temp;
	var temp2;
	var result;
	
	var bonustimes = 0;
	
	installments *= 1;
	
	for(i=1;i<=12;i++){
		temp = start%12;
		if(temp == 0) temp = 12;
		
		month_data[i] = temp;
		if(temp == summer || temp == winter){
			month_flg[i] = 1;
		}else{
			month_flg[i] = 0;
		}
		start ++;
	}
	
	result = installments % 12;
	
	if(result == 0){
		// 支払い回数が12で割り切れる場合は、毎年必ず2回
		bonustimes = installments / 6;
	}else{
		// 割り切れない場合は、出た分に何回ボーナス月が含まれるかを算出
		temp2 = 0;
		for(i=1;i<=result;i++){
			temp2 += month_flg[i];
		}
		
		bonustimes = kirisute(1,(installments / 12))*2 + temp2;
	
	}
	
	return bonustimes;
}

// ログアウトするかどうかチェックする
function checklogout(){
	var time = getUnixTime();
	var rtn;
	
	if(time - g_time >= g_logoutsec){
		// 時間経過したのでログアウト
		forcelogout();
		rtn = true;
	}else{
		rtn = false;
	}
	return rtn;	
}

// ログアウトする
function forcelogout(){
	// 強制的にログアウト ログアウトクリックイベント発生
	var url = "./users/logout?tm="+stamp();
	
	//alert(url);
	
 	location.href= url;
}

// smartメンテナンスパッケージクリック時の処理
function m_plan_click(value){
	var installments = $("#installments").val();
	
	if(value =="ev"){
		// evの場合
		// evを初期化してアクティブに
		$("#evprice").val(num2price(g_carArr['ev'])).removeAttr("readonly");
		$("#mmmprice").val(0).attr("readonly",true);
		$("#mmsprice").val(0).attr("readonly",true);
	}else{
		$("#evprice").val(num2price(g_carArr['ev'])).removeAttr("readonly");
		//$("#evprice").val(0).attr("readonly",true);
		$("#mmmprice").removeAttr("readonly");
		$("#mmsprice").removeAttr("readonly");
		

		// 初期値を入れる
		if(installments == 36){
			$("#mmmprice").val(num2price(g_carArr.mmm));
			$("#mmsprice").val(0);
			
		}else if(installments >= 60){
			$("#mmmprice").val(num2price(g_carArr.mmm));
			$("#mmsprice").val(num2price(g_carArr.mms));
		}else{
			$("#mmmprice").val(0);
			$("#mmsprice").val(0);
		}
	}
	newcar_check();
}

// リーフレットの背景に使う画像データを取得する
function get_leaflet_data(plan,classname,installments){
	var leaflet = "";
	
var data = {
				"A-Class":				["anshin2","anshin2","anshin2","marugoto2"],
				"B-Class":				["anshin2","anshin2","anshin2","marugoto2"],
				"CLA-Class":			["anshin2","anshin2","anshin2","marugoto2"],
				"CLA-Class Shooting Brake":			["anshin2","anshin2","anshin2","marugoto2"],
				"C-Class Sedan":		["anshin2","anshin2","anshin2","marugoto2"],
				"C-Class Stationwagon":	["anshin2","anshin2","anshin2","marugoto2"],
				"C-Class Coupe":		["anshin2","anshin2","anshin2","marugoto2"],
				"E-Class Sedan":		["anshin2","anshin2","anshin2","anshin"],
				"S-Class":				["anshin2","anshin2","anshin2","anshin"],
				"GLA-Class":			["anshin2","anshin2","anshin2","marugoto2"],
				"GLC":			["anshin2","anshin2","anshin2","marugoto2"]
};

	if(plan == "wp"){
		if(data[classname]){
			leaflet = data[classname][installments/12-2];
		}
	}
	/*
	
	if(plan == "wp"){
		switch(classname){
			case 'A-Class':
			case 'B-Class':
			case 'CLA-Class':
			case 'C-Class Sedan':
			case 'C-Class Stationwagon':
			case 'C-Class Coupe':
			case 'GLA-Class':
			case 'CLS-Class':
			case 'CLS-Class Shooting Brake':
			case 'GLC-Class':
				switch(installments){
					case "24":
					case "36":
					case "48":
						leaflet = "anshin2";
						break;
					case "60":
						leaflet = "marugoto";
						break;
				}
				break;
			case 'E-Class Sedan':
			case 'E-Class Stationwagon':
			case 'E-Class Coupe':
			case 'E-Class Cabriolet':
			case 'S-Class':
				switch(installments){
					case "24":
					case "36":
					case "48":
						leaflet = "anshin2";
						break;
					case "60":
						leaflet = "anshin";
						break;
				}
				break;
		}
	}
	*/
	return leaflet;
}



// URLパラメーターを解析する
function GetScriptParams()
{
    var scripts = document.getElementsByTagName( 'script' );
    var src = scripts[ scripts.length - 1 ].src;

    var query = src.substring( src.indexOf( '?' ) + 1 );
    var parameters = query.split( '&' );

    // URLクエリを分解して取得する
    var result = new Object();
    for( var i = 0; i < parameters.length; i++ )
    {
        var element = parameters[ i ].split( '=' );

        var paramName = decodeURIComponent( element[ 0 ] );
        var paramValue = decodeURIComponent( element[ 1 ] );

        result[ paramName ] = paramValue;
    }

    return result;
}

// UNIXTimeを取得する
function getUnixTime()
 {
   return parseInt((new Date)/1000);
 }
 
// 入力された年月が過去のものかどうかチェックする 過去：0 現在または未来：1
function checkYearMonth(year,month){
	var now = new Date();
	var nowYear = now.getFullYear();
	var nowMonth = now.getMonth() + 1;	
	
	var rtn;
	
	if(nowYear > year){
		// 明らかに過去
		rtn = 0;
	}else if(nowYear == year){
		// 年が同じ
		if(nowMonth > month){
			// 月が過去
			rtn = 0;
		}else{
			// 同月または未来
			rtn = 1;
		}
	}else{
		rtn = 1;
	}
	
	return rtn;
	
}

// 次の月を返す
function nextMonth(month){
	month = 1*month+1;
	if(month > 12) month = month - 12;
	
	return month;
}


// 年月の比較チェック
// year1/month1 が、year2/month2より過去の場合 0同じまたは未来:1
function checkYearMonth2(year1,month1,year2,month2){
	
	var rtn = 0;
	
	year1 = year1*1;
	month1 = month1*1;
	year2 = year2*1;
	month2 = month2*1;
	
	
	if(year2 > year1){
		// 明らかに過去
		rtn = 0;
	}else if(year2 == year1){
		// 年が同じ
		if(month2 > month1){
			// 月が過去
			rtn = 0;
		}else{
			// 同月または未来
			rtn = 1;
		}
	}else{
		// 明らかに未来
		rtn = 1;
	}
	
	return rtn;
	
}

// 次回車検等来月を表示
// 2015.08.05 車検到来機能 by morita
function enableNextInspection(){
	var tag = "";
	
	/*
	①初度登録から3年以内の車両の場合・・・初度登録月から3年後の月
　（例）2015年3月登録の車両⇒2018年3月）
	②初度登録から3年を超える車両の場合・・・初度登録から5年後の月
　（例）2012年3月登録の車両⇒2017年3月）
	*/
	
	var year;
	var month;
	
	// 初度登録から3年以内か判定
	//今日の日時を表示
	var date = new Date();
	var nyear = date.getFullYear();
	var nmonth = date.getMonth()+1;
	
	// 今日は初度登録後3年より前か？後か？	
	if((1*nyear - 1*$("#tourokuyear").val() > 3) || (1*nyear - 1*$("#tourokuyear").val()==3 && 1*nmonth >= 1*$("#tourokumonth").val())){
		// 3年以上経っている
		year = 1*$("#tourokuyear").val()+5;
		month = 1*$("#tourokumonth").val();
		//alert("3年超えてる");
	}else{
		year = 1*$("#tourokuyear").val()+3;
		month = 1*$("#tourokumonth").val();
		//alert("3年以内");
	}
	
	
	
	// 次回車検到来月のプルダウン作成
	/*
	次回車検到来月プルダウンの中身は、車両登録年月の翌月〜車両登録年月35ヶ月後でお願いできますか。
	*/
	// 2016.01.08 次回車検到来月を一か月前に
	
	month--;
	
	for(i=1;i<=36;i++){
		if(month>12){
			month -= 12;
			year ++;
		}
		if(month < 10) month = "0"+month;
		tag += "<option value='"+i+"'>"+year+"年"+month+"月</option>\n";
		month++;
	}
	
	$("#n_inspection_y").empty().append(tag).val("2").custom_selectbox();
	
	$(".u_inspection").show();
}

// 2015.08.05 車検到来機能 by morita
function setNnInspection(){
	var tag = "";
	var string = $("#n_inspection_y option:selected").text();
	
	var year = string.substr(0,4);
	var month = string.substr(5,2);

	//var string2 = string.substr(4);
	
	//var tag = "<option value='"+$("#n_inspection_y").val()+24+"'>"+(year*1+2)+string2+"</option>";
	//var tag = "<option value='"+$("#n_inspection_y").val()+24+"'>"+(year*1+2)+string2+"</option>";
	$("#nn_inspection_y").val((year*1+2)+"年"+month+"月");
	
	
	//$("#nn_inspection_y").empty().append(tag).val("").custom_selectbox();
	
	/*
	この項目には次回/次々回車検到来月までの支払回数が表示される。支払回数は次回/次々回車検到来月と同月とする。
	（例）次回車検が2016年10月で、初回月が2015年8月の場合
	⇒次回車検到来月まで：15回,次々回車検到来月まで：39回
	*/
	var times1 = 12*year+1*month;
	var times2 = 12*$("#usedcar_year").val()+1*$("#usedcar_month").val();
	
	var times = times1 - times2;
	
	// 次回車検到来月までの回数
	if(times<13){
		$("#n_inspection_m").val("--回");
	}else{
		$("#n_inspection_m").val(times+"回");
	}
	
	// 次々回車検到来月までの回数
	$("#nn_inspection_m").val(times+24+"回");
}	


	
	

			
				


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  ここから処理開始
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// 消費税デフォルト切り替え
var dt1 = new Date(2014, 4 - 1, 1);
var dt2 = new Date();
if(dt1.getTime() <= dt2.getTime()) {
	g_tax = 0.08;
	g_u_tax = 0.08;
	g_s_tax = 0.08;
}else{
	g_tax = 0.05;
	g_u_tax = 0.05;
	g_s_tax = 0.05;
}
