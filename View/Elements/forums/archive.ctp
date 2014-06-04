<?php
$this->Table->reset();
foreach ($forums as $forum):
	$this->Table->cells(array(
		array(
			$this->Html->link(
				$forum['Forum']['title'],
				array('controller' => 'forums', 'action' => 'view', $forum['Forum']['id'], 'plugin' => 'talk_back')
			),
			'Forum',
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