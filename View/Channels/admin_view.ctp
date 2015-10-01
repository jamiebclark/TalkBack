<?php echo $this->Layout->defaultHeader(
	$channel['Channel']['id'], 
	array(
		array('Add Forum', array('controller' => 'forums', 'action' => 'add', $channel['Channel']['id']))
	),
	array(
		'title' => 'Channel: "' . $channel['Channel']['title'] . '"',
	)
); 

$liOpen = '<li class="list-group-item">';
$liClose = '</li>';

?>
<div class="row">
	<div class="col-md-8">
		<?php if (!empty($channel['Channel']['description'])): ?>
			<div class="panel panel-default">
				<div class="panel-body">
					<?php echo $channel['Channel']['description']; ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="panel panel-default">
			<div class="panel-heading">Forums</div>
			<?php echo $this->element('forums/archive-admin'); ?>
		</div>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">Permissions</div>
			<div class="panel-body">
				<p class="help-block">The channel can only be seen by the following combination of credentials</p>
				<dl>
				<?php if (!empty($channel['Channel']['prefix'])): ?>
					<dt>Prefix</dt>
					<dd>Pages starting with <strong>/<?php echo $channel['Channel']['prefix']; ?>/</strong></dd>
				<?php endif; ?>
				<dt>Commenters</dt>
				<dd>
				<?php if (!empty($channel['Commenter'])): ?>
					<ul class="list-group"><?php echo $liOpen . implode($liClose . $liOpen, Hash::extract($channel, 'Commenter.{n}.full_name')) . $liClose; ?></ul>
				<?php else: ?>
					<em>All commenters</em>
				<?php endif; ?>
				</dd>
		
				<dt>Commenter Types</dt>
				<dd>
				<?php if (!empty($channel['CommenterType'])): ?>
					<ul class="list-group"><?php echo $liOpen . implode($liClose . $liOpen, Hash::extract($channel, 'CommenterType.{n}.title')) . $liClose; ?></ul>
				<?php else: ?>
					<em>All Commenter Types</em>
				<?php endif; ?>
				</dd>
				</dl>
			</div>
		</div>
	</div>
</div>
