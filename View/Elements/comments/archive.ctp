<?php
$default = [
	'url' => [
		'controller' => 'comments',
		'action' => 'index',
		$model,
	]
];
extract(array_merge($default, compact(array_keys($default))));
?>
<div id="tb-comment-archive">
<?php echo $this->Paginator->pagination(compact('url')); ?>
<?php foreach ($comments as $comment): ?>
	<h3 class="tb-comment-topic"><?php echo $this->Html->link(
		$comment['Topic']['title'],
		['controller' => 'topics', 'action' => 'view', $comment['Topic']['id']]
	); ?></h3>
	<div class="tb-comment">
		<div class="tb-comment-body">
			<?php echo $this->DisplayText->text($comment['Comment']['body']); ?>
		</div>
		<h5 class="media-title tb-comment-title"><?php 
			echo $this->Commenter->link($comment['Commenter']); 
			if ($isAdmin || ($comment['Comment']['commenter_id'] == $tbCommenterId)) {
				$url = ['controller' => 'comments', $comment['Comment']['id']];
				echo sprintf(' (%s) (%s)', 
					$this->Html->link(
						'Edit', 
						$url + ['action' => 'edit'],
						['class' => 'ajax-modal', 'data-modal-title' => 'Edit Reply']
					),
					$this->Html->link(
						'Remove', 
						$url + ['action' => 'delete'],
						null,
						'Remove this comment?'
					)
				);
				//echo $this->ModelView->actionMenu(['edit', 'delete'], $comment['Comment'], ['url' => ['controller' => 'comments']]);		
			}
		?> 
		<?php 
		$stamp = strtotime($comment['Comment']['created']);
		echo sprintf('<span class="date-commented">%s at %s</span>', date('M j, Y', $stamp), date('g:ia', $stamp));
		?></h5>
	</div>
<?php endforeach; ?>
<?php echo $this->Paginator->pagination(); ?>
</div>

<?php $this->Asset->blockStart(); ?>
$(window).load(function() {
	var $active = $('.tb-comment-archive .active');
	if ($active.length) {
		$('html,body').animate({
			'scrollTo': ($active.offset().top - 200)
		});
	}
});
<?php $this->Asset->blockEnd(); ?>