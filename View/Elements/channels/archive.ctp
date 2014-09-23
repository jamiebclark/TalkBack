<?php 
$this->Table->reset();
foreach ($channels as $channel):
	$this->Table->cells(array(
		array(
			$this->Html->link(
				$channel['Channel']['title'],
				array(
					'controller' => 'channels', 
					'action' => 'view', 
					$channel['Channel']['id'], 
					'plugin' => 'talk_back'
				)
			),
			'Channel',
		), array(
			$this->Html->tag('font', number_format($channel['Channel']['forum_count']), array('class' => 'badge badge-default')),
			'Forums',
		), array(
			$this->Html->tag('font', number_format($channel['Channel']['topic_count']), array('class' => 'badge badge-default')),
			'Topics',
		)
	), true);
endforeach;
echo $this->Table->output(array(
	'empty' => '<div class="empty-msg">No channels posted yet</div>',
	'class' => 'table tb-archive tb-channels-archive'
));