<?php
class Channel extends TalkBackAppModel {
	public $name = 'Channel';
	public $hasMany = ['TalkBack.Forum'];
	
	public $hasAndBelongsToMany = [
		'Commenter' => [
			'className' => 'TalkBack.Commenter',
			'foreignKey' => 'channel_id',
			'associationForeignKey' => 'commenter_id',
			'joinTable' => 'tb_channels_commenters',
		],
		'AdminCommenter' => [
			'className' => 'TalkBack.Commenter',
			'foreignKey' => 'channel_id',
			'associationForeignKey' => 'commenter_id',
			'joinTable' => 'tb_channel_commenter_admins',
		],
		'CommenterType' => [
			'className' => 'TalkBack.CommenterType',
			'foreignKey' => 'channel_id',
			'associationForeignKey' => 'commenter_type_id',
			'joinTable' => 'tb_channels_commenter_types',
		],
	];
	
	public function afterSave($created, $options = []) {
		$this->setDefaultTitle($this->id);
		return parent::afterSave($created, $options);
	}

/**
 * Finds all associated channels with a Commenter
 * 
 * @param int $commenterId ID of Commenter
 * @param string $prefix The current page prefix
 * @param Array $query Additional find parameters
 * @return Array|null Array of Channel IDs if they exist, null if they do not
 **/
	public function findCommenterChannelIds($commenterId = null, $prefix = null, $query = []) {
		if ($result = $this->findCommenterChannels($commenterId, $prefix, $query)) {
			return Hash::extract($result, '{n}.Channel.id');
		} else {
			return null;
		}
	}
	
	public function getCommenterAssociation($commenterId = null, $prefix = null) {
		$query = [
			'recursive' => -1,
			'group' => $this->escapeField('id'),
		];
		
		// Filters by prefix
		$query['conditions'][$this->escapeField('prefix')] = $prefix;
		
		// Filters by Commenter
		$query['joins'][] = [
			'table' => 'tb_channels_commenters',
			'alias' => 'CommenterFilter',
			'type' => 'LEFT',
			'conditions' => ['CommenterFilter.channel_id = ' . $this->escapeField('id')],
		];
		$query['conditions']['OR'][]['CommenterFilter.commenter_id'] = null;
		if (!empty($commenterId)) {
			$query['conditions']['OR'][]['CommenterFilter.commenter_id'] = $commenterId;
		}
		
		// Filters by CommenterType
		$query['joins'][] = [
			'table' => 'tb_channels_commenter_types',
			'alias' => 'CommenterTypeFilter',
			'type' => 'LEFT',
			'conditions' => ['CommenterTypeFilter.channel_id = ' . $this->escapeField('id')],
		];
		$query['conditions']['OR'][]['CommenterTypeFilter.id'] = null;	
		
		if (!empty($commenterId)) {
			if (!$this->CommenterType->isTree()) {
				$query['joins'][] = [
					'table' => $this->CommenterType->getTable(),
					'alias' => 'CommenterType',
					'type' => 'LEFT',
					'conditions' => ['CommenterType.id = CommenterTypeFilter.commenter_type_id'],
				];
			} else {
				$query['joins'][] = [
					'table' => $this->CommenterType->getTable(),
					'alias' => 'CommenterTypeParent',
					'type' => 'LEFT',
					'conditions' => ['CommenterTypeParent.id = CommenterTypeFilter.commenter_type_id'],
				];
				$query['joins'][] = [
					'table' => $this->CommenterType->getTable(),
					'alias' => 'CommenterType',
					'type' => 'LEFT',
					'conditions' => ['CommenterType.lft BETWEEN CommenterTypeParent.lft AND CommenterTypeParent.rght']
				];
			}
			$query['joins'][] = [
				'table' => 'tb_commenter_types_commenters',
				'alias' => 'CommenterCommenterType',
				'type' => 'LEFT',
				'conditions' => ['CommenterCommenterType.commenter_type_id = CommenterType.id']
			];
			$query['conditions']['OR'][]['CommenterCommenterType.commenter_id'] = $commenterId;
		} else {
			if (!empty($this->CommenterType->allCommentersId)) {
				$query['conditions']['OR'][]['CommenterTypeFilter.commenter_type_id'] = $this->CommenterType->allCommentersId;
			}
		}
		return $query;	
	}
	
/**
 * Finds all associated channels with a Commenter
 * 
 * @param int $commenterId ID of Commenter
 * @param string $prefix The current page prefix
 * @param array $query Additional query options
 * @return Array|null List of channels if they exist, null if they do not
 **/
	public function findCommenterChannels($commenterId = null, $prefix = null, $query = []) {
		$query = Hash::merge($query, $this->getCommenterAssociation($commenterId, $prefix));
		return $this->find('all', $query);
	}

/**
 * Determines if a given Commenter ID has sufficient permissions to view a Channel
 * 
 * @param int $id ID of the Channel
 * @param int $commenterId ID of the Commenter
 * @param string|null $prefix The current page Prefix
 * @return bool True on success
 **/
	public function isCommenterAllowed($id, $commenterId = null, $prefix = null) {
		if ($channelIds = $this->findCommenterChannelIds($commenterId, $prefix)) {
			return in_array($id, $channelIds);
		}
		return null;
	}
	
/**
 * Determines if a given Commenter ID is an Administrator of the current channel
 * 
 * @param int $id ID of the Channel
 * @param int $commenterId ID of the Commenter
 * @return bool True on success
 **/
	public function isCommenterAdmin($id, $commenterId) {
		$result = $this->find('first', [
			'recursive' => -1,
			'joins' => [[
				'table' => $this->hasAndBelongsToMany['AdminCommenter']['joinTable'],
				'alias' => 'AdminCommenter',
				'conditions' => ['AdminCommenter.channel_id = ' . $this->escapeField('id')],
			]],
			'conditions' => [
				'AdminCommenter.commenter_id' => $commenterId,
				$this->escapeField('id') => $id,
			]
		]);
		return !empty($result);
	}
	
	// Creates a default title for the channel if none exists
	private function setDefaultTitle($id) {
		$result = $this->read(['title', 'prefix'], $id);
		$result = $result[$this->alias];
		
		//Sets default title
		if (empty($result['title'])) {
			$title = !empty($result['prefix']) ? $result['prefix'] : 'public';
			return $this->save(['title' => Inflector::humanize($title), 'id' => $id], ['callbacks' => false]);
		}
		return null;	
	}

	public function setCurrentCommenter($commenterId = null) {
//		$result = parent::setCurrentCommenter($commenterId);
		return $this->Forum->setCurrentCommenter($commenterId);
	}
}