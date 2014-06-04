<?php
echo $this->Form->create();
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
echo $this->Form->end('Update');
