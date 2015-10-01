<?php
$this->Table->reset();
foreach ($topics as $topic):
	$class = null;
	$url = ['controller' => 'topics', 'action' => 'view', $topic['Topic']['id']];

	if (isset($topic['CurrentCommenterHasRead']) && empty($topic['CurrentCommenterHasRead']['id'])) {
		$class = 'unread';
	}
	
	$title = $this->Html->link($topic['Topic']['title'], $url);
	if (!empty($topic[0]['total_unread'])) {
		$title .= $this->Html->tag('span', '+' . $topic[0]['total_unread'], ['class' => 'label label-success pull-right']);
	}
	
	$content = $this->Html->tag('h4', $title, ['class' => 'tb-topics-archive-title']);
	$content .= '<p><small>' . $this->DisplayText->text($topic['Topic']['body'], [
		'firstParagraph' => true,
		'truncate' => 100,
	]) . '</p></small>';
	$content .= sprintf('<p>By %s on %s</p>', 
		$this->Commenter->link($topic['Commenter']),
		$this->Calendar->niceShort($topic['Topic']['created'])
	);
	
	$this->Table->cells([[
			$content,
			'Topics',
			['class' => 'tb-topics-archive-topic']
		], [
			$this->Html->link(
				number_format($topic['Topic']['comment_count']),
				$url + ['#' => 'comments'],
				['class' => 'badge']
			),
			'Replies',
			['class' => 'text-center']
		], [
			$this->Comment->quote($topic['LastComment'], [
				'alias' => 'LastComment',
				'empty' => '&nbsp;',
			]),
			'Last Commented',
			['class' => 'tb-topics-archive-comment']
		]], compact('class'));
endforeach;

echo $this->Table->output([
	'empty' => $this->Html->div('empty-msg', 'No topics posted yet'),
	'div' => 'tb-archive tb-topics-archive',
]);