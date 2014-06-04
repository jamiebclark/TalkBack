<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');
class CommentersMessage extends TalkBackAppModel {
	public $name = 'CommentersMessage';
	public $belongsTo = array(
		'Commenter' => array(
			'className' => 'TalkBack.Commenter',
			'foreignKey' => 'commenter_id',
		),
		'Message' => array(
			'className' => 'TalkBack.Message',
			'foreignKey' => 'message_id',
		),
	);
}