<?php echo $this->Layout->defaultHeader($forum['Forum']['id'], array(
	array('Add Topic', array('controller' => 'topics', 'action' => 'add', $forum['Forum']['id']))
)); 
echo $this->Layout->infoTable(array(
	'Title' => $forum['Forum']['title'],
	'Channel' => $this->Html->link($forum['Channel']['title'], array(
		'controller' => 'channels', 'action' => 'view', $forum['Channel']['id']
	)),
	'Created' => $this->Calendar->niceShort($forum['Forum']['created']),
	'Last Modified' => $this->Calendar->niceShort($forum['Forum']['modified']),
	'Description' => $this->DisplayText->text($forum['Forum']['description'])
));
?>
<h2>Topics</h2>
<?php
echo $this->element('topics/archive-admin');