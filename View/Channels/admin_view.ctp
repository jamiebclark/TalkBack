<?php echo $this->Layout->defaultHeader(
	$channel['Channel']['id'], 
	array(array('Add Forum', array('controller' => 'forums', 'action' => 'add', $channel['Channel']['id'])))
); 
?>
<div class="row">
	<div class="col-md-8">
		<h2><?php echo $channel['Channel']['title']; ?></h2>
		<?php echo $channel['Channel']['description']; ?>
		<h3>Forums</h3>
		<?php echo $this->element('forums/archive-admin'); ?>
	</div>
	<div class="col-md-4">
		<h3>Permissions</h3>
		<p>The channel can only be seen by the following combination of credentials</p>
		
		<?php if (!empty($channel['Channel']['prefix'])): ?>
			<h3>Prefix</h3>
			<p>Pages starting with <strong>/<?php echo $channel['Channel']['prefix']; ?>/</strong></p>
		<?php endif; ?>

		<h3>Commenters</h3>
		<?php if (!empty($channel['Commenter'])): ?>
			<ul><li><?php echo implode('</li><li>', Hash::extract($channel, 'Commenter.{n}.full_name')); ?></li></ul>
		<?php else: ?>
			<p><em>All commenters</em></p>
		<?php endif; ?>
		
		<h3>Commenter Types</h3>
		<?php if (!empty($channel['CommenterType'])): ?>
			<ul><li><?php echo implode('</li><li>', Hash::extract($channel, 'CommenterType.{n}.title')); ?></li></ul>
		<?php else: ?>
			<p><em>All Commenter Types</em></p>
		<?php endif; ?>
		
	</div>
</div>
