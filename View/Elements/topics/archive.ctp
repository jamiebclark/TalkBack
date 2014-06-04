<?php
$this->Table->reset();
foreach ($topics as $topic):
	$content = $this->Html->tag('h4', 
		$this->Html->link($topic['Topic']['title'], array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id']))
	);
	$content .= $this->DisplayText->text($topic['Topic']['body'], array(
		'firstParagraph' => true,
	));
	
	if (!empty($topic['LastComment'])) {
		$lastCommented = $this->Html->link(
			$this->Calendar->niceShort($topic['Topic']['modified']),
			array('controller' => 'topics', 'action' => 'view', $topic['Topic']['id'], 'page' => 'last')
		);
	} else {
		$lastCommented = '&nbsp;';
	}
	
	$this->Table->cells(array(
		array(
			$content,
			'Topics',
		), array(
			$this->Commenter->link($topic['Commenter']),
			'Commented By',
		), array(
			number_format($topic['Topic']['comment_count']),
			'Replies',
		), array(
			$lastCommented,
			'Last Commented',
		),
	), true);
endforeach;
echo $this->Table->output(array(
	'paginate' => true,
	'empty' => $this->Html->div('hero-unit', 'No topics'),
));