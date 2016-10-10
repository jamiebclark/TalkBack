<?php
$default = array(
	'label' => 'Comment',			// How to refer to the comments (can also include an ['singular' => '', 'plural' => '']
	'model' => null,				// The model associated with these comments
	'modelId' => null,				// The specific model id associated with these comments
	'isCommentable' => true,		// Can comments be added?
	'isCommentHasRead' => true,		// Should it look to see if the comment has been read?
	'viewAll' => false,				// Should it include a linke to "View All Comments"?
	'form' => true,					// Include the comment form
	'class' => '',				// Additional style class for the list
	'multiLevel' => true,			// Allow for replies to individual comments
	'panel' => false,
);
// Looks for variables set by Commentable Component
if (isset($this->viewVars['commentable'])) {
	$default = Hash::merge($default, $this->viewVars['commentable']);
}
extract(array_merge($default, compact(array_keys($default))));

$indexUrl = [
	'controller' => 'comments',
	'action' => 'index',
	$model,
	$modelId,
	'plugin' => 'talk_back',
];

if ($panel) {
	$class .= ' panel panel-default';
}

if (empty($url)) {
	$url = $indexUrl;
}

$addCommentUrl = ['action' => 'add'] + $indexUrl;

if (!is_array($label)) {
	$label = array(
		'singular' => Inflector::singularize($label),
		'plural' => Inflector::pluralize($label),
	);
}

// $this->Paginator->options['url'] = $url;
// $url = null;

if (!empty($this->request->params['paging']['Comment'])) {
	unset($this->Paginator->options['url']);
	$pagination = $this->Paginator->pagination(compact('url'));
} else {
	$pagination = '';
}
?>

<div id="comments" class="tb-comments <?php echo $class; ?>">
<?php if (!empty($label)): ?>
	<?php if ($panel): ?>
		<div class="panel-heading">
			<span class="panel-title"><?php echo $label['plural']; ?></span>
		</div>
	<?php else: ?>
		<h4><?php echo $label['plural']; ?></h4>
	<?php endif; ?>
<?php endif; ?>

<?php if ($form === 'top'): ?>
	<?php echo $this->element('TalkBack.comments/form', compact('model', 'modelId')); ?>
<?php endif; ?>

<?php if (!empty($comments)): ?>
	<?php //echo $pagination; ?>
	<?php foreach ($comments as $comment): 
		$class = 'tb-comment media';
		if (!empty($commentId) && $commentId == $comment['Comment']['id']) {
			$class .= ' active';
		}
		if ($isCommentHasRead && isset($comment['CurrentCommenterHasRead'])) {
			$class .= !empty($comment['CurrentCommenterHasRead']['id']) ? ' read' : ' unread';
		}
		if ($comment['Comment']['deleted']) {
			$class .= ' deleted';
		}
		if (!empty($comment['Comment']['depth'])) {
			$class .= ' tb-comment-depth-' . $comment['Comment']['depth'];
		}
		
		$commenterUrl = $this->Commenter->urlArray(['action' => 'view', $comment['Commenter']['id']]);
		
		?>
		<div id="comment<?php echo $comment['Comment']['id'];?>" class="<?php echo $class; ?>">
			<?php echo $this->Html->link(
				$this->Commenter->image($comment['Commenter'], ['class' => 'media-object tb-comment-thumbnail']),
				$commenterUrl,
				['escape' => false, 'class' => 'pull-left']
			); ?>
			<div class="media-body">
				<?php if ($comment['Comment']['deleted']): ?>
					<em>Deleted</em>
				<?php else: ?>
					<?php echo $this->Comment->title($comment, [
						'tag' => 'h5',
						'class' => 'media-title',
					]); ?>
					<div class="tb-comment-body">
						<?php echo $this->DisplayText->text($comment['Comment']['body']); ?>
						<?php if ($comment['Comment']['modified'] != $comment['Comment']['created']): ?>
							<p class="comment-edited">Edited on: <?php echo $this->Calendar->niceShort($comment['Comment']['modified']); ?></p>
						<?php endif; ?>
						
						<?php if (!empty($multiLevel) && !empty($isCommentable)): ?>
							<p class="text-right">
							<?php echo $this->Html->link('Reply', 
								$addCommentUrl + [2 => $comment['Comment']['id']],
								[
									'class' => 'btn btn-default btn-sm ajax-modal', 
									'data-modal-title' => 'Reply to comment'
								]
							); ?>
							</p>
						<?php endif; ?>
						
					</div>
				<?php endif; ?>
			</div>	
		</div>
	<?php endforeach; ?>
	<?php echo $pagination; ?>	
<?php endif; ?>
<?php if ($form === true || $form == 'bottom'): ?>
	<?php echo $this->element('TalkBack.comments/form', compact('model', 'modelId')); ?>
<?php elseif (!empty($isCommentable) && (!empty($comments) || $form === false)): 
	// Only add the "Add Comment" button if 1. You are allowed and 2. There are existing comments or there isn't a comment form
	?>
	<?php echo $this->Html->link('Add ' . $label['singular'], 
		$addCommentUrl,
		['class' => 'btn btn-primary ajax-modal', 'data-modal-title' => 'Add ' . $label['singular']]
	);?>	
<?php endif; ?>
<?php if (empty($pagination) && !empty($viewAll) && !empty($comments)): ?>
	<?php echo $this->Html->link('View All ' . $label['plural'], $indexUrl, ['class' => 'btn btn-default']); ?>
<?php endif; ?>

</div>

<?php $this->Asset->blockStart(); ?>
$(window).load(function() {
	var $active = $('.tb-comments .active').first();
	if ($active.length) {
		var scr = $active.offset().top - 200;
		$('html,body').animate({
			'scrollTop': scr
		});
	}
});
<?php $this->Asset->blockEnd(); ?>