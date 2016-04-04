<div class="row">
	<div class="col-md-8">
		<div class="panel panel-default">
			<div class="panel-body">
				<?php if (empty($forum['Forum']['active'])): ?>
					<div class="alert alert-warning">
						<h3 class="alert-title">Forum Inactive</h3>
						<p>This forum has been marked as <em>INACTIVE</em>. Only administrators can view it.</p>
					</div>
				<?php endif; ?>

				<h2><?php echo $forum['Forum']['title']; ?></h2>

				<div class="btn-group btn-grou-nav">
					<?php if ($currentCommenterIsAdmin): ?>
						<?php echo $this->Html->link(
							'<i class="fa fa-edit"></i> Edit Forum',
							array('action' => 'edit', $forum['Forum']['id'], 'admin' => true),
							array('class' => 'btn btn-default', 'escape' => false)
						); ?>
						<?php echo $this->Html->link(
							'<i class="fa fa-times"></i> Delete Forum',
							array('action' => 'delete', $forum['Forum']['id'], 'admin' => true),[
	'class' => 'btn btn-danger',
	'escape' => false,
	'confirm' => 'Are you sure you want to delete this forum? All associated topics will also be delete'
]); ?>
					<?php endif; ?>

					<?php if ($canTopicBeAdded): ?>
						<?php echo $this->Html->link(
							'<i class="fa fa-plus"></i> Add new Topic',
							array('controller' => 'topics', 'action' => 'add', $forum['Forum']['id']),
							array('class' => 'btn btn-primary ajax-modal', 'data-modal-title' => 'Add a new Topic', 'escape' => false)
						); ?>
					<?php endif; ?>
				</div>
				
				<?php if (!empty($forum['Forum']['description'])): ?>
					<div class="tb-description">
						<?php echo $this->DisplayText->text($forum['Forum']['description']); ?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="panel panel-default">
			<?php echo $this->element('topics/archive'); ?>
			<div class="panel-footer">
				<?php if ($canTopicBeAdded): ?>
					<?php echo $this->Html->link(	
						'<i class="fa fa-plus"></i> Add Topic', 
						array('controller' => 'topics', 'action' => 'add', $forum['Forum']['id']),
						array(
							'class' => 'btn btn-default btn-primary ajax-modal', 
							'data-modal-title' => 'Add a new Topic',
							'escape' => false,
						)
					);?>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="col-md-4">
		<?php echo $this->element('TalkBack.topics/sidebar'); ?>
	</div>
</div>
