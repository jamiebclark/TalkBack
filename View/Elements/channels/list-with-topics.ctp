<div class="tb-list-with-topics">
	<?php foreach ($channels as $channel): ?>
		<div class="tb-list-with-topic">
			<div class="panel panel-default">
				<div class="panel-heading">
					<?php echo $this->Html->link(
						$channel['Channel']['title'],
						array('controller' => 'channels', 'action' => 'view', $channel['Channel']['id'], 'plugin' => 'talk_back'),
						['class' => 'panel-title']
					); ?>
				</div>

				<?php if (!empty($channel['Channel']['description'])) : ?>
					<div class="tb-description"><?php echo $channel['Channel']['description']; ?></div>
				<?php endif; ?>

				<?php if (!empty($channel['Forum'])):
					$forums = array();
					$forums = Hash::insert($forums, '{n}.Forum', Hash::extract($channel, 'Forum.{n}'));
					echo $this->element('TalkBack.forums/archive', array('forums' => $channel));
				endif; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>