<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css('custom.css');
	?>
</head>
<body>
<div class="wapper">
			<div id="header">
			<div id="container">
			<div class="logo"><img src="<?php echo CAKEPHP_URL ?>/img/header/site-id.png" alt=""></div>
			<a class="header-right" href="<?php echo CAKEPHP_URL.'/users/login'?>">login</a>
			<a class="header-right" href="<?php echo CAKEPHP_URL.'/users/singup'?>">Sign Up</a>
			</div>
		</div>
		<div id="content">
			<?php echo $this->Session->flash(); ?>
			<?php echo $content_for_layout; ?>

		</div>
		<div id="footer">
		<div id="container">
			©Mercedes-Benz Finance Co., Ltd. All rights reserved.

			</div>
		</div>
	</div>
	</div>
</body>
</html>