<?php 
$site_path = CAKEPHP_URL;
$message='  
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Welcome to loan</title>
<style type="text/css">
@charset "utf-8";
#footerDiv{
}
.dataDiv{
}
</style>
</head>

<body>

<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
	<td style="padding:40px 0px 40px 0px;background-color:#f4f4f4;">
  <table width="702" border="0" cellpadding="0" cellspacing="0" bgcolor="#58585a" style="height:10px;line-height:10px;font-size:10px;" align="center">
	<tr> 	
  	<td valign="top" align="right" style="height:10px;line-height:10px;font-size:10px;padding:0px 0px 0px 0px;">&nbsp;</td>
	</tr>
	</table>
  
  <table width="702" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="#FFFFFF">
  <tr> 
  	<td width="700" bgcolor="#f4f4f4">
    
      <table width="700" cellpadding="0" cellspacing="0" border="0" align="center" bgcolor="#FFF">
      <tr>
        <td width="100%" align="center" bgcolor="#024b7e" style="padding:0 0px 0 0px;" colspan="3">
        	<a href="'.$site_path.'" target="_blank"><img src="'.$site_path.'/img/header/site-id.png" alt="logo" border="0" height="100" width="50" /></a>
        </td>
      </tr>
      			
      <tr> 
				<td width="1" bgcolor="#dadada"></td> 
				<td width="697" align="center">
				
					<table width="696" cellspacing="0" cellpadding="1" border="0" bgcolor="#FFFFFF">
						<tr> <td> 
						<div class="dataDiv" style="color:#666666;font-family:Lucida Grande,Lucida Sans,Lucida Sans Unicode,Arial,Helvetica,Verdana,sans-serif;font-size:12.5px;line-height:1.75em;padding:0 60px;">
						<br>
							Hello, '.$name.'<br> <br>
							Welcome to loan <br>
							<h1>Your Login information given below</h1>
							<strong>Username</strong> : '.$username.' <br>
							<strong>Email</strong> : '.$email.' <br>
							<strong>TelNumber</strong> : '.$telnumber.' <br>
							Please <a href='.$ur.'>Click here</a> to verify your account/email
														
						<br>
						Sincerely, <br>
						The emonial.com team <br><br>
						</div> 
						</td> </tr>
					</table>
					
				</td>
				<td width="1" bgcolor="#dadada"></td> 
			</tr> 
      </table>
    
    </td>
   </tr>
  </table>
    
  <table width="702" border="0" cellpadding="0" cellspacing="0" bgcolor="#68686a" style="height:10px;line-height:10px;font-size:10px;" align="center">
	<tr> 	
  	<td align="center" valign="top" style="height:10px;line-height:10px;font-size:10px;padding:0px 0 0px 0;">
    	<div id="footerDiv" style="font-family:Lucida Grande,Arial,Helvetica,Geneva,Verdana,sans-serif;color:#FFFFFF;font-size:12px;line-height:1em;text-align:center;padding:8px 10px 12px 10px;">
			Copyright &copy; <a href="'.$site_path.'" style="text-decoration:none; color:#fff;">The loan.com</a> '.date('Y').' All rights reserved. </div>
    </td>
	</tr>
	</table>  
    
  </td>
</tr>
</table>

</body>
</html>';

?>