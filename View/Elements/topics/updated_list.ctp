<?php if (!empty($updatedTopics)): ?>
	<?php foreach ($updatedTopics as $title => $sidebarTopicResult): ?>
		<?php if (empty($sidebarTopicResult)): ?>
			<div class="lead"><em>No topics to display</div>
		<?php else: ?>
			<div class="box box-list">
				<h3 class="box-header"><?php echo $title; ?></h3>
				<ul class="nav nav-stacked nav-pills">
				<?php foreach ($sidebarTopicResult as $sidebarTopic): 
					$liClass = '';
					$class = '';
					if (!empty($topic['Topic']) && $sidebarTopic['Topic']['id'] == $topic['Topic']['id']) {
						$liClass = 'active';
					}
					if (empty($sidebarTopic['CurrentCommenterHasRead']['id'])) {
						$class =  'unread';
					}
					
					$title = $this->Text->truncate($sidebarTopic['Topic']['title']);
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
					if (!empty($sidebarTopic['LastComment'])) {
						$title = $this->Comment->quote($sidebarTopic['LastComment'], [
							'before' => $this->Html->tag('h4', $title),
						]);
					} else {
						$title = $this->Html->link($title, $url, ['escape' => false]);
					}
					
					?>
					<li class="<?php echo $liClass;?>"><?php echo $title; ?></li>
				<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>
