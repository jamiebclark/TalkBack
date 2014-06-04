<?php 
$this->ModelView->setModel('TalkBack.Forum');

$this->Table->reset();
foreach ($forums as $forum):
	$this->Table->cells(array(
		array(
			$this->ModelView->link($forum['Forum']) . $forum['Forum']['description'],
			'Title',
		), array(
			$this->ModelView->actionMenu(array('view', 'edit', 'delete'), $forum['Forum']),
			'Actions',
		)
	), true);
endforeach; 
echo $this->Table->output(array('paginate' => true));