<?php
$this->Table->reset();
foreach ($commenters as $commenter): 
	$this->Table->cells([
		[
			$this->Commenter->link($commenter['Commenter'], ['prefix' => true]),
			'Commenters',
		],
	], true);
endforeach;
echo $this->Table->output(array(
	'div' => 'tb-archive tb-commenters-archive'
));