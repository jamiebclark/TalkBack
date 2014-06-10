<?php
/*
$this->Html->addCrumb($topic['Forum']['Channel']['title'], array(
	'controller' => 'channels', 'action' => 'view', $topic['Forum']['Channel']['id'],
));
$this->Html->addCrumb($topic['Forum']['title'], array(
	'controller' => 'forums', 'action' => 'view', $topic['Forum']['id']
));
$truncated = $this->Text->truncate($topic['Topic']['title']);
$this->Html->addCrumb($truncated);
$this->set('title_for_layout', $truncated);
*/
echo $this->element('topics/crumbs');

$stamp = strtotime($topic['Topic']['created']);

echo $this->Html->getCrumbs();
?>
<div class="tb-topic-view">
	<div class="tb-comment original-comment">
		<h4 class="tb-comment-title"><?php echo $topic['Topic']['title']; ?></h4>
		<?php echo $this->DisplayText->text($topic['Topic']['body']); ?>
		<h5>
			<?php echo $this->Commenter->link($topic['Commenter']); ?>
			<?php echo sprintf('<span class="date-commented">%s at %s</span>', date('M j, Y', $stamp), date('g:ia', $stamp)); ?>
		</h5>
	</div>
	<a name="comments"></a>
	<?php echo $this->element('comments', array(
		'model' => 'TalkBack.Topic',
		'modelId' => $topic['Topic']['id'],
		'url' => array($topic['Topic']['id'], '#' => 'comments'),
		'isCommentable' => $isCommentable,
	)); ?>
</div>