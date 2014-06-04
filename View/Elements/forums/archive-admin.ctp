<?php 
$this->ModelView->setModel('TalkBack.Forum');
$this->Table->reset();
foreach ($forums as $forum):
	$this->Table->checkbox($forum['Forum']['id']);
	$this->Table->cells(array(
		array(
			$this->ModelView->link($forum['Forum']) . '<br/>' . $forum['Forum']['description'],
			'Title',
		), array(
			$this->Html->link(
				$forum['Channel']['title'], 
				array('controller' => 'channels', 'action' => 'view', $forum['Channel']['id'], 'plugin' => 'talk_back')
			),
			'Channel',
		), array(
			$this->ModelView->actionMenu(array('view', 'edit', 'delete'), $forum['Forum']),
			'Actions',
		)
	), true);
endforeach; 

echo $this->Table->output(array(
	'paginate' => true,
	'withChecked' => array('delete'),
));