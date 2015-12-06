<?php
class TopicsController extends TalkBackAppController {
	public $name = 'Topics';
	
	public $components = ['TalkBack.Commentable'];
	
	public function beforeFilter($options = []) {

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
				$redirect = ['controller' => 'forums', 'action' => 'index'];
			}
			return compact('msg', 'redirect');
		});

		// Checks if the topic is editable
		$this->setValidateRedirectMethod('editable', function($args) {
			if (!$this->Topic->isEditable($args['id'], $this->Auth->user('id'), $this->CurrentCommenter->isAdmin())) {
				$msg = 'Sorry you do not have permission to edit this';
				$redirect = ['view' => $args['id']];
			}
			return compact('msg', 'redirect');
		});
		
		return parent::beforeFilter();
	}
	
	public function index($forumId = null) {
		$redirect = ['controller' => 'forums', 'action' => 'view', $forumId];
		if (!empty($this->request->params['prefix'])) {
			$redirect[$this->request->params['prefix']] = true;
		}
		if (empty($forumId)) {
			$redirect['action'] = 'index';
		}
		$this->redirect($redirect);
	}
	
	public function view($id = null) {
		$this->prefixRedirect($id);
		$this->validateRedirect(['permission' => [$id]]);
		$query = [
			'contain' => [
				'Forum' => ['Channel'],
				'Commenter',
			]
		];

		if ($this->CurrentCommenter->isAdmin()) {
			$query['contain']['CommenterHasRead']['Commenter'] = [];
		}

		$topic = $this->FormData->findModel($id, null, $query);

		$isCommentable = $this->Topic->isCommentable($id, 
			$this->CurrentCommenter->getId(), 
			$this->CurrentCommenter->isAdmin()
		);

		$this->Commentable->setComments($id, 10);
		$this->set(compact('isCommentable'));
		$this->set('isEditable', $this->Topic->isEditable(
			$id, 
			$this->Auth->user('id'), 
			$this->CurrentCommenter->isAdmin()
		));

		$this->set('neighbors', $this->Topic->findNeighbors($id));

		// Sidebar elements
		// -------------------------------
		$this->set('updatedTopics', $this->Topic->findUpdatedList([
			'conditions' => ['Topic.forum_id' => $topic['Topic']['forum_id']]
		]));
	}
	
	public function add($forumId = null) {
		$permissions = array(
			'login', 
			'hasForum' => compact('forumId')
		);
		// If users aren't allowed to make new topics, make sure they're admins
		if (!$this->Topic->Forum->canTopicBeAdded($forumId, $this->Auth->user('id'))) {
			$permissions['forumPermission'] = [$forumId];
		}

		$this->validateRedirect($permissions);

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
		$this->FormData->addData([
			'Topic' => [
				'forum_id' => $forumId,
			]
		]);
	}
	
	public function admin_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	public function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
}