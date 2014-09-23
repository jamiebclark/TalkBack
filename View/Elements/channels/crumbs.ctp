<?php
$this->Html->addCrumb('Message Board', array(
	'controller' => 'channels', 'action' => 'index', 'plugin' => 'talk_back',
));

if (!empty($channel)) {
	if ($this->request->params['action'] == 'view' && $this->request->params['controller'] == 'channels') {
		$this->Html->addCrumb($channel['Channel']['title']);
	} else {
		$this->Html->addCrumb($channel['Channel']['title'], array(
			'controller' => 'channels', 'action' => 'view', $channel['Channel']['id'], 'plugin' => 'talk_back',
		));
	}
}
