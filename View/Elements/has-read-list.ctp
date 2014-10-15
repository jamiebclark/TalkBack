<div id="tb-has-read" class="tb-has-hread-list box box-list">
	<h2 class="box-header">Commenters have read</h2>
	<?php if (!empty($result['CommenterHasRead'])): ?>
		<ul class="nav nav-stacked">
		<?php foreach ($result['CommenterHasRead'] as $commenterHasRead): 
			$title = $commenterHasRead['Commenter']['full_name'];
			$title = $this->Html->tag('span', date('m/d', strtotime($commenterHasRead['created'])), array('class' => 'badge pull-right')) . $title;
			?>
			<li>
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