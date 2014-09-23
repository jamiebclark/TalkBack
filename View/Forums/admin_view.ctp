<?php echo $this->Layout->defaultHeader($forum['Forum']['id'], array(
	array('Add Topic', array('controller' => 'topics', 'action' => 'add', $forum['Forum']['id']))
)); 
?>
<div class="row">
	<div class="col-sm-8"><?php
		echo $this->Layout->infoTable(array(
			'Title' => $forum['Forum']['title'],
			'Channel' => $this->Html->link($forum['Channel']['title'], array(
				'controller' => 'channels', 'action' => 'view', $forum['Channel']['id']
			)),
			'Created' => $this->Calendar->niceShort($forum['Forum']['created']),
			'Last Modified' => $this->Calendar->niceShort($forum['Forum']['modified']),
			'Description' => $this->DisplayText->text($forum['Forum']['description'])
		));
		?>
		<h3>Topics</h3>
		<?php
		echo $this->element('topics/archive-admin');
	?></div>
	<div class="col-sm-4">
		<h3>Permissions</h3>

		<h4>Commenters</h4>
		<?php if (!empty($forum['Commenter'])): ?>
			<ul><li><?php echo implode('</li><li>', Hash::extract($forum, 'Commenter.{n}.full_name')); ?></li></ul>
		<?php else: ?>
			<p><em>All commenters</em></p>
		<?php endif; ?>
		
		<h4>Commenter Types</h4>
		<?php if (!empty($forum['CommenterType'])): ?>
			<ul><li><?php echo implode('</li><li>', Hash::extract($forum, 'CommenterType.{n}.title')); ?></li></ul>
		<?php else: ?>
			<p><em>All Commenter Types</em></p>
		<?php endif; ?>
	</div>
</div>