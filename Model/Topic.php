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
	
	public function findSidebar($query = []) {
		$sidebarTopics = array();
		$db = $this->getDataSource();
		$query = Hash::merge([
			'relatedUnread' => 'Comment',
			'contain' => ['CurrentCommenterHasRead'],
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
		$sidebarTopics['Last Updated'] = $this->find('all',
			Hash::merge([
				'contain' => ['LastComment' => ['Commenter']],
				'order' => ['Topic.modified' => 'DESC']
			], $query)
		);
			
		// Most recent comments in the forum
		$sidebarTopics['Recently Added'] = $this->find('all', 
			Hash::merge(['order' => ['Topic.created' => 'DESC']], $query)
		);
		return $sidebarTopics;
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