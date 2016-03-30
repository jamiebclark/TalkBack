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
			'rule' => 'notBlank',
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
		if (!empty($result[$this->alias]['topic_id'])) {
			$this->_updateTopicId = $result[$this->alias]['topic_id'];
		}
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
			$result = $this->find('first', array(
				'conditions' => array(
					$this->escapeField() => $id,
					$this->escapeField('locked') => false,
				)));
			return !empty($result);
		}
		return parent::isCommentable($id, $commenterId, $isAdmin);
	}
	
/**
 * Finds the topic before and after the given topic
 *
 * @param int $id The topic id
 * @param array $query Additional query parameters
 * @return array The result array with a "next" and "prev" key
 **/
	public function findNeighbors($id, $query = []) {
		$result = $this->read(null, $id);
		$result = $result[$this->alias];

		$key = round(!empty($result['sticky'])) . $result['created'];
		$query['conditions'][]['NOT'][$this->escapeField()] = $id;
		$query['conditions'][$this->escapeField('forum_id')] = $result['forum_id'];

		$prevQuery = $query;
		$nextQuery = $query;

		$nextQuery['conditions'][sprintf('CONCAT(%s, %s) <= ', $this->escapeField('sticky'), $this->escapeField('created'))] = $key;
		$nextQuery['order'] = $this->order;

		$prevQuery['conditions'][sprintf('CONCAT(%s, %s) >= ', $this->escapeField('sticky'), $this->escapeField('created'))] = $key;
		$prevQuery['order'] = array($this->escapeField('created') => 'ASC', $this->escapeField('sticky') => 'ASC');

		return array(
			'next' => $this->find('first', $nextQuery),
			'prev' => $this->find('first', $prevQuery)
		);
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

		if (array_key_exists('isAdmin', $query)) {
			if ($query['isAdmin'] === false) {
				$query['conditions']['Forum.active'] = 1;
			}
			unset($query['isAdmin']);
		}
		
		// Most recent topics in the forum
		$query = Hash::merge([
				'contain' => ['LastComment' => ['Commenter']],
				'order' => ['Topic.modified' => 'DESC']
			], $query);
		$updatedTopics['Last Updated'] = $this->find('all', $query);
			
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