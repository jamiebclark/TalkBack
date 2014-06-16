<?php
class TalkBackAppController extends AppController {
	public $components = [
		'TalkBack.Commentable',
		'TalkBack.CurrentCommenter',
		
		'FormData.FindModel' => ['plugin' => 'TalkBack'],
		'FormData.FormData' => ['plugin' => 'TalkBack']
	];
	
	//public $helpers = array('TalkBack.TalkBack');
	protected $tb_prefix;

	private $_validationRedirectMethods = [];
	
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
	
	// Quickly finds the page prefix if it exists, or returns null if it doesn't
	protected function getPrefix() {
		return !empty($this->request->params['prefix']) ? $this->request->params['prefix'] : null;
	}
	
	// Makes sure that the given item matches up with the required prefix
	protected function prefixRedirect($id = null) {
		if (empty($this->request->params['prefix']) && $this->{$this->modelClass}->hasMethod('getPrefix')) {
			if ($prefix = $this->{$this->modelClass}->getPrefix($id)) {
				$url = [];
				foreach (['plugin', 'controller', 'action'] as $k) {
					if (!empty($this->request->params[$k])) {
						$url[$k] = $this->request->params[$k];
					}
				}
				foreach (array('pass', 'named') as $k) {
					if (!empty($this->request->params[$k])) {
						$url += $this->request->params[$k];
					}
				}					
				$url[$prefix] = true;
				$this->redirect($url);
			}
		}
		return true;
	}
	
	protected function validateRedirect($type, $args = []) {
		if (is_array($type)) {
			foreach ($type as $subType => $args) {
				if (is_numeric($subType)) {
					$subType = $args;
					$args = [];
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
				case 'permission':
					//Looks for a function called "isCommenterAllowed" to check for permission on current page
					if ($this->{$this->modelClass}->hasMethod('isCommenterAllowed')) {
						if (!$this->{$this->modelClass}->isCommenterAllowed(
							$args[0],
							$this->CurrentCommenter->getId(),
							$this->getPrefix()
						)) {
							$msg = 'Sorry, you don\'t have permission to view this ' . $this->modelClass;
							$redirect = true;
						}
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