<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');
class Comment extends TalkBackAppModel {
	public $name = 'Comment';
	public $order = ['Comment.lft' => 'DESC'];
	public $actsAs = [
		'Tree',
		'TalkBack.HasRead',
		'Containable',
	];
	
	/*
	public $hasMany = [
		'CommenterEmailControl' => [
			'className' => 'TalkBack.CommenterEmailControl',
			'foreignKey' => 'comment_id',
			'dependent' => true,
		],
	];
	*/

	public $belongsTo = [
		'Commenter' => [
			'className' => 'TalkBack.Commenter',
			'foreignKey' => 'commenter_id',
		]
	];

	public $validate = [
		'body' => [
			'rule' => 'notEmpty',
			'message' => 'Please enter a comment',
		]
	];
	
	private $_parentClassName;
	
	public function beforeSave($options = []) {
		if (!empty($this->data[$this->alias])) {
			$data =& $this->data[$this->alias];
		} else {
			$data =& $this->data;
		}
		
		// Makes sure the parent class name is stored
		if (empty($data['model']) && !empty($this->_parentClassName)) {
			$data['model'] = $this->_parentClassName;
		}
		return parent::beforeSave($options);
	}
	

	public function afterSave($created, $options = []) {
		$result = $this->read(null, $this->id);
		$result = $result[$this->alias];
		
		if (!empty($result['parent_id'])) {
			$depth = $this->find('count', array(
				'conditions' => array(
					$this->escapeField('lft') . ' <' => $result['lft'],
					$this->escapeField('rght') . ' >' => $result['rght'],
				)
			));
		} else {
			$depth = 0;
		}
		$this->updateAll(
			array($this->escapeField('depth') => $depth,), 
			array($this->escapeField('id') => $result['id'])
		);
		
		if ($Model = ClassRegistry::init($result['model'], true)) {
			$Model->updateCommentStats($result['foreign_key']);			
		}
		if ($Model->hasMethod('afterCommentSave')) {
			$Model->afterCommentSave($result['foreign_key'], $created, $options);
		}
		$this->sendEmailUpdate($this->id);
		
		return parent::afterSave($created, $options);
	}
	
	public function delete($id = null, $cascade = true) {
		if (($Model = $this->getParentModel($id)) && $Model->isCommentSoftDelete($id)) {
			return $this->softDelete($id);
		}
		return parent::delete($id, $cascade);
	}
	
	public function softDelete($id) {
		return $this->updateAll(
			array($this->escapeField('deleted') => 1),
			array($this->escapeField($this->primaryKey) => $id)
		);
	}
	
	// Finds the parent result of a given comment id
	public function findParentModelById($id, $query = []) {
		$result = $this->read(null, $id);
		$result = $result[$this->alias];
		return $this->findParentModel($result['model'], $result['foreign_key'], $query);
	}
	
	// Gets the Parent Model of the Comment
	public function getParentModel($id) {
		if ($result = $this->read('model', $id)) {
			return ClassRegistry::init($result[$this->alias]['model'], true);
		}
		return false;
	}
	
	// Finds the Parent result of a given model name and foreign key
	public function findParentModel($modelName, $foreignKey, $query = []) {
		if ($Model = ClassRegistry::init($modelName, true)) {
			$query['conditions'][$Model->escapeField($Model->primaryKey)] = $foreignKey;
			if ($result = $Model->find('first', $query)) {
				$result['modelInfo'] = [
					'alias' => $Model->alias,
					'primaryKey' => $Model->primaryKey,
					'displayField' => $Model->displayField,
				];
			}
			return $result;
		}
		return null;
	}
	
	//Stores the parent class name to make sure it gets saved
	public function setParentClassName($className) {
		$this->_parentClassName = $className;
	}
	
/**
 * Determines if a topic can be edited by the given commenter
 *
 **/
	public function isEditable($id, $commenterId = null, $isAdmin = false) {
		if ($isAdmin) {
			return true;
		} else {
			return $this->find('first', array(
				'conditions' => array(
					$this->escapeField('commenter_id') => $commenterId,
					$this->escapeField('id') => $id,
				)
			));
		}
	}
	
	// All commenters associated with a conversation of a given comment
	public function getCommentersAssociation($id) {
		$query = array(
			'recursive' => -1,
			'fields' => array($this->escapeField('foreign_key'), $this->escapeField('model')),
			'conditions' => array($this->escapeField('id') => $id),
		);
		$result = $this->find('first', $query);
		$Model = ClassRegistry::init($result[$this->alias]['model'], true);
		
		if ($additionalCommentersModel = $Model->getAdditionalCommentersModel()) {
			list($plugin, $additionalCommentersModel) = pluginSplit($additionalCommentersModel);
			$modelResult = $Model->find('first', array(
				'contain' => [$additionalCommentersModel],
				'conditions' => array(
					$Model->escapeField($Model->primaryKey) => $result[$this->alias]['foreign_key'],
				),
			));
		}

		$query = array(
			'fields' => ['Commenter.*'],
			'group' => $this->Commenter->escapeField($this->Commenter->primaryKey),
		);
		
		// Anyone commenting
		$query['joins'][] = array(
			'table' => $this->getTable(),
			'alias' => 'Comment',
			'type' => 'LEFT',
			'conditions' => array(
				'Comment.model' => $result[$this->alias]['model'],
				'Comment.foreign_key' => $result[$this->alias]['foreign_key'],
				sprintf('%s=%s', 
					'Comment.commenter_id',
					$this->Commenter->escapeField($this->Commenter->primaryKey)
				)
			)
		);
		$query['conditions']['OR'][]['NOT']['Comment.id'] = null;

		// Includes content creator (If specified in Behavior settings)
		if ($creatorField = $Model->getCommentCreatorField()) {
			$query['joins'][] = array(
				'table' => $Model->useTable,
				'type' => 'LEFT',
				'alias' => 'ModelCreator',
				'conditions' => array(
					$Model->escapeField($Model->primaryKey, 'ModelCreator') => $result[$this->alias]['foreign_key'],
					sprintf('%s=%s',
						$Model->escapeField($creatorField, 'ModelCreator'),
						$this->Commenter->escapeField($this->Commenter->primaryKey)
					)
				)
			);
			$query['conditions']['OR'][]['NOT']['ModelCreator.id'] = null;
		}
		
		// Includes Commenter IDs from an additional table specified in the Behavior settings
		if (!empty($additionalCommentersModel) && !empty($modelResult[$additionalCommentersModel])) {
			if (Hash::numeric(array_keys($modelResult[$additionalCommentersModel]))) {
				$extract = $additionalCommentersModel . '.{n}.commenter_id';
			} else {
				$extract = $additionalCommentersModel . '.commenter_id';
			}
			$additionalIds = Hash::extract($modelResult, $extract);
			if (!empty($additionalIds)) {
				$query['joins'][] = [
					'table' => $this->Commenter->useTable,
					'alias' => 'AdditionalCommenter',
					'type' => 'LEFT',
					'conditions' => [
						'AdditionalCommenter.id = Commenter.id',
						'AdditionalCommenter.id' => $additionalIds,
					]
				];
				$query['conditions']['OR'][]['NOT']['AdditionalCommenter.id'] = null;
			}		
		}
		return $query;
	}
	
	public function sendEmailUpdate($id, $blockCommenterIds = []) {
		$comment = $this->find('first', array(
			'contain' => ['Commenter'],
			'conditions' => array($this->escapeField() => $id)
		));
		
		$query = $this->getCommentersAssociation($id);
		
		// Makes sure the Commenter is not the one having sent the message
		$query['joins'][] = array(
			'table' => $this->getTable(),
			'alias' => 'NewComment',
			'type' => 'LEFT',
			'conditions' => [
				'NewComment.id' => $id,
				'Comment.model = NewComment.model',
				'Comment.foreign_key = NewComment.foreign_key',
			]
		);
		$query['conditions']['AND']['OR'] = [
			'NewComment.id' => null,
			'NOT' => ['NewComment.commenter_id = Commenter.id'],
		];
		
		// Makes sure they haven't turned off reply emails in their settings
		$query['joins'][] = [
			'table' => $this->Commenter->CommenterEmailControl->getTable(),
			'alias' => 'EmailControl',
			'type' => 'LEFT',
			'conditions' => ['Commenter.id = EmailControl.commenter_id'],
		];
		$query['conditions'][] = [
			'OR' => [
				'EmailControl.id' => null,
				'EmailControl.email_on_reply' => 1,
			]
		];

		if (!empty($blockCommenterIds)) {
			$query['conditions'][] = ['NOT' => ['Commenter.id' => $blockCommenterIds]];
		}

		$commenters = $this->Commenter->find('all', $query);

		App::uses('TalkBackEmail', 'TalkBack.Network/Email');
		$Email = new TalkBackEmail();
		foreach ($commenters as $commenter) {
			$to = $commenter[$this->Commenter->alias][Configure::read('TalkBack.Commenter.emailField')];
			if (empty($to)) {
				continue;
			}
			
			$Email->to($to);
			//TODO: Better human name
			$Email->subject('New comment posted in ' . Inflector::humanize($comment['Comment']['model']));
			$Email->helpers(['TalkBack.Comment', 'Layout.DisplayText']);
			$Email->viewVars(compact('commenter', 'comment'));
			$Email->template('TalkBack.new_comment');
			$Email->send();
			$Email->reset();
		}
		//exit();
	}
	
	private function parentCallback($method, $parentModel, $foreignKey, $id) {
		$args = func_get_args();
		$method = array_shift($args);
		$parentModel = array_shift($args);

		$Parent = ClassRegistry::init($parentModel, true);
		if (method_exists($Parent, $method)) {
			return call_user_func_array([$Parent, $method], $args);
		} else {
			return null;
		}
	}		
}