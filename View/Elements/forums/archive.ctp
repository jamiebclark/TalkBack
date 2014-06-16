<?php
$this->Table->reset();
if (!empty($forums['Channel'])) {
	$passChannel = $forums['Channel'];
}
if (!empty($forums['Forum'])) {
	$forums = $forums['Forum'];
}
foreach ($forums as $forum):
	if (!empty($forum['Channel'])) {
		$channel = $forum['Channel'];
	} else if (!empty($passChannel)) {
		$channel = $passChannel;
	}
	
	$totalUnread = !empty($forum[0]['total_unread']) ? $forum[0]['total_unread'] : 0;
	
	if (!empty($forum['Forum'])) {
		$forum = $forum['Forum'];
	}
	$url = array('controller' => 'forums', 'action' => 'view', $forum['id'], 'plugin' => 'talk_back');
	
	$topics = $this->Html->tag('span',
		number_format($forum['topic_count']),
		array('class' => 'badge')
	);
	$title = $this->Html->link(
		!empty($forum['title']) ? $forum['title'] : 'No Title',
		$url,
		['class' => !empty($totalUnread) ? 'unread' : null]
	);
	if (!empty($totalUnread)) {
		$title .= ' ' . $this->Html->link(
			number_format($totalUnread) . ' new!',
			$url,
			['class' => 'label label-success']
		);
	}
	$this->Table->cells(array(
		array(
			$title,
			'Forum',
		), array(
			$this->Html->link(
				$channel['title'],
				array('controller' => 'channels', 'action' => 'view', $channel['id'], 'plugin' => 'talk_back')
			),
			'Channel',
		), array(
			$topics,
			'Topics',
			array('class' => 'text-center'),
		), array(
			$this->Html->tag('span',
				number_format($forum['comment_count']),
				array('class' => 'badge')
			),				
			'Comments',
			array('class' => 'text-center'),
		),
	), true);
endforeach;
echo $this->Table->output(array('paginate' => true));