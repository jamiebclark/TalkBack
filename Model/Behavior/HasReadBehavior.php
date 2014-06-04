<?php
App::uses('TalkBackBehavior', 'TalkBack.Model/Behavior');
class HasReadBehavior extends TalkBackBehavior {
	public $name = 'HasRead';
	
	public function setup(Model $Model, $settings = array()) {
		$default = array();
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $default;
		}
		$this->settings[$Model->alias] = array_merge(
			$default, 
			(array) $this->settings[$Model->alias]
		);
				
		$this->_bindModels($Model);
		return parent::setup($Model, $settings);		
	}
	
	public function afterSave(Model $Model, $created, $settings = array()) {
		return parent::afterSave($Model, $created, $settings);		
	}

	#section Public methods
	//Stores the current commenter id
	public function setCurrentCommenter(Model $Model, $commenterId) {
		$this->settings[$Model->alias]['currentCommenterId'] = $commenterId;
		// Binds the model to the Read History table, 
		// but narrowing it down to only the reading history of the current commenter
		$alias = 'CurrentCommenterHasRead';
		$Model->bindModel(array(
			'hasOne' => array(
				'CurrentCommenterHasRead' => array(
					'className' => 'TalkBack.CommenterHasRead',
					'foreignKey' => 'foreign_key',
					'conditions' => array(
						'CurrentCommenterHasRead.model' => $this->getPluginClassName($Model),
						'CurrentCommenterHasRead.commenter_id' => $commenterId,
					)
				)
			)
		), false);
		return parent::setCurrentCommenter($Model, $commenterId);
	}
	
	public function markUnread(Model $Model, $id, $commenterId = null) {
		if (empty($commenterId)) {
			if (!empty($this->settings[$Model->alias]['currentCommenterId'])) {
				$commenterId = $this->settings[$Model->alias]['currentCommenterId'];
			} else {
				return null;
			}
		}
		return $Model->CommenterHasRead->deleteAll($this->_conditions($Model, $id, $commenterId), false, false);
	}
	
	public function markRead(Model $Model, $id, $commenterId = null) {
		if (empty($commenterId)) {
			if (!empty($this->settings[$Model->alias]['currentCommenterId'])) {
				$commenterId = $this->settings[$Model->alias]['currentCommenterId'];
			} else {
				return null;
			}
		}
		
		// Makes sure it hasn't already been marked as read
		$result = $Model->find('all', array(
			'fields' => array($Model->escapeField($Model->primaryKey)),
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => $this->_getTableName($Model->CommenterHasRead),
					'alias' => 'CommenterHasRead',
					'type' => 'LEFT',
					'conditions' => array(
						'CommenterHasRead.model' => $this->getPluginClassName($Model),
						'CommenterHasRead.foreign_key = ' . $Model->escapeField($Model->primaryKey),
						'CommenterHasRead.commenter_id' => $commenterId,
					)
				)
			),
			'conditions' => array(
				$Model->escapeField($Model->primaryKey) => $id,
				'CommenterHasRead.id' => NULL,
			)
		));
		
		if (!empty($result)) {
			$data = array();
			foreach ($result as $row) {
				$data[] = array(	
					'model' => $this->getPluginClassName($Model),
					'foreign_key' => $row[$Model->alias]['id'],
					'commenter_id' => $commenterId,
				);
			}
			$Model->CommenterHasRead->create();
			return $Model->CommenterHasRead->saveAll($data);
		}
		return true;
	}
		
	public function isRead(Model $Model, $id, $commenterId = null) {
		if ($result = $Model->CommenterHasRead->find('first', $this->_query($Model, $id, $commenterId))) {
			return $result['CommenterHasRead']['id'];
		}
		return false;
	}
	#endsection
	
	private function _query($Model, $id, $commenterId, $query = array()) {
		return Hash::merge(array('conditions' => $this->_conditions($Model, $id, $commenterId)), $query);
	}
	
	private function _conditions($Model, $id, $commenterId, $conditions = array()) {
		$conditions[] = array(
			'CommenterHasRead.model' => $this->getPluginClassName($Model),
			'CommenterHasRead.foreign_key' => $id,
			'CommenterHasRead.commenter_id' => $commenterId,
		);
		return $conditions;
	}
	
	// Completes appropriate binding of models
	private function _bindModels($Model) {
		$Model->bindModel(array(
			'hasMany' => array(
				'CommenterHasRead' => array(
					'className' => 'TalkBack.CommenterHasRead',
					'foreignKey' => 'foreign_key',
					'conditions' => array('CommenterHasRead.model' => $this->getPluginClassName($Model)),
				)
			)
		), false);
		$className = $Model->name;
		if (!empty($Model->plugin)) {
			$className = $Model->plugin . '.' . $className;
		}
		$Model->CommenterHasRead->bindModel(array(
			'belongsTo' => array(
				$Model->name => array(
					'className' => $className,
					'foreignKey' => 'foreign_key',
					'conditions' => array('CommenterHasRead.model' => $this->getPluginClassName($Model))
				)
			)
		), false);
	}
	private function _getTableName($Model) {
		$tableName = $Model->useTable;
		if (!empty($Model->tablePrefix)) {
			$tableName = $Model->tablePrefix . $tableName;
		}
		return $tableName;
	}
}