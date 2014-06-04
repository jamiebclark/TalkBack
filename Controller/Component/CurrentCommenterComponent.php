<?php
App::uses('Component', 'Controller');
class CurrentCommenterComponent extends Component {
	public $name = 'CurrentCommenter';
	public $controller;
	
	public $components = array('Auth');
	
	private $_commenterId = null;
	private $_commenter = null;
	private $_isAdmin = false;
	
	public function initialize(Controller $controller) {
		$this->setController($controller);
		return parent::initialize($controller);
	}
	
	public function beforeRender(Controller $controller) {
		$this->_commenter[0]['loginAction'] = $this->Auth->loginAction;
		$controller->set('currentCommenter', $this->_commenter);
		return parent::beforeRender($controller);
	}
	
	public function setController($controller) {
		$this->controller = $controller;
		$this->checkAuth();
	}
	
	public function checkAuth() {
		if (isset($this->controller->Auth)) {
			$this->set($this->controller->Auth->user('id'));
		}
	}
	
	public function set($commenterId, $reset = false) {
		if ($reset || $commenterId != $this->_commenterId) {
			if (!($currentCommenter = ClassRegistry::init('TalkBack.Commenter')->read(null, $commenterId))) {
				$commenterId = null;
			}
			
			$this->_commenterId = $commenterId;
			$this->controller->tb_currentCommenterId = $commenterId;

			$this->_commenter = $currentCommenter;
			$this->controller->curentCommenter = $currentCommenter;
			$this->controller->set(compact('currentCommenter'));

			$this->setAdmin($this->_isAdmin);
			
			$Model = ClassRegistry::init('TalkBack.Comment', true);
			if ($Model->hasMethod('setCurrentCommenter')) {
				$Model->setCurrentCommenter($commenterId);
			}
		}
	}
	
	public function getId() {
		return $this->_commenterId;
	}
	
	public function get() {
		return $this->controller->currentCommenter;
	}
	
	public function setAdmin($val = true) {
		if (empty($this->_commenterId)) {
		//	throw new Exception('Cannot set Current Commenter admin status without first setting the commenter ID');
		}
		$this->_isAdmin = $val;
		$this->controller->tb_currentControllerIsAdmin = $val;
		
		if (!empty($this->_commenter)) {
			$this->_commenter[0]['isAdmin'] = $val;
		}
	}
	
	public function isAdmin() {
		return $this->_isAdmin;
	}
	
}