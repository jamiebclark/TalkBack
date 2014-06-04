<?php
App::uses('TalkBackAppHelper', 'TalkBack.View/Helper');
class CommentHelper extends TalkBackAppHelper {
	public $name = 'TalkBack';
	public $helpers = array(
		'Html', 
		'TalkBack.Commenter',
		'Layout.DisplayText',
	);
	
	public function urlArray($url = array()) {
		return parent::urlArray($url + array('controller' => 'comments'));
	}
	
	public function title($result, $options = array()) {
		$options = array_merge(array(
			'tag' => 'h5',
			'class' => '',
			'isAdmin' => false,
		), $options);
		$options = $this->Html->addClass($options, 'tb-comment-title');
		extract($options);
		$comment = !empty($result['Comment']) ? $result['Comment'] : $result;
		$commenter = $result['Commenter'];
		$stamp = strtotime($comment['created']);
		
		$isCurrentCommenter = false;
		if (
			!empty($this->_View->viewVars['commentable']['currentCommenterId']) && 
			$comment['commenter_id'] == $this->_View->viewVars['commentable']['currentCommenterId']
		) {
			$isCurrentCommenter = true;
		}

		$out = $this->Commenter->link($commenter) . ' ';
		if ($isAdmin || $isCurrentCommenter) {
			$url = $this->urlArray(array($comment['id']));
			$out .= sprintf('(%s) (%s) ', 
				$this->Html->link(
					'Edit', 
					array('action' => 'edit') + $url,
					array('class' => 'ajax-modal', 'data-modal-title' => 'Edit Reply')
				),
				$this->Html->link(
					'Remove', 
					array('action' => 'delete') + $url,
					null,
					'Remove this comment?'
				)
			);
		}
		$out .= sprintf('<span class="date-commented">%s at %s</span>', date('M j, Y', $stamp), date('g:ia', $stamp));
		return $this->Html->tag($tag, $out, compact('class', 'id'));
	}
	
	public function body($comment, $options = array()) {
		if (is_array($comment)) {
			$comment = isset($comment['Comment']) ? $comment['Comment']['body'] : $comment['body'];
		}
		return $this->DisplayText->text($comment, $options);
	}
}