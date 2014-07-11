<?php
App::uses('TalkBackBehavior', 'TalkBack.Model/Behavior');
class CommentableBehavior extends TalkBackBehavior {
	public $name = 'Commentable';
	
	#section Callbacks
	public function setup(Model $Model, $settings = array()) {
		$default = array(
			'anonymous' => false,			// Does a commenter need to be logged in to comment?
			'softDelete' => true,			// Should comments be deleted permanently, or simply marked as "deleted"
			'hasRead' => false,				// Should we track if comments are being read
			'order' => 'DESC',				// ASC or DESC to order the lft field, or set an array for custom sort
			'creatorField' => null,			// The field of the commenter creating this item, to include them in 
											// any conversation
			'additionalCommentersModel' => null,		// An additional model that stores additional commenters part of 
														// the conversation
			'className' => $this->getPluginClassName($Model)
		);
		
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $default;
		}
		$this->settings[$Model->alias] = array_merge(
			$this->settings[$Model->alias], 
			(array) $settings
		);
		if (!empty($this->settings[$Model->alias]['creatorField'])) {
			$Model->commentCreatorField = $this->settings[$Model->alias]['creatorField'];
		}
		$this->_bindModels($Model);
		return parent::setup($Model, $settings);		
	}
	
	public function beforeFind(Model $Model, $query = array()) {
		// Limits the query to only those with the following commenter ID
		if (!empty($query['commenterId'])) {
			$query['joins'][] = array(
				'table' => $Model->Comment->useTable,
				'alias' => 'FilterComment',
				'conditions' => array(
					'FilterComment.model' => $this->getPluginClassName($Model),
					'FilterComment.commenter_id' => $query['commenterId'],
				)
			);
			$query['group'] = $Model->escapeField($Model->primaryKey);
		}
		return parent::beforeFind($Model, $query);
	}
	
	public function beforeSave(Model $Model, $options = array()) {
		$Model->Comment->setParentClassName($this->getPluginClassName($Model));
		return parent::beforeSave($Model, $options);
	}
	
	
	public function afterSave(Model $Model, $created, $settings = array()) {
		$this->updateCommentStats($Model, $Model->id);
		return parent::afterSave($Model, $created, $settings);		
	}
	#endsection
	
	#section Custom Callbacks
	public function afterCommentSave(Model $Model, $foreignKey, $commenterId, $created, $options = array()) {
		return true;
	}
	#endsection
	
	#section Getters and Setters
	public function setCurrentCommenter(Model $Model, $commenterId) {
		$Model->Comment->setCurrentCommenter($commenterId);
		return parent::setCurrentCommenter($Model, $commenterId);
	}
	
	public function getCommentCreatorField($Model) {
		return $this->settings[$Model->alias]['creatorField'];
	}
	
	public function getAdditionalCommentersModel($Model) {
		return $this->settings[$Model->alias]['additionalCommentersModel'];
	}
	#endsection
	
	public function isEditable(Model $Model, $id, $commenterId = null, $isAdmin = false) {
		$success = false;
		if ($isAdmin) {
			$success = true;
		} else {
			if (!empty($this->settings[$Model->alias]['anonymous'])) {
				$success = true;	//Allows for anonymous commenting
			} else {
				$success = $Model->find('first', array(
					'conditions' => array(
						$Model->escapeField('commenter_id') => $commenterId,
						$Model->escapeField('id') => $id,
					)
				));
			}
		}
		return $success;
	}
	
	// Checks if this model needs the comments read history tracked as well
	public function isCommentHasRead(Model $Model) {
		return !empty($this->settings[$Model->alias]['hasRead']);
	}
	
	public function isCommentable(Model $Model, $id, $commenterId = null, $isAdmin = false) {
		return true;
	}
	
	public function isCommentSoftDelete($Model) {
		return !empty($this->settings[$Model->alias]['softDelete']);
	}
	
	public function getCommentAssociation(Model $Model, $modelId = null, $commentId = null, $query = array()) {
		$query = array(
			//'contain' => array('CurrentCommenterHasRead'),
			'conditions' => array(
				'Comment.model' => $this->settings[$Model->alias]['className'],
				'Comment.foreign_key' => $modelId,
			)
		);
		if (!empty($modelId)) {
			$query['conditions']['Comment.foreign_key'] = $modelId;
		}
		if (!empty($commentId)) {
			$query['conditions']['Comment.id'] = $commentId;
		}
		if (empty($query['order']) && !empty($this->settings[$Model->alias]['order'])) {
			if (is_array($this->settings[$Model->alias]['order'])) {
				$query['order'] = $this->settings[$Model->alias]['order'];
			} else {
				$query['order'] = array('Comment.lft' => $this->settings[$Model->alias]['order']);
			}
		}
		return $query;
	}
	
	public function updateCommentStats(Model $Model, $id) {
		$stats = array(
			'first_comment_id' => 'MIN(Comment.id)',
			'last_comment_id' => 'MAX(Comment.id)',
			'comment_count' => 'COUNT(Comment.id)',
			'last_commenter_id' => 'Comment.id',
			
		);
		$data = array();
		foreach ($stats as $field => $eq) {
			if ($Model->schema($field)) {
				$query['fields'][] = "$eq AS `$field`";
				$data[$field] = 0;
			}
		}

		if (empty($data)) {
			return null;
		}

		$data[$Model->primaryKey] = $id;
		
		$result = $Model->Comment->find('first', array(
			'fields' => array(
				'MIN(Comment.id) AS first_comment_id',
				'MAX(Comment.id) AS last_comment_id',
				'COUNT(Comment.id) AS comment_count',
			),
			'conditions' => array(
				'Comment.model' => $this->settings[$Model->alias]['className'],
				'Comment.foreign_key' => $id,
			)
		));
		if (!empty($result)) {
			$data = $result[0] + $data;
		}
		
		if (isset($data['last_commenter_id'])) {
			$lastComment = $Model->Comment->read('commenter_id', $result[0]['last_comment_id']);
			$data['last_commenter_id'] = $lastComment['Comment']['commenter_id'];
		}
		
		return $Model->save($data, array('callbacks' => false, 'validate' => false));
	}
	
	private function _bindModels($Model) {
		$Model->bindModel(array(
			'hasMany' => array(
				'Comment' => array(
					'className' => 'TalkBack.Comment',
					'foreignKey' => 'foreign_key',
					'dependent' => true,
					'conditions' => array('Comment.model' => $this->settings[$Model->alias]['className']),
				)
			)
		), false);
		$Model->Comment->bindModel(array(
			'belongsTo' => array(
				$Model->alias => array(
					'className' => $this->getPluginClassName($Model),
					'foreignKey' => 'foreign_key',
					'conditions' => array('Comment.model' => $this->settings[$Model->alias]['className'])
				)
			)
		), false);
		if ($Model->schema('first_comment_id')) {
			$Model->bindModel(array(
				'belongsTo' => array(
					'FirstComment' => array(
						'className' => 'TalkBack.Comment',
						'foreignKey' => false,
						'conditions' => array(
							'FirstComment.model' => $this->settings[$Model->alias]['className'],
							'FirstComment.id = ' . $Model->escapeField('first_comment_id', $Model->alias),
						),
					)
				)
			), false);
			$Model->FirstComment->Behaviors->load('TalkBack.HasRead');
		}
		if ($Model->schema('last_comment_id')) {
			$Model->bindModel(array(
				'belongsTo' => array(
					'LastComment' => array(
						'className' => 'TalkBack.Comment',
						//'foreignKey' => false,
						'conditions' => array(
							'LastComment.model' => $this->settings[$Model->alias]['className'],
							//'LastComment.id = ' . $Model->escapeField('last_comment_id', $Model->alias),
						),
					)
				)
			), false);
			$Model->LastComment->Behaviors->load('TalkBack.HasRead');
		}
	}
	
}