<?php
class MessagesController extends TalkBackAppController {
	public $name = 'Messages';
	public $components = array(
		'TalkBack.Commentable' => array(
			'multiLevel' => false,
		)
	);
	public $helpers = array('TalkBack.Message');
	
	public function beforeFilter($options = array()) {
		$this->Message = ClassRegistry::init('TalkBack.Message', true);
		if (!empty($this->tb_currentCommenterId)) {
			$this->Message->setCurrentCommenter($this->tb_currentCommenterId);
		}
		return parent::beforeFilter($options);
	}
	
	public function index($id = null) {
		$perPage = 20;
		$this->validateRedirect('login');
		
		$currentCommenterId = $this->Auth->user('id');

		$query = $this->Message->getInboxAssociation($currentCommenterId);
		
		if (isset($this->request['named']['page'])) {
			$page = $this->request['named']['page'];
		} else {
			$page = 1;
		}
		
		if (empty($id)) {
			$activeMessage = $this->Message->find('first', array(
				'inbox' => $currentCommenterId,
				'offset' => $perPage * ($page - 1), 
				'limit' => 1
			));
			if (!empty($activeMessage)) {
				$id = $activeMessage['Message']['id'];
			}
		}
		
		if ($page === null && !empty($id) && empty($activeMessage)) {
			if ($activeMessage = $this->Message->find('first', array(
				'inbox' => $currentCommenterId,
				'conditions' => array('Message.id' => $id)
			))) {
				$position = $this->Message->find('count', array(
					'inbox' => $currentCommenterId,
					'conditions' => array('Message.created >' => $activeMessage['Message']['created'])
				));
				$this->redirect(array('action' => 'index', $id, 'page' => floor($position / $perPage) + 1));
			} else {
				$id = null;
			}
		}

		$this->paginate = array(
			'inbox' => $currentCommenterId,
			'contain' => array(
				'CommenterFrom',
				'LastComment',
				'OtherCommenter',
				'CurrentCommenterHasRead',
			),
			'limit' => $perPage,
		);
		
		try {
			$messages = $this->paginate();
		} catch (NotFoundException $e) {
			if ($page > 1) {
				$this->redirect(array('page' => 1));
			}
		}
		
		$this->set('activeMessageId', $id);
		$this->set('title_for_layout', 'Message Inbox');
		$this->set('messages', $messages);
	}
	
	public function view($id = null) {
		if (!$this->request->is('ajax')) {
			$this->redirect(array('action' => 'index', $id));
		}
		$this->FormData->findModel($id, null, array('contain' => array('Commenter')));
		$this->Message->markRead($id);
		$this->Commentable->setComments($id);
	}
	
	public function add() {
		$default = array(
			'Message' => array(
				'from_commenter_id' => $this->Auth->user('id')
			),
			//'Commenter' => array('Commenter' => array($toCommenterId)),
			'Comment' => array(
				array('commenter_id' => $this->Auth->user('id')),
			),
		);
		
		if ($args = func_get_args()) {
			foreach ($args as $commenterId) {
				$default['Commenter']['Commenter'][] = $commenterId;
			}
		}

		$this->validateRedirect('login');
		// $this->FormData->setSuccessRedirect(false);
		$this->FormData->addData(compact('default'), null, array('deep' => true));
		
		$this->render('Elements/messages/form');
	}
	
	public function edit($id = null) {
		$this->validateRedirect('login');
		$this->FormData->editData($id, null, null, null, array('deep' => true));
		$this->render('Elements/messages/form');
	}
	
	public function delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	public function _setFormElements() {
		if (!empty($this->request->data['Commenter']['Commenter'])) {
			$commenters = $this->Message->Commenter->find('list', array(
				'conditions' => array(
					'Commenter.id' => $this->request->data['Commenter']['Commenter'],
				)
			));
		}
		$this->set(compact('commenters'));
	}
}