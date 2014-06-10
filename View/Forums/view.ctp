<?php echo $this->element('forums/crumbs'); ?>
<h2><?php echo $forum['Forum']['title']; ?></h2>
<?php echo $this->element('topics/archive'); ?>
<?php echo $this->Html->link(	
	'Add Topic', 
	array('controller' => 'topics', 'action' => 'add', $forum['Forum']['id']),
	array('class' => 'btn btn-default btn-primary')
);?>
