<?php
$this->Table->reset();
foreach ($forums as $forum):
	$this->Table->cells(array(
		array(
			$this->Html->link(
				!empty($forum['Forum']['title']) ? $forum['Forum']['title'] : 'No Title',
				array('controller' => 'forums', 'action' => 'view', $forum['Forum']['id'], 'plugin' => 'talk_back')
			),
			'Forum',
		), array(
			$this->Html->link(
				$forum['Channel']['title'],
				array('controller' => 'channels', 'action' => 'view', $forum['Channel']['id'], 'plugin' => 'talk_back')
			),
			'Channel',
		), array(
			number_format($forum['Forum']['topic_count']),
			'Topics',
		), array(
			number_format($forum['Forum']['comment_count']),
			'Comments',
		),
	), true);
endforeach;
echo $this->Table->output(array('paginate' => true));