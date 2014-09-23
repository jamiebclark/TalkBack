<?php
$isAjax = $this->request->is('ajax');
?>

<?php if (!$isAjax): ?>
<div class="row">
	<div class="col-sm-8 col-md-6">
<?php endif; ?>

<?php
echo $this->Form->create();
echo $this->Form->inputs(array(
	'fieldset' => !$isAjax,
	'id',
	'title' => array(
		'label' => 'Topic title',
	),
	'body' => array(
		'label' => 'Description',
	),
));

echo $this->Form->hidden('commenter_id', array('default' => $currentCommenter['Commenter']['id']));
echo $this->Form->hidden('forum_id');
echo $this->Form->end('Update');
?>

<?php if (!$isAjax): ?>
	</div>
</div>
<?php endif; ?>