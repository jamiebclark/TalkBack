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

$stamp = strtotime($topic['Topic']['created']);

?>
<div class="row">
	<div class="col-md-8">
		<?php 
		echo $this->element('topics/crumbs');
		echo $this->Html->getCrumbs();
		?>
		<div class="tb-topic-view">
			<div class="tb-comment original-comment jumbotron">
				<h4 class="tb-comment-title"><?php echo $topic['Topic']['title']; ?></h4>
				<?php echo $this->DisplayText->text($topic['Topic']['body']); ?>
				<h5>
					<?php echo $this->Commenter->link($topic['Commenter']); ?>
					<?php echo sprintf('<span class="date-commented">%s at %s</span>', date('M j, Y', $stamp), date('g:ia', $stamp)); ?>
				</h5>
				<h5><?php echo number_format($topic['Topic']['comment_count']); ?> Comments</h5>
			</div>
			<a name="comments"></a>
			<?php echo $this->element('comments', array(
				'model' => 'TalkBack.Topic',
				'modelId' => $topic['Topic']['id'],
				'url' => array($topic['Topic']['id'], '#' => 'comments'),
				'isCommentable' => $isCommentable,
			)); ?>
		</div>
	</div>
	<div class="col-md-4">
		<?php echo $this->element('TalkBack.topics/sidebar'); ?>
	</div>
</div>