<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>新車比較見積り｜ローン見積り提案システム</title>
<link rel="stylesheet" href="/loan/css/loan.css">
<script src="<?= CAKEPHP_URL ?>/js/jquery-1.9.1.js"></script>
<script src="<?= CAKEPHP_URL ?>/js/loan_common.js?tm=<?= date("YmdHis") ?>"></script>
<script src="<?= CAKEPHP_URL ?>/js/loan.js?tm=<?= date("YmdHis") ?>"></script>
<script src="<?= CAKEPHP_URL ?>/js/run.js?tm=<?= date("YmdHis") ?>"></script> 
<script>
g_mode = "new";
var login = 1;

// 比較見積もり時に参照するログ番号
var g_logid1 = "<?= $logid ?>";
var g_logid2 = "";
var g_logid3 = "";

// 個別提案書のURLグローバル
var g_leaflet_urlArr = [];
//2016.08.26 add
var g_estimate_urlArr = [];


$(function(){
///////////////////////////////////////////////////////////////////////
// イベントハンドラ登録
///////////////////////////////////////////////////////////////////////
	$(document).on("change","#plan2",function(){
		// プラン名更新
		//data2.plan = $("#plan2").val();
		get_values(data2,2);
		
		// 金利データはリセット 2013.03.03 by morita
		data2.selectedrate = "";
		
		// テーブル描画
		init_centerarea();
	});
	
	$(document).on("change","#plan3",function(){
		// プラン名更新
		//data3.plan = $("#plan3").val();
		get_values(data3,3);
		
		// 金利データはリセット 2013.03.03 by morita
		data3.selectedrate = "";
		
		// テーブル描画
		init_rightarea();
	});
	
	$(document).on('click',"#calc2",function(){
		if($("#times2").val()){
			// 支払い回数選択時のみ反応
			if(1){
				if($("#kinri2").val()){
					// ログインしている場合は、金利選択時のみ反応
					chkConditions("check",data2,2);
				}
			}else{
				// ログインしてない場合は常にOK
				chkConditions("check",data2,2);
			}
		}
	});
	
	$(document).on("click","#calc3",function(){
		if($("#plan3").val()){
			// プラン選択時のみ反応
			if($("#times3").val()){
				// 支払い回数選択時のみ反応
				if(1){
					if($("#kinri3").val()){
						// ログインしている場合は、金利選択時のみ反応
						chkConditions("check",data3,3);
					}
				}else{
					// ログインしてない場合は常にOK
					chkConditions("check",data3,3);
				}
			}
		}
	});
	
	$(document).on("change","#times2",function(){
		// 残価を取得
		var lastpayment;
		switch($("#plan2").val()){
			/*
			case "wp":
				lastpayment = getmaxwplpt($("#installments").val(),"new");
				$("#lastpayment").val(num2price(lastpayment));
				break;
			*/
			case "wp":
			case "swp":
				lastpayment = getswplpt($("#times2").val());
				$("#lastpayment2").val(num2price(lastpayment));
				break;
			default:
				// 残価はゼロ
				$("#lastpayment2").val(0);
		}
		chkConditions("zanka",data2,2);
		if(1){
			setRateSelectbox(data2,2);
			if(data2.plan == "cls"){
				setMilageSelectbox(data2,2);
			}
		}else{
			// 通常モードにて、金利を非表示にする by morita 2013.03.03
			$("#rate2").html("");
		}
	});
	$(document).on("change","#times3",function(){
		// 残価を取得
		var lastpayment;
		switch($("#plan3").val()){
			/*
			case "wp":
				lastpayment = getmaxwplpt($("#installments").val(),"new");
				$("#lastpayment").val(num2price(lastpayment));
				break;
			*/
			case "wp":
			case "swp":
				lastpayment = getswplpt($("#times3").val());
				$("#lastpayment3").val(num2price(lastpayment));
				break;
			default:
				// 残価はゼロ
				$("#lastpayment3").val(0);
		}
		chkConditions("zanka",data3,3);
		if(1){
			setRateSelectbox(data3,3);
			if(data3.plan == "cls"){
				setMilageSelectbox(data3,3);
			}
		}else{
			// 通常モードにて、金利を非表示にする by morita 2013.03.03
			$("#rate3").html("");
		}
	});
	
	/* カンマをリアルタイムで入れる処理 */
	/*
	2014.03.05 カンマ処理をフォーカスアウト時に変更
	$(document).on("click","#zanka2,#atama2,#bonus2,#zanka3,#atama3,#bonus3",function(){
		$('this').val(num2price(price2num($('this').val())));
	});
	*/
	
	/*
	$(document).on("keyup","#zanka2,#atama2,#bonus2,#zanka3,#atama3,#bonus3",function(){
		$(this).val(num2price(price2num($(this).val())));
	});
	*/
	
	// PDFボタン
	$(document).on("click","#comparepdf",function(){
		var d = new Date();
	 
		// 年月日・曜日・時分秒の取得
		var year  = d.getYear();
		var month  = d.getMonth() + 1;
		var day    = d.getDate();
		var hour   = d.getHours();
		var minute = d.getMinutes();
		var second = d.getSeconds();
	 
		// 1桁を2桁に変換する
		if (month < 10) {month = "0" + month;}
		if (day < 10) {day = "0" + day;}
		if (hour < 10) {hour = "0" + hour;}
		if (minute < 10) {minute = "0" + minute;}
		if (second < 10) {second = "0" + second;}
		// 整形して返却
		window.open("<?= CAKEPHP_URL ?>/pdf/estimate2/none/compare/"+data1.logid+"/"+data2.logid+"/"+data3.logid+"/"+year+month+day+hour+minute+second+"_<?= $clasname ?>_<?= $carname ?>");
		setTimeout(function(){
						//SiteCatalyst code version: H.26.
						s.linkTrackVars='events'; 
						s.linkTrackEvents='event25';
						s.events='event25';
						s.tl(this,'o','Comparison_PDFDL');
						//SiteCatalyst code version: H.26.
					},500);
	});
	
	// 個別提案書ボタン
	$(document).on("click","#leaflet1,#leaflet2,#leaflet3",function(){
		var id = $(this).attr("id");
		var num = id.substr(7,1)*1;
		
		// ボタンのリンクをhiddenに設定
		$("#url").val(g_leaflet_urlArr[num-1]);
		
		// 個別提案書子画面表示
		$("#main").hide();
		$("#newcar_proposal2").show();
		
		return false;
	});
	
	// 個別提案書子画面戻る
	$(document).on("click","#newcar_proposal_close2",function(){
		// 元の画面を表示
		$("#newcar_proposal2").hide();
		$("#main").show();
		
		return false;
	});
	
	// 個別提案書ボタン
	$(document).on("click","#newcar_leaflet3",function(){
		$(this).attr("href",$("#url").val());
		
		return true;
	});
	$(document).on("click","#newcar_leaflet4",function(){
		//$(this).attr("href",$("#url").val()+"?comment="+encodeURI($("#newcar_comment2").val());
		//2016.08.26 add
		$(this).attr("href",$("#url").val()+"?comment="+encodeURI($("#newcar_comment2").val())+"&user_name="+encodeURI($("#user_name").val()));
		
		return true;
	});

	//2016.08.26 add
	//PDF画面
	$(document).on("click","#pdf1,#pdf2,#pdf3",function(){
		var id = $(this).attr("id");
		var num = id.substr(3,1)*1;
		
		// ボタンのリンクをhiddenに設定
		$("#url").val(g_estimate_urlArr[num-1]);
		console.log(g_estimate_urlArr[num-1]);
		// 個別提案書子画面表示
		$("#main").hide();
		$("#newcar_estimate2").show();
		
		return false;
	});

	// PDF画面から戻る
	$(document).on("click","#newcar_estimate_close2",function(){
		// 元の画面を表示
		$("#newcar_estimate2").hide();
		$("#main").show();
		
		return false;
	});
	$(document).on("click","#newcar_pdf4",function(){
		$(this).attr("href",$("#url").val()+"?&user_name="+encodeURI($("#estimate_user_name").val()));
		
		return true;
	});
				
});
	
/////////////////////////////////////////////////////////////////////////////////
//	Ajax関数
/////////////////////////////////////////////////////////////////////////////////
	
	
	/////////////////////////////////////////////////////////////////////////////////
	//	通常の関数
	/////////////////////////////////////////////////////////////////////////////////
	function init_leftarea(){
		var url = "<?= CAKEPHP_URL ?>/cars/hikakuhtml/"+data1.plan+"/1/<?= $bmst ?>";

		// 枠を呼び出し
		$.get(url,function(data,textStatus,XMLHttpRequest){
			$("#leftarea").html(data);
			
			$("#comparepdf").hide();
		
			// 支払い年数・回数オプションを描画
			$("#times1").html(data1.installments+(data1.plan=="std" ? 0 : 1)+"回");
			// 現金描画
			$("#genkin1").html(num2price(data1.genkin)+'円');
			// 下取描画
			$("#shitadori1").html(num2price(data1.shitadori)+'円');
			// 残債描画
			$("#zansai1").html(num2price(data1.zansai)+'円');
			// ボーナス月加算金額
			$("#bonus1").html(num2price(data1.bonuspayment)+'円');
			// 残価
			$("#zanka1").html(num2price(data1.lastpayment)+'円');
			
			if(login && data1.plan == "cls"){
				// milage
				$("#milage1").html(data1.milage+"万km");
			}
			
			// 実質年率
			$("#rate1").html(data1.selectedrate+"%");
			
			// 値をセット
			set_values(data1,1);
			
			// ボタンリンク先をセット
			$("#pdf1").attr("href","<?= CAKEPHP_URL ?>/pdf/estimate/<?= $code ?>");
			var leaflet = "<?= $leaflet ?>";
			if(leaflet){
				//$("#leaflet1").attr("href","<?= CAKEPHP_URL ?>/pdf/leaflet/l_<?= $code ?>").show();
				$("#leaflet1").show();
				g_leaflet_urlArr[0] = "<?= CAKEPHP_URL ?>/pdf/leaflet/l_<?= $code ?>";
			}else{
				$("#leaflet1").hide();
				g_leaflet_urlArr[0] = "";
			}

			//2016.08.26 add
			g_estimate_urlArr[0] = "<?= CAKEPHP_URL ?>/pdf/estimate/<?= $code ?>";
		});
	}

	function init_centerarea(){
	
		var url = "<?= CAKEPHP_URL ?>/cars/hikakuhtml/"+data2.plan+"/2/<?= $bmst ?>";
		
	
		// 枠を呼び出し
		$.get(url,function(data,textStatus,XMLHttpRequest){
			$("#centerarea").html(data);
		
			// プランオプションにプランを格納
			$("#plan2").val(data2.plan).custom_selectbox();
			
		
			// ボタンを隠す
			$("#pdf2,#leaflet2").hide();
			
			// installmentsデータを取得
			$.getJSON("<?= CAKEPHP_URL ?>/cars/ajaxinstallments",{bmst:"<?= $bmst ?>",plan: data2.plan},function(data,textstatus){
				rateArr2 = [];
				rateArr2 = data;
			
				// 支払い年数・回数オプションを描画
				$("#times2").empty().append(makeRateOptionHtml(data2.plan,rateArr2,data2.installments)).custom_selectbox();
				
				// 現金描画
				$("#genkin2").val(num2price(data2.genkin));
				
				// 下取描画
				$("#shitadori2").val(num2price(data2.shitadori));
				// 残債描画
				$("#zansai2").val(num2price(data2.zansai));
				// ボーナス月加算金額
				$("#bonus2").val(num2price(data2.bonuspayment));
				
				// 残価
				if(data2.plan=="wp" || (login && data2.plan == "als")){
					data2.lastpayment = data1.lastpayment;
					$("#zanka2").val(num2price(data2.lastpayment));
					
					chkConditions("zanka",data2,2);
				}
				
				// 実質年率
				setRateSelectbox(data2,2);

				// 値をセット
				set_values(data2,2);
	
			});
			
			
		});
	}
	
	function init_rightarea(){
		var url;
		
		if(data3.plan==""){
			url = "<?= CAKEPHP_URL ?>/cars/hikakuhtml/"+data2.plan+"/3/<?= $bmst ?>";
		}else{
			url = "<?= CAKEPHP_URL ?>/cars/hikakuhtml/"+data3.plan+"/3/<?= $bmst ?>";
		}

		// 枠を呼び出し
		$.get(url,function(data,textStatus,XMLHttpRequest){
			$("#rightarea").html(data);
			
			// プランオプションにプランを格納
			$("#plan3").val(data3.plan).custom_selectbox();
		
			// ボタンを隠す
			$("#pdf3,#leaflet3").hide();
			
			// installmentsデータを取得
			$.getJSON("<?= CAKEPHP_URL ?>/cars/ajaxinstallments",{bmst:"<?= $bmst ?>",plan: data3.plan},function(data,textstatus){
				rateArr3 = [];
				rateArr3 = data;
							
				// 支払い年数・回数オプションを描画
				$("#times3").empty().append(makeRateOptionHtml(data3.plan,rateArr3,data3.installments)).custom_selectbox();
				
				// 現金描画
				$("#genkin3").val(num2price(data3.genkin));
				// 下取描画
				$("#shitadori3").val(num2price(data3.shitadori));
				// 残債描画
				$("#zansai3").val(num2price(data3.zansai));
				// ボーナス月加算金額
				$("#bonus3").val(num2price(data3.bonuspayment));
				if(data3.plan=="wp" || (login && data3.plan == "als")){
					// 残価
					$("#zanka3").val(num2price(data3.lastpayment));
				}

				// 実質年率
				if(1){
					if(data3.installments > 0){
						setRateSelectbox(data3,3);
					}
				}
				

				// 値をセット
				//set_values(data3,3);
			
			});
			
			
		});
	}
	
	
	
	function set_values(data,num){
		
		// 月々支払額（大きい表示）ゼロ時は表示しない
		// ローン系
		if(data.monthlypayment>0){
			$("#tsukiduki"+num).html(num2price(data.monthlypayment));
		}
		
		$("#loanprincipal"+num).html(num2price(data.loanprincipal));
		$("#firstpayment"+num).html(num2price(data.firstpayment));
		$("#monthlypayment"+num).html(num2price(data.monthlypayment));
		if(data.installments == 0){
			$("#paytimes"+num).html("-");
		}else{
			$("#paytimes"+num).html(data.installments-1);
		}
		$("#secondpayment"+num).html(data.installments-1);
		$("#bonuspayment"+num).html(num2price(data.bonuspayment));
		if(data.bonustimes == 0){
			$("#bonustimes"+num).html("-");		
		}else{
			$("#bonustimes"+num).html(data.bonustimes);		
		}
		$("#lastpayment"+num).html(num2price(data.lastpayment));
		$("#interest"+num).html(num2price(data.interest));	
		$("#loantotal"+num).html(num2price(data.loantotal));
		$("#totalpayment"+num).html(num2price(data.totalpayment));
		if(data.difference>0){
			$("#difference"+num).html(num2price(data.difference));
		}else{
			$("#difference"+num).html("-");
		}
			
	}
	
	function get_values(data,num){
		var plan_before = data.plan;
		
		data.plan = $("#plan"+num).val();
		data.installments = $("#times"+num).val();
		data.genkin = price2num($("#genkin"+num).val());
		data.downpayment = data.genkin + <?= $shitadori ?> - <?= $zansai ?>;
		data.bonuspayment = price2num($("#bonus"+num).val());
		if(plan_before == "wp"){
			data.lastpayment = price2num($("#zanka"+num).val());
		}
		
		if(1){
			// セールスマンモード時は、実質年率を取得
			data.selectedrate = 1.0*$("#kinri"+num).val();
			
			// als時は、残価変更可能
			if(plan_before == "als"){
				data.lastpayment = price2num($("#zanka"+num).val());
			}
			
			if(plan_before == "cls"){
				// cls時は、マイレージ変更可能
				data.milage = $("#milage"+num).val();
			}
		}
		
	}
	
	
	// 支払い回数のセレクトボックスoptionを返す
	function makeRateOptionHtml(plan,rateArr,installments){
		html = ""
		selected = "";
		
		if(plan == ""){
			html += '<option value="">回数選択</option>'
		}else{
			if(installments == 0){
				html += '<option value="">回数選択</option>'
			}
			for(i in rateArr){
				if(i == installments){
					selected = " selected";
				}else{
					selected = "";
				}
				html += "<option value='"+rateArr[i]+"'"+selected+">"+(rateArr[i]*1+(plan=="std" ? 0:1))+"回</option>\n";
				/*
				if(plan == 'std'){
				}else{
					html += "<option value='"+rateArr[i]+"'"+selected+">"+(rateArr[i]/12)+"年</option>\n";
				}
				*/
			}
		}
		return html;
	}
	
	function makePlanOptionHtml(plan){
		var html = ""
		var selected = "";
		var plannameArr = new Object();
		
		<?php foreach($plannameArr as $key=>$value): ?>
			plannameArr["<?= $key ?>"] = "<?= $value ?>";
		<?php endforeach; ?>
		
		for(i in plannameArr){
			if(i == plan){
				selected = " selected";
			}else{
				selected = "";
			}
			html += '<option value="'+i+'"'+selected+'>'+plannameArr[i]+'</option>\n';
		}
		
		return html;
	}
	
	// 金利のセレクトボックスを描画する
	function setRateSelectbox(data,num){
		var optionTag = "";
		
		// ajaxでrate情報を取得
		var rateArr = new Object();
		
		
		$.getJSON("<?= CAKEPHP_URL ?>/cars/ajaxrate",
			{
				bmst:			"<?= $bmst ?>",
				installments:	$("#times"+num).val(),
				plan:			$("#plan"+num).val()
			},
			function(json,textstatus){
				//$("#test").html(json);
				//JSONからデータ取得し、値を更新
			
				data.innerrate = json.innerrate;
				data.lowrate = json.lowrate;
				data.normalrate = json.normalrate;
				
				var option_html;
				
				var tempArr = [data.innerrate,data.lowrate,data.normalrate];
				
				// 配列の重複要素を削除する
				tempArr = uniquearray(tempArr);
				tempArr = tempArr.sort();
				
				//option_html = "<select class='dropdown' id='kinri"+num+"'>\n";
				option_html = "";
				
				for (var i in tempArr){
					option_html += "<option value='"+tempArr[i]+"'>"+tempArr[i]+"%</option>\n";
				}
				
				//option_html += "</select>\n";
				
				
				//$("#rate"+num).html(option_html);
				$("#kinri"+num).empty().append(option_html).custom_selectbox();
				
				
				// rate_nowは、以前に選択された金利なので、金利のリスト自体が変わったときは、innerrateが選択されるようにする
				if((data.selectedrate != data.lowrate && data.selectedrate != data.normalrate && data.selectedrate != data.innerrate) || data.selectedrate != null){
					// デフォルト値を選択
					// 2012.10.31 inner rateが設定されているときに、low rateがデフォルトになる不具合修正
					if(data.innerrate>0){
						data.selectedrate = data.innerrate;
					}else if(data.lowrate>0){
						data.selectedrate = data.lowrate;
					}else{
						data.selectedrate = data.normalrate;
					}
				}
				$("#kinri"+num).val(data.selectedrate).custom_selectbox();
				
				chkConditions("zanka",data,num);
			}
		);
	}
	
	// 金利のセレクトボックスを描画する
	function setRateSelectbox_local(data,num){
		// 金利プルダウン作成
		var tempArr = findFromDBArr(g_rateArr,{"installments":data.installments});
		
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
		
		// 2014.03.05 金利を数字のみに変更
		rateArr2 = rateArr2.sort();
		
		var optionTag = "<option value=''>▼選択</option>";
		var num = rateArr2.length;

		for(var i=0;i<num;i++){
			optionTag += "<option value='"+rateArr2[i]+"'>"+addZero(rateArr2[i]+"")+"%</option>";
		}
		
		if(0){
		
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
		
		} // if(0)
		
		
		
		// オプションタグ入れ替え
		$("#kinri"+num).empty().append(optionTag).val(data.selectedrate).custom_selectbox();
	}
	
	
	
	
	//重複を取り除く関数
	function uniquearray(array) {
	　var storage = {};
	　var uniqueArray = [];
	　var i,value;
	　for ( i=0; i<array.length; i++) {
	   　value = array[i];
		  if (!(value in storage)) {
		  　storage[value] = true;
			 uniqueArray.push(value);
		   }
	   }
	   return uniqueArray;
	}	
	// cls 走行距離セレクトボックスを描画する
	function setMilageSelectbox(data,num){
		var optionTag = "";
		
		$.getJSON("<?= CAKEPHP_URL ?>/cars/ajaxmilage",
			{
				bmst:			"<?= $bmst ?>",
				installments:	$("#times"+num).val(),
			},
			function(json,textstatus){
				//$("#test").html(json);
				//JSONからデータ取得し、値を更新
				
				var kmmax = json.kmmax;
				
				var option_html;
				
				option_html = "<select class='dropdown' id='milage"+num+"'>\n";
				
				for (i=1;i<=kmmax;i++){
					option_html += "<option value='"+i+"'>"+i+"万km</option>\n";
				}
				
				option_html += "</select>\n";
				
			
				$("#km"+num).html(option_html);
				
				$("#milage"+num).val(data.milage);
			}
		);
	}
	
	/*

	//カンマ挿入関数
	function num2price(sourceStr) {
	  var destStr = toHankakuNum(String(sourceStr));
	  var tmpStr = "";
	  // NAN問題対応
	  while (destStr != (tmpStr = destStr.replace(/^([+-]?\d+)(\d\d\d)/,"$1,$2"))) {
		destStr = tmpStr;
	  }
		return destStr;
	}
	
	//カンマ削除関数
	function price2num(w) {
		//var z = w.replace(/[,a-zA-Z]/g,"");
		w = toHankakuNum(w);
		var z = w.replace(/,/g,"");
		z = Number(z);
		if(isNaN(z)){
			z=0;
		}
		return (z);
	}
	
	
	// 全角を半角に変換
	function toHankakuNum(motoText)
	{
		han = "0123456789.,-+";
		zen = "０１２３４５６７８９．，－＋";
		str = "";
		for (i=0; i<motoText.length; i++)
		{
			c = motoText.charAt(i);
			n = zen.indexOf(c,0);
			if (n >= 0) c = han.charAt(n);
			str += c;
		}
		return str;
	}
	*/
	
	
	/* 計算条件値チェック関数 */
	function chkConditions(mode,data,num){
		// 入力値を変数にゲット
		get_values(data,num);
		
		var conditionArr = [];
		

		conditionArr['mode'] = true;		// メッセージは表示する
		conditionArr['bmst'] = "<?= $bmst ?>";
		conditionArr['total'] = <?= $loanprincipal+$lastpayment+$genkin + $shitadori -$zansai ?>;						//合計金額
		conditionArr['installments'] = data.installments;			//支払い回数
		conditionArr['pricetax'] = <?= $pricetax ?>;			//税込み車両本体価格
		conditionArr['optiontotal'] = <?= $makeroption ?>;			//税込みオプション金額合計
		conditionArr['taxtotal'] = <?= $dealeroption ?>;						//税金等の諸経費合計
		conditionArr['downpayment'] = data.genkin + <?= $shitadori ?> -<?= $zansai ?>;			//頭金
		conditionArr['zansai'] = <?= $zansai ?>;			//頭金
		conditionArr['sonota'] = <?= $sonota-$discount+$mmmprice+$mmsprice+$evprice+$mbinsureance ?>;					// その他オプション
		conditionArr['loanprincipal'] = <?= $loanprincipal ?>;					// その他オプション
		conditionArr['lastpayment'] = data.lastpayment;					// その他オプション
		conditionArr['bonuspayment'] = data.bonuspayment;				// その他オプション
		conditionArr['rate'] = data.selectedrate;			// 選択された金利
		conditionArr['selectedrate'] = data.selectedrate;			// 選択された金利　_check()内部では、これも参照されているので
		
		var lptmaxmin_string="　　<br/>  ";
		var zankaritsu;
		
		
		// やっぱり常に厳密にする　2014.01.26
		conditionArr['bonustimes'] = <?= $bonustimes ?>;

		// パラメーターの値チェック
		// 金利が選択されている場合のみ行う
		//if($("#rate").val()){
		//if($("#kinri"+num).val()){
		if(conditionArr['rate']){
			var json = [];
			json= _check(data.plan,conditionArr,"new");
			
			if(mode == 'zanka'){
				if($("#times"+num).val()>0){
					// alsとwp時は、残価範囲を表示 2013.01.15 by morita
					$(".zankaminmax"+num).html("(￥"+num2price(json.lptmin) + "〜￥" + num2price(json.lptmax)+")");
				}else{
					$(".zankaminmax"+num).html("");
				}
			}else{
				//$("#test").html(json);
				//JSONからデータ取得し、値を更新
				var message = decodeURIComponent(json.message);
				message = message.split(".").join("<br />");
				
				// エラーメッセージ表示
				$("#message"+num).html(message);
				
				// チェック済み値を変数に格納
				data.normalrate = json.normalrate;
				data.lowrate = json.lowrate;
				data.innerrate = json.innerrate;
				data.rate = json.lowrate;
				if(!login){
					// 一般モードは固定
					data.selectedrate = json.lowrate;
				}
				data.genkin = json.downpayment - <?= $shitadori ?> + <?= $zansai ?>;
				data.lastpayment = json.lastpayment;
				data.bonuspayment = json.bonuspayment;
				
				// 変数を更新
				if($("#times"+num).val()>0){
					// alsとwp時は、残価範囲を表示 2013.01.15 by morita
					$(".zankaminmax"+num).html("(￥"+num2price(json.lptmin) + "〜￥" + num2price(json.lptmax)+")");
				}else{
					$(".zankaminmax"+num).html("");
				}
						
				// テキストボックス
				$("#bonus"+num).val(num2price(data.bonuspayment));
				$("#genkin"+num).val(num2price(data.genkin));
				$("#zanka"+num).val(num2price(data.lastpayment));

				calc("hikaku",data,num);
			}
		}
	}
	
	
	function calc(mode,data,num){
		selectedrate = $("#kinri"+num).val();

		// 入力値を変数にゲット
		//get_values(data,num);

				
		var conditions = [];
		// 2014.03.05 金利は数値を使用
		//conditions['rate'] = data.selectedrate;
		conditions['rate'] = selectedrate;
		conditions['installments'] = data.installments;
		conditions['pricetax'] = <?= $pricetax ?>;
		conditions['optiontotal'] = <?= $makeroption+$dealeroption ?>;
		conditions['taxtotal'] = 0;
		//conditions['downpayment'] = data.downpayment;
		conditions['downpayment'] = data.genkin + <?= $shitadori ?> - <?= $zansai ?>;
		conditions['bonuspayment'] = data.bonuspayment;
		conditions['lastpayment'] = data.lastpayment;
		conditions['sonota'] = <?= $sonota+$mmmprice+$mmsprice+$evprice+$mbinsureance-$discount ?>;
		
		// ボーナス回数を厳密に計算
		conditions['bonustimes'] = get_bonustimes(<?= $bonusmonth1 ?>,<?= $bonusmonth2 ?>,<?= $registmonth ?>,data.installments);
	
		var json = loancalc(data.plan,conditions);
		
		data.monthlypayment = json.monthlypayment;
		data.difference = json.difference;
		data.firstpayment = json.firstpayment;
		data.loanprincipal = json.loanprincipal;
		data.bonustimes = json.bonustimes;
		data.interest = json.interest;
		data.leasingprice = json.leasingprice;
		data.loantotal = json.total;
		data.totalpayment = json.total+data.downpayment;
		data.logid = json.logid;
		
		set_values(data,num);
		
		// 個別提案書のデータを確認する
		var leaflet = get_leaflet_data($("#plan"+num).val(),"<?= $classname ?>",data.installments);
		
		
		// 計算結果をサーバーに送信し、DBのIDを受け取る
		$.getJSON("<?= CAKEPHP_URL ?>/cars/insertlog/",{
						salesman:<?= $salesman ?>,
						plan:data.plan,
						classname:"<?= $classname ?>",
						bmst:"<?= $bmst ?>",
						carname:"<?= $carname ?>",
						pricetax:<?= $pricetax ?>,
						makeroption:<?= $makeroption ?>,
						dealeroption:<?= $dealeroption ?>,
						discount:<?= $discount ?>,
						mbinsureance:<?= $mbinsureance ?>,
						sonota:<?= $sonota ?>,
						mmmprice:<?= $mmmprice ?>,
						mmsprice:<?= $mmsprice ?>,
						evprice:<?= $evprice ?>,
						cartotal:<?= $cartotal ?>,
						totalpayment:data.totalpayment,
						downpayment:data.downpayment,
						shitadori:<?= $shitadori ?>,
						genkin:data.genkin,
						zansai: <?= $zansai ?>,
						loanprincipal:data.loanprincipal,
						loantotal:json.total,
						installments:$('#times'+num).val(),
						firstpayment:json['firstpayment'],
						monthlypayment:json["monthlypayment"],
						bonuspayment:data.bonuspayment,
						bonustimes:json['bonustimes'],
						registyear:<?= $registyear ?>,
						registmonth:<?= $registmonth ?>,
						bonusmonth1:<?= $bonusmonth1 ?>,
						bonusmonth2:<?= $bonusmonth2 ?>,
						rate:data.selectedrate,
						lastpayment:data.lastpayment,
						interest:json['interest'],
						leafletimage:leaflet,
						tax:<?= $tax ?>,
						tm:stamp()
				},function(json){
					// json.code data.idが入ってくる
					if(num==2){
						g_logid2 = json.id;
					}
					if(num==3){
						g_logid3 = json.id;
					}
					// pdfボタンの更新など
					$("#pdf"+num).attr("href","<?= CAKEPHP_URL ?>/pdf/estimate/"+json.code).show();
					if(leaflet){
						//$("#leaflet"+num).attr("href","<?= CAKEPHP_URL ?>/pdf/leaflet/l_"+json.code).show();
						$("#leaflet"+num).show();
						g_leaflet_urlArr[num-1] = "<?= CAKEPHP_URL ?>/pdf/leaflet/l_"+json.code;
					}else{
						g_leaflet_urlArr[num-1] = "";
					}
					//2016.08.26 add
					g_estimate_urlArr[num-1] = "<?= CAKEPHP_URL ?>/pdf/estimate/"+json.code;

					var d = new Date();
				 
					// 年月日・曜日・時分秒の取得
					var year  = d.getYear();
					var month  = d.getMonth() + 1;
					var day    = d.getDate();
					var hour   = d.getHours();
					var minute = d.getMinutes();
					var second = d.getSeconds();
				 
					// 1桁を2桁に変換する
					if (month < 10) {month = "0" + month;}
					if (day < 10) {day = "0" + day;}
					if (hour < 10) {hour = "0" + hour;}
					if (minute < 10) {minute = "0" + minute;}
					if (second < 10) {second = "0" + second;}
					// 整形して返却
					// 比較見積もりボタン
					$("#hikakupdf").attr("href","<?= CAKEPHP_URL ?>/pdf/estimate/compare/"+g_logid1+"/"+g_logid2+"/"+g_logid3+"/"+year+month+day+hour+minute+second+"_<?= $clasname ?>_<?= $carname ?>").show();

		});
	}
	


///////////////////////////////////////////////////////////////////////////
//	ここから処理開始
///////////////////////////////////////////////////////////////////////////
	var rateArr2 = new Object();
	var rateArr3 = new Object();
	
	var data1 = new Object();
	var data2 = new Object();
	var data3 = new Object();
	
	uiInit();
	logicInit(function(){
	
		
		data1.plan = "<?= $plan ?>";
		data1.selectedrate = <?= $rate ?>;
		data1.downpayment = <?= $downpayment ?>;
		data1.genkin = <?= $genkin ?>;
		data1.shitadori = <?= $shitadori ?>;
		data1.zansai = <?= $zansai ?>;
		data1.firstpayment = <?= $firstpayment ?>;
		data1.monthlypayment = <?= $monthlypayment ?>;
		data1.installments = <?= $installments ?>;
		data1.bonuspayment = <?= $bonuspayment ?>;
		data1.bonustimes = <?= $bonustimes ?>;
		data1.lastpayment = <?= $lastpayment ?>;
		data1.interest = <?= $interest ?>;
		data1.loantotal = <?= $loantotal ?>;
		data1.totalpayment = <?= $loantotal+$downpayment ?>;
		data1.loanprincipal = <?= $loanprincipal ?>;
		data1.difference = 0;
		data1.leasingprice = 0;
		data1.logid = <?= $logid ?>;
		data1.milage = 0;
		
		data2.plan = "<?= $plan ?>";
		data2.normalrate = <?= $rate ?>;
		data2.lowrate = <?= $rate ?>;
		data2.innerrate = <?= $rate ?>;
		data2.selectedrate = <?= $rate ?>;
		data2.downpayment = <?= $downpayment ?>;
		data2.genkin = <?= $genkin ?>;
		data2.shitadori = <?= $shitadori ?>;
		data2.zansai = <?= $zansai ?>;
		data2.firstpayment = <?= $firstpayment ?>;
		data2.monthlypayment = <?= $monthlypayment ?>;
		data2.installments = <?= $installments ?>;
		data2.bonuspayment = <?= $bonuspayment ?>;
		data2.bonustimes = <?= $bonustimes ?>;
		data2.lastpayment = <?= $lastpayment ?>;
		data2.interest = <?= $interest ?>;
		data2.loantotal = <?= $loantotal ?>;
		data2.totalpayment = <?= $loantotal+$downpayment ?>;
		data2.loanprincipal = <?= $loanprincipal ?>;
		data2.difference = 0;
		data2.leasingprice = 0;
		data2.logid = <?= $logid ?>;
		data2.milage = 0;
		
		data3.plan = "";
		data3.normalrate = 0;
		data3.lowrate = 0;
		data3.innerrate = 0;
		data3.selectedrate = 0;
		data3.downpayment = 0;
		data3.genkin = 0;
		data3.shitadori = 0;
		data3.zansai = 0;
		data3.firstpayment = 0;
		data3.monthlypayment = 0;
		data3.installments = 0;
		data3.bonuspayment = 0;
		data3.bonustimes = 0;
		data3.lastpayment = 0;
		data3.interest = 0;
		data3.loantotal = 0;
		data3.totalpayment = 0;
		data3.loanprincipal = 0;
		data3.difference = 0;
		data3.leasingprice = 0;
		data3.logid = 0;
		data3.milage = <?= $milage ?>;
			
		// carレコードの取得
		$.getJSON("<?= CAKEPHP_URL ?>/cars/carjson/<?= $bmst ?>&tm="+stamp(),function(json){
			// グローバルに格納
			g_carArr = json;
			carArr = g_carArr;
			
			g_tax = <?= $tax ?>;
			
			if(g_tax == 0.05) {
				// 消費税5%
				// なにもしない
			}else{
				// 消費税8%
				// テーブル読み直し
				g_carArr['price'] = g_carArr['price2'];
				g_carArr['pricetax'] = g_carArr['pricetax2'];
				g_carArr['mmm'] = g_carArr['mmm2'];
				g_carArr['mms'] = g_carArr['mms2'];
				g_carArr['ev'] = g_carArr['ev2'];
			}
	
	
			//var login = "<?= $login ?>";
			
			<?php if(!$login && $plan == "cls") $plan = "als"; ?>
			
			
		
			// 値を初期設定
			<?php /*foreach($rateArr as $arr): ?>
				rateArr2['<?= $arr['Rate']['installments'] ?>'] = <?= $arr['Rate']['rate'] ?>;
			<?php endforeach; */ ?>
		<?php
			$milage = 0;
			$leasingprice = 0;
			$logid = $id;
		?>
			
			init_leftarea()
			init_centerarea();
			init_rightarea();
		});

	});


</script>
</head>
<body>
<!-- ////////////////////新車比較見積りここから//////////////////// -->
<div>
<input type="hidden" name="makeroption" id="makeroption" value="<?= $makeroption ?>" />
<div id="newcar_comparing">
<div class="header01">
<div class="header-lay">
<p class="site-id"><img src="/loan/img/header/site-id.png" alt="Mercedes-Benz -Finance Calculation-" width="364" height="50"></p>
<p class="btn02"><a href="#" target="_blank" id="hikakupdf"><img src="/loan/img/header/btn-comparing-pdf.png" alt="比較見積りPDF" width="208" height="46"></a></p>
<p class="btn01"><a href="#" class="window-close"><img src="/loan/img/header/btn-close.png" alt="閉じる" width="168" height="46"></a></p>
<!-- /.header01-lay --></div>
<!-- /.header01 --></div>

<div class="contents">
<div class="main-contents01" id="main">

<div class="blk-comparing01">
<div id="comparegamen">
<!--
///////////////////////////////////////////////////////////////////////////
//	左エリア
///////////////////////////////////////////////////////////////////////////
-->
<div id="leftarea" class="cont">
</div>
<!--
///////////////////////////////////////////////////////////////////////////
//	左エリア
///////////////////////////////////////////////////////////////////////////
-->

<!--
///////////////////////////////////////////////////////////////////////////
//	中央エリア
///////////////////////////////////////////////////////////////////////////
-->
<div id="centerarea" class="cont">
データ読み込み中・・・・・
</div>
<!--
///////////////////////////////////////////////////////////////////////////
//	中央エリア
///////////////////////////////////////////////////////////////////////////
-->

<!--
///////////////////////////////////////////////////////////////////////////
//	右エリア
///////////////////////////////////////////////////////////////////////////
-->
<div id="rightarea" class="cont">
</div>
<!--
///////////////////////////////////////////////////////////////////////////
//	右エリア
///////////////////////////////////////////////////////////////////////////
-->
</div>
<!-- /.blk-comparing01 --></div>

<!-- /.main-contents01 --></div>
<!-- ////////////////////新車個別提案書（PDF）ここから//////////////////// -->
<br>
<div id="newcar_proposal2">
<div class="blk-versatile02">
<p class="title-type01">個別提案書PDF出力</p>
<p><h4>お客様氏名</h4></p>
<p><input type="text" id="user_name" maxlength="10" style="width:150px;" />&nbsp;様</p>
<p><h4>コメント</h4></p>
<p><textarea class="textarea-type01" maxlength="250" id="newcar_comment2"></textarea></p>
<p class="align-type03">250文字以内</p>
<ul class="list-image01">
<li style="display:none;"><a href="#" id="newcar_leaflet3" target="_blank"></a></li>
<li><center><a href="#" id="newcar_leaflet4" target="_blank"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-pdf-output-leaflet.png" alt="PDF出力" width="300" height="50"></a></center></li>
</ul>
<p class="btn-back01"><a href="#" id="newcar_proposal_close2"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-back01.png" alt="戻る" width="124" height="32"></a></p>
<input type="hidden" id="url" name="url"/>
<!-- /.blk-versatile02 --></div>
<!-- /#newcar_proposal --></div>
<!-- ////////////////////新車個別提案書（PDF）ここまで//////////////////// -->

<!-- ////////////////////見積書（PDF）ここから//////////////////// -->
<div id="newcar_estimate2">
<div class="blk-versatile02">
<p class="title-type01">見積書PDF出力</p>
<p><h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;お客様氏名</h4></p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="estimate_user_name" maxlength="20" style="width:280px;" />&nbsp;様</p>
<p style="display:none;">コメント</p>
<p style="display:none;"><textarea class="textarea-type01" maxlength="250" id="newcar_comment"></textarea></p>
<p class="align-type03" style="display:none;">250文字以内</p>
<ul class="list-image01">
<li style="display:none;"><a href="#" id="newcar_leaflet" target="_blank"></a></li>
<br /><br /><br />
<li><center><a href="#" id="newcar_pdf4" target="_blank"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-pdf-output-estimate.png" alt="PDF出力" width="300" height="50"></a></center></li>
</ul>
<p class="btn-back01"><a href="#" id="newcar_estimate_close2"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-back01.png" alt="戻る" width="124" height="32"></a></p>
<!-- /.blk-versatile02 --></div>
<!-- /#newcar_proposal --></div>
<!-- ////////////////////見積書（PDF）ここまで//////////////////// -->

<div class="footer01">
<!--<p class="version"></p>-->
<p class="copyright">&copy;Mercedes-Benz Finance Co., Ltd. All rights reserved.</p>
<!-- /.footer01 --></div>
<!-- /#newcar_comparing --></div>
<!-- ////////////////////新車比較見積りここまで//////////////////// -->
<!-- /.contents --></div>
</div><!-- newcar_normal -->
<script>
	// 比較pdfを消す
	$("#hikakupdf").hide();
	// 個別提案書画面を消す
	$("#newcar_proposal2").hide();
	//2016.08.26 add
	$("#newcar_estimate2").hide();

</script>
</body>
</html>

