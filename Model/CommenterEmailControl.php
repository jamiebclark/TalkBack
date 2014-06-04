<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');

class CommenterEmailControl extends TalkBackAppModel {
	public $name = 'CommenterEmailControl';

	public $belongsTo = array(
		'Commenter' => array(
			'className' => 'TalkBack.Commenter',
			'foreignKey' => 'commenter_id',
		),
		'Comment' => array(
			'className' => 'TalkBack.Comment',
			'foreignKey' => 'comment_id',
		),
	);
}