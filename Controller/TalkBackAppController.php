<?php
class TalkBackAppController extends AppController {
	public $components = array(
		'TalkBack.Commentable',
		'TalkBack.CurrentCommenter',
		
		'FormData.FindModel' => array('plugin' => 'TalkBack'),
		'FormData.FormData' => array(
			'plugin' => 'TalkBack',
		)
	);
	
	//public $helpers = array('TalkBack.TalkBack');
	protected $tb_prefix;

	private $_validationRedirectMethods = array();
	
	public function beforeFilter() {
		$this->savePrefix();
		$this->CurrentCommenter->setAdmin();
		return parent::beforeFilter();
	}
	
	public function beforeRender() {
		$return = parent::beforeRender();
		if ($this->tb_prefix == 'admin') {
			$this->layout = 'admin';
		}
		return $return;
	}
	
	private function savePrefix() {
		if (!empty($this->request->params['prefix'])) {
			$this->tb_prefix = $this->request->params['prefix'];
			if (!method_exists($this, $this->request->params['action'])) {
				$action = substr($this->request->params['action'], strlen($this->tb_prefix) + 1);
				if (method_exists($this, $action)) {
					$this->view = $action;
					$this->request->params['action'] = $action;
				}
			}
		}
	}

	
	protected function setValidateRedirectMethod($name, $method) {
		$this->_validationRedirectMethods[$name] = $method;
	}
	
	protected function validateRedirect($type, $args = array()) {
		if (is_array($type)) {
			foreach ($type as $subType => $args) {
				if (is_numeric($subType)) {
					$subType = $args;
					$args = array();
				}
				$this->validateRedirect($subType, $args);
			}
		} else {
			switch ($type) {
				case 'login':
					if (!$this->Auth->loggedIn()) {
						$msg = 'Please log in first';
						$redirect = $this->Auth->redirectUrl();
					}
				break;
				default:
					if (!empty($this->_validationRedirectMethods[$type])) {
						$return = call_user_func($this->_validationRedirectMethods[$type], $args);
						foreach (array('msg', 'redirect') as $key) {
							if (!empty($return[$key])) {
								$$key = $return[$key];
							}
						}
					}
				break;
			}
		
			if (!empty($msg)) {
				$this->Session->setFlash($msg);
			}
			if (!empty($redirect)) {
				if ($redirect === true) {
					$redirect = $this->referer();
				}
				$this->redirect($redirect);
			}
		}
		return true;
	}	
}