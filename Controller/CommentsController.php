<?php
class CommentsController extends TalkBackAppController {
	public $name = 'Comments';
	
	public function beforeFilter() {
		$this->Components->unload('TalkBack.Commentable');
		$this->setValidateRedirectMethod('editable', function($args) {
			if (!$this->Comment->isEditable($args['id'], $this->Auth->user('id'), $this->CurrentCommenter->isAdmin())) {
				$msg = 'Sorry you do not have permission to edit this';
				$redirect = array('view' => $args['id']);
			}
			return compact('msg', 'redirect');
		});
		$this->setValidateRedirectMethod('hasModel', function($args) {
			if (empty($this->request->data['Comment']['model']) && empty($args['model'])) {
				$msg = 'Please select a model where these comments should go before adding a comment';
				$redirect = true;
			}
			return compact('msg', 'redirect');
		});
		$this->setValidateRedirectMethod('hasAssociation', function($args) {
			if (empty($args['model']) || empty($args['foreignKey'])) {
				$msg = 'Please specify which comments to be displayed';
				$redirect = true;
			}
			return compact('msg', 'redirect');
		});
		
		/**
		 * If an action is not found, it checks to see if the action exists without the prefix
		 * This allows adding and editing comments from custom prefixes without stripping the original prefix,
		 * which would break the redirect
		 **/
		$action = $this->request->params['action'];
		if (!method_exists($this, $action) && !empty($this->request->params['prefix'])) {
			$action = substr($action, strlen($this->request->params['prefix']) + 1);	//Strip prefix
			if (method_exists($this, $action)) {
				return call_user_func_array(array($this, $action), $this->request->params['pass']);
			}
		}
		
		return parent::beforeFilter();
	}
	
	public function index($model = null, $foreignKey = null) {
		$this->validateRedirect(array('hasAssociation' => compact('model', 'foreignKey')));

		if (!($Model = ClassRegistry::init($model, true))) {
			throw NotFoundException('The associated model could not be loaded');
		}
		
		$this->Commentable = $this->Components->load('TalkBack.Commentable', array(
			'modelClass' => $model,
		));
		$this->Commentable->initialize($this);
		
		if (!($modelResult = $Model->read(null, $foreignKey))) {
			throw NotFoundException('The associated model result was not found');
		}

		$modelTitle = $modelResult[$Model->alias][$Model->displayField];
		$modelUrl = array(
			'controller' => Inflector::tableize($Model->alias), 
			'action' => 'view', 
			$Model->id, 
			'plugin' => Inflector::underscore($Model->plugin)
		);
		$this->set('title_for_layout', "$modelTitle Comments");
		$this->Commentable->setComments($foreignKey);
		$this->set(compact('modelResult', 'model', 'modelTitle', 'modelUrl', 'foreignKey'));
	}
	
	public function view($id = null) {
		$this->redirect($this->getModelUrl($id));
	}

	public function add($model = null, $foreignKey = null, $parentId = null) {
		$this->validateRedirect(array('login', 'hasModel' => compact('model')));
		
		// $this->FormData->setSuccessRedirect(false);
		
		$this->FormData->addData(array(
			'default' => array(
				'Comment' => array(
					'model' => $model,
					'foreign_key' => $foreignKey,
					'commenter_id' => $this->CurrentCommenter->getId(),
					'parent_id' => $parentId,
				)
			)
		));
		$this->render('Elements/comments/form');
	}
	
	public function edit($id = null) {
		$this->validateRedirect(array('login', 'editable' => compact('id')));
		$this->FormData->editData($id);
		$this->render('Elements/comments/form');
	}
	
	public function delete($id = null) {
		$this->validateRedirect(array('login', 'editable' => compact('id')));
		$url = $this->getModelUrl($id);
		$this->FormData->deleteData($id, array('redirect' => $url));		
	}
	
	public function _setFormElements() {
		if (!empty($this->request->data['Comment']['parent_id'])) {
			$parentComment = $this->Comment->read(null, $this->request->data['Comment']['parent_id']);
		}
		if (!empty($this->request->data['Comment']['foreign_key']) && !empty($this->request->data['Comment']['model'])) {
			$parent = $this->Comment->findParentModel(
				$this->request->data['Comment']['model'],
				$this->request->data['Comment']['foreign_key']
			);
		}
		$this->set(compact('parent', 'parentComment'));
	}
	
	private function getModelUrl($id) {
		$result = $this->FormData->findModel($id);
		list($plugin, $model) = pluginSplit($result['Comment']['model']);
		$url = array('controller' => Inflector::tableize($model), 'action' => 'view', $result['Comment']['foreign_key']);
		$url['plugin'] = !empty($plugin) ? Inflector::underscore($plugin) : false;
		$url['comment'] = $id;
		return $url;
	}
	

}