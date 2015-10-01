<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');

class Forum extends TalkBackAppModel {
	public $name = 'Forum';
	public $actsAs = ['TalkBack.HasRead' => ['relatedUnread' => 'Topic']];
	public $hasMany = ['TalkBack.Topic'];
	public $belongsTo = [
		'Channel' => [
			'className' => 'TalkBack.Channel',
			'counterCache' => true,
		]
	];
	
	public $hasAndBelongsToMany = [
		'Commenter' => [
			'className' => 'TalkBack.Commenter',
			'foreignKey' => 'forum_id',
			'associationForeignKey' => 'commenter_id',
			'joinTable' => 'tb_forums_commenters',
		],
		'CommenterType' => [
			'className' => 'TalkBack.CommenterType',
			'foreignKey' => 'forum_id',
			'associationForeignKey' => 'commenter_type_id',
			'joinTable' => 'tb_forums_commenter_types',
		]
	];
	
	public $validate = [
		'title' => [
			'rule' => 'notEmpty',
			'message' => 'Please give the topic a title',
		]
	];

	public function beforeSave($options = array()) {
		$private = !empty($this->data['Commenter']['Commenter']) || !empty($this->data['CommenterType']['CommenterType']);
		if (!empty($this->data[$this->alias])) {
			$this->data[$this->alias]['private'] = $private;
		} else {
			$this->data['private'] = $private;
		}
		return parent::beforeSave($options);
	}

	public function beforeFind($query = array()) {
		$oQuery = $query;
		if (array_key_exists('isAdmin', $query)) {
			if ($query['isAdmin'] === false) {
				$query['conditions'][$this->escapeField('active')] = 1;
			}
			unset($query['isAdmin']);
		}

		if ($query != $oQuery) {
			return $query;
		}
		return parent::beforeFind($query);
	}
/**
 * Determines if a given Commenter ID is an Administrator of the current forum
 * 
 * @param int $id ID of the Forum
 * @param int $commenterId ID of the Commenter
 * @return bool True on success
 **/
	public function isCommenterAdmin($id, $commenterId) {
		$result = $this->read('channel_id', $id);
		return $this->Channel->isCommenterAdmin($result[$this->alias]['channel_id'], $commenterId);
	}

/**
 * Determines if a given Commenter ID has sufficient permissions to view a Forum
 * 
 * @param int $id ID of the Forum
 * @param int $commenterId ID of the Commenter
 * @param string|null $prefix The current page Prefix
 * @return bool True on success
 **/
	public function isCommenterAllowed($id, $commenterId = null, $prefix = null) {
		$result = $this->find('first', [
			'contain' => ['Commenter', 'CommenterType'],
			'conditions' => [$this->escapeField() => $id]
		]);

		if (!$this->Channel->isCommenterAllowed($result[$this->alias]['channel_id'], $commenterId, $prefix)) {
			// Makes sure they're allowed in the channel first
			return false;
		} else if ($this->Channel->isCommenterAdmin($result[$this->alias]['channel_id'], $commenterId)) {
			// Checks if they're a channel admin
			return true;
		} else if (!empty($result[$this->alias]['private'])) {
			// Checks if they're an allowed commenter id
			$commenterIds = Hash::extract($result, 'Commenter.{n}.id');
			if (in_array($commenterId, $commenterIds)) {
				return true;
			}
			// Checks if they're an allowed commenter type
			$query = $this->CommenterType->joinCommenter($this->alias, $commenterId, ['recursive' => -1]);
			$result = $this->find('first', $query);
			return !empty($result);
		}
	}
/**
 * Determines if a user can add a topic to a Forum
 *
 * @param int $id Forum id
 * @param int $id The Commenter id
 * @return bool True if yes, false if no;
 **/
	public function canTopicBeAdded($id, $commenterId = null) {
		if (empty($commenterId)) {
			return false;
		}
		$result = $this->find('first', array(
			'contain' => array('Channel'),
			'conditions' => array($this->escapeField() => $id)
		));

		// Checks if Users are allowed to add topics in the Channel settings
		if (!empty($result['Channel']['allow_topics'])) {
			return true;
		}
		// Otherwise they must be an admin
		return $this->isCommenterAdmin($id, $commenterId);
	}
	
/**
 * Finds all the forums a commenter has access to. You can also limit this search by a specific prefix
 *
 * @param int $id The Commenter id
 * @param string $prefix Limit search by this optional prefix
 * @return array|null Forum result if found, null if not found
 **/
	public function findCommenterForums($commenterId = null, $prefix = null) {
		if ($channelIds = $this->Channel->findCommenterChannelIds($commenterId, $prefix)) {
			return $this->find('all', ['conditions' => [$this->escapeField('channel_id') => $channelIds]]);
		} else {
			return null;
		}
	}

	public function setCurrentCommenter($commenterId = null) {
		return $this->Topic->setCurrentCommenter($commenterId);
	}

	public function getPrefix($id) {
		$channel = $this->find('first', [
			'contain' => ['Channel'],
			'conditions' => [$this->escapeField('id') => $id]
		]);
		return $channel['Channel']['prefix'];
	}

/**
 * Sets the total amount of topics inside of a channel
 *
 * @param int $id The channel id
 * @return void;
 **/
	public function setTopicCount($id) {
		$result = $this->read('channel_id', $id);
		$topicCount = $this->Topic->find('count', array('conditions' => array('Topic.forum_id' => $id)));

		$this->create();
		if ($success = $this->save(array(
			'id' => $id,
			'topic_count' => $topicCount,
		), array('callbacks' => false, 'validate' => false))) {
			$this->Channel->setTopicCount($result[$this->alias]['channel_id']);
		}
		return $success;
	}
}