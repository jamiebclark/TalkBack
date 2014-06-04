<?php
App::uses('CakeEmail', 'Network/Email');
class TalkBackEmail extends CakeEmail {
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->talkBackInit();
	}
	
	public function reset() {
		$return = parent::reset();
		$this->talkBackInit();
		return $return;
	}
	
	private function talkBackInit() {
		//Pre-loads information from the config
		if (Configure::read('TalkBack.email.from')) {
			$this->from(Configure::read('TalkBack.email.from'));
		}
		if (Configure::read('TalkBack.email.config')) {
			$this->config(Configure::read('TalkBack.email.config'));
		}
	}
}
