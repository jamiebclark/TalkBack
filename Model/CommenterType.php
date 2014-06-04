<?php

App::uses('TalkBackAppModel', 'TalkBack.Model');
class CommenterType extends TalkBackAppModel {
	public $name = 'CommenterType';
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
		
	);
	
	private $hasConfigure = false;		// Whether there exists a CommenterType model
	
	// If the custom CommenterType has a root node that includes non-commenters, include it here
	public $allCommentersId = false;
	
	public function __construct($id = false, $table = null, $ds = null) {
		$this->hasConfigure = $this->constructFromConfigure();
		return parent::__construct($id, $table, $ds);
	}	
	
	// Returns false if the user chose not to specify a commenter type
	public function hasCommenterType() {
		return $this->hasConfigure;
	}
	
	public function isTree() {
		return $this->schema('lft') && $this->schema('rght');
	}
}