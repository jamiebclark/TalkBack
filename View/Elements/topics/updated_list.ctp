<?php if (!empty($updatedTopics)): ?>
	<?php foreach ($updatedTopics as $title => $sidebarTopicResult): ?>
		<?php if (empty($sidebarTopicResult)): ?>
			<?php // Empty display ?>
			<?php // <div class="lead"><em>No topics to display</div> ?>
		<?php else: ?>
			<div class="panel panel-default">
				<div class="panel-heading"><?php echo $title; ?></div>
				<div class="list-group">
				<?php foreach ($sidebarTopicResult as $sidebarTopic): 
					$class = 'list-group-item';
					if (!empty($topic['Topic']) && $sidebarTopic['Topic']['id'] == $topic['Topic']['id']) {
						$class .= ' active';
					}
					if (empty($sidebarTopic['CurrentCommenterHasRead']['id'])) {
						$class .=  ' unread';
					}
					$title = $this->Text->truncate($sidebarTopic['Topic']['title']);
					$title = $this->Html->tag('h6', $title, ['class' => 'tb-topic-comment-quote-title']);

					if (!empty($sidebarTopic[0]['total_unread'])) {
						$title .= $this->Html->tag('span', 
							'+' . $sidebarTopic[0]['total_unread'], [
							'class' => 'label label-success pull-right'
						]);
					}
					
					$url = [
						'controller' => 'topics', 
						'action' => 'view', 
						$sidebarTopic['Topic']['id'],
						'plugin' => 'talk_back',
					];
					if (!empty($sidebarTopic['LastComment']['id'])) {
						echo $this->Comment->quote($sidebarTopic['LastComment'], [
							'class' => $class,
							'before' => $title,
						]);
					} else {
						echo $this->Html->link($title, $url, ['escape' => false, 'class' => $class]);
					}
					?>
				<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
