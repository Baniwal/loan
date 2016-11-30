<?php if(!$login && $plan == "cls") $plan = "als"; ?>
<?php if($num==1): ?>
<p class="title-type02">入力された条件</p>
<p class="title-type03"><?= $plannameArr[$plan] ?></p>
<table border="1" class="tbl-type01">
<thead>
<tr>
<th colspan="2" class="pay">月々のお支払い<strong><span id="tsukiduki<?= $num ?>"></span>円</strong></th>
</tr>
		<tr>
        <th>お支払回数</th><td><span id="times1"></span></td>
        </tr>
        <tr>
		<th>実質年率</th>
		<td><div id="rate<?= $num ?>"></div></td>
        </tr>
        <tr>
		<th>現金</th><td><span id="genkin1"></span></td>
        </tr>
        <tr>
		<th>ボーナス月加算金額</th><td><span id="bonus1"></span></td>
        </tr>
        <tr>
		<?php if($plan=="wp"): ?>
			<th>残価<br/>&nbsp;</th>
			<td><span id="zanka1"></span></td>
		<?php else: ?>
			<th>&nbsp;<br/>&nbsp;</th>
			<td>&nbsp;</td>
		<?php endif; ?>
        </tr>
</tbody>
</table>

	<?php else: ?>
<?php if($num==2): ?>    
<p class="title-type02">条件1：お支払い条件のみ変更</p>
<?php else: ?>
<p class="title-type02">条件2：条件1を元にファイナンスプランを変更</p>
<?php endif; ?>
<p class="space-btm01 align-type03">
<div class="select-custom02">
<div class="inner"><span>&nbsp;</span></div>
<select class='dropdown' name="plan<?= $num ?>" id="plan<?= $num ?>">
<?php if($num == 3): ?>
    <option value="">プラン選択</option>
<?php endif; ?>
<option value="wp"><?= $plannameArr['wp'] ?></option>
<?php if($swpmodel == 1 || ($swpmodel == 2 && $login)): ?>
<option value="swp"><?= $plannameArr['swp'] ?></option>
<?php endif; ?>
<option value="std"><?= $plannameArr['std'] ?></option>
</select>
<!-- /.select-custom02 --></div>
<table border="1" class="tbl-type01">
<thead>
<tr>
<th colspan="2" class="pay">月々のお支払い<strong><span id="tsukiduki<?= $num ?>"></span>円</strong></th>
</tr>
</thead>
<tbody>
<tr>
<th>お支払い回数</th>
<td>
<div class="select-custom03 side-left">
<div class="inner"><span>&nbsp;</span></div>
<select name="times<?= $num ?>" id="times<?= $num ?>">	</select>
<!-- /.select-custom03 --></div>
　</td>
</tr>
<tr>
<th>実質年率</th>
<td>
<div class="select-custom03 side-left">
<div class="inner"><span>&nbsp;</span>
</div>
<select id="kinri<?= $num ?>"></select></div>
</td>
</tr>
<tr>
<th>現金</th>
<td><input class="input-type01"type="tel" size="9" maxlength="10" name="genkin<?= $num ?>" id="genkin<?= $num ?>" value="0" />円</td>
</tr>
<tr>
<th>ボーナス月加算金額</th>
<td><input type="tel" class="input-type01" size="9" maxlength="10" name="bonus<?= $num ?>" id="bonus<?= $num ?>" value="0" />円</td>
</tr>
<tr>
		<?php if($plan=="wp"): ?>
			<th>残価<br/>&nbsp;<span class="zankaminmax<?= $num ?>"></span></th>
			<td><input type="tel" class="input-type01" size="9" maxlength="10" name="zanka<?= $num ?>" id="zanka<?= $num ?>" value="0" />円</td>
		<?php else: ?>
			<th><br/>&nbsp;</th>
			<td>&nbsp;</td>
		<?php endif; ?>
</tr>
</tbody>
</table>
<?php endif; ?>

<table border="1" class="tbl-type01">
<tbody>
<tr>
<th>ローン元金</th>
<td><span id="loanprincipal<?= $num ?>"></span>円</td>
</tr>
<tr>
<th>初回お支払い金額</th>
<td><span id="firstpayment<?= $num ?>">663,822</span>円</td>
</tr>
<tr>
<th>月々お支払い金額</th>
<td><span id="monthlypayment<?= $num ?>"></span>円</td>
</tr>
<tr>
<th>ボーナス月加算金額</th>
<td><span id="bonuspayment<?= $num ?>"></span>円</td>
</tr>
<tr>
<th>残価</th>
<td><span id="lastpayment<?= $num ?>"></span>円</td>
</tr>
<tr>
<th>分割払い手数料</th>
<td><span id="interest<?= $num ?>"></span>円</td>
</tr>
<tr>
<th>分割払いお支払い総額</th>
<td><span id="loantotal<?= $num ?>"></span>円</td>
</tr>
<tr>
<th>お支払い総額</th>
<td><span id="totalpayment<?= $num ?>"></span>円</td>
</tr>
</tbody>
</table>


<?php if($num==1): ?>
<div class="message" id="message<?= $num ?>"></div>
<ul class="list-result-btn01">
<li><a href="#" target="_blank" id="pdf1"><img src="/loan/img/contents/btn-estimate02.png" alt="見積書PDF" width="107" height="46"></a></li>
<li><a href="#" target="_blank" id="leaflet1"><img src="/loan/img/contents/btn-proposal02.png" alt="個別提案書PDF" width="107" height="46"></a></li>
<li></li>
</ul>
<?php else: ?>
<div class="message" id="message<?= $num ?>"></div>
<ul class="list-result-btn01">
<li><a href="#" target="_blank" id="pdf<?= $num ?>"><img id="pdfimage<?= $num ?>" src="/loan/img/contents/btn-estimate02.png" alt="見積書PDF" width="107" height="46"></a></li>
<li><a href="#" target="_blank" id="leaflet<?= $num ?>"><img src="/loan/img/contents/btn-proposal02.png" alt="個別提案書PDF" width="107" height="46"></a></li>
<li><a href="#" id="calc<?= $num ?>"><img src="/loan/img/contents/btn-calc02.png" alt="計算" width="78" height="46"></a></li>
</ul>
<?php endif; ?>
