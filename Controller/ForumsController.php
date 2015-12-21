<?php
class ForumsController extends TalkBackAppController {
	
	public function index($channelId = null) {
		if (!empty($channelId)) {
			$this->redirect(array('controller' => 'channels', 'action' => 'view', $channelId));
		} else {
			$currentCommenterId = $this->CurrentCommenter->getId();
			$channelIds = $this->Forum->Channel->findCommenterChannelIds(
				$currentCommenterId,
				$this->getPrefix()
			);
			
			$this->paginate = [
				//'relatedUnread' => 'Comment',
				'isAdmin' => $this->CurrentCommenter->isAdmin(),
				'contain' => ['Channel'],
				'conditions' => ['Forum.channel_id' => $channelIds],
			];
			$this->set('forums', $this->paginate());
		}
	}
	
	public function view($id = null) {
		$this->validateRedirect(array('permission' => array($id)));
		
		$result = $this->FormData->findModel($id);
		if (!$this->CurrentCommenter->isAdmin() && empty($result['Forum']['active'])) {
			$this->Session->SetFlash('Sorry, that forum isn\'t available right now');
			$this->redirect(array('action' => 'index'));
		}

		$this->paginate = array(
			'Topic' => array(
				'contain' => array('CurrentCommenterHasRead', 'Commenter', 'LastComment'),
				'conditions' => array('Topic.forum_id' => $id)
			)
		);
		$this->set('canTopicBeAdded', $this->Forum->canTopicBeAdded($id, $this->Auth->user('id')));
		$this->set('topics', $this->paginate('Topic'));		
		$this->set('updatedTopics', $this->Forum->Topic->findUpdatedList([
			'isAdmin' => $this->CurrentCommenter->isAdmin(),
			'conditions' => ['Forum.id' => $id]
		]));
		$this->set('title_for_layout', 'Forum: ' . $result['Forum']['title']);
	}
	
	public function add($channelId = null) {
		$this->FormData->addData(array(
			'default' => array(
				'Forum' => array('channel_id' => $channelId)
			)
		));
	}

	public function admin_index() {
		$this->set('forums', $this->paginate());
	}
	
	public function admin_view($id = null) {
		$result = $this->FormData->findModel($id, ['contain' => ['Commenter', 'CommenterType']]);
		$this->paginate = ['Topic' => ['conditions' => ['Topic.forum_id' => $id]]];
		$this->set('topics', $this->paginate('Topic'));
	}

	public function admin_add($channelId = null) {
		$this->FormData->addData(array(
			'default' => array(
				'Forum' => array('channel_id' => $channelId)
			)
		));
	}
	
	public function admin_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	public function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	public function _setFormElements() {
		$channels = $this->Forum->Channel->find('list');
		$commenterTypes = $this->Forum->CommenterType->find('list');
		$commenters = $this->FormData->findHabtmList('Commenter');

		$this->set(compact('channels', 'commenterTypes', 'commenters'));
	}
}