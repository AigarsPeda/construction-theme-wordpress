/**
 * Project CPT block editor: one language switcher for title + content.
 * Loads from i18n meta into the main title + block editor (same as pages).
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

	function i18nHasAnyContent(map) {
		return LANGUAGES.some(function (lang) {
			var row = map[lang.slug] || {};
			return !!(row.title || row.excerpt);
		});
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

	function LanguagePanel() {
		var meta = useSelect(function (select) {
			return select('core/editor').getEditedPostAttribute('meta') || {};
		}, []);
		var postType = useSelect(function (select) {
			return select('core/editor').getCurrentPostType();
		}, []);
		var postId = useSelect(function (select) {
			return select('core/editor').getCurrentPostId();
		}, []);
		var isReady = useSelect(function (select) {
			var editor = select('core/editor');
			if (!editor.getCurrentPostId()) {
				return false;
			}
			// Wait until the post entity has been resolved (meta available).
			return !editor.isEditedPostDirty() || !!editor.getCurrentPost();
		}, []);
		var blocks = useSelect(function (select) {
			return select('core/block-editor').getBlocks();
		}, []);
		var title = useSelect(function (select) {
			return select('core/editor').getEditedPostAttribute('title') || '';
		}, []);
		var content = useSelect(function (select) {
			return select('core/editor').getEditedPostContent() || '';
		}, []);
		var isSaving = useSelect(function (select) {
			return select('core/editor').isSavingPost();
		}, []);

		var editPost = useDispatch('core/editor').editPost;
		var resetBlocks = useDispatch('core/block-editor').resetBlocks;

		var rawMeta = meta[META_I18N];
		var metaResolved = Object.prototype.hasOwnProperty.call(meta, META_I18N);
		var i18n = normalizeI18n(rawMeta);
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
		var switching = useRef(false);

		function applyLanguage(lang, nextI18n, opts) {
			opts = opts || {};
			var row = nextI18n[lang] || { title: '', excerpt: '' };
			var nextTitle = row.title || '';
			var nextExcerpt = row.excerpt || '';
			// Never blank out a filled editor with empty i18n (race / missing meta).
			if (opts.preserveIfEmpty) {
				if (!nextTitle && title) {
					nextTitle = title;
					nextI18n[lang] = Object.assign({}, row, { title: nextTitle });
				}
				if (!nextExcerpt && content) {
					nextExcerpt = content;
					nextI18n[lang] = Object.assign({}, nextI18n[lang], { excerpt: nextExcerpt });
				}
			}
			switching.current = true;
			editPost({
				title: nextTitle,
				meta: Object.assign({}, meta, {
					[META_I18N]: nextI18n,
					[META_LANG]: lang,
				}),
			});
			resetBlocks(plainToBlocks(nextExcerpt));
			window.setTimeout(function () {
				switching.current = false;
			}, 100);
		}

		function syncCurrentToMeta(force) {
			if (switching.current || !booted.current) {
				return;
			}
			var currentHtml = serialize(blocks);
			var currentTitle = title;
			var nextI18n = normalizeI18n(meta[META_I18N]);
			var prev = nextI18n[activeLang] || { title: '', excerpt: '' };

			// Do not overwrite stored translations with empty editor state.
			if (!force && !currentTitle && !currentHtml.trim() && (prev.title || prev.excerpt)) {
				return;
			}
			if (
				!force &&
				(prev.excerpt || '') === currentHtml &&
				(prev.title || '') === currentTitle
			) {
				return;
			}
			nextI18n[activeLang] = {
				title: currentTitle || prev.title || '',
				excerpt: currentHtml || prev.excerpt || '',
			};
			editPost({
				meta: Object.assign({}, meta, {
					[META_I18N]: nextI18n,
					[META_LANG]: activeLang,
				}),
			});
		}

		// Boot once meta is present (or clearly absent after post load).
		useEffect(
			function () {
				if (postType !== 'construction_project' || booted.current || !postId) {
					return;
				}
				// Underscore meta may arrive a tick after the post; wait until key exists
				// or we already have title/content to seed from.
				if (!metaResolved && !title && !content) {
					return;
				}

				booted.current = true;
				var nextI18n = normalizeI18n(rawMeta);

				// Seed from current editor fields if meta is empty.
				if (!i18nHasAnyContent(nextI18n)) {
					nextI18n.lv = {
						title: title || '',
						excerpt: content || '',
					};
					editPost({
						meta: Object.assign({}, meta, {
							[META_I18N]: nextI18n,
							[META_LANG]: 'lv',
						}),
					});
					return;
				}

				var lang = activeLang;
				var row = nextI18n[lang] || { title: '', excerpt: '' };
				if (!row.title && !row.excerpt && (nextI18n.lv.title || nextI18n.lv.excerpt)) {
					lang = 'lv';
				}
				applyLanguage(lang, nextI18n, { preserveIfEmpty: true });
			},
			[postType, postId, metaResolved, rawMeta, title, content, activeLang]
		);

		useEffect(
			function () {
				if (postType !== 'construction_project' || !booted.current || switching.current) {
					return;
				}
				if (syncTimer.current) {
					window.clearTimeout(syncTimer.current);
				}
				syncTimer.current = window.setTimeout(function () {
					syncCurrentToMeta(false);
				}, 500);
				return function () {
					if (syncTimer.current) {
						window.clearTimeout(syncTimer.current);
					}
				};
			},
			[blocks, title, activeLang, postType]
		);

		useEffect(
			function () {
				if (postType !== 'construction_project') {
					return;
				}
				if (isSaving && !wasSaving.current) {
					syncCurrentToMeta(true);
				}
				if (!isSaving && wasSaving.current) {
					// After save, PHP may reset post_title to LV — restore active language view.
					window.setTimeout(function () {
						var m = wp.data.select('core/editor').getEditedPostAttribute('meta') || {};
						var nextI18n = normalizeI18n(m[META_I18N]);
						var lang = m[META_LANG] || activeLang;
						if (i18nHasAnyContent(nextI18n)) {
							applyLanguage(lang, nextI18n, { preserveIfEmpty: true });
						}
					}, 150);
				}
				wasSaving.current = !!isSaving;
			},
			[isSaving, postType]
		);

		if (postType !== 'construction_project') {
			return null;
		}

		function switchLang(nextLang) {
			if (nextLang === activeLang || switching.current) {
				return;
			}
			var currentHtml = serialize(blocks);
			var nextI18n = normalizeI18n(i18n);
			nextI18n[activeLang] = {
				title: title,
				excerpt: currentHtml,
			};
			applyLanguage(nextLang, nextI18n);
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'construction-project-language',
				title: S.panel || 'Language',
				className: 'construction-project-desc-lang-panel',
			},
			el('p', { style: { marginTop: 0, fontSize: '12px', color: '#50575e' } }, S.help || ''),
			el(
				'p',
				{ style: { marginBottom: '8px', fontWeight: 600, fontSize: '12px' } },
				(S.editing || 'Editing:') + ' ' + activeLang.toUpperCase()
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

	function VisibilityPanel() {
		var postType = useSelect(function (select) {
			return select('core/editor').getCurrentPostType();
		}, []);
		var status = useSelect(function (select) {
			return select('core/editor').getEditedPostAttribute('status') || 'draft';
		}, []);
		var isSaving = useSelect(function (select) {
			return select('core/editor').isSavingPost();
		}, []);
		var editPost = useDispatch('core/editor').editPost;
		var savePost = useDispatch('core/editor').savePost;
		var trashPost = useDispatch('core/editor').trashPost;

		if (postType !== 'construction_project') {
			return null;
		}

		var isEnabled = status === 'publish';

		function setEnabled(enabled) {
			editPost({ status: enabled ? 'publish' : 'draft' });
			window.setTimeout(function () {
				savePost();
			}, 0);
		}

		function deleteProject() {
			var msg =
				S.deleteConfirm || 'Move this project to Trash? You can restore it later from Trash.';
			if (!window.confirm(msg)) {
				return;
			}
			trashPost();
		}

		return el(
			PluginDocumentSettingPanel,
			{
				name: 'construction-project-visibility',
				title: S.visibility || 'On the website',
				className: 'construction-project-visibility-panel',
			},
			el(
				'p',
				{ style: { marginTop: 0, fontSize: '12px', color: '#50575e' } },
				S.visibilityHelp || ''
			),
			el(
				'p',
				{
					style: {
						margin: '0 0 10px',
						fontWeight: 600,
						fontSize: '12px',
						color: isEnabled ? '#007017' : '#9a6700',
					},
				},
				isEnabled ? S.enabled || 'Visible on the site' : S.disabled || 'Disabled (hidden)'
			),
			el(
				'div',
				{ style: { display: 'flex', flexWrap: 'wrap', gap: '8px' } },
				el(
					Button,
					{
						variant: isEnabled ? 'secondary' : 'primary',
						isBusy: isSaving,
						disabled: isSaving,
						onClick: function () {
							setEnabled(!isEnabled);
						},
					},
					isEnabled ? S.disable || 'Disable project' : S.enable || 'Enable project'
				),
				el(
					Button,
					{
						variant: 'tertiary',
						isDestructive: true,
						disabled: isSaving,
						onClick: deleteProject,
					},
					S.delete || 'Delete project'
				)
			)
		);
	}

	registerPlugin('construction-project-language', {
		render: LanguagePanel,
		icon: 'translation',
	});

	registerPlugin('construction-project-visibility', {
		render: VisibilityPanel,
		icon: 'visibility',
	});
})(window.wp);
