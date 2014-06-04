<h2><?php echo $this->Html->link($modelTitle, $modelUrl); ?></h2>
<?php echo $this->element('comments', array(
	'url' => array('action' => 'index', $model, $foreignKey)
)); ?>