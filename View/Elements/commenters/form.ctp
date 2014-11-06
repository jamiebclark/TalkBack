<h2>Commenter Settings</h2><?php
echo $this->Form->create();
echo $this->Form->hidden('id');
echo $this->Form->hidden('commenter_id');
?>
<fieldset id="email-control">
	<legend>Notification Emails</legend>
	<p class="help-block">When should you receive an automated email?</p>
	<?php
	echo $this->Form->input('CommenterEmailControl.email_on_reply', [
		'label' => 'Notify on replies',
		'default' => 1,
	]);
?></fieldset>
<?php echo $this->Form->end('Update'); ?>