<?php
if (empty($channel)) {
	if (!empty($forum['Channel'])) {
		$channel = $forum['Channel'];
	} else if (!empty($forum['Forum']['Channel'])) {
		$channel = $forum['Forum']['Channel'];
	}
	if (!empty($channel)) {
		$channel = array('Channel' => $channel);
	}
}

echo $this->element('TalkBack.channels/crumbs', compact('channel'));

if (empty($topic) && ($this->request->params['controller'] == 'forums' && $this->request->params['action'] == 'view')) {
	$this->Html->addCrumb($forum['Forum']['title']);
} else {
	$this->Html->addCrumb($forum['Forum']['title'], array(
		'controller' => 'forums', 'action' => 'view', $forum['Forum']['id'], 'plugin' => 'talk_back',
	));
}