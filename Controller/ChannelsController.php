<?php
class ChannelsController extends TalkBackAppController {
	public $name = 'Channels';
	
	public function index() {
		$prefix = !empty($this->request->params['prefix']) ? $this->request->params['prefix'] : null;
		$channels = $this->Channel->findCommenterChannels($this->CurrentCommenter->getId(), $prefix);
		$this->set(compact('channels'));
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
		$this->set('sidebarTopics', $this->Channel->Forum->Topic->findSidebar([
			'conditions' => ['Forum.channel_id' => $id]
		]));
		
		$this->paginate = ['Forum' => ['conditions' => ['Forum.channel_id' => $id]]];
		$this->set('forums', $this->paginate('Forum'));
	}
	
	public function admin_index() {
		$this->set('channels', $this->paginate());
	}
	
	public function admin_view($id = null) {
		$this->FormData->findModel($id);
		$this->paginate = ['Forum' => ['conditions' => ['channel_id' => $id]]];
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

		$adminCommenters = $this->findHabtmList('AdminCommenter');
		$commenters = $this->findHabtmList('Commenter');
		
		$this->set(compact('prefixes', 'commenterTypes', 'commenters', 'adminCommenters'));
	}
	
	private function findHabtmList($modelName) {
		if (!empty($this->request->data[$modelName][0])) {
			$extract = $modelName . '.{n}.id';
		} else if (!empty($this->request->data[$modelName][$modelName])) {
			$extract = $modelName . '.' . $modelName . '.{n}';
		} else {
			$extract = false;
		}
		if ($extract) {
			return $this->Channel->{$modelName}->find('list', [
				'conditions' => [$modelName . '.id' => Hash::extract($this->request->data, $extract)]
			]);
		}
		return null;
	}
}