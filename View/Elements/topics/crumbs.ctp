<?php
if (empty($forum) && !empty($topic['Forum'])) {
	$forum = array('Forum' => $topic['Forum']);
}
if (!empty($forum)) {
	echo $this->element('TalkBack.forums/crumbs', compact('forum'));
}

$truncated = $this->Text->truncate($topic['Topic']['title']);
$this->Html->addCrumb($truncated);

$this->set('title_for_layout', $truncated);
