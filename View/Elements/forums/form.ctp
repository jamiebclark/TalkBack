<?php
echo $this->Form->create();
?>
<div class="row">
	<div class="col-md-6"><?php
		echo $this->Form->inputs([
			'id',
			'title',
			'description',
		]);

		if (!empty($currentCommenterIsAdmin)) {
			echo $this->Form->input('active', [
				'class' => 'checkbox',
				'after' => '<span class="help-block">If unchecked, this forum will only be visible to administrators</span>',
			]);

		} else {
			echo $this->Form->hidden('active');
		}

		if ($isAdmin) {
			echo $this->Form->input('channel_id');
		} else {
			echo $this->Form->hidden('channel_id');
		}

		?>
		<?php if ($currentCommenterIsAdmin): ?>
			<fieldset>
				<legend>Permissions</legend>
				<?php 
					echo $this->Form->input('Commenter.Commenter', array(
						'label' => 'Channel Members',
						'after' => $this->Commenter->addInput(),
						'between' => '<span class="help-block"><strong>Only</strong> users added to this list will see this forum</span>'
						
					));
					
					echo $this->Form->input('CommenterType.CommenterType', [
						'label' => 'Member Types',
						'between' => '<span class="help-block">Limit this forum to only specific user types</span>',
					]);
				?>
			</fieldset>
		<?php endif; ?>

		<?php echo $this->Form->button('Update', ['class' => 'btn btn-primary btn-lg']); ?>
	</div>
</div>
<?php echo $this->Form->end(); ?>
