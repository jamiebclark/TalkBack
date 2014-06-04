
<div class="tb-messages media-list">
<?php //echo $this->Paginator->pagination(); ?>
<?php foreach ($messages as $message): 
	$class = '';
	if (isset($this->viewVars['activeMessageId']) && $this->viewVars['activeMessageId'] == $message['Message']['id']) {
		$class .= ' active';
	} else if (isset($message['CurrentCommenterHasRead']) && empty($message['CurrentCommenterHasRead']['id'])) {
		$class .= ' unread';
	}
	$url = Router::url(array('action' => 'view', $message['Message']['id']));
	?>
	<a href="<?php echo $url; ?>" class="tb-message media <?php echo $class; ?>">
		<div class="pull-left">
			<?php echo $this->Message->thumb($message, array('class' => 'media-object')); ?>
		</div>
		<div class="media-body">
			<h4 class="tb-message-date pull-right">
				<?php echo $this->Time->timeAgoInWords(
					$message['LastComment']['created'],
					array(
						'end' => '1 month',
						'format' => 'F js, Y',
					)
				); ?>
			</h4>
			<h3 class="tb-message-title media-title">
				<?php echo $message['Message']['subject']; ?>
			</h3>
			<div class="tb-message-exceprt">
				<?php echo $this->Comment->body($message['LastComment'], array(
					'html' => false,
					'truncate' => 50,
				)); ?>
			</div>
		</div>
	</a>
<?php endforeach; ?>
<?php echo $this->Paginator->pagination(); ?>
</div>
