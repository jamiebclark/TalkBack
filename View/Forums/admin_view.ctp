<?php echo $this->Layout->defaultHeader(
	$forum['Forum']['id'], 
	array(
		array('Add Topic', array('controller' => 'topics', 'action' => 'add', $forum['Forum']['id'])),
	),
	array('title' => 'Forum: "'. $forum['Forum']['title'] . '"')
); 

$liOpen = '<li class="list-group-item">';
$liClose = '</li>';

?>
<div class="row">
	<div class="col-sm-8">
		<div class="panel panel-default">
			<div class="panel-heading">About</div>
			<?php
			echo $this->Layout->infoTable(array(
				'Title' => $forum['Forum']['title'],
				'Channel' => $this->Html->link($forum['Channel']['title'], array(
					'controller' => 'channels', 'action' => 'view', $forum['Channel']['id']
				)),
				'Active' => $forum['Forum']['active'] ? '<span class="label label-success">Yes</span>' : '<span class="label label-danger">No</span>',
				'Created' => $this->Calendar->niceShort($forum['Forum']['created']),
				'Last Modified' => $this->Calendar->niceShort($forum['Forum']['modified']),
				'Description' => $this->DisplayText->text($forum['Forum']['description'])
			));
			?>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">Topics</div>
			<?php echo $this->element('topics/archive-admin'); ?>
		</div>
	</div>
	<div class="col-sm-4">
		<div class="panel panel-default">
			<div class="panel-heading">Permissions</div>
			<div class="panel-body">
				<dl>
					<dt>Commenters</dt>
					<dd>
					<?php if (!empty($forum['Commenter'])): ?>
						<ul class="list-group">
							<?php echo $liOpen . implode($liClose . $liOpen , Hash::extract($forum, 'Commenter.{n}.full_name')) . $liClose; ?>
						</ul>
					<?php else: ?>
						<p><em>All commenters</em></p>
					<?php endif; ?>
					</dd>

					<dt>Commenter Types</dt>
					<dd>
					<?php if (!empty($forum['CommenterType'])): ?>
						<ul class="list-group">
							<?php echo $liOpen . implode($liClose . $liOpen , Hash::extract($forum, 'CommenterType.{n}.title')) . $liClose; ?>
						</ul>
					<?php else: ?>
						<em>All Commenter Types</em>
					<?php endif; ?>
					</dd>
				</dl>
			</div>
		</div>
	</div>
</div>