<?php
class CommentersController extends TalkBackAppController {
	public $name = 'Commenters';
	
	public function search($q = null) {
		if (empty($q) && !empty($this->request->query['term'])) {
			$q = $this->request->query['term'];
		}
		
		$idField = 'Commenter.' . $this->Commenter->primaryKey;
		$displayField = 'Commenter.' . $this->Commenter->displayField;
		
		if (!empty($q)) {
			$query = array(
				'fields' => array(
					"$idField AS value",
					"$displayField AS label",
				),
				'conditions' => array(
					'OR' => array(
						array($displayField . ' LIKE' => "$q%"),
						array($displayField . ' LIKE' => "%$q%"),
					)
				),
				'limit' => 10,
			);
			$commenters = $this->Commenter->find('all', $query);
			$json = Hash::extract($commenters, '{n}.Commenter');
			$json = Hash::remove($json, '{n}.id');
		}
		
		echo json_encode($json);
		exit();
		
		$this->viewClass = 'Json';
		$this->set(compact('json'));
		$this->set('_serialize', array('json'));
	}
	
	public function view($id = null) {
		$this->FindModel->find($id);
		$this->layout = 'TalkBack.Commenters/profile';
	}
	
	public function comments($id = null) {
		$this->FindModel->find($id);
		$this->layout = 'TalkBack.Commenters/profile';
		
		$this->paginate = array(
			'Comment' => array(
				'conditions' => array(
					'Comment.commenter_id' => $id,
				),
				'order' => array('Comment.created' => 'DESC'),
			)
		);
		$this->set('comments', $this->paginate('Comment'));	
		$this->render('/Elements/comments/archive');
	}
	
	public function	topics($id = null) {
		$this->FindModel->find($id);
		$this->layout = 'TalkBack.Commenters/profile';

		$this->FormData->findModel($id);
		$this->paginate = array(
			'Topic' => array(
				'conditions' => array(
					'Topic.commenter_id' => $id,
				),
				'order' => array('Topic.created' => 'DESC'),
			)
		);
		$this->set('topics', $this->paginate('Topic'));
		$this->render('/Elements/topics/archive');
	}
	
	public function admin_index() {
	
	}
	
	public function admin_view() {
	
	}
}