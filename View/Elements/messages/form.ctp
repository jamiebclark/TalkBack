<?php 
echo $this->Form->create();
echo $this->Form->hidden('id');
echo $this->Commenter->addInput();

echo $this->Form->inputs(array(
	'from_commenter_id' => array('type' => 'hidden'),
	'Commenter.Commenter' => array(
		'label' => 'To',
	),
	'subject',
	'Comment.0.id',
	//'Comment.0.model' => array('type' => 'hidden', 'value' => 'TalkBack.Message'),
	'Comment.0.body',
	'Comment.0.commenter_id' => array('type' => 'hidden'),
	'fieldset' => false,
));
echo $this->Form->hidden('Commenter.Commenter.', array('value' => $currentCommenter['Commenter']['id']));
echo $this->Form->end('Send');
