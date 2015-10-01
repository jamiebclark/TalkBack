<?php 
$this->ModelView->setModel('TalkBack.Forum');
$this->Table->reset();
foreach ($forums as $forum):
	$class = '';
	if (empty($forum['Forum']['active'])) {
		$class .= ' empty';
	}
	$this->Table->checkbox($forum['Forum']['id']);
	$this->Table->cells(array(
		array(
			$this->ModelView->link($forum['Forum']) . '<p><small>' . $forum['Forum']['description'] . '</small></p>',
			'Title',
		), array(
			$this->Html->link(
				$forum['Channel']['title'], 
				array('controller' => 'channels', 'action' => 'view', $forum['Channel']['id'], 'plugin' => 'talk_back')
			),
			'Channel',
			'Channel.title',
		), array(
			$forum['Forum']['private'] ? '<span class="label label-danger">Yes</span>' : '<span class="label label-default">No</span>',
			'Private',
			'Forum.private',
		), array(
			$forum['Forum']['active'] ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>',
			'Active',
			'Forum.active',
		), array(
			$this->ModelView->actionMenu(array('view', 'edit', 'delete'), $forum['Forum']),
			'Actions',
		)
	), compact('class'));
endforeach; 

echo $this->Table->output(array(
	'paginate' => true,
	'withChecked' => array('delete'),
));