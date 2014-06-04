$(document).on('ready ajaxComplete', function() {
	$('input[name*="add_commenter"]').each(function() {
		var $this = $(this),
			$form = $this.closest('form');

		if ($this.data('target')) {
			var $commenterSelect = $($this.data('target'), $form);
		} else {
			var $commenterSelect = $('select[name="data[Commenter][Commenter][]"]', $form);
		}
			
		if ($commenterSelect.length && $this.data('source')) {
			var selectBg = $commenterSelect.css('background-color'),
				highlightBg = '#D3DCD0';
			if (!selectBg) {
				selectBg = '#FFFFFF';
			}
			$this.autocomplete({
				'source': $this.data('source'),
				'select': function (e, ui) {
					e.preventDefault();
					$(this).val('');
					var $option = $('option[value=' + ui.item.value + ']', $commenterSelect);
					if ($option.length) {
						$option.prop('selected', true);
					} else {
						$commenterSelect
							.append($('<option></option>')
								.attr('value', ui.item.value)
								.text(ui.item.label)
								.prop('selected', true)
							);
					}
					$commenterSelect
						.animate({backgroundColor: highlightBg}, 'fast')
						.animate({backgroundColor: selectBg}, 'fast');
				}
			});
		}
	});
});
