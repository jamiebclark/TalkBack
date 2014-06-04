<?php 
$this->Table->reset();
foreach ($channels as $channel):
	$this->Table->cells(array(
		array(
			$this->Html->link(
				$channel['Channel']['title'],
				array('controller' => 'channels', 'action' => 'view', $channel['Channel']['id'], 'plugin' => 'talk_back')
			),
			'Channel',
		), array(
			number_format($channel['Channel']['forum_count']),
			'Forumns',
		), array(
			number_format($channel['Channel']['topic_count']),
			'Topics',
		)
	), true);
endforeach;
echo $this->Table->output();