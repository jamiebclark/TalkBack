<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');

class CommenterEmailControl extends TalkBackAppModel {
	public $name = 'CommenterEmailControl';

	public $belongsTo = [
		'Commenter' => ['className' => 'TalkBack.Commenter', 'foreignKey' => 'commenter_id'],
		//'Comment' => ['className' => 'TalkBack.Comment', 'foreignKey' => 'comment_id'],
	];
}