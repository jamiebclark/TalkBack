<?php
echo $this->Layout->defaultHeader();
$this->Table->reset();
foreach ($channels as $channel):
	$this->Table->cells(array(
		array($this->ModelView->link($channel['Channel']), 'Channel'),
		array($channel['Channel']['prefix'], 'Prefix'),
		array(number_format($channel['Channel']['forum_count']), 'Forums'),
		array(number_format($channel['Channel']['topic_count']), 'Topics'),
		array($this->ModelView->actionMenu(array('view', 'edit', 'delete'), $channel['Channel']), 'Actions'),
	), true);
endforeach;