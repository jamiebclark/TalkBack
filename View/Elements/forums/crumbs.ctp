<?php
if (empty($channel)) {
	if (!empty($topic['Channel'])) {
		$channel = $topic['Channel'];
	} else if (!empty($topic['Forum']['Channel'])) {
		$channel = $topic['Forum']['Channel'];
	}
	if (!empty($channel)) {
		$channel = array('Channel' => $channel);
	}
}

if (!empty($channel)) {
	echo $this->element('TalkBack.channels/crumbs', compact('channel'));
}

$this->Html->addCrumb($forum['Forum']['title'], array(
	'controller' => 'forums', 'action' => 'view', $forum['Forum']['id'], 'plugin' => 'talk_back',
));
