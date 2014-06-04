<?php 
echo $this->Layout->defaultHeader();
foreach ($comments as $comment): ?>
	<div class="media">
		<h4><?php echo $this->Html->link($comment['Topic']['title'], array(
			'controller' => 'topics', 'action' => 'view', $comment['Topic']['id']
		)); ?></h4>
		<h5><?php echo $this->TalkBack->commenterLink($comment['Commenter']); ?></h5>
		<?php echo $this->DisplayText->text($comment['Comment']['description']); ?>
	</div>
<?php endforeach; ?>