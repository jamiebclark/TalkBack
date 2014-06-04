<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');

class Forum extends TalkBackAppModel {
	public $name = 'Forum';
	public $hasMany = array('TalkBack.Topic');
	public $belongsTo = array(
		'Channel' => array(
			'className' => 'TalkBack.Channel',
			'counterCache' => true,
		)
	);
	
/**
 * Determines if a given Commenter ID is an Administrator of the current forum
 * 
 * @param int $id ID of the Forum
 * @param int $commenterId ID of the Commenter
 * @return bool True on success
 **/
	public function isCommenterAdmin($id, $commenterId) {
		$result = $this->read('channel_id', $id);
		return $this->Channel->isCommenterAdmin($result[$this->alias]['channel_id'], $commenterId);
	}
}