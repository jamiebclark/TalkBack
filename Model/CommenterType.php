<?php

App::uses('TalkBackAppModel', 'TalkBack.Model');
class CommenterType extends TalkBackAppModel {
	public $name = 'CommenterType';
	public $hasMany = array(
		'CommentersCommenterType' => array(
			'className' => 'TalkBack.CommentersCommenterType',
		)
	);
	public $hasAndBelongsToMany = array(
		'Commenter' => array(
			'className' => 'TalkBack.Commenter',
			'joinTable' => 'tb_commenter_types_commenters',
			'foreignKey' => 'commenter_type_id',
			'associationForeignKey' => 'commenter_id',
		),
		'Channel' => array(
			'className' => 'TalkBack.Channel',
			'foreignKey' => 'commenter_type_id',
			'associationForeignKey' => 'channel_id',
			'joinTable' => 'tb_channels_commenter_types',
		),
		'Forum' => array(
			'className' => 'TalkBack.Forum',
			'foreignKey' => 'commenter_type_id',
			'associationForeignKey' => 'forum_id',
			'joinTable' => 'tb_forums_commenter_types',
		),
	);
	
	const COMMENTER_TYPE_FILTER_ALIAS = 'CommenterTypeFilter';

	private $hasConfigure = false;		// Whether there exists a CommenterType model
	
	// If the custom CommenterType has a root node that includes non-commenters, include it here
	public $allCommentersId = false;
	
	public function __construct($id = false, $table = null, $ds = null) {
		$this->hasConfigure = $this->constructFromConfigure();
		if ($config = Configure::read('TalkBack.CommentersCommenterType')) {
			$this->bindModel(array(
				'hasMany' => array(
					'CommentersCommenterType' => array(
						'className' => 'TalkBack.CommentersCommenterType',
						'foreignKey' => $config['commenterTypeForeignKey'],
					)
				),
				'hasAndBelongsToMany' => array(
					'Commenter' => array(
						'joinTable' => $this->CommentersCommenterType->getTable(),
						'foreignKey' => $config['commenterTypeForeignKey'],
						'associationForeignKey' => $config['commenterForeignKey'],
					)
				)
			), false);
		}

		return parent::__construct($id, $table, $ds);
	}	
	
	// Returns false if the user chose not to specify a commenter type
	public function hasCommenterType() {
		return $this->hasConfigure;
	}
	
	public function isTree() {
		return $this->schema('lft') && $this->schema('rght');
	}

	public function joinCommenter($modelName, $commenterId, $query = array(), $options = array()) {
		$conditions = array();
		$habtm = $this->{$modelName}->hasAndBelongsToMany[$this->alias];

		if (!empty($commenterId)) {
			$query = $this->joinToModel($modelName, $query, $options);
			$query['joins'][] = [
				'table' => $this->CommentersCommenterType->getTable(),
				'alias' => 'CommentersCommenterType',
				'type' => 'LEFT',
				'conditions' => [$this->hasMany['CommentersCommenterType']['foreignKey'] . ' = CommenterType.id']
			];
			$conditions['OR'][]['CommentersCommenterType.' . $this->hasMany['CommentersCommenterType']['foreignKey']] = $commenterId;
		} else {
			if (!empty($this->allCommentersId)) {
				$conditions['OR'][][self::COMMENTER_TYPE_FILTER_ALIAS . '.' . $habtm['associationForeignKey']] = $this->allCommentersId;
			}
		}
		$query['conditions'][] = $conditions;
		return $query;
	}

/**
 * Links a model to the CommenterType
 * 
 * @param string $modelName The given modelName
 * @param array $query The given find query
 * @param array $options Additional options
 * @return array The adjusted query array
 **/
	public function joinToModel($modelName, $query = array(), $options = array()) {
		$habtm = $this->{$modelName}->hasAndBelongsToMany[$this->alias];

		$options = array_merge(array(
			'table' => $habtm['joinTable'],
			'conditions' => $this->escapeField($habtm['foreignKey'], self::COMMENTER_TYPE_FILTER_ALIAS) . '=' . $this->{$modelName}->escapeField(),
			'exclusive' => false,
		), $options);

		extract($options);

		$alias = self::COMMENTER_TYPE_FILTER_ALIAS;

		// Filters by HABTM intermediate table
		$query['joins'][] = [
			'table' => $table,
			'alias' => $alias,
			'type' => $exclusive ? 'INNER' : 'LEFT',
			'conditions' => $conditions,
		];

		if (!$exclusive) {
			$query['conditions']['OR'][]["$alias.id"] = null;
		}

		$foreignKey = Configure::read('TalkBack.CommentersCommenterType.commenterTypeForeignKey');

		if (!$this->isTree()) {
			$query['joins'][] = [
				'table' => $this->getTable(),
				'alias' => 'CommenterType',
				'type' => 'LEFT',
				'conditions' => ["CommenterType.id = $alias." . $habtm['associationForeignKey']],
			];
		} else {
			$query['joins'][] = [
				'table' => $this->getTable(),
				'alias' => 'CommenterTypeParent',
				'type' => 'LEFT',
				'conditions' => ["CommenterTypeParent.id = $alias." . $habtm['associationForeignKey']],
			];
			$query['joins'][] = [
				'table' => $this->getTable(),
				'alias' => 'CommenterType',
				'type' => 'LEFT',
				'conditions' => ['CommenterType.lft BETWEEN CommenterTypeParent.lft AND CommenterTypeParent.rght']
			];
		}
		return $query;
	}
}