<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');
class Commenter extends TalkBackAppModel {
	public $name = 'Commenter';
	public $hasMany = [
		'Comment' => [
			'className' => 'TalkBack.Comment',
			'foreignKey' => 'commenter_id',
		],
		'CommentersMessage' => [
			'className' => 'TalkBack.CommentersMessage',
			'foreignKey' => 'commenter_id',
		],
		'CommenterEmailBlock' => [
			'className' => 'TalkBack.CommenterEmailBlock',
			'foreignKey' => 'commenter_id',
			'dependent' => true,
		],
	];

	public $hasOne = [
			'CommenterEmailControl' => [
			'className' => 'TalkBack.CommenterEmailControl',
			'foreignKey' => 'commenter_id',
			'dependent' => true,
		],
	];

	public $hasAndBelongsToMany = [
		'CommenterType' => [
			'className' => 'TalkBack.Commenter',
			'joinTable' => 'tb_commenter_types_commenters',
			'foreignKey' => 'commenter_id',
			'associationForeignKey' => 'commenter_type_id',
		],
		'Channel' => [
			'className' => 'TalkBack.Channel',
			'foreignKey' => 'commenter_id',
			'associationForeignKey' => 'channel_id',
			'joinTable' => 'tb_channels_commenters',
		],
		'ChannelAdmin' => [
			'className' => 'TalkBack.Channel',
			'foreignKey' => 'commenter_id',
			'associationForeignKey' => 'channel_id',
			'joinTable' => 'tb_channel_commenter_admins',
		],
	];
	
	public $recursive = 0;
	
	
	public function __construct($id = false, $table = null, $ds = null) {
		$this->constructFromConfigure();
		return parent::__construct($id, $table, $ds);
	}

	/*
	public $hasMany = [
		'Comment' => [
			'className' => 'TalkBack.Comment',
			'foreignKey' => 'commenter_id',
		],
		'Topic' => [
			'className' => 'TalkBack.Topic',
			'foreignKey' => 'commenter_id',
		],
		'MessageTo' => [
			'className' => 'TalkBack.Message',
			'foreignKey' => 'commenter_id',
		],
		'MessageFrom' => [
			'className' => 'TalkBack.Message',
			'foreignKey' => 'from_commenter_id',
		],
		'ReadTopic' => [
			'className' => 'TalkBack.ReadTopic',
			'foreignKey' => 'commenter_id',
			'dependent' => true,
		],
		'ReadComment' => [
			'className' => 'TalkBack.ReadComment',
			'foreignKey' => 'commenter_id',
			'dependent' => true,
		]
	];
	*/
}