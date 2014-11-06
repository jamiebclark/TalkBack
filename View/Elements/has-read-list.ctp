<div id="tb-has-read" class="tb-has-hread-list panel panel-default">
	<div class="panel-heading">Commenters have read</div>
	<?php if (!empty($result['CommenterHasRead'])): ?>
		<ul class="list-group">
		<?php foreach ($result['CommenterHasRead'] as $commenterHasRead): 
			$title = $commenterHasRead['Commenter']['full_name'];
			$title = $this->Html->tag('span', date('m/d', strtotime($commenterHasRead['created'])), array('class' => 'badge pull-right')) . $title;
			?>
			<li class="list-group-item">
				<?php echo $this->Html->link(
					$title,
					array('controller' => 'commenters', 'action' => 'view', $commenterHasRead['Commenter']['id']),
					array('escape' => false)
				); ?>
			</li>
		<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</div>