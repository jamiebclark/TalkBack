<?php
/**
 * Controller to manage the TalkBack Message Board channels
 *
 * @package app.Plugin.TalkBack.Controller
 **/
App::uses('Hash', 'Utility');

class ChannelsController extends TalkBackAppController {
	public $name = 'Channels';
	
	public function index() {
		$prefix = !empty($this->request->params['prefix']) ? $this->request->params['prefix'] : null;
		$channels = $this->Channel->findCommenterChannels($this->CurrentCommenter->getId(), $prefix, ['contain' => ['Forum']]);

		$forumConditions = ['Forum.channel_id' => Hash::extract($channels, '{n}.Channel.id')];
		if (!$this->CurrentCommenter->isAdmin()) {
			$forumConditions['Forum.active'] = 1;
		}

		$updatedTopics = $this->Channel->Forum->Topic->findUpdatedList(['conditions' => $forumConditions]);

		$this->set(compact('channels', 'updatedTopics'));
	}
	
	public function view($id = null) {
		$result = $this->FormData->findModel($id);
		$result = $result['Channel'];
		$prefix = $this->getPrefix();
		if (!empty($result['prefix']) != $prefix) {
			$this->redirect([
				'action' => 'view',
				$id,
				$prefix => false,
				$result['prefix'] => true,
			]);
		}
		$this->validateRedirect(['permission' => [$id]]);

		if (empty($id)) {
			$this->redirect(['action' => 'index']);
		}

		$this->set('commenterCount', $this->Channel->Commenter->find('count', ['channelId' => $id]));
		$this->set('updatedTopics', $this->Channel->Forum->Topic->findUpdatedList([
			'conditions' => ['Forum.channel_id' => $id],
			'isAdmin' => $this->CurrentCommenter->isAdmin(),
		]));
		
		$isAdmin = $this->Channel->isCommenterAdmin($id, $this->Auth->user('id'));

		$this->paginate = ['Forum' => [
			'isAdmin' => $this->CurrentCommenter->isAdmin(),
			'conditions' => ['Forumn.channel_id' => $id]
		]];

		$this->set('forums', $this->paginate('Forum'));

		$this->set('talkBackPermissions', [
			'Channel' => [
				'canEdit' => $isAdmin,
				'canCreateForum' =>$isAdmin || $result['Channel']['allow_forums'],
			]
		]);
	}
	
	public function commenters($id) {
		$this->FormData->findModel($id);

		$this->paginate = ['Commenter' => ['channelId' => $id]];
		$commenters = $this->paginate('Commenter');
		$this->set(compact('commenters'));
	}

	public function admin_index() {
		$this->set('channels', $this->paginate());
	}
	
	public function admin_view($id = null) {
		$this->FormData->findModel($id);
		$this->paginate = ['Forum' => [
			'isAdmin' => $this->CurrentCommenter->isAdmin(),
			'conditions' => ['channel_id' => $id]
		]];

		$this->set('forums', $this->paginate('Forum'));
	}
	
	public function admin_edit($id = null) {
		$this->FormData->editData($id);
	}
	
	public function admin_add() {
		$this->FormData->addData([
			'default' => [
				'AdminCommenter' => ['AdminCommenter' => [$this->CurrentCommenter->getId()]]
			]
		]);
	}
	
	public function admin_delete($id = null) {
		$this->FormData->deleteData($id);
	}
	
	public function _setFormElements() {
		$prefixes = Configure::read('Routing.prefixes');
		$prefixes = array_combine($prefixes, $prefixes);
		$prefixes = ['' => ' -- Public (no prefix) -- '] + $prefixes;
		
		if ($this->Channel->CommenterType->isTree()) {
			$commenterTypes = $this->Channel->CommenterType->generateTreeList();
		} else {
			$commenterTypes = $this->Channel->CommenterType->find('list');
		}

		$adminCommenters = $this->FormData->findHabtmList('AdminCommenter');
		$commenters = $this->FormData->findHabtmList('Commenter');
		
		$this->set(compact('prefixes', 'commenterTypes', 'commenters', 'adminCommenters'));
	}	
}