(function ($) {
	'use strict';

	var frame;
	var $id = $('#construction_logo_id');
	var $preview = $('#construction-logo-preview img');
	var $remove = $('#construction-logo-remove');
	var placeholder =
		(window.constructionSettings && constructionSettings.placeholderLogo) ||
		$preview.attr('src');

	$('#construction-logo-upload').on('click', function (e) {
		e.preventDefault();

		if (frame) {
			frame.open();
			return;
		}

		frame = wp.media({
			title: 'Select logo',
			button: { text: 'Use this logo' },
			library: { type: 'image' },
			multiple: false,
		});

		frame.on('select', function () {
			var attachment = frame.state().get('selection').first().toJSON();
			var url = attachment.url || '';
			if (attachment.sizes && attachment.sizes.medium) {
				url = attachment.sizes.medium.url;
			} else if (attachment.sizes && attachment.sizes.thumbnail) {
				url = attachment.sizes.thumbnail.url;
			}
			$id.val(attachment.id);
			$preview.attr('src', url || attachment.url);
			$remove.show();
		});

		frame.open();
	});

	$remove.on('click', function (e) {
		e.preventDefault();
		$id.val('0');
		$preview.attr('src', placeholder);
		$remove.hide();
	});
})(jQuery);
