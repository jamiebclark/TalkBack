<?php 
echo $this->Form->create(); 
?>
<div class="row">
	<div class="col-sm-6">
		<fieldset>
			<legend>Channel Description</legend><?php
				echo $this->Form->hidden('id');
				echo $this->Form->input('title');
				echo $this->Form->input('description', array('rows' => 10));
		?></fieldset>
		<fieldset>
			<legend>Admins</legend>
			<p>Only the following users will be able to add and make changes to this channel</p><?php
				echo $this->Form->input('AdminCommenter.AdminCommenter', array(
					'label' => 'Administrators',
					'options' => $adminCommenters,
					'id' => 'admin-commenters',
					'after' => $this->Commenter->addInput(array(
						'data-target' => '#admin-commenters',
					))
				));
		?></fieldset>
		<fieldset>
			<legend>Permissions</legend><?php
				echo $this->Form->inputs(array(
					'fieldset' => false,
					'allow_forums' => array(
						'label' => 'Allow all members to create new forums',
					),
					'allow_topics' => array(
						'label' => 'Allow all members to post new topics',
					)
				));
		?></fieldset>
	</div>
	<div class="col-sm-6">
		<fieldset>
			<legend>Channel Access</legend><?php 
				echo $this->Form->input('prefix', array(
					'label' => 'URL Prefix',
					'options' => $prefixes,
					'after' => '<span class="help-block">If you would like to limit this channel to only showing up for a specific page prefix,
						like <strong>/admin/</strong> or <strong>/staff/</strong></span>',
				));
				
				echo $this->Form->input('Commenter.Commenter', array(
					'label' => 'Channel Members',
					'after' => '<span class="help-block"><strong>Only</strong> users added to this list will see this channel</span>' . $this->Commenter->addInput(),
					
				));
				
				echo $this->Form->input('CommenterType.CommenterType', array(
					'label' => 'Member Types',
					'after' => '<span class="help-block">Limit this channel to only specific user types</span>',
				));
		?></fieldset>
	</div>
</div>
<?php 
echo $this->Form->end('Update');
