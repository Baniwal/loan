<h3>Add Users</h3>


<?php

echo $form->create('User',array('action'=>'add'));
echo $form->input('username');
echo $form->input('password');
echo $form->end('Add');

?>