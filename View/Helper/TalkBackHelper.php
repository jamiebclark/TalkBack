<?php
class TalkBackHelper extends AppHelper {
	public $name = 'TalkBack';
	public $helpers = ['Html', 'TalkBack.Commenter'];
	
	public function commentTitle($result, $options = []) {
		$options = array_merge([
			'tag' => 'h5',
			'class' => '',
			'isAdmin' => false,
		], $options);
		$options = $this->Html->addClass($options, 'tb-comment-title');
		extract($options);
		$comment = !empty($result['Comment']) ? $result['Comment'] : $result;
		$commenter = $result['Commenter'];
		$stamp = strtotime($comment['created']);

		$out = $this->Commenter->link($commenter) . ' ';
		if ($isAdmin || ($comment['commenter_id'] == $this->_View->viewVars['tbCommenterId'])) {
			$url = ['controller' => 'comments', $comment['id'], 'plugin' => 'talk_back'];
			$out .= sprintf('(%s) (%s) ', 
				$this->Html->link(
					'Edit', 
					$url + ['action' => 'edit'],
					['class' => 'ajax-modal', 'data-modal-title' => 'Edit Reply']
				),
				$this->Html->link(
					'Remove', 
					$url + ['action' => 'delete'],[
	'confirm' => 'Remove this comment?'
])
			);
		}
		$out .= sprintf('<span class="date-commented">%s at %s</span>', date('M j, Y', $stamp), date('g:ia', $stamp));
		return $this->Html->tag($tag, $out, compact('class', 'id'));
	}
}