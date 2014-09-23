<?php
echo $this->Form->create();
?>
<div class="row">
	<div class="col-md-6"><?php
		echo $this->Form->inputs(array(
			'id',
			'title',
			'description',
		));
		if ($isAdmin) {
			echo $this->Form->input('channel_id');
		} else {
			echo $this->Form->hidden('channel_id');
		}

		echo $this->Form->submit('Update');
	?></div>
	<?php if ($isAdmin): ?>
		<div class="col-sm-6">
		<fieldset>
			<legend>Permissions</legend>
			<?php 
				echo $this->Form->input('Commenter.Commenter', array(
					'label' => 'Channel Members',
					'after' => $this->Commenter->addInput(),
					'between' => '<span class="help-block"><strong>Only</strong> users added to this list will see this forum</span>'
					
				));
				
				echo $this->Form->input('CommenterType.CommenterType', array(
					'label' => 'Member Types',
					'between' => '<span class="help-block">Limit this forum to only specific user types</span>',
				));
			?>
		</fieldset>
		</div>
	<?php endif; ?>
</div>
<?php echo $this->Form->end(); ?>
