<?php
class Channel extends TalkBackAppModel {
	public $name = 'Channel';
	public $hasMany = array(
		'TalkBack.Forum',
	);
	
	public $hasAndBelongsToMany = array(
		'Commenter' => array(
			'className' => 'TalkBack.Commenter',
			'foreignKey' => 'channel_id',
			'associationForeignKey' => 'commenter_id',
			'joinTable' => 'tb_channels_commenters',
		),
		'AdminCommenter' => array(
			'className' => 'TalkBack.Commenter',
			'foreignKey' => 'channel_id',
			'associationForeignKey' => 'commenter_id',
			'joinTable' => 'tb_channel_commenter_admins',
		),
		'CommenterType' => array(
			'className' => 'TalkBack.CommenterType',
			'foreignKey' => 'channel_id',
			'associationForeignKey' => 'commenter_type_id',
			'joinTable' => 'tb_channels_commenter_types',
		),
	);
	
	public function afterSave($created, $options = array()) {
		$this->setDefaultTitle($this->id);
		return parent::afterSave($created, $options);
	}

/**
 * Finds all associated channels with a Commenter
 * 
 * @param int $commenterId ID of Commenter
 * @param string $prefix The current page prefix
 * @return Array|null Array of Channel IDs if they exist, null if they do not
 **/
	public function findCommenterChannelIds($commenterId = null, $prefix = null) {
		if ($result = $this->findCommenterChannels($commenterId, $prefix)) {
			return Hash::extract($result, '{n}.Channel.id');
		} else {
			return null;
		}
	}
	
	public function getCommenterAssociation($commenterId = null, $prefix = null) {
		$query = array(
			'recursive' => -1,
			'group' => $this->escapeField('id'),
		);
		
		// Filters by prefix
		$query['conditions'][$this->escapeField('prefix')] = $prefix;
		
		// Filters by Commenter
		$query['joins'][] = array(
			'table' => 'tb_channels_commenters',
			'alias' => 'CommenterFilter',
			'type' => 'LEFT',
			'conditions' => array('CommenterFilter.channel_id = ' . $this->escapeField('id')),
		);
		$query['conditions']['OR'][]['CommenterFilter.commenter_id'] = null;
		if (!empty($commenterId)) {
			$query['conditions']['OR'][]['CommenterFilter.commenter_id'] = $commenterId;
		}
		
		// Filters by CommenterType
		$query['joins'][] = array(
			'table' => 'tb_channels_commenter_types',
			'alias' => 'CommenterTypeFilter',
			'type' => 'LEFT',
			'conditions' => array('CommenterTypeFilter.channel_id = ' . $this->escapeField('id')),
		);
		$query['conditions']['OR'][]['CommenterTypeFilter.id'] = null;	
		
		if (!empty($commenterId)) {
			if (!$this->CommenterType->isTree()) {
				$query['joins'][] = array(
					'table' => $this->CommenterType->getTable(),
					'alias' => 'CommenterType',
					'type' => 'LEFT',
					'conditions' => array('CommenterType.id = CommenterTypeFilter.commenter_type_id'),
				);
			} else {
				$query['joins'][] = array(
					'table' => $this->CommenterType->getTable(),
					'alias' => 'CommenterTypeParent',
					'type' => 'LEFT',
					'conditions' => array('CommenterTypeParent.id = CommenterTypeFilter.commenter_type_id'),
				);
				$query['joins'][] = array(
					'table' => $this->CommenterType->getTable(),
					'alias' => 'CommenterType',
					'type' => 'LEFT',
					'conditions' => array('CommenterType.lft BETWEEN CommenterTypeParent.lft AND CommenterTypeParent.rght')
				);
			}
			$query['joins'][] = array(
				'table' => 'tb_commenter_types_commenters',
				'alias' => 'CommenterCommenterType',
				'type' => 'LEFT',
				'conditions' => array(
					'CommenterCommenterType.commenter_type_id = CommenterType.id'
				)
			);
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
 * @return Array|null List of channels if they exist, null if they do not
 **/
	public function findCommenterChannels($commenterId = null, $prefix = null) {
		$query = $this->getCommenterAssociation($commenterId, $prefix);
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
		$result = $this->find('first', array(
			'recursive' => -1,
			'joins' => array(
				array(
					'table' => $this->hasAndBelongsToMany['AdminCommenter']['joinTable'],
					'alias' => 'AdminCommenter',
					'conditions' => array('AdminCommenter.channel_id = ' . $this->escapeField('id')),
				),
			),
			'conditions' => array(
				'AdminCommenter.commenter_id' => $commenterId,
				$this->escapeField('id') => $id,
			)
		));
		return !empty($result);
	}
	
	// Creates a default title for the channel if none exists
	private function setDefaultTitle($id) {
		$result = $this->read(array('title', 'prefix'), $id);
		$result = $result[$this->alias];
		
		//Sets default title
		if (empty($result['title'])) {
			$title = !empty($result['prefix']) ? $result['prefix'] : 'public';
			return $this->save(array('title' => Inflector::humanize($title), 'id' => $id), array('callbacks' => false));
		}
		return null;	
	}
}