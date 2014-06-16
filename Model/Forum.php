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
		$result = $this->read('channel_id', $id);
		return $this->Channel->isCommenterAllowed($result[$this->alias]['channel_id'], $commenterId, $prefix);
	}
	
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
}