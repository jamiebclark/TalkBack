<?php
$this->Html->addCrumb('Message Board', array(
	'controller' => 'channels', 'action' => 'index', 'plugin' => 'talk_back',
));
$this->Html->addCrumb($channel['Channel']['title'], array(
	'controller' => 'channels', 'action' => 'view', $channel['Channel']['id'], 'plugin' => 'talk_back',
));
