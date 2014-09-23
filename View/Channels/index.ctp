<div class="row">
	<div class="col-sm-8">
		<h2>Message Board Channels</h2>
		<?php echo $this->element('channels/list-with-topics'); ?>
	</div>
	<div class="col-sm-4">
		<?php echo $this->element('TalkBack.topics/updated_list'); ?>
	</div>
</div>