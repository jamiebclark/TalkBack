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
				'contain' => ['Channel'],
				'conditions' => ['Forum.channel_id' => $channelIds],
			];
			$this->set('forums', $this->paginate());
		}
	}
	
	public function view($id = null) {
		$this->validateRedirect(array('permission' => array($id)));
		
		$this->FormData->findModel($id);
		$this->paginate = array(
			'Topic' => array(
				'contain' => array('CurrentCommenterHasRead', 'Commenter', 'LastComment'),
				'conditions' => array('Topic.forum_id' => $id)
			)
		);
		$this->set('topics', $this->paginate('Topic'));		
		$this->set('updatedTopics', $this->Forum->Topic->findUpdatedList([
			'conditions' => ['Forum.id' => $id]
		]));
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