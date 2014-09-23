<div class="row">
	<div class="col-md-8">
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
					array('action' => 'delete', $forum['Forum']['id'], 'admin' => true),
					array('class' => 'btn btn-danger', 'escape' => false),
					'Are you sure you want to delete this forum? All associated topics will also be delete'
				); ?>
			<?php endif; ?>

			<?php echo $this->Html->link(
				'<i class="fa fa-plus"></i> Add new Topic',
				array('controller' => 'topics', 'action' => 'add', $forum['Forum']['id']),
				array('class' => 'btn btn-primary ajax-modal', 'data-modal-title' => 'Add a new Topic', 'escape' => false)
			); ?>
		</div>
		
		<?php if (!empty($forum['Forum']['description'])): ?>
			<div class="tb-description">
				<?php echo nl2br($forum['Forum']['description']); ?>
			</div>
		<?php endif; ?>

		<?php echo $this->element('topics/archive'); ?>

		<?php echo $this->Html->link(	
			'Add Topic', 
			array('controller' => 'topics', 'action' => 'add', $forum['Forum']['id']),
			array('class' => 'btn btn-default btn-primary ajax-modal', 'data-modal-title' => 'Add a new Topic')
		);?>
	</div>
	<div class="col-md-4">
		<?php echo $this->element('TalkBack.topics/sidebar'); ?>
	</div>
</div>
