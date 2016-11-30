<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<title>ログイン｜ローン見積り提案システム</title>
<link rel="stylesheet" href="/loan/css/loan.css">
</head>

<body>
<div id="loginform">
<div class="header01">
<div class="header-lay">
<p class="site-id"><img src="<?= CAKEPHP_URL ?>/img/header/site-id.png" alt="Mercedes-Benz -Finance Calculation-" width="364" height="50"></p>
<!-- /.header01-lay --></div>
<!-- /.header01 --></div>

<div class="contents">
<div class="main-contents01">
<div class="blk-login01">
<form action="<?= CAKEPHP_URL ?>/users/login" method="post">
<input type="hidden" name="_method" value="POST">
<ul class="login-input">
<li><img src="<?= CAKEPHP_URL ?>/img/login/term-loginid.png" alt="ユーザー名" width="80" height="20" class="term"><input name="data[User][username]" type="tel" id="username" maxlength="50" value="" style="ime-mode: disabled;"></li>
<li><img src="<?= CAKEPHP_URL ?>/img/login/term-pw.png" alt="パスワード" width="80" height="20" class="term"><input type="password" name="data[User][password]" id="password" value=""></li>
</ul>
<p class="submit"><input type="image" src="<?= CAKEPHP_URL ?>/img/login/btn-login.png" alt="ログイン"></p>
</form>
<p class="download"><a href="http://www.zbass.jp/regform2016.xls" target="_blank">ファイナンス・シミュレーションツール ユーザー登録用紙（Excel）の<br />ダウンロードはこちら</a></p>
<!-- /.blk-login01 --></div>

<!-- /.main-contents01 --></div>
<!-- /.contents --></div>

<div class="footer01">
<p class="version"></p>
<p class="copyright">&copy;Mercedes-Benz Finance Co., Ltd. All rights reserved.</p>
<!-- /.footer01 --></div>
<!-- /#loginform --></div>
</html>

