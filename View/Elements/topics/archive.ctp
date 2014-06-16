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
	
	$lastUrl = array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id'], 'page' => 'last');
	if (!empty($topic['LastComment'])) {
		$lastCommented = '"' . $this->Text->truncate($topic['LastComment']['body']) . '"';
		if (!empty($topic['LastComment']['Commenter'])) {
			$lastCommented .= '<br/>' . $this->Commenter->name($topic['LastComment']['Commenter']);
		}
		$lastCommented .= '<br/>' . $this->Calendar->niceShort($topic['LastComment']['created']);
		$lastCommented = $this->Html->link($lastCommented, $lastUrl, ['escape' => false]);
	} else {
		$lastCommented = '&nbsp;';
	}
	
	$this->Table->cells([[
			$content,
			'Topics',
		], [
			$this->Html->tag('span', 
				number_format($topic['Topic']['comment_count']),
				['class' => 'badge']
			),
			'Replies',
			['class' => 'text-center']
		], [
			$lastCommented,
			'Last Commented',
		]], compact('class'));
endforeach;

echo $this->Table->output([
	'empty' => $this->Html->div('jumbotron', 'No topics posted yet'),
]);