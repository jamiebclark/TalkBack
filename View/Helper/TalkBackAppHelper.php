<?php
class TalkBackAppHelper extends AppHelper {
	public $helpers = array('Html');
	
	private $_setAssets = false;
	
	public function beforeRender($viewFile = null) {
		if (!$this->_setAssets) {
			$this->Html->css('TalkBack.style', null, array('inline' => false));
			$this->Html->script('TalkBack.script.js', array('inline' => false));
			$this->_setAssets = true;
		}
		return parent::beforeRender($viewFile);
	}
	
	public function urlArray($url = array()) {
		$url += array(
			'controller' => null,
			'action' => null,
			'plugin' => 'talk_back',
		);
		if ($prefixes = Configure::read('Routing.prefixes')) {
			$url += array_fill_keys($prefixes, false);
		}
		return $url;
	}

	protected function urlPrefixReset($url = []) {
		if (!empty($this->request->params['prefix'])) {
			$url[$this->request->params['prefix']] = false;
		}
		return $url;
	}
}