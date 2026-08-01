/**
 * Project gallery media picker + drag reorder.
 */
(function ($) {
	'use strict';

	var frame;
	var $list = $('#construction-project-gallery-list');
	var i18n = window.constructionProjectsAdmin || {};

	function itemHtml(attachment) {
		var url = attachment.url || '';
		if (attachment.sizes) {
			if (attachment.sizes.thumbnail) {
				url = attachment.sizes.thumbnail.url;
			} else if (attachment.sizes.medium) {
				url = attachment.sizes.medium.url;
			}
		}
		var removeLabel = i18n.remove || 'Remove';
		return (
			'<li data-id="' +
			attachment.id +
			'">' +
			'<img src="' +
			url +
			'" alt="" />' +
			'<button type="button" class="button-link construction-project-gallery-remove" aria-label="' +
			removeLabel +
			'">&times;</button>' +
			'<input type="hidden" name="construction_project_gallery[]" value="' +
			attachment.id +
			'" />' +
			'</li>'
		);
	}

	function existingIds() {
		var ids = [];
		$list.find('li').each(function () {
			ids.push(String($(this).data('id')));
		});
		return ids;
	}

	if ($list.length && $.fn.sortable) {
		$list.sortable({
			items: 'li',
			placeholder: 'ui-sortable-placeholder',
			tolerance: 'pointer',
		});
	}

	$('#construction-project-gallery-add').on('click', function (e) {
		e.preventDefault();

		if (frame) {
			frame.open();
			return;
		}

		frame = wp.media({
			title: i18n.title || 'Select project images',
			button: { text: i18n.button || 'Add to gallery' },
			library: { type: 'image' },
			multiple: true,
		});

		frame.on('select', function () {
			var selection = frame.state().get('selection');
			var have = existingIds();
			selection.each(function (model) {
				var attachment = model.toJSON();
				if (have.indexOf(String(attachment.id)) !== -1) {
					return;
				}
				$list.append(itemHtml(attachment));
			});
		});

		frame.open();
	});

	$list.on('click', '.construction-project-gallery-remove', function (e) {
		e.preventDefault();
		$(this).closest('li').remove();
	});
})(jQuery);
