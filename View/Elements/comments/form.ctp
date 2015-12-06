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

$default = [
	'model' => null,
	'modelId' => null,
	'currentCommenterId' => null,
	'prefix' => null,
];
extract(array_merge($default, compact(array_keys($default))));

if (!empty($this->viewVars['commentable']['model'])) {
	$model = $this->viewVars['commentable']['model'];
}
if (!empty($this->viewVars['commentable']['modelId'])) {
	$modelId = $this->viewVars['commentable']['modelId'];
}

if (!empty($this->request->params['prefix'])) {
	$prefix = $this->request->params['prefix'];
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
		<?php echo $this->Commenter->image($currentCommenter['Commenter'], [
			'class' => 'media-object tb-comment-thumbnail'
		]); ?>
	</div>
	<div class="media-body"><?php
		echo $this->Form->create('Comment', [
			'url' => [
				'controller' => 'comments',
				'action' => 'add',
				$model,
				$modelId,
				//$this->params['prefix'] => false,
				'plugin' => 'talk_back',
			],
		]);
		echo $this->Form->hidden('id');
		echo $this->Form->hidden('model', ['default' => $model]);
		echo $this->Form->hidden('foreign_key', ['default' => $modelId]);
		echo $this->Form->hidden('commenter_id', ['default' => $currentCommenterId]);
		echo $this->Form->hidden('prefix', ['default' => $prefix]);
		echo $this->Form->hidden('parent_id');

		
		$textOptions = [
			'placeholder' => 'Your ' . $label['singular'],
			'rows' => 5,
			'label' => false,
		];
		if ($isLoggedIn) {
			echo $this->Form->input('body', $textOptions);
			
			/*
			echo $this->Form->hidden('CommenterEmailControl.id');
			echo $this->Form->hidden('CommenterEmailControl.commenter_id', ['default' => $currentCommenterId]);
			echo $this->Form->inputs([
				'legend' => 'Email Settings',
				'CommenterEmailControl.email_on_reply' => [
					'label' => 'Email if someone replies',
				],
			]);
			*/
			
			echo $this->Form->end('Add ' . $label['singular'], ['class' => 'btn btn-primary']);
		} else {
			$textOptions['placeholder'] = 'Please sign in first';
			$textOptions['disabled'] = true;
			echo $this->Form->input('body', $textOptions);
			echo $this->Form->end();
		}
	?></div>
</div>
	