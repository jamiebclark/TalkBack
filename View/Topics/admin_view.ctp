<?php 
echo $this->Layout->defaultHeader($topic['Topic']['id'], 
	array(
		array('Public View', array('action' => 'view', $topic['Topic']['id'], 'admin' => false))
	),
	array('title' => 'Topic: "' . $topic['Topic']['title'] . '"')
); 
?>
<div class="panel panel-default">
	<div class="panel-heading"><span class="panel-title">Topic</span></div>
	<div class="panel-body">
		<h2><?php echo $this->Html->tag('h2', $topic['Topic']['title']); ?></h2>
		<?php echo $this->DisplayText->text($topic['Topic']['body'], array('class' => 'lead')); ?>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading"><span class="panel-title">Replies</span></div>
	<?php echo $this->element('comments/archive-admin'); ?>
</div>