<div class="tb-message-view">
	<h3 class="tb-message-view-title"><?php echo $message['Message']['subject']; ?></h3>
	<dl class="dl dl-horizontal">
		<dt>Participants:</dt>
		<dd><?php 
			$list = array();
			foreach ($message['Commenter'] as $commenter):
				$list[] = $this->Commenter->link($commenter);
			endforeach;
			echo $this->Text->toList($list);
		?></dd>
	</dl>

	<?php echo $this->element('comments', array(
		'model' => 'TalkBack.Message',
		'modelId' => $message['Message']['id'],
		'form' => 'top',
		'label' => array(
			'singular' => 'Reply',
			'plural' => 'Replies',
		),
		'class' => 'tb-comments-lg',
	)); ?>
</div>