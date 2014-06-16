<?php if (!empty($sidebarTopics)): ?>
	<?php foreach ($sidebarTopics as $title => $sidebarTopicResult): ?>
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
				$title = '';
				
				if (!empty($sidebarTopic['LastComment'])) {
					$title .= '"' . $this->Text->truncate($sidebarTopic['LastComment']['body']) . '"' . '<br/>';
				}
				if (!empty($sidebarTopic['LastComment']['Commenter'])) {
					$title .= $this->Commenter->name($sidebarTopic['LastComment']['Commenter']) . '<br/>';
				}
				$title .= $this->Text->truncate($sidebarTopic['Topic']['title']);
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
				?>
				<li class="<?php echo $liClass;?>"><?php
					echo $this->Html->link($title, $url, compact('class') + ['escape' => false]);
				?></li>
			<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
<?php endif; ?>
