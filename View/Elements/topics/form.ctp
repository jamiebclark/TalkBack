<div class="row">
	<div class="col-sm-8 col-md-6">
		<?php
		echo $this->Form->create();
		echo $this->Form->inputs(array(
			'id',
			'title',
			'body',
		));

		echo $this->Form->hidden('commenter_id', array('default' => $currentCommenter['Commenter']['id']));
		echo $this->Form->hidden('forum_id');
		echo $this->Form->end('Update');
	?>
	</div>
</div>