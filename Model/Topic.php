<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');
class Topic extends TalkBackAppModel {
	public $name = 'Topic';

	public $actsAs = [
		'TalkBack.Commentable' => ['hasRead' => true, 'order' => 'ASC'],
		'TalkBack.HasRead' => ['relatedUnread' => 'Comment']
	];
	
	public $belongsTo = [
		'TalkBack.Commenter',
		'Forum' => ['className' => 'TalkBack.Forum', 'counterCache' => true	]
	];
	
	public $order = ['Topic.sticky' => 'DESC', 'Topic.created' => 'DESC'];

	public $validate = [
		'title' => [
			'rule' => 'notEmpty',
			'message' => 'Please give the topic a title',
		]
	];

/**
 * Stores the topic id to be updated after a topic is deleted
 * @var int
 **/
	private $_updateTopicId = null;

	public function afterSave($created, $options = array()) {
		if ($created) {
			$result = $this->read();
			$this->Forum->setTopicCount($result[$this->alias]['forum_id']);
		}
		return parent::afterSave($created, $options);
	}

	public function beforeDelete($cascade = true) {
		$result = $this->read();
		$this->_updateTopicId = $result[$this->alias]['topic_id'];
		return parent::beforeDelete($cascade);
	}

	public function afterDelete() {
		if (!empty($this->_updateTopicId)) {
			$this->Forum->setTopicCount($this->_updateTopicId);
			$this->_updateTopicId = null;
		}
		return parent::afterDelete();
	}

/**
 * Determines if a given Commenter ID has sufficient permissions to view a Topic
 * 
 * @param int $id ID of the Topic
 * @param int $commenterId ID of the Commenter
 * @param string|null $prefix The current page Prefix
 * @return bool True on success
 **/
	public function isCommenterAllowed($id, $commenterId = null, $prefix = null) {
		$result = $this->find('first', [
			'contain' => ['Forum' => ['Channel']],
			'conditions' => [$this->escapeField('id') => $id]
		]);
		if (empty($result['Forum']['Channel'])) {
			return null;
		}			
		return $this->Forum->Channel->isCommenterAllowed($result['Forum']['Channel']['id'], $commenterId, $prefix);
	}
	
	
/**
 * Determines if a given Commenter ID is an Administrator of the current topic
 * 
 * @param int $id ID of the Topic
 * @param int $commenterId ID of the Commenter
 * @return bool True on success
 **/
	public function isCommenterAdmin($id, $commenterId) {
		$result = $this->read('forum_id', $id);
		return $this->Forum->isCommenterAdmin($result[$this->alias]['forum_id'], $commenterId);
	}
	
	//Extends existing function to make sure the topic isn't locked
	public function isCommentable($id, $commenterId = null, $isAdmin = false) {
		if (empty($isAdmin)) {
			$comment = $this->Comment->find('first', [
				'recursive' => -1,
				'joins' => [[
					'table' => 'topics',
					'alias' => 'Topic',
					'conditions' => [
						'Topic.id = ' . $this->escapeField('topic_id'),
					]
				]],
				'conditions' => [
					$this->escapeField('id') => $id,
					'Topic.locked' => false,
				]
			]);
			return !empty($comment);
		}
		return parent::isCommentable($id, $commenterId, $isAdmin);
	}
	
	public function findUpdatedList($query = []) {
		$updatedTopics = array();
		$db = $this->getDataSource();
		$currentCommenterId = $this->getCurrentCommenterId();
		$query = Hash::merge([
			'relatedUnread' => 'Comment',
			'contain' => !empty($currentCommenterId) ? ['CurrentCommenterHasRead'] : [],
			'joins' => [
				[
					'table' => $db->fullTableName($this->Forum),
					'alias' => 'Forum',
					'conditions' => [
						$this->Forum->escapeField($this->primaryKey) . ' = ' . $this->escapeField('forum_id')
					]
				],
			],
			'limit' => 5,
		], $query);
		
		// Most recent topics in the forum
		$updatedTopics['Last Updated'] = $this->find('all',
			Hash::merge([
				'contain' => ['LastComment' => ['Commenter']],
				'order' => ['Topic.modified' => 'DESC']
			], $query)
		);
			
		// Most recent comments in the forum
		$updatedTopics['Recently Added'] = $this->find('all', 
			Hash::merge(['order' => ['Topic.created' => 'DESC']], $query)
		);
		return $updatedTopics;
	}
	
	public function getPrefix($id) {
		$channel = $this->find('first', [
			'contain' => ['Forum' => ['Channel']],
			'conditions' => [$this->escapeField('id') => $id]
		]);
		if (empty($channel['Forum']['Channel']['prefix'])) {
			return null;
		}
		return $channel['Forum']['Channel']['prefix'];
	}	
}