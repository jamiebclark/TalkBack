<?php
App::uses('TalkBackAppModel', 'TalkBack.Model');

class CommentersCommenterType extends TalkBackAppModel {
	public $name = 'CommentersCommenterType';

	public $useTable = 'commenter_types_commenters';
	
	public $belongsTo = array(
		'Commenter' => ['className' => 'TalkBack.Commenter'],
		'CommenterType' => ['className' => 'TalkBack.CommenterType']
	);

	public function __construct($id = false, $table = null, $ds = null) {
		$this->constructFromConfigure();
		return parent::__construct($id, $table, $ds);
	}
}