<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<meta name="viewport" content="width=1000;"> 
<title>ローン見積り提案システム</title>
<link rel="stylesheet" href="<?=CAKEPHP_URL ?>/css/loan.css">
<script src="<?= CAKEPHP_URL ?>/js/jquery-1.9.1.js"></script>
<script src="<?= CAKEPHP_URL ?>/js/loan_common.js?tm=<?= $_GET['version'] ?>"></script>
<script src="<?= CAKEPHP_URL ?>/js/loan.js?tm=<?= $_GET['version'] ?>"></script>
<script src="<?= CAKEPHP_URL ?>/js/run.js?tm=<?= $_GET['version'] ?>"></script>
</head>

<body>
<script>
	// 昔のバージョンをブックマークしている場合は、強制ログアウト
	/*
	var mytime = String(g_param.tm);
	
	var year = mytime.substr(0,4);
	var month = mytime.substr(4,2);
	var day = mytime.substr(6,2);
	
	var hiduke=new Date(); 
	
	//年・月・日・曜日を取得する
	var year2 = hiduke.getFullYear();
	var month2 = hiduke.getMonth()+1;
	var day2 = hiduke.getDate();
	
	// URL の日付が異なっていた場合は強制ログアウト
	if(year*1 != year2*1 || month*1 != month2*1 || day*1 != day2*1){
		//alert("強制的にログアウトします");
		forcelogout();
	}
	*/
</script>

<!-- ==================================================================================================================================================== -->
<!-- 新車 -->
<!-- ==================================================================================================================================================== -->
<div id="newcar">
<div class="header01">
<div class="header-lay">
<p class="site-id"><img src="<?= CAKEPHP_URL ?>/img/header/site-id.png" alt="Mercedes-Benz -Finance Calculation-" width="364" height="50"></p>
<ul class="nav-car01">
<li><img src="<?= CAKEPHP_URL ?>/img/header/nav-newcar_a.png" alt="新車" width="156" height="36"></li>
<li><a href="#" class="used-tab"><img src="<?= CAKEPHP_URL ?>/img/header/nav-usedcar_o.png" alt="中古車" width="151" height="36"></a></li>
<li><a href="#" class="serviceloan-tab"><img src="<?= CAKEPHP_URL ?>/img/header/nav-serviceloan_o.png" alt="サービスローン" width="156" height="36"></a></li>
</ul>
<!-- /.header01-lay --></div>
<!-- /.header01 --></div>
<div class="contents">
<div class="main-contents01">

<!-- ////////////////////新車条件入力ここから//////////////////// -->
<div id="newcar_input">
<div class="info-input01">
<table border="1" class="tbl-type02">
<col style="width:166px;">
<col style="width:365px;">
<col style="width:125px;">
<col style="width:324px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-class.png" alt="クラス" width="44" height="22"></th>
<td>
<div class="select-custom01 width03">
<div class="inner"><span>&nbsp;</span></div>
<select name="classname" id="classname">
	<option value="">▼選択してください</option>
<?php foreach($carArrs as $key=>$carArr): ?>
	<option value="<?= $carArr['Car']['classname'] ?>"><?= $carArr['Car']['qc_classname'] ?></option>
<?php endforeach; ?>
</select>
<!-- /.select-custom01 --></div>
</td>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-first-years.png" alt="車両登録年月" width="86" height="22"></th>
<td class="valign-type02">
<div class="select-custom01 width02 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="year" id="newcar_year">
	<?php for($year=date("Y");$year<=date("Y")+1;$year++): ?>
	<option value="<?= $year ?>" <?php if($year==date("Y")) echo 'selected'; ?>><?= $year ?>(平成<?= $year-1988 ?>)年</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-year01.png" alt="年" width="26" height="22" class="side-left">
<div class="select-custom01 width00 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="month" id="newcar_month">
	<?php for($month=1;$month<=12;$month++): ?>
	<option value="<?= $month ?>" <?php if($month==date("m")) echo 'selected'; ?>><?= $month ?>月</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-month01.png" alt="月" width="26" height="22" class="side-left"><span id="newcar_zei">【消費税率 8％】</span>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-model.png" alt="モデル" width="46" height="22"></th>
<td colspan="5">
<div class="select-custom01 width04">
<div class="inner"><span>&nbsp;</span></div>
<select name="carname" id="carname">
</select>
<!-- /.select-custom01 --></div>
</td>
</tr>
</tbody>
</table>
<!-- /.info-input01 --></div>

<div class="info-input02">
<div class="cont01" id="newcar_input">
<table border="1" class="tbl-type02">
<col style="width:166px;">
<col style="width:319px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-car-price.png" alt="車両本体価格（税込）" width="124" height="22"></th>
<td><input type="text" name="pricetax" id="pricetax" class="input-type04" maxlength="10"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-maker-option.png" alt="メーカーオプション（取得税課税）" width="120" height="24"></th>
<td><input type="tel" name="makeroption" id="makeroption" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><br>
<span class="caption02" id="maker_option_comment">※税込み金額をご入力ください【消費税率 8％】<br>※残価算入オプションがある場合はメーカーオプション欄に<br>ご入力ください</span></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-store-option.png" alt="販売店オプション（取得税非課税）" width="110" height="24"></th>
<td><input type="tel" name="dealeroption" id="dealeroption" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><!--<img src="<?= CAKEPHP_URL ?>/img/contents/term-discount.png" alt="値引き" width="16" height="22">--></th>
<td><input type="tel" name="discount" id="discount" class="input-type04" value="" maxlength="9" style="ime-mode: disabled;"><!--<img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22">--></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-price-subtotal.png" alt="現金価格小計" width="88" height="22"></th>
<td><input type="text" name="cartotal" id="cartotal" class="input-type04" readonly><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-tax.png" alt="税金／販売諸費用" width="116" height="22"></th>
<td><input type="tel" name="sonota" id="sonota" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-car-premium.png" alt="自動車任意保険料" width="114" height="22"></th>
<td><input type="tel" name="mbinsureance" id="mbinsureance" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th></th>
<td><span id="m_plan_check"><input name="m_plan" type="radio" value="gs" checked />ガソリン車プラン&nbsp;&nbsp;<input name="m_plan" type="radio" value="ev" />EV車プラン</span></td>
</tr>
<tr>
<th><img id="mmm_mbj" src="<?= CAKEPHP_URL ?>/img/contents/term-maintenance.png" alt="メンテナンスプラス" width="118" height="22"><img id="mmm_smart" src="<?= CAKEPHP_URL ?>/img/contents/term-maintenance_smart.png" alt="smartメンテナンス" width="118" height="22"></th>
<td><input type="tel" name="mmmprice" id="mmmprice" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img id="mms_mbj" src="<?= CAKEPHP_URL ?>/img/contents/term-pledge.png" alt="保証プラス" width="70" height="22"><img id="mms_smart" src="<?= CAKEPHP_URL ?>/img/contents/term-pledge_smart.png" alt="smartメンテナンスプラス" width="118" height="22"></th>
<td><input type="tel" name="mmsprice" id="mmsprice" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><!--<img id="ev_mbj" src="<?= CAKEPHP_URL ?>/img/contents/term-ev.png" alt="" width="70" height="22">--><img id="ev_smart" src="<?= CAKEPHP_URL ?>/img/contents/term-ev_smart.png" alt="保証プラス" width="118" height="22"></th>
<td><input type="tel" name="evprice" id="evprice" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22" id="ev_yen"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-pay-total.png" alt="支払金額合計" width="88" height="22"></th>
<td><input type="text" readonly value="10,000,000" class="input-type04" value="0" id="totalpayment"/><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
</tbody>
</table>
<!-- /.cont01 --></div>
<div class="cont02">
<table border="1" class="tbl-type02">
<col style="width:171px;">
<col style="width:118px;">
<col style="width:89px;">
<col style="width:117px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-loan-kind.png" alt="ローン種別" width="80" height="22"></th>
<td colspan="3">
<div class="select-custom01 width03">
<div class="inner"><span>&nbsp;</span></div>
<select name="plan" id="plan"></select>
<!-- /.select-custom01 --></div>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-pay-num.png" alt="お支払い回数" width="86" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="installments" id="installments"></select>
<!-- /.select-custom01 --></div>
</td>
<th style="padding-right:9px;"><img src="<?= CAKEPHP_URL ?>/img/contents/term-interest-rate.png" alt="金利" width="34" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="rate" id="rate"></select>
<!-- /.select-custom01 --></div>
</td>
</tr>
<tr id="row_downpayment">
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-cash-pay.png" alt="現金支払い額" width="90" height="22">
<td colspan="3"><input type="tel" name="genkin" id="genkin" class="input-type04" value="0" maxlength="10" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-trade-in-price.png" alt="下取車価格" width="75" height="22"></th>
<td><input type="text" name="shitadori" id="shitadori" class="input-type06" value="0" maxlength="10" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
<th style="padding-right:9px;"><img src="<?= CAKEPHP_URL ?>/img/contents/term-trade-debt.png" alt="下取車残債額" width="100" height="22"></th>
<td><input type="tel" name="zansai" id="zansai" class="input-type06" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-loan-capital.png" alt="ローン元金" width="84" height="22"></th>
<td colspan="3"><input type="text" name="loanprincipal" id="loanprincipal" readonly value="0" class="input-type04"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-residual-value.png" alt="残価" width="34" height="22" maxlength="9"></th>
<td colspan="3"><input type="tel" name="lastpayment" id="lastpayment" class="input-type04" value="0" maxlength="10" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><br><span class="caption01 text-type02" id="lptmaxmin"></span></td>
</tr>
</tbody>
</table>
<div class="info-input03">
<table border="1" class="tbl-type02">
<col style="width:151px;">
<col style="width:319px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-added.png" alt="ボーナス月加算" width="96" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="bonus" value="0">有</label></li>
<li><label><input type="radio" name="bonus" value="1" checked>無</label></li>
</ul>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-price.png" alt="ボーナス月加算金額" width="124" height="22"></th>
<td><input type="tel" name="bonuspayment" id="bonuspayment" class="input-type02" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-add-limit.png" alt="加算金額上限" width="88" height="22"></th>
<td><span class="input-text01" id="bptmax"></span><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><a href="#" id="getbptmax" class="btn-calc01 space-left02">上限計算</a></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-sum.png" alt="ボーナス月設定（夏）" width="126" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="bonusmonth1" value="6" checked>6月</label></li>
<li><label><input type="radio" name="bonusmonth1" value="7">7月</label></li>
<li><label><input type="radio" name="bonusmonth1" value="8">8月</label></li>
</ul>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-win.png" alt="（冬）" width="32" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="bonusmonth2" value="12" checked>12月</label></li>
<li><label><input type="radio" name="bonusmonth2" value="1">1月</label></li>
</ul>
</td>
</tr>
</tbody>
</table>
<!-- /.info-input03 --></div>
<!-- /.cont02 --></div>
<!-- /.info-input02 --></div>

<p class="pra-calc01"><a href="#" id="newcar_calc"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-calc01.png" alt="計算" width="300" height="50"></a></p>
<!-- /#newcar_input --></div>
<!-- ////////////////////新車条件入力ここまで//////////////////// -->

<!-- ////////////////////新車計算結果ここから//////////////////// -->
<div id="newcar_result">
<div class="blk-versatile01">
<p class="title-model01"><span id="newcar_result_classname"></span><br><span id="newcar_result_carname"></span></p>
<div class="tbl-result01">
<table border="1">
<tbody>
<tr>
<th>車両本体価格<span class="sup">（消費税込み）</span></th>
<td id="newcar_result_pricetax"></td>
</tr>
<tr>
<th>支払金額合計</th>
<td id="newcar_result_totalpayment"></td>
</tr>
<tr>
<th id="label_newcar_result_genkin">現金</th>
<td id="newcar_result_genkin"></td>
</tr>
<tr>
<th id="label_newcar_result_shitadori">下取車価格</th>
<td id="newcar_result_shitadori"></td>
</tr>
<tr>
<th>下取車残債額</th>
<td id="newcar_result_zansai"></td>
</tr>
<!--
<tr>
<th>追加売買代金</th>
<td id="newcar_result_tsuika"></td>
</tr>
-->
<tr>
<th>ローン元金</th>
<td id="newcar_result_loanprincipal"></td>
</tr>
<tr>
<th>プラン名</th>
<td id="newcar_result_plan"></td>
</tr>
<tr>
<th>お支払い回数</th>
<td id="newcar_result_installments"></td>
</tr>
<tr>
<th>実質年率</th>
<td id="newcar_result_rate">%</td>
</tr>
<tr>
<th>月々お支払い金額</th>
<td id="newcar_result_monthlypayment"></td>
</tr>
<tr>
<th>ボーナス月加算金額</th>
<td id="newcar_result_bonuspayment"></td>
</tr>
<tr id="tr_lastpayment">
<th>残価</th>
<td id="newcar_result_lastpayment"></td>
</tr>
<tr>
<th>分割払いお支払い総額</th>
<td id="newcar_result_loantotal"></td>
</tr>
<tr>
<th>お支払い総額</th>
<td id="newcar_result_alltotalpayment"></td>
</tr>
</tbody>
</table>
<!-- /.tbl-result01 --></div>
<ul class="list-result-btn01">
<!--<li><a href="#" target="_blank" id="newcar_pdf"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-estimate01.png" alt="見積書（PDF）" width="138" height="46"></a></li>-->
<li><a href="#" id="newcar_estimate_open"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-estimate01.png" alt="見積書（PDF）" width="138" height="46"></a></li>
<li><a href="#" id="newcar_proposal_open"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-proposal01.png" alt="個別提案書（PDF）" width="138" height="46"></a></li>
<li><a href="#" id="newcar_display" target="_blank"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-sheet01.png" alt="ディスプレイ用シート（PDF）" width="138" height="46"></a></li>
<li><a href="#" id="newcar_compare" target="_blank"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-comparing01.png" alt="比較見積り作成" width="138" height="46"></a></li>
</ul>
<p class="btn-back01"><a href="#" id="newcar_result_close"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-back01.png" alt="戻る" width="124" height="32"></a></p>
<!-- /.blk-versatile01 --></div>
<!-- /#newcar_result --></div>
<!-- ////////////////////新車計算結果ここまで//////////////////// -->

<!-- ////////////////////新車個別提案書（PDF）ここから//////////////////// -->
<div id="newcar_proposal">
<div class="blk-versatile02">
<p class="title-type01">個別提案書PDF出力</p>
<p><h4>お客様氏名</h4></p>
<p><input type="text" id="user_name" maxlength="10" style="width:150px;" />&nbsp;様</p>
<p><h4>コメント</h4></p>
<p><textarea class="textarea-type01" maxlength="250" id="newcar_comment"></textarea></p>
<p class="align-type03">250文字以内</p>
<ul class="list-image01">
<li style="display:none;"><a href="#" id="newcar_leaflet" target="_blank"></a></li>
<li><center><a href="#" id="newcar_leaflet2" target="_blank" onClick="newcar_leflet_comment()"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-pdf-output-leaflet.png" alt="PDF出力" width="300" height="50"></a></center></li>
</ul>
<p class="btn-back01"><a href="#" id="newcar_proposal_close"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-back01.png" alt="戻る" width="124" height="32"></a></p>
<!-- /.blk-versatile02 --></div>
<!-- /#newcar_proposal --></div>
<!-- ////////////////////新車個別提案書（PDF）ここまで//////////////////// -->


<!-- ////////////////////見積書（PDF）ここから//////////////////// -->
<div id="newcar_estimate">
<div class="blk-versatile02">
<p class="title-type01">見積書PDF出力</p>
<br /><br />
<p><h4>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;お客様氏名</h4></p>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" id="estimate_user_name" maxlength="20" style="width:280px;" />&nbsp;様</p>
<p style="display:none;">コメント</p>
<p style="display:none;"><textarea class="textarea-type01" maxlength="250" id="newcar_comment"></textarea></p>
<p class="align-type03" style="display:none;">250文字以内</p>
<ul class="list-image01">
<li style="display:none;"><a href="#" id="newcar_pdf" target="_blank"></a></li>
<br /><br /><br />
<li><center><a href="#" id="newcar_pdf2" target="_blank" onClick="newcar_estimate()"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-pdf-output-estimate.png" alt="PDF出力" width="300" height="50"></a></center></li>
</ul>
<p class="btn-back01"><a href="#" id="newcar_estimate_close"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-back01.png" alt="戻る" width="124" height="32"></a></p>
<!-- /.blk-versatile02 --></div>
<!-- /#newcar_proposal --></div>
<!-- ////////////////////見積書（PDF）ここまで//////////////////// -->


<!-- /.main-contents01 --></div>
<!-- /.contents --></div>
<!-- /#newcar --></div>


<!-- ==================================================================================================================================================== -->
<!-- 中古車 -->
<!-- ==================================================================================================================================================== -->
<div id="usedcar">
<div class="header01">
<div class="header-lay">
<p class="site-id"><img src="<?= CAKEPHP_URL ?>/img/header/site-id.png" alt="Mercedes-Benz -Finance Calculation-" width="364" height="50"></p>
<ul class="nav-car01">
<li><a href="#" class="new-tab"><img src="<?= CAKEPHP_URL ?>/img/header/nav-newcar_o.png" alt="新車" width="156" height="36"></a></li>
<li><img src="<?= CAKEPHP_URL ?>/img/header/nav-usedcar_a.png" alt="中古車" width="151" height="36"></li>
<li><a href="#" class="serviceloan-tab"><img src="<?= CAKEPHP_URL ?>/img/header/nav-serviceloan_n.png" alt="サービスローン" width="156" height="36"></a></li>
</ul>
<!-- /.header01-lay --></div>
<!-- /.header01 --></div>

<div class="contents">
<div class="main-contents01">
<!-- 中古車条件入力ここから -->
<div id="usedcar_input">
<div class="info-input01">
<table border="1" class="tbl-type02">
<col style="width:166px;">
<col style="width:365px;">
<col style="width:125px;">
<col style="width:324px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-class.png" alt="クラス" width="44" height="22"></th>
<td>
<div class="select-custom01 width03">
<div class="inner"><span>&nbsp;</span></div>

<select name="u_classname" id="u_classname">
	<option value="">▼選択してください</option>
<?php foreach($u_carArrs as $key=>$carArr): ?>
	<option value="<?= $carArr['Car']['bmst'] ?>"><?= $carArr['Car']['qc_classname'] ?></option>
<?php endforeach; ?>
</select>
<!-- /.select-custom01 --></div>
</td>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-first-years.png" alt="車両登録年月" width="86" height="22"></th>
<td class="valign-type02">
<div class="select-custom01 width02 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="usedcar_year" id="usedcar_year">
	<?php for($year=date("Y");$year<=date("Y")+1;$year++): ?>
	<option value="<?= $year ?>" <?php if($year==date("Y")) echo 'selected'; ?>><?= $year ?>(平成<?= $year-1988 ?>)年</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-year01.png" alt="年" width="26" height="22" class="side-left">
<div class="select-custom01 width00 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="usedcar_month" id="usedcar_month">
	<?php for($month=1;$month<=12;$month++): ?>
	<option value="<?= $month ?>" <?php if($month==date("m")) echo 'selected'; ?>><?= $month ?>月</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-month01.png" alt="月" width="26" height="22" class="side-left"><span id="usedcar_zei">【消費税率 8％】</span>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-model.png" alt="モデル" width="46" height="22"></th>
<td><input type="text" name="u_carname" id="u_carname" class="input-type05" maxlength="45"></td>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-year-month.png" alt="初度登録年月" width="86" height="22"></th>
<td>
<div class="select-custom01 width02 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="tourokuyear" id="tourokuyear">
<?php for($year = date("Y")-7;$year<=date("Y");$year++): ?>
<?php
		// 注意！！　登録年の1文字目を半角スペースにしないと、getYearDiffでエラーになる（クイックチャートとのからみだと思う）
?>
	<option value=" <?= $year ?>"><?= $year ?>(平成<?= $year-1988 ?>)年</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-year01.png" alt="年" width="26" height="22" class="side-left">
<div class="select-custom01 width00 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="tourokumanth" id="tourokumonth">
	<?php for($month=1;$month<=12;$month++): ?>
	<option value="<?= $month ?>" <?php if($month==date("m")) echo 'selected'; ?>><?= $month ?>月</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-month01.png" alt="月" width="26" height="22" class="side-left">
</td>
</tr>
</table>
<!-- /.info-input01 --></div>

<div class="info-input02">
<div class="cont01">
<table border="1" class="tbl-type02">
<col style="width:166px;">
<col style="width:319px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-car-price.png" alt="車両本体価格（税込）" width="124" height="22"></th>
<td><input type="tel" name="u_pricetax" id="u_pricetax" value="1,000,000" class="input-type04" maxlength="10" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-maker-option.png" alt="メーカーオプション（取得税課税）" width="120" height="24"></th>
<td><input type="tel" name="u_makeroption" id="u_makeroption" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><br>
<span class="caption02" id="u_maker_option_comment"></span></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-store-option.png" alt="販売店オプション（取得税非課税）" width="110" height="24"></th>
<td><input type="tel" name="u_dealeroption" id="u_dealeroption" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><!--<img src="<?= CAKEPHP_URL ?>/img/contents/term-discount.png" alt="値引き" width="16" height="22">--></th>
<td><input type="tel" name="u_discount" id="u_discount" class="input-type04" maxlength="9" value="" style="ime-mode: disabled;"><!--<img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22">--></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-price-subtotal.png" alt="現金価格小計" width="88" height="22"></th>
<td><input type="text" name="u_cartotal" id="u_cartotal" class="input-type04" maxlength="10"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-tax.png" alt="税金／販売諸費用" width="116" height="22"></th>
<td><input type="tel" name="u_sonota" id="u_sonota" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-car-premium.png" alt="自動車任意保険料" width="114" height="22"></th>
<td><input type="tel" name="u_mbinsureance" id="u_mbinsureance" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<!--
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-maintenance.png" alt="メンテナンスプラス" width="118" height="22"></th>
<td><input type="text" name="u_mmmprice" id="u_mmmprice" value="0" readonly class="input-type04"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-pledge.png" alt="保証プラス" width="70" height="22"></th>
<td><input type="text" name="u_mmsprice" id="u_mmsprice" value="0" readonly class="input-type04"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
-->
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-pay-total.png" alt="支払金額合計" width="88" height="22"></th>
<td><input type="text" readonly value="10,000,000" class="input-type04" id="u_totalpayment"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
</tbody>
</table>
<!-- /.cont01 --></div>
<div class="cont02">
<table border="1" class="tbl-type02">
<col style="width:171px;">
<col style="width:86px;">
<col style="width:131px;">
<col style="width:107px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-loan-kind.png" alt="ローン種別" width="80" height="22"></th>
<td colspan="3">
<div class="select-custom01 width03">
<div class="inner"><span>&nbsp;</span></div>
<select name="u_plan" id="u_plan">
</select>
<!-- /.select-custom01 --></div>
</td>
</tr>
<tr class="u_inspection">
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-n_inspection_y.png" alt="次回車検到来月" width="100" height="22"></th>
<td>
<input type="hidden" name="usedwp" id="usedwp" value="<?= $usedwp ?>" />
<div class="select-custom04">
<select name="n_inspection_y" id="n_inspection_y">
</select>
</div>
</td>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-n_inspection_m.png" alt="次回車検到来月まで" width="140" height="22"></th>
<td><input type="text" name="n_inspection_m" id="n_inspection_m" readonly maxlength="5" /></td>
</tr>

<tr class="u_inspection">
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-nn_inspection_y.png" alt="次々回車検到来月" width="110" height="22"></th>
<td><input type="text" name="nn_inspection_y" id="nn_inspection_y" readonly /></td>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-nn_inspection_m.png" alt="次々回車検到来月まで" width="140" height="22"></th>
<td><input type="text" name="nn_inspection_m" id="nn_inspection_m" readonly  readonly maxlength="5" /></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-pay-num.png" alt="お支払い回数" width="86" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="u_installments" id="u_installments"></select>
<!-- /.select-custom01 --></div>
</td>
<th style="padding-right:9px;"><img src="<?= CAKEPHP_URL ?>/img/contents/term-interest-rate.png" alt="金利" width="34" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="u_rate" id="u_rate"><option>2.9%</option><option>3.5%</option></select>
<!-- /.select-custom01 --></div>
</td>
</tr>

<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-cash-pay.png" alt="現金支払い額" width="90" height="22"></th>
<td colspan="3"><input type="tel" name="u_genkin" id="u_genkin" class="input-type04" maxlength="10" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-trade-in-price.png" alt="下取車価格" width="75" height="22"></th></th>
<td colspan="3"><input type="tel" name="u_shitadori" id="u_shitadori" class="input-type04" maxlength="10" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<!--
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-trade-debt.png" alt="下取り車残債額" width="100" height="22"></th>
<td colspan="3"><input type="tel" name="u_zansai" id="u_zansai" class="input-type04"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
-->
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-loan-capital.png" alt="ローン元金" width="84" height="22"></th>
<td colspan="3"><input type="text" name="u_loanprincipal" id="u_loanprincipal" class="input-type04"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-residual-value.png" alt="残価" width="34" height="22"></th>
<td colspan="3"><input type="tel" name="u_lastpayment" id="u_lastpayment" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><br><span class="caption01 text-type02" id="u_lptmaxmin"></span></td>
</tr>
</tbody>
</table>
<div class="info-input03">
<table border="1" class="tbl-type02">
<col style="width:151px;">
<col style="width:319px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-added.png" alt="ボーナス月加算" width="96" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="u_bonus" value="0">有</label></li>
<li><label><input type="radio" name="u_bonus" value="1" checked>無</label></li>
</ul>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-price.png" alt="ボーナス月加算金額" width="124" height="22"></th>
<td><input type="tel" name="u_bonuspayment" id="u_bonuspayment" class="input-type02" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-add-limit.png" alt="加算金額上限" width="88" height="22"></th>
<td><span class="input-text01" id="u_bptmax"></span><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><a href="#" id="u_getbptmax" class="btn-calc01 space-left02">上限計算</a></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-sum.png" alt="ボーナス月設定（夏）" width="126" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="u_bonusmonth1" value="6" checked>6月</label></li>
<li><label><input type="radio" name="u_bonusmonth1" value="7">7月</label></li>
<li><label><input type="radio" name="u_bonusmonth1" value="8">8月</label></li>
</ul>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-win.png" alt="（冬）" width="32" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="u_bonusmonth2" value="12" checked>12月</label></li>
<li><label><input type="radio" name="u_bonusmonth2" value="1">1月</label></li>
</ul>
</td>
</tr>
</tbody>
</table>
<!-- /.info-input03 --></div>
<!-- /.cont02 --></div>
<!-- /.info-input02 --></div>
<p class="pra-calc01"><a href="#" id="usedcar_calc"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-calc01.png" alt="計算" width="300" height="50"></a></p>

<!-- /#usedcar_input --></div>
<!-- 中古車条件入力ここまで -->

<!-- ////////////////////中古車計算結果ここから//////////////////// -->
<div id="usedcar_result">
<div class="blk-versatile01">
<p class="title-model01"><!--<span id="usedcar_result_classname"></span><br>--><span id="usedcar_result_carname"></span></p>
<div class="tbl-result01">
<table border="1">
<tbody>
<tr>
<th>車両本体価格<span class="sup">（消費税込み）</span></th>
<td id="usedcar_result_pricetax"></td>
</tr>
<tr>
<th>支払金額合計</th>
<td id="usedcar_result_totalpayment"></td>
</tr>
<tr>
<th>現金</th>
<td id="usedcar_result_genkin"></td>
</tr>
<tr>
<th>下取車価格</th>
<td id="usedcar_result_shitadori"></td>
</tr>
<!--
<tr>
<th>下取車残債額</th>
<td id="usedcar_result_zansai"></td>
</tr>
-->
<tr>
<th>ローン元金</th>
<td id="usedcar_result_loanprincipal"></td>
</tr>
<tr>
<th>プラン名</th>
<td id="usedcar_result_plan"></td>
</tr>
<tr>
<th>お支払い回数</th>
<td id="usedcar_result_installments"></td>
</tr>
<tr>
<th>実質年率</th>
<td id="usedcar_result_rate">%</td>
</tr>
<tr>
<th>月々お支払い金額</th>
<td id="usedcar_result_monthlypayment"></td>
</tr>
<tr>
<th>ボーナス月加算金額</th>
<td id="usedcar_result_bonuspayment"></td>
</tr>
<tr id="tr_u_lastpayment">
<th>残価</th>
<td id="usedcar_result_lastpayment"></td>
</tr>
<tr>
<th>分割払いお支払い総額</th>
<td id="usedcar_result_loantotal"></td>
</tr>
<tr>
<th>お支払い総額</th>
<td id="usedcar_result_alltotalpayment"></td>
</tr>
</tbody>
</table>
<!-- /.tbl-result01 --></div>
<ul class="list-result-btn01">
<li><a href="#" target="_blank" id="u_pdf"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-estimate01.png" alt="見積書（PDF）" width="138" height="46"></a></li>
<li><a href="#" target="_blank" id="u_display"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-sheet01.png" alt="ディスプレイ用シート（PDF）" width="138" height="46"></a></li>
<!--<li><a href="<?= CAKEPHP_URL ?>/usedcar_comparing.html" target="_blank"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-comparing01.png" alt="比較見積り作成" width="138" height="46"></a></li>-->
</ul>
<p class="btn-back01"><a href="#" id="usedcar_result_close"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-back01.png" alt="戻る" width="124" height="32"></a></p>
<!-- /.blk-versatile01 --></div>
<!-- /#usedcar_result --></div>
<!-- ////////////////////中古車計算結果ここまで//////////////////// -->

<!-- /.main-contents01 --></div>
<!-- /.contents --></div>
<!-- /#usedcar --></div>

<!-- ==================================================================================================================================================== -->
<!-- サービスローン -->
<!-- ==================================================================================================================================================== -->
<div id="serviceloan">
<div class="header01">
<div class="header-lay">
<p class="site-id"><img src="<?= CAKEPHP_URL ?>/img/header/site-id.png" alt="Mercedes-Benz -Finance Calculation-" width="364" height="50"></p>
<ul class="nav-car01">
<li><a href="#" class="new-tab"><img src="<?= CAKEPHP_URL ?>/img/header/nav-newcar_n.png" alt="新車" width="156" height="36"></a></li>
<li><a href="#" class="used-tab"><img src="<?= CAKEPHP_URL ?>/img/header/nav-usedcar_n.png" alt="中古車" width="151" height="36"></a></li>
<li><img src="<?= CAKEPHP_URL ?>/img/header/nav-serviceloan_a.png" alt="サービスローン" width="156" height="36"></li>
</ul>
<!-- /.header01-lay --></div>
<!-- /.header01 --></div>

<div class="contents">
<div class="main-contents01">
<!-- サービスローン入力ここから -->
<div id="serviceloan_input">
<div class="info-input04">
<div class="cont01">
<table border="1" class="tbl-type02">
<col style="width:166px;">
<col style="width:319px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-vi-cost.png" alt="車検費用" width="60" height="22"></th>
<td><input type="tel" name="s_vicost" id="s_vicost" value="0" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-cost-repairs.png" alt="修理代" width="45" height="22"></th>
<td><input type="tel" name="s_repair" id="s_repair" value="0" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-mainte-check.png" alt="整備・点検費用" width="95" height="22"></th>
<td><input type="tel" name="s_maintenance" id="s_maintenance" value="0" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-option.png" alt="オプション代金" width="95" height="22"></th>
<td><input type="tel" name="s_option" id="s_option" value="0" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-other.png" alt="その他" width="45" height="22"></th>
<td><input type="tel" name="s_sonota" id="s_sonota" value="0" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><!--<img src="<?= CAKEPHP_URL ?>/img/contents/term-discount.png" alt="値引き" width="16" height="22">--></th>
<td><input type="tel" name="s_discount" id="s_discount" value="" class="input-type04" maxlength="9" style="ime-mode: disabled;"><!--<img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22">--></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-maintenance.png" alt="メンテナンスプラス" width="118" height="22"></th>
<td><input type="tel" name="s_mmmprice" id="s_mmmprice" value="0" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-pledge.png" alt="保証プラス" width="70" height="22"></th>
<td><input type="tel" name="s_mmsprice" id="s_mmsprice" value="0" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-pay-total.png" alt="支払金額合計" width="88" height="22"></th>
<td><input type="text" name="s_totalpayment" id="s_totalpayment" value="0" readonly class="input-type04"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-dp.png" alt="頭金" width="35" height="22"></th>
<td><input type="tel" name="s_genkin" id="s_genkin" value="0" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-loan-capital.png" alt="ローン元金" width="84" height="22"></th>
<td><input type="text" name="s_loanprincipal" id="s_loanprincipal" value="0" readonly class="input-type04"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-limit-lower.png" alt="下限" width="34" height="22"></th>
<td><span class="text-input01" id="s_lptmin">30,000</span><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
</tbody>
</table>
<!-- /.cont01 --></div>
<div class="cont02">
<table border="1" class="tbl-type02 space-btm04">
<col style="width:171px;">
<col style="width:86px;">
<col style="width:131px;">
<col style="width:107px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-pay-num.png" alt="お支払い回数" width="86" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="s_installments" id="s_installments">
<?php foreach(array(3,6,10,12,15,18,24,30,36) as $installments): ?>
<option value="<?= $installments ?>" <?php if($installments==24) echo "selected" ?>><?= $installments ?>回</option>
<?php endforeach; ?>
</select>
<!-- /.select-custom01 --></div>
</td>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-interest-rate.png" alt="実質年率" width="34" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="s_rate" id="s_rate">
<option value="6.99">6.99%</option>
<option value="6.49">6.49%</option>
</select>
<!-- /.select-custom01 --></div>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-fy.png" alt="お支払い回数" width="60" height="22"></th>
<td colspan="3">
<div class="select-custom01 width02 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="s_year" id="s_year">
	<?php for($year=date("Y");$year<=date("Y")+5;$year++): ?>
	<option value="<?= $year ?>" <?php if($year==date('Y', strtotime('+1 month'))) echo 'selected'; ?>><?= $year ?>(平成<?= $year-1988 ?>)年</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-year01.png" alt="年" width="26" height="22" class="side-left">
<div class="select-custom01 width00 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="s_month" id="s_month">
	<?php for($month=1;$month<=12;$month++): ?>
	<option value="<?= $month ?>" <?php if($month==date('m', strtotime('+1 month'))) echo 'selected'; ?>><?= $month ?>月</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-month01.png" alt="月" width="26" height="22" class="side-left"></td>
</tr>
</tbody>
</table>
<div class="info-input03">
<table border="1" class="tbl-type02">
<col style="width:151px;">
<col style="width:319px;">
<tbody>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-added.png" alt="ボーナス月加算" width="96" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="s_bonus" value="0">有</label></li>
<li><label><input type="radio" name="s_bonus" value="1" checked="checked">無</label></li>
</ul>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-price.png" alt="ボーナス月加算金額" width="124" height="22"></th>
<td><input type="tel" name="s_bonuspayment" id="s_bonuspayment" class="input-type02"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-add-limit.png" alt="加算金額上限" width="88" height="22"></th>
<td><span class="input-text01" id="s_bptmax"></span><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><a href="#" id="s_getbptmax" class="btn-calc01 space-left02">上限計算</a></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-sum.png" alt="ボーナス月設定（夏）" width="126" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="s_bonusmonth1" id="" value="6" checked="checked">6月</label></li>
<li><label><input type="radio" name="s_bonusmonth1" id="" value="7">7月</label></li>
<li><label><input type="radio" name="s_bonusmonth1" id="" value="8">8月</label></li>
</ul>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-win.png" alt="（冬）" width="32" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="s_bonusmonth2" id="" value="12" checked="checked">12月</label></li>
<li><label><input type="radio" name="s_bonusmonth2" id="" value="1">1月</label></li>
</ul>
</td>
</tr>
</tbody>
</table>
<!-- /.info-input03 --></div>
<!-- /.cont02 --></div>
<!-- /.info-input02 --></div>
<p class="pra-calc01"><a href="#" id="serviceloan_calc"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-calc01.png" alt="計算" width="300" height="50"></a></p>

<!-- /#serviceloan_input --></div>
<!-- サービスローン入力ここまで -->

<!-- ////////////////////サービスローン結果ここから//////////////////// -->
<div id="serviceloan_result">
<div class="blk-versatile01">
<p class="title-model01">サービスローン</p>
<div class="tbl-result01">
<table border="1">
<tbody>
<tr>
<th>支払金額合計</th>
<td id="service_result_totalpayment"></td>
</tr>
<tr>
<th>現金</th>
<td id="service_result_genkin"></td>
</tr>
<tr>
<th>ローン元金</th>
<td id="service_result_loanprincipal"></td>
</tr>
<tr>
<th>プラン名</th>
<td id="service_result_plan">サービスローン</td>
</tr>
<tr>
<th>お支払い回数</th>
<td id="service_result_installments"></td>
</tr>
<tr>
<th>実質年率</th>
<td id="service_result_rate">%</td>
</tr>
<tr>
<th>月々お支払い金額</th>
<td id="service_result_monthlypayment"></td>
</tr>
<tr>
<th>ボーナス月加算金額</th>
<td id="service_result_bonuspayment"></td>
</tr>
<tr>
<th>分割払いお支払い総額</th>
<td id="service_result_loantotal"></td>
</tr>
<tr>
<th>お支払い総額</th>
<td id="service_result_alltotalpayment"></td>
</tr>
</tbody>
</table>
<!-- /.tbl-result01 --></div>
<ul class="list-result-btn01">
<li><a href="#" target="_blank" id="s_pdf"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-estimate01.png" alt="見積書（PDF）" width="138" height="46"></a></li>
<!--
<li><a href="#" target="_blank"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-sheet01.png" alt="ディスプレイ用シート（PDF）" width="138" height="46"></a></li>
<li><a href="/loan/usedcar_comparing.html" target="_blank"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-comparing01.png" alt="比較見積り作成" width="138" height="46"></a></li>

-->
</ul>
<p class="btn-back01"><a href="#" id="serviceloan_result_close"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-back01.png" alt="戻る" width="124" height="32"></a></p>
<!-- /.blk-versatile01 --></div>
<!-- /#serviceloan_result --></div>
<!-- ////////////////////サービスローン結果ここまで//////////////////// -->

<!-- /.main-contents01 --></div>
<!-- /.contents --></div>
<!-- /#newcar --></div>

<input type="hidden" name="salesman" id="salesman" value="<?= $salesman ?>" />
<div class="footer01">
<p class="version"></p>
<p class="copyright">&copy;Mercedes-Benz Finance Co., Ltd. All rights reserved.</p>
<!-- /.footer01 --></div>

<script>
	// 昔のバージョンをブックマークしている場合は、強制リロード
	var mytime = String(g_param.tm);
	
	var year = mytime.substr(0,4);
	var month = mytime.substr(4,2);
	var day = mytime.substr(6,2);
	
	var hiduke=new Date(); 
	
	//年・月・日・曜日を取得する
	var year2 = hiduke.getFullYear();
	var month2 = hiduke.getMonth()+1;
	var day2 = hiduke.getDate();
	
	// URL の日付が異なっていた場合は強制ログアウト
	if(year*1 != year2*1 || month*1 != month2*1 || day*1 != day2*1){
		 //alert("旧バージョンのURLが指定されました。最新版をリロードします");
		forcelogout();
		var url = "<?= CAKEPHP_URL ?>/?version="+mytime
		
		//alert(url);
		
		//location.href= url;
	}

	uiInit();
	logicInit(function(){
		// コールバック関数指定しないと、jsエラーが出るので、空っぽ関数を指定して回避
		// これ以外の方法は現在は不明。
	});
</script>

</body>
</html>
