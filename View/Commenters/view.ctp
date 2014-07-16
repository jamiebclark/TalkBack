<h2><?php echo $commenter['Commenter'][Configure::read('TalkBack.Commenter.displayField')]; ?></h2>
<h3>Activity</h3>
<?php echo $this->Paginator->pagination(); ?>
<div class="media-list">
<?php foreach ($comments as $comment): ?>
	<div class="media">
		<?php echo $this->Comment->parentLink($comment); ?>
		<?php echo $this->Comment->title($comment); ?>
		<?php echo $this->Comment->body($comment, ['truncate' => 150]); ?>
	</div>
<?php endforeach; ?>
</div>
<?php echo $this->Paginator->pagination(); ?>