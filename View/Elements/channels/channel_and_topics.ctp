<div class="tb-channel-and-topics">
	<?php foreach ($channels as $channel): ?>
		<div class="tb-channel-and-topic">
			<h2><?php echo $channel['Channel']['title']; ?></h2>
			<?php if (!empty($channel['Channel']['description'])) : ?>
				<div><?php echo $channel['Channel']['description']; ?></div>
			<?php endif; ?>
			<?php if (!empty($channel['Forum'])):
				$forums = array();
				$forums = Hash::insert($forums, '{n}.Forum', Hash::extract($channel, 'Forum.{n}'));
				echo $this->element('TalkBack.forums/archive', array('forums' => $channel));
			endif; ?>
		</div>
	<?php endforeach; ?>
</div>