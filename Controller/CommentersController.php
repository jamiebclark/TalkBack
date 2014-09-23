<?php
class CommentersController extends TalkBackAppController {
	public $name = 'Commenters';
	
	public function index() {
		$commenters = $this->paginate();
		$this->set(compact('commenters'));
	}
	
	public function search($q = null) {
		if (empty($q) && !empty($this->request->query['term'])) {
			$q = $this->request->query['term'];
		}
		
		$idField = 'Commenter.' . $this->Commenter->primaryKey;
		$displayField = 'Commenter.' . $this->Commenter->displayField;
		
		if (!empty($q)) {
			$query = [
				'fields' => [
					"$idField AS value",
					"$displayField AS label",
				],
				'conditions' => [
					'OR' => [
						[$displayField . ' LIKE' => "$q%"],
						[$displayField . ' LIKE' => "%$q%"],
					]
				],
				'limit' => 10,
			];
			$commenters = $this->Commenter->find('all', $query);
			$json = Hash::extract($commenters, '{n}.Commenter');
			$json = Hash::remove($json, '{n}.id');
		}
		
		echo json_encode($json);
		exit();
		
		$this->viewClass = 'Json';
		$this->set(compact('json'));
		$this->set('_serialize', ['json']);
	}
	
	public function view($id = null) {
		$this->FormData->findModel($id);
		//$this->layout = 'TalkBack.Commenters/profile';

		$this->paginate = [
			'Comment' => [
				'conditions' => [
					'Comment.prefix' => $this->tb_prefix,
					'Comment.commenter_id' => $id,
				]
			]
		];
		$this->set('comments', $this->paginate('Comment'));
	}

	public function edit() {
		$this->validateRedirect('loggedIn');
		$id = $this->CurrentCommenter->getId();
		$this->FormData->editData($id);
		$this->render('/Elements/commenters/form');
	}
	/*
	public function comments($id = null) {
		$this->FormData->findModel($id);
		$this->layout = 'TalkBack.Commenters/profile';
		
		$this->paginate = [
			'Comment' => [
				'conditions' => ['Comment.commenter_id' => $id],
				'order' => ['Comment.created' => 'DESC'],
			]
		];

		$this->set('comments', $this->paginate('Comment'));	
		$this->render('/Elements/comments/archive');
	}
	
	public function	topics($id = null) {
		$this->FormData->findModel($id);
		$this->layout = 'TalkBack.Commenters/profile';

		$this->FormData->findModel($id);
		$this->paginate = [
			'Topic' => [
				'conditions' => ['Topic.commenter_id' => $id],
				'order' => ['Topic.created' => 'DESC'],
			]
		];
		$this->set('topics', $this->paginate('Topic'));
		$this->render('/Elements/topics/archive');
	}
	*/

	public function admin_index() {
	
	}
	
	public function admin_view() {
	
	}
}