/**
 * Project CPT block editor: switch description language (LV / EN / RU).
 * Content uses the same block editor as pages; each language is stored in i18n meta.
 */
(function (wp) {
	'use strict';

	var el = wp.element.createElement;
	var useEffect = wp.element.useEffect;
	var useRef = wp.element.useRef;
	var registerPlugin = wp.plugins.registerPlugin;
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel;
	var Button = wp.components.Button;
	var useSelect = wp.data.useSelect;
	var useDispatch = wp.data.useDispatch;
	var parse = wp.blocks.parse;
	var serialize = wp.blocks.serialize;

	var cfg = window.constructionProjectsEditor || {};
	var LANGUAGES = Array.isArray(cfg.languages) && cfg.languages.length
		? cfg.languages
		: [
				{ slug: 'lv', name: 'Latviešu' },
				{ slug: 'en', name: 'English' },
				{ slug: 'ru', name: 'Русский' },
		  ];
	var META_I18N = cfg.metaI18n || '_construction_project_i18n';
	var META_LANG = cfg.metaLang || '_construction_project_editing_lang';
	var S = cfg.strings || {};

	function emptyI18n() {
		var map = {};
		LANGUAGES.forEach(function (lang) {
			map[lang.slug] = { title: '', excerpt: '' };
		});
		return map;
	}

	function normalizeI18n(raw) {
		var map = emptyI18n();
		if (!raw || typeof raw !== 'object') {
			return map;
		}
		LANGUAGES.forEach(function (lang) {
			var row = raw[lang.slug] || {};
			map[lang.slug] = {
				title: row.title != null ? String(row.title) : '',
				excerpt: row.excerpt != null ? String(row.excerpt) : '',
			};
		});
		return map;
	}

	function plainToBlocks(text) {
		var t = (text || '').trim();
		if (!t) {
			return [];
		}
		if (t.indexOf('<!-- wp:') !== -1 || t.indexOf('<') !== -1) {
			return parse(t);
		}
		return parse(
			'<!-- wp:paragraph -->\n<p>' +
				t.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;') +
				'</p>\n<!-- /wp:paragraph -->'
		);
	}

	function DescriptionLanguagePanel() {
		var meta = useSelect(function (select) {
			return select('core/editor').getEditedPostAttribute('meta') || {};
		}, []);
		var postType = useSelect(function (select) {
			return select('core/editor').getCurrentPostType();
		}, []);
		var blocks = useSelect(function (select) {
			return select('core/block-editor').getBlocks();
		}, []);
		var isSaving = useSelect(function (select) {
			return select('core/editor').isSavingPost();
		}, []);

		var editPost = useDispatch('core/editor').editPost;
		var resetBlocks = useDispatch('core/block-editor').resetBlocks;

		var i18n = normalizeI18n(meta[META_I18N]);
		var activeLang =
			meta[META_LANG] && String(meta[META_LANG])
				? String(meta[META_LANG])
				: 'lv';
		if (
			!LANGUAGES.some(function (l) {
				return l.slug === activeLang;
			})
		) {
			activeLang = 'lv';
		}

		var booted = useRef(false);
		var syncTimer = useRef(null);
		var wasSaving = useRef(false);

		function syncCurrentToMeta(force) {
			var currentHtml = serialize(blocks);
			var nextI18n = normalizeI18n(meta[META_I18N]);
			if (!force && (nextI18n[activeLang].excerpt || '') === currentHtml) {
				return;
			}
			nextI18n[activeLang] = Object.assign({}, nextI18n[activeLang], {
				excerpt: currentHtml,
			});
			editPost({
				meta: Object.assign({}, meta, {
					[META_I18N]: nextI18n,
					[META_LANG]: activeLang,
				}),
			});
		}

		useEffect(
			function () {
				if (postType !== 'construction_project' || booted.current) {
					return;
				}
				booted.current = true;
				var desired = (i18n[activeLang] && i18n[activeLang].excerpt) || '';
				var current = serialize(blocks);
				if (desired.trim() && desired.trim() !== current.trim()) {
					resetBlocks(plainToBlocks(desired));
				} else if (!desired.trim() && !current.trim() && i18n.lv && i18n.lv.excerpt) {
					editPost({
						meta: Object.assign({}, meta, {
							[META_LANG]: 'lv',
						}),
					});
					resetBlocks(plainToBlocks(i18n.lv.excerpt));
				}
			},
			[postType]
		);

		useEffect(
			function () {
				if (postType !== 'construction_project' || !booted.current) {
					return;
				}
				if (syncTimer.current) {
					clearTimeout(syncTimer.current);
				}
				syncTimer.current = setTimeout(function () {
					syncCurrentToMeta(false);
				}, 400);
				return function () {
					if (syncTimer.current) {
						clearTimeout(syncTimer.current);
					}
				};
			},
			[blocks, activeLang, postType]
		);

		useEffect(
			function () {
				if (postType !== 'construction_project') {
					return;
				}
				if (isSaving && !wasSaving.current) {
					syncCurrentToMeta(true);
				}
				wasSaving.current = !!isSaving;
			},
			[isSaving, postType, blocks, activeLang, meta]
		);

		if (postType !== 'construction_project') {
			return null;
		}

		function switchLang(nextLang) {
			if (nextLang === activeLang) {
				return;
			}
			var currentHtml = serialize(blocks);
			var nextI18n = normalizeI18n(i18n);
			nextI18n[activeLang] = Object.assign({}, nextI18n[activeLang], {
				excerpt: currentHtml,
			});
			var nextHtml = (nextI18n[nextLang] && nextI18n[nextLang].excerpt) || '';
			editPost({
				meta: Object.assign({}, meta, {
					[META_I18N]: nextI18n,
					[META_LANG]: nextLang,
				}),
			});
			resetBlocks(plainToBlocks(nextHtml));
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'construction-project-description-lang',
				title: S.panel || 'Description language',
				className: 'construction-project-desc-lang-panel',
			},
			el('p', { style: { marginTop: 0, fontSize: '12px', color: '#50575e' } }, S.help || ''),
			el(
				'p',
				{ style: { marginBottom: '8px', fontWeight: 600, fontSize: '12px' } },
				(S.editing || 'Editing description:') + ' ' + activeLang.toUpperCase()
			),
			el(
				'div',
				{ className: 'construction-project-desc-lang' },
				LANGUAGES.map(function (lang) {
					return el(
						Button,
						{
							key: lang.slug,
							variant: activeLang === lang.slug ? 'primary' : 'secondary',
							onClick: function () {
								switchLang(lang.slug);
							},
						},
						lang.name || lang.slug.toUpperCase()
					);
				})
			)
		);
	}

	registerPlugin('construction-project-description-lang', {
		render: DescriptionLanguagePanel,
		icon: 'translation',
	});
})(window.wp);
