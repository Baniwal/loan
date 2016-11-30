<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="cache-control" content="no-cache">
<title>ローン計算シート</title>
<link rel="stylesheet" href="<?=CAKEPHP_URL ?>/css/loan.css">
<script src="<?= CAKEPHP_URL ?>/js/jquery-1.9.1.js"></script>
<script src="<?= CAKEPHP_URL ?>/js/loan_common.js"></script>
<script src="<?= CAKEPHP_URL ?>/js/loan.js"></script>
<script src="<?= CAKEPHP_URL ?>/js/run.js"></script>
</head>

<body>

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
	<?php for($year=date("Y");$year<=date("Y")+5;$year++): ?>
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
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-month01.png" alt="月" width="26" height="22" class="side-left"><span id="newcar_zei">【消費税率 5％】</span>
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
<td><input type="text" name="makeroption" id="makeroption" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><br>
<span class="caption02" id="maker_option_comment">※税込み金額をご入力ください【消費税率 5％】<br>※サウンドスイートの場合は残価に算入できないためJPOSをご活用ください</span></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-store-option.png" alt="販売店オプション（取得税非課税）" width="110" height="24"></th>
<td><input type="text" name="dealeroption" id="dealeroption" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-discount.png" alt="値引き" width="16" height="22"></th>
<td><input type="text" name="discount" id="discount" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-price-subtotal.png" alt="現金価格小計" width="88" height="22"></th>
<td><input type="text" name="cartotal" id="cartotal" class="input-type04" readonly><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-tax.png" alt="税金／販売諸費用" width="116" height="22"></th>
<td><input type="text" name="sonota" id="sonota" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-car-premium.png" alt="自動車任意保険料" width="114" height="22"></th>
<td><input type="text" name="mbinsureance" id="mbinsureance" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th></th>
<td><span id="m_plan_check"><input name="m_plan" type="radio" value="gs" checked />ガソリン車プラン&nbsp;&nbsp;<input name="m_plan" type="radio" value="ev" />EV車プラン</span></td>
</tr>
<tr>
<th><img id="mmm_mbj"src="<?= CAKEPHP_URL ?>/img/contents/term-maintenance.png" alt="メンテナンスプラス" width="118" height="22"><img id="mmm_smart"src="<?= CAKEPHP_URL ?>/img/contents/term-maintenance_smart.png" alt="smartメンテパック スタートプラン" width="118" height="22"></th>
<td><input type="text" name="mmprice" id="mmmprice" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img id="mms_mbj" src="<?= CAKEPHP_URL ?>/img/contents/term-pledge.png" alt="保証プラス" width="70" height="22"><img id="mms_smart" src="<?= CAKEPHP_URL ?>/img/contents/term-pledge_smart.png" alt="smartメンテパック セカンドプラン" width="118" height="22"></th>
<td><input type="text" name="mmsprice" id="mmsprice" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img id="ev_mbj" src="<?= CAKEPHP_URL ?>/img/contents/term-ev.png" alt="" width="70" height="22"><img id="ev_smart" src="<?= CAKEPHP_URL ?>/img/contents/term-ev_smart.png" alt="smartメンテパック EV専用プラン" width="118" height="22"></th>
<td><input type="text" name="evprice" id="evprice" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-pay-total.png" alt="支払金額合計" width="88" height="22"></th>
<td><input type="text" readonly value="10,000,000" class="input-type04" value="0" id="totalpayment"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
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
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-interest-rate.png" alt="実質年率" width="34" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="rate" id="rate"></select>
<!-- /.select-custom01 --></div>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-down-pay.png" alt="頭金／下取" width="84" height="22"></th>
<td colspan="3"><input type="text" name="downpayment" id="downpayment" class="input-type04" value="0" maxlength="10" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-trade-debt.png" alt="下取り車残債額" width="100" height="22"></th>
<td colspan="3"><input type="text" name="zansai" id="zansai" class="input-type04" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-loan-capital.png" alt="ローン元金" width="84" height="22"></th>
<td colspan="3"><input type="text" name="loanprincipal" id="loanprincipal" readonly value="0" class="input-type04"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-residual-value.png" alt="残価" width="34" height="22" maxlength="9"></th>
<td colspan="3"><input type="text" name="lastpayment" id="lastpayment" class="input-type04" value="0" maxlength="10" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><br><span class="caption01 text-type02" id="lptmaxmin"></td>
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
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-price.png" alt="ボーナス月加算額" width="124" height="22"></th>
<td><input type="text" name="bonuspayment" id="bonuspayment" class="input-type02" value="0" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
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
<li><label><input type="radio" name="bonusmonth2" value="1">1月</label></li>
<li><label><input type="radio" name="bonusmonth2" value="12" checked>12月</label></li>
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
<th id="label_newcar_result_downpayment">頭金／下取</th>
<td id="newcar_result_downpayment"></td>
</tr>
<tr>
<th>下取り車残債額</th>
<td id="newcar_result_zansai"></td>
</tr>
<tr>
<th>追加売買代金</th>
<td id="newcar_result_tsuika"></td>
</tr>
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
<li><a href="#" target="_blank" id="newcar_pdf"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-estimate01.png" alt="見積書（PDF）" width="138" height="46"></a></li>
<li><a href="#" id="newcar_proposal_open"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-proposal01.png" alt="個別提案書（PDF）" width="138" height="46"></a></li>
<li><a href="#" id="newcar_display" target="_blank"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-sheet01.png" alt="ディスプレイ用シート（PDF）" width="138" height="46"></a></li>
<li><a href="<?= CAKEPHP_URL ?>/cars/compare" id="compare" target="_blank"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-comparing01.png" alt="比較見積り作成" width="138" height="46"></a></li>
</ul>
<p class="btn-back01"><a href="#" id="newcar_result_close"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-back01.png" alt="戻る" width="124" height="32"></a></p>
<!-- /.blk-versatile01 --></div>
<!-- /#newcar_result --></div>
<!-- ////////////////////新車計算結果ここまで//////////////////// -->

<!-- ////////////////////新車個別提案書（PDF）ここから//////////////////// -->
<div id="newcar_proposal">
<div class="blk-versatile02">
<p class="title-type01">個別提案書PDF出力</p>
<ul class="list-image01">
<li><a href="#" id="newcar_leaflet" target="_blank"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-pdf-cmnt-no.png" alt="コメントを入力せずにPDF出力" width="546" height="46"></a></li>
<li><a href="#" id="newcar_leaflet2" target="_blank" onClick="newcar_leflet_comment()"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-pdf-cmnt.png" alt="コメントを反映してPDF出力" width="546" height="46"></a></li>
</ul>
<p><textarea cols="30" rows="5" class="textarea-type01" maxlength="250" id="newcar_comment"></textarea></p>
<p class="align-type03">250文字以内</p>
<p class="btn-back01"><a href="#" id="newcar_proposal_close"><img src="<?= CAKEPHP_URL ?>/img/contents/btn-back01.png" alt="戻る" width="124" height="32"></a></p>
<!-- /.blk-versatile02 --></div>
<!-- /#newcar_proposal --></div>
<!-- ////////////////////新車個別提案書（PDF）ここまで//////////////////// -->

<!-- /.main-contents01 --></div>
<!-- /.contents --></div>
<div class="footer01">
<p class="version"></p>
<p class="copyright">&copy;Mercedes-Benz Finance Co., Ltd. All rights reserved.</p>
<!-- /.footer01 --></div>
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
<select name="year" id="usedcar_year">
	<?php for($year=date("Y");$year<=date("Y")+5;$year++): ?>
	<option value="<?= $year ?>" <?php if($year==date("Y")) echo 'selected'; ?>><?= $year ?>(平成<?= $year-1988 ?>)年</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-year01.png" alt="年" width="26" height="22" class="side-left">
<div class="select-custom01 width00 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="month" id="usedcar_month">
	<?php for($month=1;$month<=12;$month++): ?>
	<option value="<?= $month ?>" <?php if($month==date("m")) echo 'selected'; ?>><?= $month ?>月</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-month01.png" alt="月" width="26" height="22" class="side-left"><span id="usedcar_zei">【消費税率 5％】</span>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-model.png" alt="モデル" width="46" height="22"></th>
<td><input type="text" name="u_carname" id="u_carname" class="input-type05"></td>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-year-model.png" alt="年式" width="34" height="22"></th>
<td>
<div class="select-custom01 width02 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="tourokuyear" id="tourokuyear">
<option value="">▼選択</option>
<?php for($year = date("Y")-7;$year<date("Y");$year++): ?>
<?php
		// 注意！！　登録年の1文字目を半角スペースにしないと、getYearDiffでエラーになる（クイックチャートとのからみだと思う）
?>
	<option value=" <?= $year ?>"><?= $year ?>(平成<?= $year-1988 ?>)年</option>
	<?php endfor; ?>
</select>
<!-- /.select-custom01 --></div>
<img src="<?= CAKEPHP_URL ?>/img/contents/txt-year01.png" alt="年" width="26" height="22"></td>
</tr>
</tbody>
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
<td><input type="text" name="u_pricetax" id="u_pricetax" value="1,000,000" class="input-type04" maxlength="10" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-maker-option.png" alt="メーカーオプション（取得税課税）" width="120" height="24"></th>
<td><input type="text" name="u_makeroption" id="u_makeroption" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><br>
<span class="caption02" id="u_maker_option_comment"></span></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-store-option.png" alt="販売店オプション（取得税非課税）" width="110" height="24"></th>
<td><input type="text" name="u_dealeroption" id="u_dealeroption" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-discount.png" alt="値引き" width="16" height="22"></th>
<td><input type="text" name="u_discount" id="u_discount" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-price-subtotal.png" alt="現金価格小計" width="88" height="22"></th>
<td><input type="text" name="u_cartotal" id="u_cartotal" class="input-type04" maxlength="10"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-tax.png" alt="税金／販売諸費用" width="116" height="22"></th>
<td><input type="text" name="u_sonota" id="u_sonota" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-car-premium.png" alt="自動車任意保険料" width="114" height="22"></th>
<td><input type="text" name="u_mbinsureance" id="u_mbinsureance" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
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
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-pay-num.png" alt="お支払い回数" width="86" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="u_installments" id="u_installments"></select>
<!-- /.select-custom01 --></div>
</td>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-interest-rate.png" alt="実質年率" width="34" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="u_rate" id="u_rate"><option>2.9%</option><option>3.5%</option></select>
<!-- /.select-custom01 --></div>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-down-pay.png" alt="頭金／下取" width="84" height="22"></th>
<td colspan="3"><input type="text" name="u_downpayment" id="u_downpayment" class="input-type04" maxlength="10" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<!--
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-trade-debt.png" alt="下取り車残債額" width="100" height="22"></th>
<td colspan="3"><input type="text" name="u_zansai" id="u_zansai" class="input-type04"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
-->
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-loan-capital.png" alt="ローン元金" width="84" height="22"></th>
<td colspan="3"><input type="text" name="u_loanprincipal" id="u_loanprincipal" class="input-type04"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-residual-value.png" alt="残価" width="34" height="22"></th>
<td colspan="3"><input type="text" name="u_lastpayment" id="u_lastpayment" class="input-type04" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"><br><span class="caption01 text-type02" id="u_lptmaxmin"></span></td>
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
<li><label><input type="radio" name="u_bonus" id="u_bonu" value="0">有</label></li>
<li><label><input type="radio" name="u_bonus" id="u_bonu" value="1" checked>無</label></li>
</ul>
</td>
</tr>
<tr>
<th><img src="<?= CAKEPHP_URL ?>/img/contents/term-bonus-price.png" alt="ボーナス月加算額" width="124" height="22"></th>
<td><input type="text" name="u_bonuspayment" id="u_bonuspayment" class="input-type02" maxlength="9" style="ime-mode: disabled;"><img src="<?= CAKEPHP_URL ?>/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
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
<li><label><input type="radio" name="u_bonusmonth2" value="1">1月</label></li>
<li><label><input type="radio" name="u_bonusmonth2" value="12" checked>12月</label></li>
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
<th>頭金／下取</th>
<td id="usedcar_result_downpayment"></td>
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
<th>ボーナス月加算額</th>
<td id="usedcar_result_bonuspayment"></td>
</tr>
<tr>
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

<div class="footer01">
<p class="version"></p>
<p class="copyright">&copy;Mercedes-Benz Finance Co., Ltd. All rights reserved.</p>
<!-- /.footer01 --></div>
<!-- /#usedcar --></div>

<!-- ==================================================================================================================================================== -->
<!-- サービスローン -->
<!-- ==================================================================================================================================================== -->
<div id="serviceloan">
<div class="header01">
<div class="header-lay">
<p class="site-id"><img src="/loan/img/header/site-id.png" alt="Mercedes-Benz -Finance Calculation-" width="364" height="50"></p>
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
<th><img src="/loan/img/contents/term-vi-cost.png" alt="車検費用" width="60" height="22"></th>
<td><input type="text" name="" id="" value="105,000" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-cost-repairs.png" alt="修理代" width="45" height="22"></th>
<td><input type="text" name="" id="" value="105,000" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-mainte-check.png" alt="整備・点検費用" width="95" height="22"></th>
<td><input type="text" name="" id="" value="105,000" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-option.png" alt="オプション代金" width="95" height="22"></th>
<td><input type="text" name="" id="" value="210,000" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-other.png" alt="その他" width="45" height="22"></th>
<td><input type="text" name="" id="" value="100,000" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-discount.png" alt="値引き" width="16" height="22"></th>
<td><input type="text" name="" id="" value="50,000" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-maintenance.png" alt="メンテナンスプラス" width="118" height="22"></th>
<td><input type="text" name="" id="" value="108,000" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-pledge.png" alt="保証プラス" width="70" height="22"></th>
<td><input type="text" name="" id="" value="89,250" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-pay-total.png" alt="支払金額合計" width="88" height="22"></th>
<td><input type="text" name="" id="" value="10,000,000" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-dp.png" alt="頭金" width="35" height="22"></th>
<td><input type="text" name="" id="" value="272,250" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-loan-capital.png" alt="ローン元金" width="84" height="22"></th>
<td><input type="text" name="" id="" value="500,000" class="input-type04"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-limit-lower.png" alt="下限" width="34" height="22"></th>
<td><span class="text-input01">30,000</span><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
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
<th><img src="/loan/img/contents/term-pay-num.png" alt="お支払い回数" width="86" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="u_installments" id="u_installments"><option>1</option><option>2</option><option>3</option><option>4</option><option>5</option></select>
<!-- /.select-custom01 --></div>
</td>
<th><img src="/loan/img/contents/term-interest-rate.png" alt="実質年率" width="34" height="22"></th>
<td>
<div class="select-custom01 width01">
<div class="inner"><span>&nbsp;</span></div>
<select name="" id=""><option>2.9%</option><option>3.5%</option></select>
<!-- /.select-custom01 --></div>
</td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-fy.png" alt="お支払い回数" width="60" height="22"></th>
<td colspan="3">
<div class="select-custom01 width02 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select ame="" id="">
<option value="2013">2013(平成25)</option>
<option value="2014">2014(平成26)</option>
<option value="2015">2015(平成27)</option>
<option value="2016">2016(平成28)</option>
<option value="2017">2017(平成29)</option>
<option value="2018">2018(平成30)</option>
</select>
<!-- /.select-custom01 --></div>
<img src="/loan/img/contents/txt-year01.png" alt="年" width="26" height="22" class="side-left">
<div class="select-custom01 width00 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="" id="">
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
<option value="6">6</option>
<option value="7">7</option>
<option value="8">8</option>
<option value="9">9</option>
<option value="10">10</option>
<option value="11">11</option>
<option value="12">12</option>
</select>
<!-- /.select-custom01 --></div>
<img src="/loan/img/contents/txt-month01.png" alt="月" width="26" height="22" class="side-left"></td>
</tr>
</tbody>
</table>
<div class="info-input03">
<table border="1" class="tbl-type02">
<col style="width:151px;">
<col style="width:319px;">
<tbody>
<tr>
<th><img src="/loan/img/contents/term-bonus-added.png" alt="ボーナス月加算" width="96" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="s_bonus" id="" value="0">有</label></li>
<li><label><input type="radio" name="s_bonus" id="" value="1" checked="checked">無</label></li>
</ul>
</td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-bonus-price.png" alt="ボーナス月加算額" width="124" height="22"></th>
<td><input type="text" name="s_bonuspayment" id="" class="input-type02"><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"></td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-add-limit.png" alt="加算金額上限" width="88" height="22"></th>
<td><span class="input-text01">10,000,000</span><img src="/loan/img/contents/txt-yen01.png" alt="円" width="26" height="22"><a href="#" class="btn-calc01 space-left02">上限計算</a></td>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-bonus-sum.png" alt="ボーナス月設定（夏）" width="126" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="s_bonusmonth1" id="" value="6" checked="checked">6月</label></li>
<li><label><input type="radio" name="s_bonusmonth1" id="" value="7">7月</label></li>
<li><label><input type="radio" name="s_bonusmonth1" id="" value="8">8月</label></li>
</ul>
</td>
</tr>
<tr>
<th><img src="/loan/img/contents/term-bonus-win.png" alt="（冬）" width="32" height="22"></th>
<td>
<ul class="list-radio01">
<li><label><input type="radio" name="s_bonusmonth2" id="" value="1">1月</label></li>
<li><label><input type="radio" name="s_bonusmonth2" id="" value="12" checked="checked">12月</label></li>
</ul>
</td>
</tr>
</tbody>
</table>
<!-- /.info-input03 --></div>
<!-- /.cont02 --></div>
<!-- /.info-input02 --></div>
<p class="pra-calc01"><a href="#" id="serviceloan_calc"><img src="/loan/img/contents/btn-calc01.png" alt="計算" width="300" height="50"></a></p>

<!-- /#serviceloan_input --></div>
<!-- サービスローン入力ここまで -->

<!-- ////////////////////サービスローン結果ここから//////////////////// -->
<div id="serviceloan_result">
<div class="blk-versatile01">
<p class="title-model01">C63 AMG COUPE ブラックシリーズ<br>Performance Studio Edition</p>
<div class="tbl-result01">
<table border="1">
<tbody>
<tr>
<th>車両本体価格<span class="sup">（消費税込み）</span></th>
<td><strong>3,350,000円</strong></td>
</tr>
<tr>
<th>支払金額合計</th>
<td>0円</td>
</tr>
<tr>
<th>頭金／下取り</th>
<td>0円</td>
</tr>
<tr>
<th>下取車残債額</th>
<td>0円</td>
</tr>
<tr>
<th>ローン元金</th>
<td>0円</td>
</tr>
<tr>
<th>プラン名</th>
<td>ウェルカムプラン</td>
</tr>
<tr>
<th>支払い回数</th>
<td>36回</td>
</tr>
<tr>
<th>実質年率</th>
<td>2.9%</td>
</tr>
<tr>
<th>2回目以降お支払い額</th>
<td>0円</td>
</tr>
<tr>
<th>ボーナス加算額</th>
<td>0円</td>
</tr>
<tr>
<th>残価</th>
<td>0円</td>
</tr>
<tr>
<th>ローンお支払い金額</th>
<td>0円</td>
</tr>
<tr>
<th>合計お支払い金額</th>
<td><strong>3,576,725円</strong></td>
</tr>
</tbody>
</table>
<!-- /.tbl-result01 --></div>
<ul class="list-result-btn01">
<li><a href="#" target="_blank"><img src="/loan/img/contents/btn-estimate01.png" alt="見積書（PDF）" width="138" height="46"></a></li>
<li><a href="#" target="_blank"><img src="/loan/img/contents/btn-sheet01.png" alt="ディスプレイ用シート（PDF）" width="138" height="46"></a></li>
<li><a href="/loan/usedcar_comparing.html" target="_blank"><img src="/loan/img/contents/btn-comparing01.png" alt="比較見積り作成" width="138" height="46"></a></li>
</ul>
<p class="btn-back01"><a href="#" id="serviceloan_result_close"><img src="/loan/img/contents/btn-back01.png" alt="戻る" width="124" height="32"></a></p>
<!-- /.blk-versatile01 --></div>
<!-- /#serviceloan_result --></div>
<!-- ////////////////////サービスローン結果ここまで//////////////////// -->

<input type="hidden" name="salesman" id="salesman" value="<?= $salesman ?>" />
<a href="<?= CAKEPHP_URL ?>/users/logout" >ログアウト</a>

<script>
	uiInit();
	logicInit();
</script>

</body>
</html>
