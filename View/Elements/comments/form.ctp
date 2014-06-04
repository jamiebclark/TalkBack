<?php
if (empty($label)) {
	$label = 'Comment';
}
if (!is_array($label)) {
	$label = array(
		'singular' => Inflector::singularize($label),
		'plural' => Inflector::pluralize($label),
	);
}

$model = null;
$modelId = null;
$currentCommenterId = null;

if (!empty($this->viewVars['commentable']['model'])) {
	$model = $this->viewVars['commentable']['model'];
}
if (!empty($this->viewVars['commentable']['modelId'])) {
	$modelId = $this->viewVars['commentable']['modelId'];
}

if ($isLoggedIn = !empty($currentCommenter['Commenter'])) {
	$currentCommenterId = $currentCommenter['Commenter']['id'];
}

?>

<?php if (!$this->request->is('ajax')): ?>
	<?php if (!empty($parent)): ?>
		<h3><?php echo $parent[$parent['modelInfo']['alias']][$parent['modelInfo']['displayField']]; ?></h3>
	<?php endif; ?>
<?php endif; ?>

<?php if (!empty($parentComment)): ?>
	<div class="parentcomment">
		<h3>In reply to:</h3>
		<blockquote><?php echo $parentComment['Comment']['body']; ?></blockquote>
	</div>
<?php endif; ?>

<?php if (!$isLoggedIn): ?>
	<p>Please <?php echo $this->Html->link('Sign in', $currentCommenter[0]['loginAction']); ?> before posting anything</p>
<?php endif; ?>
<div class="media tb-comment">
	<div class="pull-left">
		<?php echo $this->Commenter->image($currentCommenter['Commenter'], array(
			'class' => 'media-object tb-comment-thumbnail'
		)); ?>
	</div>
	<div class="media-body"><?php
		echo $this->Form->create('Comment', array(
			'url' => array(
				'controller' => 'comments',
				'action' => 'add',
				$model,
				$modelId,
				//$this->params['prefix'] => false,
				'plugin' => 'talk_back',
			),
		));
		echo $this->Form->hidden('id');
		echo $this->Form->hidden('model', array('default' => $model));
		echo $this->Form->hidden('foreign_key', array('default' => $modelId));
		echo $this->Form->hidden('commenter_id', array('default' => $currentCommenterId));
		echo $this->Form->hidden('parent_id');

		
		$textOptions = array(
			'placeholder' => 'Your ' . $label['singular'],
			'rows' => 5,
			'label' => false,
		);
		if ($isLoggedIn) {
			echo $this->Form->input('body', $textOptions);
			
			/*
			echo $this->Form->hidden('CommenterEmailControl.id');
			echo $this->Form->hidden('CommenterEmailControl.commenter_id', array('default' => $currentCommenterId));
			echo $this->Form->inputs(array(
				'legend' => 'Email Settings',
				'CommenterEmailControl.email_on_reply' => array(
					'label' => 'Email if someone replies',
				),
			));
			*/
			
			echo $this->Form->end('Add ' . $label['singular'], array('class' => 'btn btn-primary'));
		} else {
			$textOptions['placeholder'] = 'Please sign in first';
			$textOptions['disabled'] = true;
			echo $this->Form->input('body', $textOptions);
			echo $this->Form->end();
		}
	?></div>
</div>
	