<div class="row">
	<div class="col-md-8">
		<?php echo $this->element('channels/crumbs'); ?>
		<h2><?php echo $channel['Channel']['title']; ?></h2>
		<p><?php echo $channel['Channel']['description']; ?></p>
		<?php echo $this->element('forums/archive'); ?>
	</div>
	<div class="col-md-4">
		<?php echo $this->element('TalkBack.topics/sidebar'); ?>
	</div>
</div>
