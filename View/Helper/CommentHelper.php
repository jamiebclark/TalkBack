<?php
App::uses('TalkBackAppHelper', 'TalkBack.View/Helper');
class CommentHelper extends TalkBackAppHelper {
	public $name = 'TalkBack';
	public $helpers = [
		'Html', 
		'TalkBack.Commenter',
		'Layout.DisplayText',
		'Layout.Calendar',
		'Text',
	];
	
	public function quote($result, $options = []) {
		$options = array_merge([
			'alias' => 'Comment',
			'url' => true,
			'truncate' => 100,
			'before' => '',
			'after' => '',
			'empty' => '',
		], $options);
		$options = $this->addClass($options, 'tb-topic-comment-quote');
		extract($options);
		
		$comment = !empty($result[$alias]) ? $result[$alias] : $result;
		if (!empty($comment['Commenter'])) {
			$commenter = $comment['Commenter'];
		} else if (!empty($result['Commenter'])) {
			$commenter = $result['Commenter'];
		} else {
			$commenter = null;
		}
		
		if (empty($result) || !isset($result['body'])) {
			return $empty;
		}
		
		if ($url === true) {
			$url = [
				'controller' => 'comments',
				'action' => 'view',
				$comment['id'],
				'plugin' => 'talk_back',
			];
		}

		$out = '"' . $this->Text->truncate($comment['body'], $truncate) . '"';
		$out .= '<footer>';
		if (!empty($commenter)) {
			$out .= $this->Commenter->name($commenter) . '<br/>';
		}
		$out .= $this->Calendar->niceShort($comment['created']);
		$out .= '</footer>';
		
		return $this->Html->link($before . $out . $after, $url, [
			'escape' => false,
			'class' => $class
		]);
	}
	
	public function urlArray($url = []) {
		return parent::urlArray($url + ['controller' => 'comments']);
	}
	
	public function parentLink($result) {
		$comment = !empty($result['Comment']) ? $result['Comment'] : $result;
		return $this->Html->link(
			sprintf('Parent: %s #%d', $comment['model'], $comment['foreign_key']),
			['controller' => 'comments', 'action' => 'view', $comment['id']]
		);
	}

	public function title($result, $options = []) {
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
		
		$isCurrentCommenter = false;
		if (
			!empty($this->_View->viewVars['commentable']['currentCommenterId']) && 
			$comment['commenter_id'] == $this->_View->viewVars['commentable']['currentCommenterId']
		) {
			$isCurrentCommenter = true;
		}

		$out = $this->Commenter->link($commenter) . ' ';
		if ($isAdmin || $isCurrentCommenter) {
			$url = $this->urlArray([$comment['id']]);
			$out .= sprintf('(%s) (%s) ', 
				$this->Html->link(
					'Edit', 
					['action' => 'edit'] + $url,
					['class' => 'ajax-modal', 'data-modal-title' => 'Edit Reply']
				),
				$this->Html->link(
					'Remove', 
					['action' => 'delete'] + $url,
					null,
					'Remove this comment?'
				)
			);
		}
		$out .= sprintf('<span class="date-commented">%s at %s</span>', date('M j, Y', $stamp), date('g:ia', $stamp));
		return $this->Html->tag($tag, $out, compact('class', 'id'));
	}
	
	public function body($comment, $options = []) {
		if (is_array($comment)) {
			$comment = isset($comment['Comment']) ? $comment['Comment']['body'] : $comment['body'];
		}
		return $this->DisplayText->text($comment, $options);
	}
}