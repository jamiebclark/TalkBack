<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');
class Commenter extends TalkBackAppModel {
	public $name = 'Commenter';
	public $hasMany = array(
		'Comment' => array(
			'className' => 'TalkBack.Comment',
			'foreignKey' => 'commenter_id',
		),
		'CommentersMessage' => array(
			'className' => 'TalkBack.CommentersMessage',
			'foreignKey' => 'commenter_id',
		),
		'CommenterEmailControl' => array(
			'className' => 'TalkBack.CommenterEmailControl',
			'foreignKey' => 'commenter_id',
			'dependent' => true,
		),
		'CommenterEmailBlock' => array(
			'className' => 'TalkBack.CommenterEmailBlock',
			'foreignKey' => 'commenter_id',
			'dependent' => true,
		),
	);

	public $hasAndBelongsToMany = array(
		'CommenterType' => array(
			'className' => 'TalkBack.Commenter',
			'joinTable' => 'tb_commenter_types_commenters',
			'foreignKey' => 'commenter_id',
			'associationForeignKey' => 'commenter_type_id',
		),
		'Channel' => array(
			'className' => 'TalkBack.Channel',
			'foreignKey' => 'commenter_id',
			'associationForeignKey' => 'channel_id',
			'joinTable' => 'tb_channels_commenters',
		),
		'ChannelAdmin' => array(
			'className' => 'TalkBack.Channel',
			'foreignKey' => 'commenter_id',
			'associationForeignKey' => 'channel_id',
			'joinTable' => 'tb_channel_commenter_admins',
		),
	);
	
	public $recursive = 0;
	
	
	public function __construct($id = false, $table = null, $ds = null) {
		$this->constructFromConfigure();
		return parent::__construct($id, $table, $ds);
	}

	/*
	public $hasMany = array(
		'Comment' => array(
			'className' => 'TalkBack.Comment',
			'foreignKey' => 'commenter_id',
		),
		'Topic' => array(
			'className' => 'TalkBack.Topic',
			'foreignKey' => 'commenter_id',
		),
		'MessageTo' => array(
			'className' => 'TalkBack.Message',
			'foreignKey' => 'commenter_id',
		),
		'MessageFrom' => array(
			'className' => 'TalkBack.Message',
			'foreignKey' => 'from_commenter_id',
		),
		'ReadTopic' => array(
			'className' => 'TalkBack.ReadTopic',
			'foreignKey' => 'commenter_id',
			'dependent' => true,
		),
		'ReadComment' => array(
			'className' => 'TalkBack.ReadComment',
			'foreignKey' => 'commenter_id',
			'dependent' => true,
		)
	);
	*/
}