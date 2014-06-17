<?php
$this->Table->reset();
foreach ($topics as $topic):
	$class = null;
	if (isset($topic['CurrentCommenterHasRead']) && empty($topic['CurrentCommenterHasRead']['id'])) {
		$class = 'unread';
	}
	
	$title = $this->Html->link($topic['Topic']['title'], ['controller' => 'topics', 'action' => 'view', $topic['Topic']['id']]);
	if (!empty($topic[0]['total_unread'])) {
		$title .= $this->Html->tag('span', '+' . $topic[0]['total_unread'], ['class' => 'label label-success pull-right']);
	}
	
	$content = $this->Html->tag('h4', $title);
	$content .= $this->DisplayText->text($topic['Topic']['body'], [
		'firstParagraph' => true,
		'truncate' => 100,
	]);
	$content .= sprintf('<p>By %s on %s</p>', 
		$this->Commenter->link($topic['Commenter']),
		$this->Calendar->niceShort($topic['Topic']['created'])
	);
	
	$this->Table->cells([[
			$content,
			'Topics',
			['class' => 'tb-topics-archive-topic']
		], [
			$this->Html->tag('span', 
				number_format($topic['Topic']['comment_count']),
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
	'empty' => $this->Html->div('jumbotron', 'No topics posted yet'),
	'class' => 'tb-topics-archive',
]);