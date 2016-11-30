<div class="inner_pages_contaner">
<div id="container">
<div class="signup_form_inner">
<div style="color:chartreuse;text-align:center">
    <?php echo $msg;?>
</div>
<?php echo $form->create('User',array('action'=>'singup')); ?>
<?php 


function Createusername() 
{ 
    $s = rand('1000','10000');
    $guidText = substr($s,0,4);
    //$guidText = time(); 
    return $guidText;
}			
$username = Createusername();
$date = date('Y-m-d H:i:s');
?>

  <div class="form-group">
    <input id="username" name="data[User][username]" maxlength="50" type="hidden" value="<?php echo $username?>">
    <input name="data[User][access]" type="hidden" value="<?php echo $date?>">
  </div>
  <div class="form-group">
    <label for="exampleInputEmail1">Fullname</label>
	<ul>
		<li><input id="sirname" name="data[User][sirname]" maxlength="50" type="text" placeholder="Sirname" required></li>
		<li><input id="firstname" name="data[User][firstname]" maxlength="50" type="text" placeholder="Firstname" required></li>
	</ul>
  </div>
  
    <div class="form-group">
    	<label for="exampleInputEmail1">Kana</label>
    	<ul>
			<li><input id="sirnamekana" name="data[User][sirnamekana]" maxlength="50" type="text" placeholder="Sirname(kana)" required></li>
			<li><input id="firstnamekana" name="data[User][firstnamekana]" maxlength="50" type="text" placeholder="Firstname(kana)" required></li>
		</ul>
    </div>
    
	<div class="form-group">
    <label for="exampleInputPassword1">Select Area/City</label>
	<div class="dorp_sign" style="position:relative;width:200px;height:25px;border:0;padding:0;margin:0;">
		<select class="form-control" id="dealerarea" name="data[User][dealerarea]" required>
			<option value="">Select Area/City</option>
			<?php 
				foreach($dealerarea as $res)
				{
			?>
			<option value="<?php echo $res['Dealer']['Area'];?>"> <?php echo $res['Dealer']['Area'];?> </option>
			<?php  
				}
			?>
		</select>
	</div>
  </div>
  
	
	<div class="form-group">
		<label for="exampleInputPassword1">Select Dealername</label>
		<div class="dorp_sign" style="position:relative;width:200px;height:25px;border:0;padding:0;margin:0;">
			<select id="dealername" style="position:absolute;top:0px;left:0px;width:200px; height:25px;line-height:20px;margin:0;padding:0;" onchange="document.getElementById('displayValue').value=this.options[this.selectedIndex].text; document.getElementById('idValue').value=this.options[this.selectedIndex].value;" name="data[User][dealername]" required>

			</select>
			<input name="data[User][dealername]" placeholder="Dealername" id="displayValue" style="position:absolute;top:0px;left:0px;width:183px;width:180px\9;#width:180px;border:1px solid #556;" onfocus="this.select()" type="text">
			<input name="idValue" id="idValue" type="hidden">
		</div>
	</div>


    <div class="form-group">
    	<label for="exampleInputEmail1">Email address</label>
    	<input type="email" class="form-control" id="email" name="data[User][email]" placeholder="Email" required>
    </div>
    
    <div class="form-group">
    <label for="exampleInputEmail1">Tel Number</label>
    <input type="tel" class="form-control" id="telnumber" name="data[User][telnumber]" pattern="[0][0-9]{9}" placeholder="Tel Number" required>
  </div>
   
  <div class="form-group">
    <label for="exampleInputPassword1">Fax Number</label>
    <input type="text" class="form-control" id="faxnumber" name="data[User][faxnumber]" placeholder="Fax">
  </div>
  
  
 
    <div class="form-group">
	 <label for="exampleInputPassword1"></label>
  <button type="submit" class="btn btn-default">Submit</button>
  </div>
</form>
</div>
</div>
</div>
<script type="text/javascript" src="<?php echo CAKEPHP_URL ?>/app/webroot/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#dealerarea").change(function()
	{
		//alert("hello");
		$("#loding1").show();
		var dealerarea = $(this).val();
		var dataString = 'dealerarea='+ dealerarea;
		$.ajax
		({
			type: "GET",
			url: "<?php echo CAKEPHP_URL ?>/users/getdealername",
			data: dataString,
			cache: false,
			success: function(html)
			{
				$("#loding1").hide();
				$("#dealername").html(html);
			} 
		});
	});
});
</script>
