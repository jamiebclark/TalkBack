<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');

class CommentersCommenterType extends TalkBackAppModel {
	public $name = 'CommentersCommenterType';

	public $belongsTo = array(
		'Commenter' => array('className' => 'TalkBack.Commenter'),
		'CommenterType' => array('className' => 'TalkBack.CommenterType')
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->constructFromConfigure();
		return parent::__construct($id, $table, $ds);
	}
}