<?php
class CommenterEmailControlsController extends TalkBackController {
	public $name = 'CommenterEmailControls';
	
	public function index($commenterId = null, $commentId = null) {
		if (!empty($commenterId) && !empty($commentId)) {
			if ($commenterEmailControl = $this->CommenterEmailControl->find('first', array(
				'contain' => array('Commenter', 'Comment'),
				'conditions' => array(
					'CommenterEmailControl.commenter_id' => $commenterId,
					'CommenterEmailControl.comment_id' => $commentId,
				)
			))) {
				$commenter = array('Commenter' => $commenterEmailControl['Commenter']);
				$comment = array('Comment' => $commenterEmailControl['Comment']);
			} else {
				$commenter = $this->CommenterEmailControl->Commenter->read(null, $commenterId);
				$comment = $this->CommenterEmailControl->Comment->read(null, $commentId);
			}				
		}
		
		if (empty($commenter) || empty($comment)) {
			throw new NotFoundException();
		}
	}
}