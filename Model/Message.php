<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');

class Message extends TalkBackAppModel {
	public $name = 'Message';
	public $actsAs = array(
		'TalkBack.Commentable' => array(
			'additionalCommentersModel' => 'TalkBack.CommentersMessage',
			'order' => 'DESC',
		),
		'TalkBack.HasRead',
	);
	
	public $hasMany = array(
		'CommentersMessage' => array(
			'className' => 'TalkBack.CommentersMessage',
			'foreignKey' => 'message_id',
		),
	);
	
	public $belongsTo = array(
		'CommenterFrom' => array(
			'className' => 'TalkBack.Commenter',
			'foreignKey' => 'from_commenter_id',
		),
	);
	public $hasAndBelongsToMany = array(
		'Commenter' => array(
			'className' => 'TalkBack.Commenter',
			'joinTable' => 'tb_commenters_messages',
			'associationForeignKey' => 'commenter_id',
			'foreignKey' => 'message_id',
		)
	);
	public $displayField = 'subject';
	
	public $order = array('Message.modified' => 'DESC');

	/*
	public function afterSave($created, $options = array()) {
		$result = $this->find('first', array(
			'contain' => array('Commenter'),
			'conditions' => array($this->escapeField($this->primaryKey) => $this->id),
		));
		//If there's been  reply to the message, add the sender to the conversation
		if ($result[$this->alias]['comment_count'] > 1)
	}
	*/
	
	public function beforeFind($query = array()) {
		// Links the find only with the inbox of a specific commenter
		if (!empty($query['inbox'])) {
			$query = $this->getInboxAssociation($query['inbox'], $query);
			unset($query['inbox']);
		}
		return parent::beforeFind($query);
	}
	
	public function afterCommentSave($id, $created, $options = array()) {
		$comment = $this->Comment->read(null);
		$lastComment = $this->Comment->find('first', $this->getCommentAssociation($id, $this->Comment->id, array(
			'order' => array('Comment.created' => 'DESC')
		)));
		$this->updateAll(array(
			$this->escapeField('modified') => '"' . $lastComment['Comment']['created'] . '"',
		), array(
			$this->escapeField('id') => $id,
		));
		
		// Marks the message as unread for all except the user that wrote it
		if ($commenters = $this->CommentersMessage->find('all', array(
			'fields' => array('CommentersMessage.commenter_id'),
			'recursive' => -1,
			'conditions' => array(
				'CommentersMessage.message_id' => $id,
				'NOT' => array('CommentersMessage.commenter_id' => $comment['Comment']['commenter_id'])
			)
		))) {
			$this->markUnread($id, Hash::extract($commenters, '{n}.CommentersMessage.commenter_id'));
		}
		return parent::afterCommentSave($id, $created, $options);		
	}
	
	public function getInboxAssociation($commenterId, $query = array()) {
		$query = Hash::merge($query, array(
			'fields' => array('*'),
			'joins' => array(
				array(
					'table' => 'tb_commenters_messages',
					'alias' => 'CommentersMessage',
					'conditions' => array(
						'CommentersMessage.message_id = ' . $this->escapeField('id'),
					),
				), array(
					'type' => 'LEFT',
					'table' => $this->Commenter->useTable,
					'alias' => 'LastCommenter',
					'conditions' => array(
						sprintf('%s = %s', 
							$this->escapeField('id'), 
							$this->Commenter->escapeField($this->Commenter->primaryKey, 'LastCommenter')
						),
					)
				),
			),
			'conditions' => array(
				'OR' => array(
					'CommentersMessage.commenter_id' => $commenterId,
					$this->escapeField('from_commenter_id') => $commenterId,
				),
			),
			'order' => array('Message.created' => 'DESC'),
			'group' => 'Message.id',
		));
		return $query;
	}
	
	
	public function setCurrentCommenter($commenterId) {
		$this->bindOtherCommenter($commenterId, false);
		$this->FirstComment->setCurrentCommenter($commenterId);
		$this->LastComment->setCurrentCommenter($commenterId);
		return parent::setCurrentCommenter($commenterId);
	}
	
/**
 * Creates a model binding that involves all commenters in a message except for the current user
 *
 **/
	public function bindOtherCommenter($commenterId, $reset = true) {
		$alias = 'OtherCommenter';
		$association = $this->hasAndBelongsToMany['Commenter'];
		$association['conditions'][$alias . '.id <>'] = $commenterId;
		return $this->bindModel(array('hasAndBelongsToMany' => array($alias => $association)), $reset);
	}
}