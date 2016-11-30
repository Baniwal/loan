<div class="inner_pages_contaner">
<div id="container">
<div class="signup_form_inner">
<div style="color:chartreuse;text-align:center">
<?php 
if(isset($error))
	echo $error;
	
if(isset($pwd_msg))
	echo $pwd_msg;
	
$ur = $this->params['url']['ur'];
?>
</div><br>
<?php echo $form->create('User',array('action'=>'verify?action=updatepassword&ur='.$ur)); ?> 

 <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
    <input type="password" class="form-control" id="password" name="data[User][password]" placeholder="Password" required>
  </div>
  
 <div class="form-group">
	 <label for="exampleInputPassword1"></label>
	 <button type="submit" class="btn btn-default">Submit</button>
  </div>
</form>
</div>
</div>
</div>