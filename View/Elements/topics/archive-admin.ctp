<?php
$this->ModelView->setModel('TalkBack.Topic');
$this->Table->reset();
foreach ($topics as $topic):
	$this->Table->cells(array(
		array($this->ModelView->link($topic['Topic']), 'Topic'),
		array(number_format($topic['Topic']['comment_count']), 'Comments'),
		array($this->ModelView->actionMenu(array('view', 'edit', 'delete'), $topic['Topic'])),
	), true);
endforeach;
echo $this->Table->output(array('paginate' => true));