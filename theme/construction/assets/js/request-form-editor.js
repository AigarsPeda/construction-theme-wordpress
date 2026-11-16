/**
 * Editor side of the "Request form" block: a live server preview plus a link
 * to the form settings (fields, required flags, texts, emails).
 */
(function (wp) {
	'use strict';

	var el = wp.element.createElement;
	var __ = wp.i18n.__;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var ServerSideRender = wp.serverSideRender;
	var settingsUrl = (window.constructionRequestFormEditor || {}).settingsUrl || '';

	wp.blocks.registerBlockType('construction/request-form', {
		edit: function () {
			return el(
				'div',
				useBlockProps(),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Form settings', 'construction') },
						el('p', null, __('Fields, required flags, texts, error messages and the confirmation email are edited per language under Appearance → Request form.', 'construction')),
						settingsUrl
							? el('a', { href: settingsUrl, target: '_blank', rel: 'noopener' }, __('Open form settings', 'construction'))
							: null
					)
				),
				el('div', { style: { pointerEvents: 'none' } }, el(ServerSideRender, { block: 'construction/request-form' }))
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp);
