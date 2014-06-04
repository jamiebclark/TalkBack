<?php
$this->ModelView->setModel('TalkBack.Comment');
$this->Table->reset();
foreach ($comments as $comment):
	$this->Table->cells(array(
		array(
			$this->DisplayText->text($comment['Comment']['body']),
			'Reply'
		), array(
			$this->TalkBack->commenterLink($comment['Commenter']),
			'Commenter',
		), array(
			$this->Calendar->niceShort($comment['Comment']['created']),
			'Created',
		), array(
			$this->ModelView->actionMenu(array('edit', 'delete'), $comment['Comment']),
			'Actions',
		),
	), true);
endforeach;
echo $this->Table->output(array('paginate' => true));