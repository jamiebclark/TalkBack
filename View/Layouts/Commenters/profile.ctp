<?php $this->extend('default'); ?>
<h2><?php echo $commenter['Commenter'][Configure::read('TalkBack.Commenter.displayField')]; ?></h2>
<?php echo $this->Layout->tabMenu(array(
	array('Profile', array('action' => 'view', $commenter['Commenter']['id'])),
	array('Topics', array('action' => 'topics', $commenter['Commenter']['id'])),
	array('Replies', array('action' => 'comments', $commenter['Commenter']['id'])),
));
?>
<?php echo $this->fetch('content'); ?>