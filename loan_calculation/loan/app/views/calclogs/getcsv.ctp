<?php
if($search == "search"){
	$csv->addRow(array_keys($logArrs[0]['Calclog']));
    foreach ($logArrs as $logArr) {
        $csv->addRow($logArr['Calclog']);
    }
    $csv->setFilename("calclogs.csv");
    echo $csv->render(true, $encode, 'utf-8');
}else{
?>
<?php echo $html->css('ui-lightness/jquery-ui-1.8.16.custom.css'); ?>
<?php echo $javascript->link('jquery-1.6.2.min.js', false); ?>
<?php echo $javascript->link('jquery-ui-1.8.16.custom.js', false); ?>
<?php echo $javascript->link('jquery.ui.core.js', false); ?>
<?php echo $javascript->link('jquery.ui.datepicker.js', false); ?>

<script type="text/javascript" charset="utf-8">
jQuery(function($){
	$("#start").datepicker();
	$("#end").datepicker();
});
</script>
<h2>AccessLog CSV Export</h2>
<form name="form" action = "getcsv" method="get">
ログ取得開始日時<br />
Date：<input type="text" name="start" id="start" value="<?= $start ?>">Time:<input type="text" name="starttime" id="starttime" value="<?= $starttime ?>"><br>
ログ取得終了日時<br />
Date：<input type="text" name="end" id="end" value="<?= $end ?>">Time:<input type="text" name="endtime" id="endtime" value="<?= $endtime ?>">
<br />
<fieldset><legend>モード</legend>
<input type="radio" name="salesmanmode" value="1" checked/>セールスマンモード&nbsp;
<input type="radio" name="salesmanmode" value="2" />一般モード&nbsp;
<input type="radio" name="salesmanmode" value="0"/>両方
</fieldset>
<fieldset><legend>プラン</legend>
<input type="checkbox" name="plan[]" value="wp" checked="checked"/>ウェルカムプラン<br />
<input type="checkbox" name="plan[]" value="swp" checked="checked"/>スーパーウェルカムプラン<br />
<input type="checkbox" name="plan[]" value="sup" checked="checked"/>スタートアッププラン<br />
<input type="checkbox" name="plan[]" value="std" checked="checked"/>スタンダードローン<br />
<input type="checkbox" name="plan[]" value="als" checked="checked"/>オートリース（オープンエンド方式）<br />
<input type="checkbox" name="plan[]" value="cls" checked="checked"/>オートリース（クローズエンド方式）<br />
<input type="checkbox" name="plan[]" value="quickchart" checked="checked"/>クイックチャート<br />
</fieldset>

<br />
<br />
<br>
パスワード：<input type="password" name="password">
<input type="hidden" name="search" value="search"><br />
<input type="submit" name="submit" value="CSV Download(SJIS)">
<input type="submit" name="submit" value="CSV Download(UTF8)">
</form>
<?php } ?>

