<?php
/**
 * The Model of Commenters within the TalkBack Plugin
 *
 * Using the Config file, you can set this model to use your regular User class
 * @package app.Plugin.TalkBack.Model
 **/

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
		'CommentersCommenterType' => [
			'className' => 'TalkBack.CommentersCommenterType',
		]
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
			'className' => 'TalkBack.CommenterType',
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
		'Forum' => [
			'className' => 'TalkBack.Forum',
			'foreignKey' => 'commenter_id',
			'associationForeignKey' => 'forum_id',
			'joinTable' => 'tb_forums_commenters',
		],
	];
	
	public $recursive = 0;
	
	public function __construct($id = false, $table = null, $ds = null) {
		$this->constructFromConfigure();
		if ($config = Configure::read('TalkBack.CommentersCommenterType')) {
			$this->bindModel(array(
				'hasMany' => array(
					'CommentersCommenterType' => array(
						'className' => 'TalkBack.CommentersCommenterType',
						'foreignKey' => $config['commenterForeignKey'],
					)
				),
				'hasAndBelongsToMany' => array(
					'CommenterType' => array(
						'joinTable' => $config['useTable'],
						'foreignKey' => $config['commenterForeignKey'],
						'associationForeignKey' => $config['commenterTypeForeignKey'],
					)
				)
			), false);
		}

		return parent::__construct($id, $table, $ds);
	}

	public function beforeFind($query) {
		$oQuery = $query;
		
		if (!empty($query['channelId'])) {
			//$channel = $this->find('first', array('conditions' => array('Channel.id' => $query['channelId'])))
			$commenters = $this->find('list', array(
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'tb_channels_commenters',
						'alias' => 'ChannelsCommenter',
						'conditions' => array(
							'ChannelsCommenter.commenter_id = ' . $this->escapeField(),
							'ChannelsCommenter.channel_id' => $query['channelId']
						)
					)
				)
			));
			// Filters by commenter first
			if (!empty($commenters)) {
				$query['conditions'][][$this->escapeField()] = array_keys($commenters);
			} else {
				// Then checks commenter type
				$commenterTypes = $this->CommenterType->find('list', array(
					'recursive' => -1,
					'joins' => array(
						array(
							'table' => 'tb_channels_commenter_types',
							'alias' => 'ChannelsCommenterType',
							'conditions' => array(
								'ChannelsCommenterType.commenter_type_id = CommenterType.id',
								'ChannelsCommenterType.channel_id' => $query['channelId'],
							)
						)
					)
				));

				if (!empty($commenterTypes)) {
					$query = $this->CommenterType->joinToModel($this->alias, $query, array('exclusive' => true));
					$query['conditions'][]['CommenterType.id'] = array_keys($commenterTypes);
				}
			}
			unset($query['channelId']);
		}

		if (!empty($query['forumId'])) {
			$commenters = $this->find('list', array(
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'tb_forums_commenters',
						'alias' => 'ForumsCommenter',
						'conditions' => array(
							'ForumsCommenter.commenter_id = ' . $this->escapeField(),
							'ForumsCommenter.forum_id' => $query['forumId']
						)
					)
				)
			));
			// Filters by commenter first
			if (!empty($commenters)) {
				$query['conditions'][][$this->escapeField()] = array_keys($commenters);
			} else {
				// Then checks commenter type
				$commenterTypes = $this->CommenterType->find('list', array(
					'recursive' => -1,
					'joins' => array(
						array(
							'table' => 'tb_forums_commenter_types',
							'alias' => 'ForumsCommenterType',
							'conditions' => array(
								'ForumsCommenterType.commenter_type_id = CommenterType.id',
								'ForumsCommenterType.forum_id' => $query['forumId'],
							)
						)
					)
				));

				if (!empty($commenterTypes)) {
					$query = $this->CommenterType->joinToModel($this->alias, $query, array('exclusive' => true));
					$query['conditions'][]['CommenterType.id'] = array_keys($commenterTypes);
				}
			}
			unset($query['forumId']);
		}

		if ($oQuery != $query) {
			return $query;
		}
		return parent::beforeFind($query);
	}

	protected function _findCount($state, $query, $results = array()) {
		if ($state == 'before') {
			if (!empty($query['callbacks'])) {
				$query = $this->beforeFind($query);			
			}
			unset($query['fields']);
		}
		return parent::_findCount($state, $query, $results);
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