<?php
App::uses('TalkBackBehavior', 'TalkBack.Model/Behavior');
class HasReadBehavior extends TalkBackBehavior {
	public $name = 'HasRead';
	
	public function setup(Model $Model, $settings = []) {
		$default = [
			'relatedUnread' => null,		// Automatically tally the unread count for a related model
		];
		if (!isset($this->settings[$Model->alias])) {
			$this->settings[$Model->alias] = $default;
		}
		$this->settings[$Model->alias] = array_merge(
			(array) $this->settings[$Model->alias],
			(array) $settings			
		);

		$this->_bindModels($Model);
		return parent::setup($Model, $settings);		
	}
	
	public function afterSave(Model $Model, $created, $settings = []) {
		return parent::afterSave($Model, $created, $settings);		
	}
	
	public function beforeFind(Model $Model, $query = []) {
		$relatedUnread = $this->settings[$Model->alias]['relatedUnread'];
		if (isset($query['relatedUnread'])) {
			$relatedUnread = $query['relatedUnread'];
		}
		if (!empty($relatedUnread)) {
			if (is_object($Model->{$relatedUnread})) {
				$RelatedModel = $Model->{$relatedUnread};
			} else {
				$RelatedModel = ClassRegistry::init($Model->{$relatedUnread}, true);
			}
			if (empty($RelatedModel)) {
				throw new Exception('Could not find association of ' . $relatedUnread);
			}
			$query = $Model->getRelatedUnreadAssociation($RelatedModel, $query);
			unset($query['relatedUnread']);
		}
		return $query;
	}

	public function getRelatedUnreadAssociation(Model $Model, Model $RelatedModel, $query = [], $currentCommenterId = null) {
		// Skips this if related model is TalkBack.Comment and "hasRead" has been turned off in its settings
		if ($RelatedModel->alias == 'Comment' && $Model->hasMethod('isCommentHasRead') && !$Model->isCommentHasRead()) {
			return $query;
		}
		if (!$RelatedModel->hasMethod('getUnreadSubquery')) {
			throw new Exception('Cannot get related unread, ' . $RelatedModel->alias . ' must have the HasReadBehavior loaded');
		}
		$alias = 'RelatedUnread';
		$subQuery = $RelatedModel->getUnreadSubquery([], $currentCommenterId);
		$join = ['table' => "($subQuery)", 'alias' => $alias, 'type' => 'LEFT'];
		// Find relation
		if ((!$conditions = $this->findModelRelationCondition($Model, $RelatedModel, null, $alias))) {
			// Sometimes relationship will be stripped due to Containable, so check from the other direction
			$conditions = $this->findModelRelationCondition($RelatedModel, $Model, $alias, null);
		}
		
		if (empty($conditions)) {
			throw new Exception(sprintf('Could not find the relation between %s and %s', $Model->alias, $RelatedModel->alias));
		}
		
		$join['conditions'] = $conditions;
		$query['joins'][] = $join;
		$query['group'] = $Model->escapeField($Model->primaryKey);
		
		if (empty($query['fields'])) {
			$query['fields'][] = '*';
		} else if (!is_array($query['fields'])) {
			$query['fields'] = array($query['fields']);
		}
		$query['fields'][] = "SUM($alias.is_unread) AS total_unread";
		$query['fields'][] = "SUM($alias.is_read) AS total_read";
		
		return $query;
	}
	
	// Generates a sub-query using the association information with a model's read status
	public function getUnreadSubquery(Model $Model, $query = [], $currentCommenterId = null, $alias = null) {
		$db = $Model->getDataSource();
		return $db->buildStatement($Model->getUnreadAssociation(array_merge([
			'fields' => [$Model->alias . '.*'],
			'table' => $db->fullTableName($Model),
			'alias' => $Model->alias,
			'group' => $Model->escapeField($Model->primaryKey),
		], $query), $currentCommenterId, $alias), $Model);
	}
	
	// Finds the association with where a given commenter has viewed the current model results
	public function getUnreadAssociation(Model $Model, $query = [], $currentCommenterId = null, $alias = null) {
		if (empty($alias)) {
			$alias = $Model->alias;
		}
		if (!empty($currentCommenterId)) {
			$Model->setCurrentCommenter($currentCommenterId);
		}
		if (!empty($this->_currentCommenterId)) {
			$commenterId = $this->_currentCommenterId;
			if (empty($query['fields'])) {
				$query['fields'][] = '*';
			}
			$query['fields'][] = 'IF(HasRead.id IS NULL, 1, 0) AS is_unread';
			$query['fields'][] = 'IF(HasRead.id IS NOT NULL, 1, 0) AS is_read';
			
			$query['joins'][] = [
				'table' => $Model->CommenterHasRead->getTable(),
				'alias' => 'HasRead',
				'type' => 'LEFT',
				'conditions' => [
					'HasRead.model' => $this->getPluginClassName($Model),
					'HasRead.foreign_key = ' . $Model->escapeField($Model->primaryKey),
					'HasRead.commenter_id' => $commenterId,
				]
			];
		}
		return $query;
	}
	
	public function findUnread(Model $Model, $query = [], $commenterId = null) {
		if (!empty($this->_currentCommenterId)) {
			$commenterId = $this->_currentCommenterId;
			$query['fields'][] = 'COUNT(DISTINCT(' . $Model->escapeField($Model->primaryKey) . ')) AS `total_unread`';
			$query['joins'][] = [
				'table' => $Model->CommenterHasRead->getTable(),
				'alias' => 'HasRead',
				'type' => 'LEFT',
				'conditions' => [
					'HasRead.model' => $this->getPluginClassName($Model),
					'HasRead.foreign_key = ' . $Model->escapeField($Model->primaryKey),
					'HasRead.commenter_id' => $commenterId,
				]
			];
			return $Model->find('all', $query);
		}
		return null;
	}
	
	#section Public methods
	//Stores the current commenter id
	public function setCurrentCommenter(Model $Model, $commenterId) {
		$this->_currentCommenterId = $commenterId;
		// Binds the model to the Read History table, 
		// but narrowing it down to only the reading history of the current commenter
		$alias = 'CurrentCommenterHasRead';
		$Model->bindModel([
			'hasOne' => [
				'CurrentCommenterHasRead' => [
					'className' => 'TalkBack.CommenterHasRead',
					'foreignKey' => 'foreign_key',
					'conditions' => [
						'CurrentCommenterHasRead.model' => $this->getPluginClassName($Model),
						'CurrentCommenterHasRead.commenter_id' => $commenterId,
					]
				]
			]
		], false);
		return parent::setCurrentCommenter($Model, $commenterId);
	}
	
	public function markUnread(Model $Model, $id, $commenterId = null) {
		if (empty($commenterId)) {
			if (!empty($this->_currentCommenterId)) {
				$commenterId = $this->_currentCommenterId;
			} else {
				return null;
			}
		}
		return $Model->CommenterHasRead->deleteAll($this->_conditions($Model, $id, $commenterId), false, false);
	}
	
	public function markRead(Model $Model, $id, $commenterId = null) {
		if (empty($commenterId)) {
			if (!empty($this->_currentCommenterId)) {
				$commenterId = $this->_currentCommenterId;
			} else {
				return null;
			}
		}
		
		// Makes sure it hasn't already been marked as read
		$result = $Model->find('all', [
			'fields' => [$Model->escapeField($Model->primaryKey)],
			'recursive' => -1,
			'joins' => [[
					'table' => $this->_getTableName($Model->CommenterHasRead),
					'alias' => 'CommenterHasRead',
					'type' => 'LEFT',
					'conditions' => [
						'CommenterHasRead.model' => $this->getPluginClassName($Model),
						'CommenterHasRead.foreign_key = ' . $Model->escapeField($Model->primaryKey),
						'CommenterHasRead.commenter_id' => $commenterId,
					]
				]],
			'conditions' => [
				$Model->escapeField($Model->primaryKey) => $id,
				'CommenterHasRead.id' => NULL,
			]
		]);
		
		if (!empty($result)) {
			$data = [];
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
	
	private function _query($Model, $id, $commenterId, $query = []) {
		return Hash::merge(array('conditions' => $this->_conditions($Model, $id, $commenterId)), $query);
	}
	
	private function _conditions($Model, $id, $commenterId, $conditions = []) {
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

	private function findModelRelationCondition(Model $Model1, Model $Model2, $alias1 = null, $alias2 = null) {
		$relation = null;
		foreach (['hasMany', 'hasOne', 'belongsTo'] as $relationType) {
			if (isset($Model1->{$relationType}[$Model2->alias])) {
				$relation = $Model1->{$relationType}[$Model2->alias];
				break;
			}
		}
		if (empty($relation)) {
			return null;
		} 
		
		if ($relationType == 'belongsTo') {
			$conditions = [sprintf('%s = %s', 
				$Model1->escapeField($relation['foreignKey'], $alias1),
				$Model2->escapeField($Model2->primaryKey, $alias2)
			)];
		} else {
			$conditions = [sprintf('%s = %s', 
				$Model2->escapeField($relation['foreignKey'], $alias2),
				$Model1->escapeField($Model1->primaryKey, $alias1)
			)];
		}
		
		$replace = [];
		if (!empty($alias1)) {
			$replace[$Model1->alias] = $alias1;
		}
		if (!empty($alias2)) {
			$replace[$Model2->alias] = $alias2;
		}
		$find = array_keys($replace);
		if (!empty($relation['conditions'])) {
			foreach ($relation['conditions'] as $key => $val) {
				if (!empty($replace)) {
					if (is_numeric($key)) {
						$val = str_replace($find, $replace, $val);
					} else {
						$key = str_replace($find, $replace, $key);
					}
				}
			}
			$conditions[$key] = $val;
		}
		return $conditions;
	}
}