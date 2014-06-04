<?php $this->extend('default'); 
echo $this->Layout->navBar(array(
	array('Channels', array('controller' => 'channels', 'action' => 'index')),
	array('Forums', array('controller' => 'forums', 'action' => 'index')),
));
 echo $this->fetch('content'); ?>