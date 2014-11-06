<div class="row">
	<div class="col-md-8">
		<h2><?php echo $channel['Channel']['title']; ?></h2>

		<div class="btn-group btn-grou-nav">
			<?php if (!empty($talkBackPermissions['Channel']['canEdit'])): ?>
				<?php echo $this->Html->link('Edit Channel',
					array('action' => 'edit', $channel['Channel']['id'], 'admin' => true),
					array('class' => 'btn btn-default', 'escape' => false)
				); ?>
			<?php endif; ?>
			<?php if (!empty($talkBackPermissions['Channel']['canCreateForum'])): ?>
				<?php echo $this->Html->link('Add new Forum',
					array('controller' => 'forums', 'action' => 'add', $channel['Channel']['id']),
					array('class' => 'btn btn-default', 'escape' => false)
				); ?>
			<?php endif; ?>
		</div>
		
		<?php if (!empty($channel['Channel']['description'])): ?>
			<div class="tb-description">
				<?php echo nl2br($channel['Channel']['description']); ?>
			</div>
		<?php endif; ?>

		<?php echo $this->element('forums/archive'); ?>
	</div>
	<div class="col-md-4">
		<div class="panel panel-default">
			<div class="panel-heading">
				<?php echo $this->Html->link('Users', array('action' => 'commenters', $channel['Channel']['id'])); ?>
			</div>
			<div class="panel-body"><?php echo $this->Html->link(
				sprintf('<strong>%s</strong> users', number_format($commenterCount)),
				array('action' => 'commenters', $channel['Channel']['id']),
				array('escape' => false)
			); ?>
			</div>
		</div>

		<?php echo $this->element('TalkBack.topics/sidebar'); ?>
	</div>
</div>
