/************************************************************************

	ローン計算シートjsファイル







*************************************************************************/
// グローバル変数
var g_mode = "new";						// 計算モード new/used/service
var g_rateArr = new Array();			// 現在選択されているプラン・モデルでの支払い回数・金利の配列 新車のみ

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
var g_bptmax = "";		// ボーナス加算MAX
var g_u_bptmax = "";		// ボーナス加算MAX

var carArr = [];		// 現在のcarテーブル情報配列（loan_common.jsとの互換性のため）
var g_carArr = []		// 現在の新車carテーブル情報配列
var g_u_carArr = [];	// 現在の中古車carテーブル情報配列

var g_tax = 0.05;		// 消費税（デフォルトでは5%）新車
var g_u_tax = 0.05;		// 消費税（デフォルトでは5%）中古車

var usedCarParams = [];

var plusmode = 0;			// プラスモード時は、1になる

var plannameArr = {
						"wp":"ウェルカムプラン",
						"swp":"スーパーウェルカムプラン",
						"std":"スタンダードローン",
						"sup":"スタートアッププラン"
					};
var u_plannameArr = {
						"wp":"ユーズドカーウェルカムプラン",
						"swp":"スーパーウェルカムプラン",
						"std":"スタンダードローン",
						"sup":"スタートアッププラン"
					};

					
$(function() {
	// 新車タブをクリック
	$("a.new-tab").on("click",function(){
		// 新車タブがクリックされたときの処理
		carArr = g_carArr;
		$("#usedcar, #serviceloan").hide(0,function(){
			$("#newcar").show();
		});
		return false;
	});
	
	// 新車の計算ボタンクリック
	$("#newcar_calc").on("click",function(){
		// プラスモードでwp swp意外は、ダイアログを出すだけ
		if(plusmode == 1 && ($("#plan").val()=="std" || $("#plan").val()=="sup")){
			alert("ウェルカムプランまたは、スーパーウェルカムプランにて計算してください");
		}else{
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
					conditions['downpayment'] = textbox2num('downpayment')-textbox2num('zansai');
					conditions['bonuspayment'] = textbox2num('bonuspayment');
					conditions['lastpayment'] = textbox2num('lastpayment');
					conditions['sonota'] = textbox2num('sonota')+textbox2num('mmmprice')+textbox2num('mmsprice')+textbox2num('evprice')+textbox2num('mbinsureance')-textbox2num('discount');
					
					// 個別提案書のデータを確認する
					var leaflet = "";
					if($("#plan").val() == "wp"){
						switch($("#classname").val()){
							case 'A-Class':
							case 'B-Class':
							case 'CLA-Class':
							case 'C-Class Sedan':
							case 'C-Class Stationwagon':
							case 'C-Class Coupe':
								switch($("#installments").val()){
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
								switch($("#installments").val()){
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
					
					var resultArr = loancalc($("#plan").val(),conditions);
					$("#monthlypayment").text(num2price(resultArr["monthlypayment"]));
					
					// 計算結果をサーバーに送信し、DBのIDを受け取る
					$.getJSON("./cars/insertlog/",{
									salesman:$("#salesman").val(),
									plan:$("#plan").val(),
									classname:$("#classname").val(),
									bmst:$("#carname").val(),
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
									downpayment:textbox2num('downpayment'),
									zansai:textbox2num('zansai'),
									loanprincipal:textbox2num('loanprincipal'),
									loantotal:resultArr['total'],
									installments:$('#installments').val(),
									firstpayment:resultArr['firstpayment'],
									monthlypayment:resultArr["monthlypayment"],
									bonuspayment:textbox2num('bonuspayment'),
									bonustimes:resultArr['bonustimes'],
									bonusmonth1:$("input[name='bonusmonth1']:checked").val(),
									bonusmonth2:$("input[name='bonusmonth2']:checked").val(),
									rate:addZero(g_rate),
									lastpayment:textbox2num('lastpayment'),
									interest:resultArr['interest'],
									leafletimage:leaflet,
									tm:stamp()
							},function(data){
						// DBのIDを受信
						//$("#newcar_pdf").attr("href","./pdf/estimate/"+data.code);
						$("#newcar_compare").attr("href","./cars/compare/"+data.id);
						$("#newcar_leaflet").attr("href","./pdf/leaflet/"+data.id);
						//$("#newcar_leaflet2").attr("href","./pdf/leaflet/"+data.id);
						$("#newcar_display").attr("href","./pdf/display/"+data.id);
						
						alert("ok");
						
						// 結果表示
						$("#newcar_result_classname").text($("#classname").val());
						$("#newcar_result_carname").text(g_carname);
		
						$("#newcar_result_plan").text(plannameArr[$("#plan").val()]);
						$("#newcar_result_pricetax").text($('#pricetax').val()+"円");
						$("#newcar_result_totalpayment").text($('#totalpayment').val()+"円");
						$("#newcar_result_downpayment").text($('#downpayment').val()+"円");
						$("#newcar_result_zansai").text($('#zansai').val()+"円");
						$("#newcar_result_tsuika").text("0円"),
						$("#newcar_result_monthlypayment").text(num2price(resultArr['monthlypayment'])+"円");
						$("#newcar_result_loanprincipal").text($('#loanprincipal').val()+"円");
						
						// 残価欄の制御
						/*
						if(("#lastpayment").val() > 0){
							// 残価欄出現
							$("#newcar_result").remove();
						}else{
							// 残価欄消去
							$("#tr_lastpayment").hide();
						}
						*/
						$("#newcar_result_lastpayment").text($('#lastpayment').val()+"円");
						$("#newcar_result_bonuspayment").text($('#bonuspayment').val()+"円");
						$("#newcar_result_downpayment").text($('#downpayment').val()+"円");
						$("#newcar_result_installments").text($('#installments').val()*1+1*(($("#plan").val()=="wp" ||$("#plan").val()=="swp") ? 1 : 0 )+"回");
						$("#newcar_result_bonustimes").text(textbox2num('bonustimes')+"円");
						$("#newcar_result_rate").text(addZero(g_rate)+"％");
						$("#newcar_result_loantotal").text(num2price(resultArr['total'])+"円");
						$("#newcar_result_alltotalpayment").text(num2price(resultArr['total']+textbox2num('downpayment'))+"円");
						
						
						// 受信後に画面表示
						// 頭金ラベルを変更
						$("#label_newcar_result_downpayment").empty().append("頭金／下取");
						$("#newcar_result").show();
						if(leaflet){
							$("#newcar_proposal_open").show();
						}else{
							$("#newcar_proposal_open").hide();
						}
						$("#compare").show();
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
					
					// 個別提案書のデータを確認は不要！
					
					var resultArr = loancalc($("#plan").val(),conditions);
					
					// 追加売買分の計算
					var conditions2 = [];
					conditions2['rate'] = tsuikakinriArr[$('#installments').val()];
					conditions2['installments'] = $('#installments').val();
					conditions2['pricetax'] = textbox2num('zansai')-textbox2num('downpayment');
					conditions2['optiontotal'] = 0;
					conditions2['taxtotal'] = 0;
					conditions2['downpayment'] = 0;				// プラス計算時は、新車分頭金は絶対にゼロ！
					conditions2['bonuspayment'] = 0;
					conditions2['lastpayment'] = 0;
					conditions2['sonota'] = 0;
	
					var resultArr2 = loancalc("dummy",conditions2);
					
					// 計算結果をサーバーに送信し、DBのIDを受け取る
					$.getJSON("./cars/insertlog/",{
									salesman:$("#salesman").val(),
									plan:$("#plan").val(),
									classname:$("#classname").val(),
									bmst:$("#carname").val(),
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
									downpayment:textbox2num('downpayment'),
									zansai:textbox2num('zansai'),
									loanprincipal:textbox2num('loanprincipal'),
									loantotal:number_format(resultArr['total']),
									installments:$('#installments').val(),
									firstpayment:resultArr['firstpayment'],
									monthlypayment:resultArr["monthlypayment"],
									bonuspayment:textbox2num('bonuspayment'),
									bonustimes:resultArr['bonustimes'],
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
									tm:stamp()
							},function(data){
						// DBのIDを受信
						$("#newcar_pdf").attr("href","./pdf/estimate/"+data.code);
						$("#newcar_leaflet").attr("href","./pdf/leaflet/"+data.id);
						//$("#newcar_leaflet2").attr("href","./pdf/leaflet/"+data.id);
						$("#newcar_display").attr("href","./pdf/display/"+data.id);
						
						// 結果表示
						$("#newcar_result_classname").text($("#classname").val());
						$("#newcar_result_carname").text(g_carname);
		
						$("#newcar_result_plan").text(plannameArr[$("#plan").val()]+"プラス");
						$("#newcar_result_pricetax").text($('#pricetax').val()+"円");
						$("#newcar_result_totalpayment").text($('#totalpayment').val()+"円");
						$("#newcar_result_downpayment").text($('#downpayment').val()+"円");
						$("#newcar_result_zansai").text($('#zansai').val()+"円"),
						$("#newcar_result_tsuika").text(num2price(textbox2num('zansai')-textbox2num('downpayment'))+"円"),
						$("#newcar_result_monthlypayment").text(num2price(resultArr['monthlypayment']+resultArr2['monthlypayment'])+"円");
						$("#newcar_result_loanprincipal").text($('#loanprincipal').val()+"円");
						
						// 残価欄の制御
						/*
						if(("#lastpayment").val() > 0){
							// 残価欄出現
							$("#newcar_result").remove();
						}else{
							// 残価欄消去
							$("#tr_lastpayment").hide();
						}
						*/
						$("#newcar_result_lastpayment").text($('#lastpayment').val()+"円");
						$("#newcar_result_bonuspayment").text($('#bonuspayment').val()+"円");
						$("#newcar_result_downpayment").text($('#downpayment').val()+"円");
						$("#newcar_result_installments").text($('#installments').val()*1+1*(($("#plan").val()=="wp" ||$("#plan").val()=="swp") ? 1 : 0 )+"回");
						$("#newcar_result_bonustimes").text(textbox2num('bonustimes')+"円");
						$("#newcar_result_rate").text(addZero(g_rate)+"％ / "+addZero(conditions2['rate'])+"％");
						$("#newcar_result_loantotal").text(num2price(resultArr['total']+resultArr2['total'])+"円");
						$("#newcar_result_alltotalpayment").text(num2price(resultArr['total']+resultArr2['total']+textbox2num('downpayment'))+"円");
						
						
						// 受信後に画面表示
						// 頭金ラベルを変更
						$("#label_newcar_result_downpayment").empty().append("頭金");
						$("#newcar_result").show();
						$("#newcar_proposal_open").hide();
						$("#compare").hide();
						$("#newcar_display").hide();
					});
				}
						
				
			});
		}
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
			$("#ev_mbj").hide();
			$("#mmm_smart").show();
			$("#mms_smart").show();
			$("#ev_smart").show();
		}else{
			$("#mmm_smart").hide();
			$("#mms_smart").hide();
			$("#ev_smart").hide();
			$("#mmm_mbj").show();
			$("#mms_mbj").show();
			$("#ev_mbj").show();
		}
		// ajaxで、モデル名リストをダウンロードする
		$.getJSON("./cars/carnamejson/"+$(this).val()+"?tm="+stamp(),function(json){
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
			check_newcar_calc_button();
		});
	});
	
	// モデル名選択後の処理
	$("#carname").on("change",function(){
		var bmst = $(this).val();
		
		newcar_input_enable();
		
		// carレコードの取得
		$.getJSON("./cars/carjson/"+bmst+"&tm="+stamp(),function(json){
			// グローバルに格納
			g_carArr = json;
			carArr = g_carArr;
			
			if(g_tax == 0.05) {
				// 消費税5%
				// なにもしない
			}else{
				// 消費税8%
				// テーブル読み直し
				g_carArr['price'] = g_carArr['price2'];
				
				g_carArr['pricetax'] = g_carArr['pricetax2'];
			}
			
			
			//車両本体価格を変更
			$("#pricetax").val(num2price(g_carArr.pricetax));
			
			
			// 車両本体価格・保証・メンテナンス代を反映する
			calcNewCarParams();
			
			g_carname = g_carArr.carname;

			
			// プランプルダウンを更新
			if(g_carArr['swpmodel'] == 1 || g_carArr['swpmodel'] == 2){
				optionTag = "<option value=''>▼選択してください</option>";
				optionTag += "<option value='wp'>ウェルカムプラン</option>";
				optionTag += "<option value='swp'>スーパーウェルカムプラン</option>";
				optionTag += "<option value='std'>スタンダードローン</option>";
				optionTag += "<option value='sup'>スタートアッププラン</option>";
			}else{
				optionTag = "<option value=''>▼選択してください</option>";
				optionTag += "<option value='wp'>ウェルカムプラン</option>";
				optionTag += "<option value='std'>スタンダードローン</option>";
				optionTag += "<option value='sup'>スタートアッププラン</option>";
			}
			$("#plan").empty().append(optionTag).val("").custom_selectbox();
			
			$("#rate,#installments").val("").empty().custom_selectbox();
			
			check_newcar_calc_button();
		});
	});
	
	// プラン選択後の処理
	$("#plan").on("change",function(){
		// すでに、モデルが決まっている場合は、イベント強制発行
		/*
		if($("#carname").val()){
			$('#carname').trigger('change');
		}
		*/
		
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
			optionTag += "<option value='"+installments+"'>"+(installments*1+temp*1)+"回</option>";
		}
		
		
		// 金利プルダウンをリセット
		$("#rate").val("").empty().custom_selectbox();
		
		// オプションタグ入れ替え
		$("#installments").empty().append(optionTag).val("").custom_selectbox();
			
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
		
		
		
		// オプションタグ入れ替え
		$("#rate").empty().append(optionTag).custom_selectbox();
		
/*************************************************************************************************************************************
			smart のサービスプログラムについて
			
			EV専用プランはEV車でWP6年を選んだ場合のみアクティブになります。
			それ以外はEV車でもスタートプランとセカンドプランになります。
			
			＝＝
			smartにつきましては、下記のような対応をお願いいたします。
			・3年の場合はスタートプランがデフォルト表記
			・3年の場合はセカンドプランは空白で、自由記入
			・5年の場合（通常ローンの5年以上含む）はスタートプラン、セカンドプランの両方がデフォルト表記
			・6年（EV）の場合はEV専用プランがデフォルト表記（EV以外は6年選択不可のため、EV専用プランも記入等一切不可）
			・6年（EV）の場合、EV専用プランがデフォルトで表記されるが、それを削除してスタートプラン、セカンドプランを入力することも可能。
			　ただ、EV専用プランとメンテパック（スタートプラン、セカンドプランのいずれかまたは両方）の両方に金額を入力することは不可
			・上記デフォルト表記の場合でも、表記削除、金額変更入力は可能
			・上記以外の年数（2年や4年等）はすべて空白で、自由記入
			
*************************************************************************************************************************************/
				var plan = $("#plan").val();
				var installments = 1*$("#installments").val();
				
				if($("#classname").val() == "smart"){
					
					
					if(g_carArr['ev'] > 0 && installments == 72 && plan == "wp"){
						// チェックボックス出現
						$("#m_plan_check").show();
						// evnがデフォルトになる
						$("input[name='m_plan']").val(["ev"]).trigger("change");
						
						// evを初期化してアクティブに
						$("#evprice").val(num2price(g_carArr['ev'])).removeAttr("readonly");
					}else{
						// チェックボックスなくす
						$("#m_plan_check").hide();
						// evを初期化して非表示に
						$("#evprice").val(0).attr("readonly",true);
						
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
				}else{
/*************************************************************************************************************************************
			
			MBのサービスプログラムについて
			【仕様】
			MB 
			WP61回、SWP61回、STD60〜84回、SUP96〜120回（お支払い年数が5年以上のもの全て）
			→保証プラスの初期値をmmsの値にする（消費税8%時にはmms2の値）
			
			WP25〜49回、SWP25〜49回、STD3〜54回（お支払い年数が5年未満）
			→保証プラスの初期値をゼロにする
			
			メンテナンスプラスはお支払い回数・年数に関わらずmmmの値を初期表示（8%時はmmm2の値）
*************************************************************************************************************************************/
				// smart意外の場合
				if(g_carArr['ev'] > 0){
					// evを初期化してアクティブに
					// チェックボックス出現
					$("#m_plan_check").show();
					
					// evnがデフォルトになる
					$("#m_plan").val("ev").trigger("change");
					
					// evを初期化してアクティブに
					$("#evprice").val(num2price(g_carArr['ev'])).removeAttr("readonly");
					
					// その他の処理はまだわからない
				}else{
					// チェックボックス隠す
					$("#m_plan_check").hide();
					// evを初期化してグレーアウト
					$("#evprice").val(0).attr("readonly",true);
					
					// mmm mmsのデフォルト値コピー処理
					// mmmの変更：常に初期表示
					$("#mmmprice").val(num2price(g_carArr.mmm));
					
					// mms2は、回数などによる
					
					if(installments >= 60){
						$("#mmsprice").val(num2price(g_carArr.mms));
					}else{
						$("#mmsprice").val(0);
					}
				}
			}
		
		newcar_check();
		check_newcar_calc_button();
	});
	
	// 金利選択時の処理
	$("#rate").on("change",function(){
		var rateArr =findFromDBArr(Rates,{"patternid":g_carArr[$("#plan").val()+"ratepattern"],"installments":$('#installments').val()});
		
		g_rate = 1.0*g_rateArr[0][$(this).val()];

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
	
	
	
	// 中古車タブをクリック
	$("a.used-tab").on("click",function(){
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
			conditions['downpayment'] = textbox2num('u_downpayment');
			conditions['bonuspayment'] = textbox2num('u_bonuspayment');
			conditions['lastpayment'] = textbox2num('u_lastpayment');
			conditions['sonota'] = textbox2num('u_sonota')+textbox2num('u_mbinsureance')-textbox2num('u_discount');
			
			var resultArr = loancalc($("#u_plan").val(),conditions);
			$("#monthlypayment").text(num2price(resultArr["monthlypayment"]));
			
			// 計算結果をサーバーに送信し、DBのIDを受け取る
			$.getJSON("./cars/insertlog/",{
							salesman:$("#salesman").val(),
					  		plan:$("#u_plan").val(),
					  		classname:"used",
							bmst:$("#u_classname").val(),
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
							downpayment:textbox2num('u_downpayment'),
							zansai:textbox2num('u_zansai'),
							loanprincipal:textbox2num('u_loanprincipal'),
							loantotal:number_format(resultArr['total']),
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
							tm:stamp()
					},function(data){
				// DBのIDを受信
				$("#u_pdf").attr("href","./pdf/estimate/"+data.code);
				//$("#leaflet").attr("href","./pdf/leaflet/"+data.id);
				//$("#leaflet2").attr("href","./pdf/leaflet/"+data.id);
				$("#u_display").attr("href","./pdf/display/"+data.id);
				
				// 結果表示
				$("#usedcar_result_classname").text($("#u_classname").val());
				$("#usedcar_result_carname").text($("#u_carname").val());

				$("#usedcar_result_plan").text(u_plannameArr[$("#u_plan").val()]);
				$("#usedcar_result_pricetax").text($('#u_pricetax').val()+"円");
				$("#usedcar_result_totalpayment").text($('#u_totalpayment').val()+"円");
				$("#usedcar_result_downpayment").text($('#u_downpayment').val()+"円");
				$("#usedcar_result_zansai").text($('#u_zansai').val()+"円");
				$("#usedcar_result_monthlypayment").text(num2price(resultArr['monthlypayment'])+"円");
				$("#usedcar_result_loanprincipal").text($('#u_loanprincipal').val()+"円");
				$("#usedcar_result_lastpayment").text($('#u_lastpayment').val()+"円");
				$("#usedcar_result_bonuspayment").text($('#u_bonuspayment').val()+"円");
				$("#usedcar_result_downpayment").text($('#u_downpayment').val()+"円");
				$("#usedcar_result_installments").text($('#u_installments').val()*1+1*(($("#u_plan").val()=="wp" ||$("#u_plan").val()=="swp") ? 1 : 0 )+"回");
				$("#usedcar_result_bonustimes").text(textbox2num('bonustimes')+"円");
				$("#usedcar_result_rate").text($("#u_rate").val()+"％");
				$("#usedcar_result_loantotal").text(num2price(resultArr['total'])+"円");
				$("#usedcar_result_alltotalpayment").text(num2price(resultArr['total']+textbox2num('u_downpayment'))+"円");
				
				
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
	
	// テキストボックスパラメーターを変更（focusout)
	//$("#discount,#lastpayment,#makeroption,#dealeroption,#sonota,#mmmprice,#mms2price,#zansai,#downpayment,#bonuspayment").on("focusout",function(){
	$('input[type="text"]').on("focusout",function(){
		// usedかどうか判断
		var id = $(this).attr("id");
		// 左2文字が「u_」かどうか
		if(id.substring(0,2) == "u_"){
			usedcar_check();
		}else{
			newcar_check();
		}
	});
	
	$('input[type=text]').on("keyup",function(){
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
	
	
	
	// ボーナス加算額上限表示ボタンクリック
	$("#getbptmax").on("click",function(){
		$("#bptmax").text(num2price(g_bptmax));
		return false;
	});
	// ボーナス加算額上限表示ボタンクリック
	$("#u_getbptmax").on("click",function(){
		$("#u_bptmax").text(num2price(g_u_bptmax));
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
		// 消費税切り替え
		var dt1 = new Date(2014, 4 - 1, 1);
		var dt2 = new Date($("#newcar_year").val()*1, $("#newcar_month").val()*1 - 1, 1);
		
		if(dt1.getTime() > dt2.getTime()) {
			// 消費税5%
			g_tax = 0.05;
		}else{
			g_tax = 0.08;
		}
		$("#newcar_zei").empty().append("【消費税率 "+g_tax*100+"％】");
		$("#maker_option_comment").empty().append("※税込み金額をご入力ください【消費税率 "+g_tax*100+"％】<br>※サウンドスイートの場合は残価に算入できないためJPOSをご活用ください</span>");
		
		// モデル選択イベント発火
		if($("#carname").val()){
			$("#carname").trigger("change");
		}
	});
	
	// メンテナンスパッケージ関連
	$("input[name='m_plan']").on("change",function(){
		if($(this).val()=="ev"){
			// evの場合
			// evを初期化してアクティブに
			$("#evprice").val(num2price(g_carArr['ev'])).removeAttr("readonly");
			$("#mmmprice").val(0).attr("readonly",true);
			$("#mmsprice").val(0).attr("readonly",true);
		}else{
			$("#evprice").val(0).attr("readonly",true);
			$("#mmmprice").val(0).removeAttr("readonly").val(num2price(g_carArr.mmm));
			$("#mmsprice").val(0).removeAttr("readonly").val(num2price(g_carArr.mms));
		}
		
	});
	
	
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
// 中古車関連


	// 登録年月変更
	$("#usedcar_year,#usedcar_month").on("change",function(){
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
		$("#u_maker_option_comment").empty().append("※税込み金額をご入力ください【消費税率 "+g_u_tax*100+"％】<br>※サウンドスイートの場合は残価に算入できないためJPOSをご活用ください</span>");
		
		
		// モデル選択前に戻す
		$("#u_classname").val("");
		usedcar_input_disable();
		$("#u_classname").trigger("change");
		/*
		if($("#u_classname").val()){
			$("#u_classname").trigger("change");
		}
		*/
	});


	// 中古車クラス変更時→新車のモデル名変更時と同様にする
	$("#u_classname").on("change",function(){	
	
		// 年式プルダウンを有効にするだけ
		$("#tourokuyear").removeAttr('disabled').select_disabled_off().custom_selectbox();
		check_usedcar_calc_button();
	});
	
	
	// 年式を選択後の処理
	$("#tourokuyear").on("change",function(){
		
		usedcar_input_enable();
		
		// 5年以上昔のはwpだめ
		var yeardiff = $("#usedcar_year").val() - $("#tourokuyear").val();
		
		if( yeardiff <= 5){
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
	
	// 車両本体価格変更後（フォーカスアウト）
	$("#u_pricetax").on("focusout",function(){
		// 車両データを変更
		g_u_carArr['pricetax'] = textbox2num("u_pricetax");
		//carArr['pricetax'] = g_u_carArr['pricetax'];
		
		g_u_carArr['price'] = Math.round(textbox2num("u_pricetax")/(1.0+g_u_tax));
		//carArr['price'] = g_u_carArr['price'];
		check_usedcar_calc_button();
	});
		
		
	// プラン選択後の処理
	$("#u_plan").on("change",function(){

		// rateレコードの取得
		var patternid = g_u_carArr[$("#plan").val()+"ratepattern"];
		
		// 経過件数・プランによって支払い回数プルダウンが変わる
		var yeardiff = $("#usedcar_year").val() - $("#tourokuyear").val();

		
		// 支払い回数プルダウン作成
		optionTag = "<option value=''>▼選択</option>";
		
		if($("#u_plan").val()=="wp"){
			for(var i=24; i<=60;i+=12){
				// オプションタグ生成
				if(useddata[i]["yeardiff"] >= yeardiff){
					optionTag += "<option value='"+i+"'>"+(i+1)+"回</option>";
				}
			}
		}else{
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
		
		// carレコードの取得
		$.getJSON("./cars/carjson/"+$("#u_classname").val()+"&tm="+stamp(),function(json){
			// グローバルに格納
			g_u_carArr = json;
			carArr = g_u_carArr;

			
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
					lastpayment = getmaxwplpt($("#u_installments").val(),"used");
					$("#u_lastpayment").val(num2price(lastpayment));
					break;
				default:
					// 残価はゼロ
					$("#u_lastpayment").val(0);
			}
												
			// 金利プルダウン作成
			optionTag = "<option value=''>▼選択</option>";
			optionTag += "<option value='4.49'>4.49%</option>";
			//optionTag += "<option value='2.9'>2.9%</option>";
			
			// オプションタグ入れ替え
			$("#u_rate").empty().append(optionTag).custom_selectbox();
			
			usedcar_check();
			check_usedcar_calc_button();
		});
		
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
			bonus_off();
		}else{
			bonus_on();
		}
	});
	
	//  モデル名変更時
	$("#u_carname").on("change",function(){
		check_usedcar_calc_button();
	});
	
	
});


// 画面描画後におこなう初期化関数
function uiInit(){
	// 画面描画後に初期化する
	$("#usedcar").hide();
	$("#newcar_result").hide();
	$("#usedcar_result").hide();
	
	$("#newcar_calc").hide();
	$("#usedcar_calc").hide();
	
	// smartのmmm/mms画像を非表示に
	$("#mmm_smart").hide();
	$("#mms_smart").hide();
	$("#ev_smart").hide();
	
	// m_plan のラジオボタンを非表示に
	$("#m_plan_check").hide();
}

// パラメーターチェック関数
function newcar_check(){
		// 新車パラメーターを更新
		// パラメーターチェック
		var conditionArr = [];

		conditionArr['mode'] = true;		// メッセージは表示する
		conditionArr['bmst'] = $("#carname").val();
		conditionArr['total'] = textbox2num('totalpayment');						//合計金額
		conditionArr['installments'] = $('#installments').val()*1;	//支払い回数
		conditionArr['pricetax'] = textbox2num('pricetax');			//税込み車両本体価格
		conditionArr['optiontotal'] = textbox2num('makeroption');			//税込みオプション金額合計
		conditionArr['taxtotal'] = textbox2num('dealeroption');						//税金等の諸経費合計
		conditionArr['downpayment'] = textbox2num('downpayment');			//頭金
		conditionArr['sonota'] = +textbox2num('sonota')-textbox2num('discount')+textbox2num('mmmprice')+textbox2num('mmsprice')+textbox2num('evprice')+textbox2num('mbinsureance');					// その他オプション
		conditionArr['loanprincipal'] = textbox2num('loanprincipal');					// その他オプション
		conditionArr['lastpayment'] = textbox2num('lastpayment');					// その他オプション
		conditionArr['bonuspayment'] = textbox2num('bonuspayment');				// その他オプション
		conditionArr['rate'] = g_rate;			// 選択された金利
		conditionArr['selectedrate'] = g_rate;			// 選択された金利　_check()内部では、これも参照されているので
		
		var lptmaxmin_string="　　<br/>  ";
		var zankaritsu;


		// パラメーターの値チェック
		// 金利が選択されている場合のみ行う
		if($("#rate").val()){
			var checkArr = [];
			checkArr= _check($("#plan").val(),conditionArr,"new");
			
			g_bptmax = checkArr['bptmax'];
			
			// パラメーター修正
			$("#lastpayment").val(num2price(checkArr['lastpayment']));
			$("#bonuspayment").val(num2price(checkArr['bonuspayment']));
			$("#downpayment").val(num2price(checkArr['downpayment']));
			$("#loanprincipal").val(num2price(checkArr['loanprincipal']));

			// 残価率・上限下限を取得
			if($("#plan").val() == "wp"){
				zankaritsu = Math.round(textbox2num('lastpayment')/g_carArr['price']*100);
				lptmaxmin_string = num2price(checkArr['lptmax'])+"円（上限） / "+num2price(checkArr['lptmin'])+"円（下限） 残価率"+zankaritsu+"%<br/>※上限はメーカーオプションを含んだ金額です";
			}
			
			// メッセージ表示
			if(checkArr['message']){
				alert(checkArr['message']);
				newcar_check();
			}
		}else{
			g_bptmax = "";
		}
		
		// ボーナス加算MAX取得ボタンイベント発火
		$("#getbptmax").trigger("click");
		
		// 残価上限下限表示
		$("#lptmaxmin").empty().append(lptmaxmin_string);
		
		
		calcNewCarParams();
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
		conditionArr['downpayment'] = textbox2num('u_downpayment');			//頭金
		conditionArr['sonota'] = +textbox2num('u_sonota')-textbox2num('u_discount')+textbox2num('mbinsureance');					// その他オプション
		conditionArr['loanprincipal'] = textbox2num('u_loanprincipal');					// その他オプション
		conditionArr['lastpayment'] = textbox2num('u_lastpayment');					// その他オプション
		conditionArr['bonuspayment'] = textbox2num('u_bonuspayment');				// その他オプション
		conditionArr['rate'] = $("#u_rate").val()*1.0;			// 選択された金利
		conditionArr['selectedrate'] = $("#u_rate").val()*1.0;		// 選択された金利　_check()内部では、これも参照されているので
		
		var lptmaxmin_string="　　<br/>  ";
		var zankaritsu;


		// パラメーターの値チェック
		// 金利が選択されている場合のみ行う
		if($("#u_rate").val()){
			var checkArr = [];
			checkArr= _check($("#u_plan").val(),conditionArr,"used");
			
			g_u_bptmax = checkArr['bptmax'];
			
			// パラメーター修正
			$("#u_lastpayment").val(num2price(checkArr['lastpayment']));
			$("#u_bonuspayment").val(num2price(checkArr['bonuspayment']));
			$("#u_downpayment").val(num2price(checkArr['downpayment']));
			$("#u_loanprincipal").val(num2price(checkArr['loanprincipal']));

			// 残価率・上限下限を取得
			if($("#u_plan").val() == "wp"){
				zankaritsu = Math.round(textbox2num('u_lastpayment')/g_u_carArr['price']*100);
				lptmaxmin_string = num2price(checkArr['lptmax'])+"円（上限） / "+num2price(checkArr['lptmin'])+"円（下限） 残価率"+zankaritsu+"%<br/>※上限はメーカーオプションを含んだ金額です";
			}
			
			// メッセージ表示
			if(checkArr['message']){
				alert(checkArr['message']);
				newcar_check();
			}
		}else{
			g_bptmax = "";
		}
		
		// ボーナス加算MAX取得ボタンイベント発火
		$("#u_getbptmax").trigger("click");
		
		// 残価上限下限表示
		$("#u_lptmaxmin").empty().append(lptmaxmin_string);
		
		
		calcUsedCarParams();
}

// ロジックの初期化関数
function logicInit(){
	// ajaxにてデータ取得
	$.getJSON("./cars/gettable/Rate?tm="+stamp(),function(json){
		Rates = json;
		$.getJSON("./cars/gettable/Bptrate?tm="+stamp(),function(json){
			Bptrates = json;
			$.getJSON("./cars/gettable/Lpprate?tm="+stamp(),function(json){
				Lpprates = json;
				$.getJSON("./cars/gettable/Lptrate?tm="+stamp(),function(json){
					Lptrates = json;
					$.getJSON("./cars/gettable/Initrate?tm="+stamp(),function(json){
						Initrates = json;
					});
				});
			});
		});
	});
	newcar_input_disable();
	usedcar_input_disable();
}

// 新車のパラメーターを計算する
function calcNewCarParams(){
		// cartotal: 最終的な車の値段
		// totalpayment: 車の値段＋税金やメンテナンスパッケージ費用
			
		var cartotal = textbox2num('pricetax') + textbox2num('makeroption') + textbox2num('dealeroption') - textbox2num('discount');
		
		$("#cartotal").val(num2price(cartotal));
		
		var totalpayment = cartotal + textbox2num('mbinsureance') + textbox2num('sonota') + textbox2num('mmmprice') + textbox2num('mmsprice');
		
		$("#totalpayment").val(num2price(totalpayment));
		
		var loanprincipal = totalpayment - textbox2num('downpayment') + textbox2num('zansai');
		$("#loanprincipal").val(num2price(loanprincipal));
		
		// プラスモード判断
		if(textbox2num('downpayment') >= textbox2num('zansai')){
			// 通常モード
			plusmode = 0;
		}else{
			// プラスモード
			plusmode = 1;
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
		
		var loanprincipal = totalpayment - textbox2num('u_downpayment') + textbox2num('u_zansai');
		$("#u_loanprincipal").val(num2price(loanprincipal));
		
		check_usedcar_calc_button();
		
}

// 新車画面入力を無効にする
function newcar_input_disable(){
	// モデルが選択されていないので、ブランクにしてリードオンリーに
	$("#pricetax,#makeroption,#dealeroption,#discount,#total,#sonota,#mbinsureance,#mmmprice,#mmsprice,#evprice,#totalpayment,#downpayment,#zansai,#loanprincipal,#lastpayment,#bonus,#bonusmonth1,#bonusmonth2").val("").attr('readonly',true);
	$("#plan,#rate,#installments").empty().val("").attr("disabled","disabled").select_disabled_on().custom_selectbox();
	
	bonus_off();
	$("input[name='bonus']:radio").attr("disabled",true).radio_disabled_on().custom_selectbox();
	$("#m_plan_check").hide();
	
}

// 新車画面入力を有効にする
function newcar_input_enable(){
	// モデルが選択されていないので、ブランクにしてリードオンリーに
	$("#makeroption,#dealeroption,#discount,#sonota,#mbinsureance,#mmmprice,#mmsprice,#downpayment,#zansai,#bonus,#bonusmonth1,#bonusmonth2").removeAttr('readonly').val(0);
	$("#plan,#rate,#installments").removeAttr('disabled').select_disabled_off().custom_selectbox();
	
	//bonus_on();
	$("input[name='bonus']:radio").removeAttr("disabled").radio_disabled_off().custom_selectbox();
}

// 中古車画面入力を無効にする
function usedcar_input_disable(){
	// モデルが選択されていないので、ブランクにしてリードオンリーに
	$("#u_makeroption,#u_dealeroption,#u_discount,#u_cartotal,#u_sonota,#u_mbinsureance,#u_mmmprice,#u_mmsprice,#u_totalpayment,#u_downpayment,#u_zansai,#u_loanprincipal,#u_lastpayment,#u_bonus,#u_bonusmonth1,#u_bonusmonth2").val("").attr('readonly',true);
	$("#u_plan,#u_rate,#u_installments").empty().val("").attr("disabled","disabled").select_disabled_on().custom_selectbox();
	$("#tourokuyear").val("").attr("disabled","disabled").select_disabled_on().custom_selectbox();
	
	used_bonus_off();
	$("input[name='u_bonus']:radio").attr("disabled",true).radio_disabled_on().custom_selectbox();
	
}

// 中古車画面入力を有効にする
function usedcar_input_enable(){
	// モデルが選択されていないので、ブランクにしてリードオンリーに
	$("#u_makeroption,#u_dealeroption,#u_discount,#u_sonota,#u_mbinsureance,#u_mmmprice,#u_mmsprice,#u_downpayment,#u_bonus,#u_bonusmonth1,#u_bonusmonth2").removeAttr('readonly').val(0);
	$("#u_plan,#u_rate,#u_installments").removeAttr('disabled').select_disabled_off().custom_selectbox();
	
	//used_bonus_on();
	$("input[name='u_bonus']:radio").removeAttr("disabled").radio_disabled_off().custom_selectbox();
}

function bonus_on(){
	// 有効になったとき
	// ボーナス関連をenableにする
	$("#bonuspayment").removeAttr("readonly").val(0).custom_selectbox();
	$('input[name="bonusmonth1"]').removeAttr("disabled").radio_disabled_off().custom_selectbox();
	$('input[name="bonusmonth2"]').removeAttr("disabled").radio_disabled_off().custom_selectbox();
}

function bonus_off(){
	// 無効になったとき
	$("#bonuspayment").val(0);
	// ボーナス関連をreadonlyにする
	$("#bonuspayment").attr('readonly', true).custom_selectbox();;
	$('input[name="bonusmonth1"]:radio').attr('disabled', true).radio_disabled_on().custom_selectbox();
	$('input[name="bonusmonth2"]:radio').attr('disabled', true).radio_disabled_on().custom_selectbox();
}
function used_bonus_on(){
	// 有効になったとき
	// ボーナス関連をenableにする
	$("#u_bonuspayment").removeAttr("readonly").val(0).custom_selectbox();
	$('input[name="u_bonusmonth1"]').removeAttr("disabled").radio_disabled_off().custom_selectbox();
	$('input[name="u_bonusmonth2"]').removeAttr("disabled").radio_disabled_off().custom_selectbox();
}

function used_bonus_off(){
	// 無効になったとき
	$("#u_bonuspayment").val(0);
	// ボーナス関連をreadonlyにする
	$("#u_bonuspayment").attr('readonly', true).custom_selectbox();;
	$('input[name="u_bonusmonth1"]:radio').attr('disabled', true).radio_disabled_on().custom_selectbox();
	$('input[name="u_bonusmonth2"]:radio').attr('disabled', true).radio_disabled_on().custom_selectbox();
}

function textbox2num(id){
	return price2num($("#"+id).val());
}

function get_makeroptiontotal(){
	return textbox2num("makeroption")/(1.00+g_tax);
}

function newcar_leflet_comment(){
	var url = $("#newcar_leaflet").attr("href")+"?comment="+$("#newcar_comment").val();
	//alert(url);
	
	$("#newcar_leaflet2").attr("href",encodeURI(url));
	
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
	if($("#u_classname").val() && $("#u_carname").val() && $("#u_installments").val() && $("#u_rate").val() && $("#u_plan").val() && $("#tourokuyear").val() && ( ( $("#u_plan").val() == "wp" && textbox2num("u_loanprincipal")>=500000) || ( $("#u_plan").val() == "std" && textbox2num("u_loanprincipal")>=300000))){
		$("#usedcar_calc").show();
	}else{
		$("#usedcar_calc").hide();
	}
}



/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  ここから処理開始
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
