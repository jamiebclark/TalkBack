<div class="row">
	<div class="col-sm-4">
		<div class="box box-list">
			<h2 class="box-header tb-message-list-heading">
				<div class="pull-right tb-message-list-nav"><?php 
					echo $this->Html->link(
						'<i class="fa fa-plus"></i> Compose', 
						array('action' => 'add'), 
						array('class' => 'ajax-modal btn btn-large btn-default', 'escape' => false)
					); 
				?></div>
				<?php echo $title_for_layout; ?>
			</h2>
			<div id="tb-messages-list">
				<?php echo $this->element('messages/archive'); ?>
			</div>
		</div>
	</div>
	<div class="col-sm-8">
		<div id="tb-message-window" data-url="<?php echo Router::url(array('action' => 'view', $activeMessageId)); ?>">
			<h3>Get Started!</h3>
			<p>
			<?php if (empty($messages)): ?>
				Looks like you haven't messaged anyone yet! 
			<?php endif; ?>
			<?php echo $this->Html->link(
				'Compose a new message', 
				array('action' => 'add'), 
				array('class' => 'ajax-modal btn btn-large btn-default')
			); ?>
			</p>
		</div>
	</div>
</div>
<?php $this->Html->scriptStart(array('inline' => false)); ?>
(function($) {
	$.fn.loadMessage = function(url) {
		return this.each(function() {
			var $window = $('#tb-message-window'),
				$messageLinks = $('.tb-messages');
			$window.addClass('loading');
			$(this).removeClass('unread');
			console.log(url);
			$.ajax(url, {
				dataType: 'html'
			})
			.done(function(data) {
				if ($(data).hasClass('tb-message-view')) {
					var $content = $(data);
				} else {
					var $content = $('.tb-message-view', $(data));
				}
				$window.html($content.html()).removeClass('loading');
				$('.tb-messages a').each(function() {
					if ($(this).attr('href') == url) {
						$(this).addClass('active');
					} else {
						$(this).removeClass('active');
					}
				});
			});
		});
	};
})(jQuery);

$(document).ready(function() {
	$('.tb-message').click(function(e) {
		e.preventDefault();
		$(this).loadMessage($(this).attr('href'));
	});
	
	if ($('#tb-message-window').data('url')) {
		$('#tb-message-window').loadMessage($('#tb-message-window').data('url'));
	}
});

<?php $this->Html->scriptEnd(); ?>