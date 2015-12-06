<?php
App::uses('Component', 'Controller');
class CommentableComponent extends Component {
	public $name = 'Commentable';
	public $controller;
	public $components = array('Auth', 'TalkBack.CurrentCommenter');
	
	public $settings = array();
	
	// Helpers to ensure add to each controller
	private $_helpers = array(
		'TalkBack.Comment',
		'TalkBack.Commenter',
	);
		
	private $_viewVars = array();
	
	public function __construct(ComponentCollection $collection, $settings = array()) {
		$settings = array_merge(array(
			'modelClass' => null, 		// Set in initialize() after controller is loaded
			'multiLevel' => true,		// Can commenters reply directly to specific comments
		), $settings);
		
		return parent::__construct($collection, $settings);
	}

	public function initialize(Controller $controller) {
		$this->controller = $controller;
		
		if (empty($this->settings['modelClass'])) {
			$this->settings['modelClass'] = $controller->modelClass;
		}

		$this->CurrentCommenter->setController($controller);
		
		$this->setHelpers();
		return parent::initialize($controller);
	}

	public function beforeRender(Controller $controller) {
		// Settings to be passed to view
		$setSettings = array('multiLevel');
		foreach ($setSettings as $key) {
			$this->setViewVar($key, $this->settings[$key]);
		}
		
		// Sends view vars to View, wrapped in a "commentable" variable
		if (!empty($this->_viewVars)) {
			$controller->set(array('commentable' => $this->_viewVars));
		}
		return parent::beforeRender($controller);
	}
	
	public function setComments($modelId, $limit = 10, $paginate = true) {
		$Model = $this->_getModel();

		$query = $Model->getCommentAssociation($modelId);
		$query['limit'] = $limit;
		
		if ($this->controller->name == 'Comments') {
			$this->controller->paginate = $query;
			$paginateModel = null;
		} else {
			$this->controller->paginate = array('Comment' => $query);
			$paginateModel = 'Comment';
		}

		if ($paginate) {
			try {
				$comments = $this->controller->paginate($paginateModel);			
			} catch(NotFoundException $e) {
				if (isset($this->controller->request['named']['page'])) {
					$this->controller->redirect(array($modelId));
				}
			}
		} else {
			$comments = $Model->Comment->find('all', $query);
		}
		
		$commentId = null;
		$anchor = 'comments';
		if (!empty($this->controller->request->named['comment'])) {
			$commentId = $this->controller->request->named['comment'];
			$anchor = 'comment' . $commentId;
		}

		$paging = $this->controller->request['paging']['Comment'];
		$perPage = $paging['limit'];
		$pageCount = $paging['pageCount'];
		$page = isset($this->controller->request['named']['page']) ? $this->controller->request['named']['page'] : null;
		if ($page === null || !is_numeric($page) || $page > $pageCount) {
			if (
				!empty($commentId) && 
				($comment = $Model->Comment->find('first', 
					$Model->getCommentAssociation($modelId, $commentId)
				))
			) {
				$query = $Model->getCommentAssociation($modelId);
				$query['conditions']['Comment.created >'] = $comment['Comment']['created'];
				$position = $Model->Comment->find('count', $query);
				
				$page = floor($position / $perPage) + 1;
			} else if ($page == 'last') {
				$page = $pageCount;
			}
			if (!empty($page)) {
				$this->controller->redirect(array($modelId, 'page' => $page, '#' => $anchor));
			}
		}
		$isFirstPage = $paging['page'] == 1;
		
		// Sets model and associated comments as being read
		if ($currentCommenterId = $this->CurrentCommenter->getId()) {
			if ($Model->hasMethod('markRead')) {
				$Model->markRead($modelId, $currentCommenterId);
			}
			if ($isCommentHasRead = $Model->isCommentHasRead()) {
				$Model->Comment->markRead(Hash::extract($comments, '{n}.Comment.id'), $currentCommenterId);
			}
		}
		$this->controller->set(compact('comments'));
		
		// Loads default paginate URL
		$params = $this->controller->request->params + array('named' => array(), 'pass' => array(), 'prefix' => null);
		$url = array(
			'controller' => $params['controller'],
			'action' => $params['action'],
			'prefix' => $params['prefix'],
			'#' => 'comments',
		);
		foreach (array('pass', 'named') as $key) {
			foreach ($params[$key] as $field => $val) {
				$url[$field] = $val;
			}
		}
		//debug(compact('url', 'params'));
		
		// Sets variables to be used with comment elements
		$this->setViewVar(
			compact('url', 'modelId', 'isFirstPage', 'currentCommenterId', 'isCommentHasRead') + 
			array('model' => $Model->getPluginClassName())
		);
	}
	
	private function setViewVar($var, $val = null) {
		if (is_array($var)) {
			$this->_viewVars = $var + $this->_viewVars;
		} else {
			$this->_viewVars[$var] = $val;
		}
	}
	
	// Set all necessary helpers
	private function setHelpers() {
		foreach ($this->_helpers as $helper => $helperSettings) {
			if (is_numeric($helper)) {
				$helper = $helperSettings;
				$helperSettings = array();
			}
			if (!isset($this->controller->helpers[$helper])) {
				$this->controller->helpers[$helper] = $helperSettings;
			}
		}
	}

	// Return the current model object
	private function _getModel($modelName = null) {
		$Model = null;
		if (empty($modelName)) {
			$modelName = $this->settings['modelClass'];
		}
		list($plugin, $name) = pluginSplit($modelName);
		if ($Model = ClassRegistry::init($modelName, true)) {
			return $Model;
		} else if (empty($plugin) && !empty($this->controller->plugin)) {
			$modelName = $this->controller->plugin . '.' . $name;
			if ($Model = $this->_getModel($modelName)) {
				$this->settings['modelClass'] = $modelName;
			}
		}
		return $Model;
	}
}