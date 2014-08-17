<?php
class TopicsController extends TalkBackAppController {
	public $name = 'Topics';
	
	public $components = array('TalkBack.Commentable');
	
	public function beforeFilter() {

		// Checks user permissions for the forum
		$this->setValidateRedirectMethod('forumPermission', function($args) {
			if (!$this->Topic->Forum->isCommenterAllowed(
				$args[0],
				$this->CurrentCommenter->getId(),
				$this->getPrefix()
			)) {
				$msg = 'You don\'t have permission to edit this Forum';
				$redirect = true;
			}
			return compact('msg', 'redirect');
		});

		// Checks if a Forum ID has been passed to the view
		$this->setValidateRedirectMethod('hasForum', function($args) {
			if (empty($this->request->data['Topic']['forum_id']) && empty($args['forumId'])) {
				$msg = 'Please select a forum before adding a topic';
				$redirect = array('controller' => 'forums', 'action' => 'index');
			}
			return compact('msg', 'redirect');
		});

		// Checks if the topic is editable
		$this->setValidateRedirectMethod('editable', function($args) {
			if (!$this->Topic->isEditable($args['id'], $this->Auth->user('id'), $this->isAdmin())) {
				$msg = 'Sorry you do not have permission to edit this';
				$redirect = array('view' => $args['id']);
			}
			return compact('msg', 'redirect');
		});
		
		return parent::beforeFilter();
	}
	
	public function index($channelId = null) {
		$redirect = array('controller' => 'channels', 'action' => 'view', $channelId);
		if (!empty($this->request->params['prefix'])) {
			$redirect[$this->request->params['prefix']] = true;
		}
		if (empty($channelId)) {
			$redirect['action'] = 'index';
		}
		$this->redirect($redirect);
	}
	
	public function view($id = null) {
		$this->prefixRedirect($id);
		$this->validateRedirect(array('permission' => array($id)));
		$topic = $this->FormData->findModel($id, null, array(
			'contain' => array(
				'Forum' => array('Channel'),
				'Commenter',
			)
		));

		$isCommentable = $this->Topic->isCommentable($id, 
			$this->CurrentCommenter->getId(), 
			$this->CurrentCommenter->isAdmin()
		);

		$this->Commentable->setComments($id, 10);
		$this->set(compact('isCommentable'));
		
		// Sidebar elements
		// -------------------------------
		$this->set('updatedTopics', $this->Topic->findUpdatedList([
			'conditions' => ['Topic.forum_id' => $topic['Topic']['forum_id']]
		]));
	}
	
	public function add($forumId = null) {
		$this->validateRedirect(array('forumPermission' => array($forumId), 'login', 'hasForum' => compact('forumId')));
		$this->FormData->addData(array(
			'default' => array(
				'Topic' => array(
					'forum_id' => $forumId,
					'commenter_id' => $this->Auth->user('id')
				)
			)
		));	
		$this->render('Elements/topics/form');
	}

	public function edit($id = null) {
		$this->validateRedirect(array('login', 'editable' => compact('id')));
		$this->FormData->editData($id);
		$this->render('elements/topics/form');
	}
	
	public function admin_index() {
		$this->set('topics', $this->paginate());
	}
	
	public function admin_view($id = null) {
		$this->FormData->findModel($id);
		$this->paginate = array(
			'Comment' => $this->Topic->getCommentAssociation($id)
		);
		$this->set('comments', $this->paginate('Comment'));
	}
	
	public function admin_add($forumId = null) {
		$this->FormData->addData(array(
			'Topic' => array(
				'forum_id' => $forumId,
			)
		));
	}
	
	public function admin_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	public function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
}