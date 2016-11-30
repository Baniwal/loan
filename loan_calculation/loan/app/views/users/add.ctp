<h3>Add Users</h3>

<?php echo $form->create('User',array('action'=>'add')); ?>
  <div class="form-group">
    <label for="exampleInputEmail1">username</label>
  <input id="username" name="data[User][username]" maxlength="50" type="text">
  </div>
 
 <div class="form-group">
    <label for="exampleInputEmail1">fullname</label>
  <input id="fullname" name="data[User][fullname]" maxlength="50" type="text">
  </div>
  
   <div class="form-group">
    <label for="kana">kana</label>
    <input id="kana" name="data[User][kana]" maxlength="50" type="text" placeholder="kana">
  </div>
  
   <div class="form-group">
    <label for="dealername">dealername</label>
    <select>

    </select>
     <input id="dealername" name="data[User][dealername]" maxlength="50" type="text" placeholder="dealername">
   
  </div>
  
   <div class="form-group">
    <label for="exampleInputEmail1">email</label>
     <input id="email" name="data[User][email]" maxlength="50" type="text" placeholder="email">
  </div>
   
    <div class="form-group">
    <label for="exampleInputEmail1">telnumber</label>
     <input id="telnumber" name="data[User][telnumber]" maxlength="50" type="text" placeholder="telnumber">
  </div>

  
  <div class="form-group">
    <label for="exampleInputPassword1">Password</label>
     <input id="password" name="data[User][password]" maxlength="50" type="text" placeholder="password">
  </div>
  </div>

  <button type="submit" value="Add" class="btn btn-default">Submit</button>
</form>

