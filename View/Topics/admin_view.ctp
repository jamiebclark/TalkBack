<?php 
echo $this->Layout->defaultHeader($topic['Topic']['id'], array(
	array('Public View', array('action' => 'view', $topic['Topic']['id'], 'admin' => false))
)); 
?>
<h2><?php echo $this->Html->tag('h2', $topic['Topic']['title']); ?></h2>
<?php echo $this->DisplayText->text($topic['Topic']['body'], array('class' => 'lead')); ?>

<h3>Replies</h3>
<?php
echo $this->element('comments/archive-admin');