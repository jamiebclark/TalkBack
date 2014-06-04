<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');
class Topic extends TalkBackAppModel {
	public $name = 'Topic';

	public $actsAs = array(
		'TalkBack.Commentable' => array(
			'order' => 'ASC'
		),
		'TalkBack.HasRead'
	);
	
	public $belongsTo = array(
		'TalkBack.Commenter',
		'Forum' => array(
			'className' => 'TalkBack.Forum',
			'counterCache' => true,
		)
	);
	
	public $order = array(
		'Topic.sticky' => 'DESC',
		'Topic.created' => 'DESC',
	);
	
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
			$comment = $this->Comment->find('first', array(
				'recursive' => -1,
				'joins' => array(
					array(
						'table' => 'topics',
						'alias' => 'Topic',
						'conditions' => array(
							'Topic.id = ' . $this->escapeField('topic_id'),
						)
					)
				),
				'conditions' => array(
					$this->escapeField('id') => $id,
					'Topic.locked' => false,
				)
			));
			return !empty($comment);
		}
		return parent::isCommentable($id, $commenterId, $isAdmin);
	}
}