/**
 * Construction block editor: Projects grid / Home projects with click-to-edit modal.
 * One post per project; LV/EN/RU titles live in the i18n REST field.
 */
(function (wp) {
	'use strict';

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var useState = wp.element.useState;
	var useEffect = wp.element.useEffect;
	var useCallback = wp.element.useCallback;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var MediaUpload = wp.blockEditor.MediaUpload;
	var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
	var apiFetch = wp.apiFetch;
	var addQueryArgs = wp.url.addQueryArgs;
	var components = wp.components;
	var Button = components.Button;
	var Modal = components.Modal;
	var TextControl = components.TextControl;
	var TextareaControl = components.TextareaControl;
	var Spinner = components.Spinner;
	var Notice = components.Notice;
	var Placeholder = components.Placeholder;

	var cfg = window.constructionBlocksEditor || {};
	var S = cfg.strings || {};
	var LANGUAGES = Array.isArray(cfg.languages) && cfg.languages.length
		? cfg.languages
		: [
				{ slug: 'lv', name: 'Latviešu' },
				{ slug: 'en', name: 'English' },
				{ slug: 'ru', name: 'Русский' },
		  ];

	function plain(value) {
		if (value && typeof value === 'object') {
			if (typeof value.rendered === 'string') {
				return value.rendered.replace(/<[^>]+>/g, '').trim();
			}
			if (typeof value.raw === 'string') {
				return value.raw;
			}
		}
		return value == null ? '' : String(value);
	}

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

	function stripHtml(value) {
		var s = plain(value);
		if (!s) {
			return '';
		}
		if (typeof document !== 'undefined') {
			var d = document.createElement('div');
			d.innerHTML = s;
			return (d.textContent || d.innerText || '').replace(/\s+/g, ' ').trim();
		}
		return s.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
	}

	function displayTitle(project) {
		var i18n = normalizeI18n(project && project.i18n);
		if (i18n.lv && i18n.lv.title) {
			return i18n.lv.title;
		}
		for (var i = 0; i < LANGUAGES.length; i++) {
			var slug = LANGUAGES[i].slug;
			if (i18n[slug] && i18n[slug].title) {
				return i18n[slug].title;
			}
		}
		return plain(project && project.title);
	}

	function displayExcerpt(project) {
		var i18n = normalizeI18n(project && project.i18n);
		if (i18n.lv && i18n.lv.excerpt) {
			return stripHtml(i18n.lv.excerpt);
		}
		return stripHtml(project && project.excerpt);
	}

	function coverUrl(project) {
		var media =
			project &&
			project._embedded &&
			project._embedded['wp:featuredmedia'] &&
			project._embedded['wp:featuredmedia'][0];
		if (!media) {
			return '';
		}
		if (media.media_details && media.media_details.sizes) {
			var sizes = media.media_details.sizes;
			if (sizes.medium) {
				return sizes.medium.source_url;
			}
			if (sizes.thumbnail) {
				return sizes.thumbnail.source_url;
			}
		}
		return media.source_url || '';
	}

	function galleryMeta(project) {
		if (project && Array.isArray(project.gallery)) {
			return project.gallery
				.map(function (id) {
					return parseInt(id, 10);
				})
				.filter(function (id) {
					return id > 0;
				});
		}
		var meta = (project && project.meta) || {};
		var ids = meta._construction_project_gallery;
		if (!Array.isArray(ids)) {
			return [];
		}
		return ids
			.map(function (id) {
				return parseInt(id, 10);
			})
			.filter(function (id) {
				return id > 0;
			});
	}

	function fetchProjects() {
		return apiFetch({
			path: addQueryArgs('/wp/v2/construction-projects', {
				per_page: 100,
				status: 'publish,draft,pending,future,private',
				_embed: 1,
				orderby: 'date',
				order: 'desc',
				context: 'edit',
			}),
		});
	}

	function fetchMedia(ids) {
		if (!ids.length) {
			return Promise.resolve([]);
		}
		return apiFetch({
			path: addQueryArgs('/wp/v2/media', {
				include: ids.join(','),
				per_page: ids.length,
				context: 'edit',
			}),
		}).then(function (items) {
			var map = {};
			(items || []).forEach(function (item) {
				map[item.id] = item;
			});
			return ids
				.map(function (id) {
					return map[id];
				})
				.filter(Boolean);
		});
	}

	function mediaThumb(item) {
		if (!item) {
			return '';
		}
		if (item.sizes) {
			if (item.sizes.thumbnail) {
				return item.sizes.thumbnail.url || item.sizes.thumbnail.source_url || '';
			}
			if (item.sizes.medium) {
				return item.sizes.medium.url || item.sizes.medium.source_url || '';
			}
		}
		if (item.media_details && item.media_details.sizes) {
			var sizes = item.media_details.sizes;
			if (sizes.thumbnail) {
				return sizes.thumbnail.source_url;
			}
			if (sizes.medium) {
				return sizes.medium.source_url;
			}
		}
		return item.source_url || item.url || '';
	}

	function normalizeAttachment(att) {
		if (!att || !att.id) {
			return null;
		}
		return {
			id: parseInt(att.id, 10),
			url: att.url || att.source_url || '',
			source_url: att.source_url || att.url || '',
			sizes: att.sizes || null,
			media_details: att.media_details || null,
		};
	}

	function ProjectEditModal(props) {
		var projectId = props.projectId;
		var onClose = props.onClose;
		var onSaved = props.onSaved;

		var _lang = useState(LANGUAGES[0].slug);
		var activeLang = _lang[0];
		var setActiveLang = _lang[1];
		var _i18n = useState(emptyI18n());
		var i18n = _i18n[0];
		var setI18n = _i18n[1];
		var _origI18n = useState(emptyI18n());
		var origI18n = _origI18n[0];
		var setOrigI18n = _origI18n[1];
		var _s = useState('');
		var slug = _s[0];
		var setSlug = _s[1];
		var _c = useState(0);
		var coverId = _c[0];
		var setCoverId = _c[1];
		var _cu = useState('');
		var coverPreview = _cu[0];
		var setCoverPreview = _cu[1];
		var _g = useState([]);
		var galleryIds = _g[0];
		var setGalleryIds = _g[1];
		var _gi = useState([]);
		var galleryItems = _gi[0];
		var setGalleryItems = _gi[1];
		var _busy = useState(true);
		var busy = _busy[0];
		var setBusy = _busy[1];
		var _saving = useState(false);
		var saving = _saving[0];
		var setSaving = _saving[1];
		var _notice = useState(null);
		var notice = _notice[0];
		var setNotice = _notice[1];

		useEffect(
			function () {
				var cancelled = false;
				setBusy(true);
				setNotice(null);
				apiFetch({
					path: addQueryArgs('/wp/v2/construction-projects/' + projectId, {
						_embed: 1,
						context: 'edit',
					}),
				})
					.then(function (data) {
						if (cancelled) {
							return null;
						}
						var nextI18n = normalizeI18n(data.i18n);
						if (!nextI18n.lv.title && plain(data.title)) {
							nextI18n.lv.title = plain(data.title);
						}
						if (!nextI18n.lv.excerpt && plain(data.excerpt)) {
							nextI18n.lv.excerpt = plain(data.excerpt);
						}
						LANGUAGES.forEach(function (lang) {
							if (nextI18n[lang.slug] && nextI18n[lang.slug].excerpt) {
								nextI18n[lang.slug].excerpt = stripHtml(nextI18n[lang.slug].excerpt);
							}
						});
						setOrigI18n(normalizeI18n(data.i18n));
						setI18n(nextI18n);
						setSlug(data.slug || '');
						setCoverId(data.featured_media || 0);
						setCoverPreview(coverUrl(data));
						var ids = galleryMeta(data);
						setGalleryIds(ids);
						return fetchMedia(ids);
					})
					.then(function (items) {
						if (cancelled || !items) {
							return;
						}
						setGalleryItems(items);
					})
					.catch(function () {
						if (!cancelled) {
							setNotice({ status: 'error', message: S.loadError || 'Could not load.' });
						}
					})
					.finally(function () {
						if (!cancelled) {
							setBusy(false);
						}
					});
				return function () {
					cancelled = true;
				};
			},
			[projectId]
		);

		var setField = useCallback(
			function (field, value) {
				setI18n(function (prev) {
					var next = Object.assign({}, prev);
					var row = Object.assign({}, next[activeLang] || { title: '', excerpt: '' });
					row[field] = value;
					next[activeLang] = row;
					return next;
				});
			},
			[activeLang]
		);

		var onSelectCover = useCallback(function (media) {
			var att = normalizeAttachment(media);
			if (!att) {
				return;
			}
			setCoverId(att.id);
			setCoverPreview(mediaThumb(att) || att.url);
		}, []);

		var onSelectGallery = useCallback(function (media) {
			var list = (Array.isArray(media) ? media : [media])
				.map(normalizeAttachment)
				.filter(Boolean);
			if (!list.length) {
				return;
			}
			setGalleryIds(function (prev) {
				var next = prev.slice();
				list.forEach(function (att) {
					if (next.indexOf(att.id) === -1) {
						next.push(att.id);
					}
				});
				return next;
			});
			setGalleryItems(function (prev) {
				var map = {};
				prev.forEach(function (item) {
					map[Number(item.id)] = item;
				});
				list.forEach(function (att) {
					map[att.id] = att;
				});
				return Object.keys(map).map(function (k) {
					return map[k];
				});
			});
		}, []);

		var removeGallery = useCallback(function (id) {
			var nid = Number(id);
			setGalleryIds(function (prev) {
				return prev.filter(function (x) {
					return Number(x) !== nid;
				});
			});
			setGalleryItems(function (prev) {
				return prev.filter(function (item) {
					return Number(item.id) !== nid;
				});
			});
		}, []);

		var save = useCallback(
			function () {
				setSaving(true);
				setNotice(null);
				var payloadI18n = normalizeI18n(origI18n);
				LANGUAGES.forEach(function (lang) {
					var slugLang = lang.slug;
					var origEx = (origI18n[slugLang] && origI18n[slugLang].excerpt) || '';
					var editedEx = (i18n[slugLang] && i18n[slugLang].excerpt) || '';
					var origPlain = stripHtml(origEx);
					payloadI18n[slugLang] = Object.assign({}, payloadI18n[slugLang], {
						title: (i18n[slugLang] && i18n[slugLang].title) || '',
					});
					// Preserve rich HTML from the full editor when the quick field
					// still matches the plain-text version of that HTML.
					if (origEx && origEx !== origPlain && editedEx === origPlain) {
						payloadI18n[slugLang].excerpt = origEx;
					} else {
						payloadI18n[slugLang].excerpt = editedEx;
					}
				});
				var galleryPayload = galleryIds
					.map(function (id) {
						return parseInt(id, 10);
					})
					.filter(function (id) {
						return id > 0;
					});
				var featured = coverId || 0;
				if (!featured && galleryPayload.length) {
					featured = galleryPayload[0];
				}
				apiFetch({
					path: '/wp/v2/construction-projects/' + projectId,
					method: 'POST',
					data: {
						slug: slug,
						featured_media: featured,
						gallery: galleryPayload,
						i18n: payloadI18n,
					},
				})
					.then(function (data) {
						setOrigI18n(normalizeI18n(data.i18n));
						if (typeof onSaved === 'function') {
							onSaved(data);
						}
						if (typeof onClose === 'function') {
							onClose();
						}
					})
					.catch(function (err) {
						var msg = S.saveError || 'Could not save.';
						if (err && err.message) {
							msg = err.message;
						}
						if (err && err.code === 'rest_invalid_param' && /slug/i.test(JSON.stringify(err))) {
							msg = S.slugTaken || msg;
						}
						setNotice({ status: 'error', message: msg });
						setSaving(false);
					});
			},
			[projectId, slug, coverId, galleryIds, i18n, origI18n, onSaved, onClose]
		);

		var fullEdit =
			cfg.editPostTpl && projectId ? cfg.editPostTpl.replace('%d', String(projectId)) : '';
		var activeRow = i18n[activeLang] || { title: '', excerpt: '' };

		return el(
			Modal,
			{
				title: S.editProject || 'Edit project',
				onRequestClose: onClose,
				className: 'construction-project-edit-modal',
				style: { maxWidth: '720px' },
			},
			busy
				? el('div', { style: { padding: '2rem', textAlign: 'center' } }, el(Spinner))
				: el(
						Fragment,
						null,
						notice
							? el(
									Notice,
									{
										status: notice.status,
										isDismissible: true,
										onRemove: function () {
											setNotice(null);
										},
									},
									notice.message
							  )
							: null,
						el(
							'div',
							{
								style: {
									display: 'flex',
									gap: '6px',
									flexWrap: 'wrap',
									marginBottom: '1rem',
								},
							},
							LANGUAGES.map(function (lang) {
								return el(
									Button,
									{
										key: lang.slug,
										variant: activeLang === lang.slug ? 'primary' : 'secondary',
										onClick: function () {
											setActiveLang(lang.slug);
										},
									},
									lang.name || lang.slug.toUpperCase()
								);
							})
						),
						el(TextControl, {
							label: (S.title || 'Title') + ' (' + activeLang.toUpperCase() + ')',
							value: activeRow.title,
							onChange: function (value) {
								setField('title', value);
							},
						}),
						el(TextareaControl, {
							label: (S.description || 'Description') + ' (' + activeLang.toUpperCase() + ')',
							value: activeRow.excerpt,
							onChange: function (value) {
								setField('excerpt', value);
							},
							rows: 4,
						}),
						el(TextControl, {
							label: S.slug || 'Slug',
							value: slug,
							onChange: setSlug,
							help: 'Shared across languages — used in /projekti/#slug',
						}),
						el(
							'div',
							{ className: 'construction-project-edit-cover', style: { marginBottom: '1.25rem' } },
							el('p', { style: { fontWeight: 600, marginBottom: '0.5rem' } }, S.cover || 'Cover'),
							coverPreview
								? el('img', {
										src: coverPreview,
										alt: '',
										style: {
											display: 'block',
											width: '100%',
											maxHeight: '220px',
											objectFit: 'cover',
											borderRadius: '8px',
											marginBottom: '0.5rem',
										},
								  })
								: null,
							el(
								MediaUploadCheck,
								null,
								el(
									'div',
									{ style: { display: 'flex', gap: '0.5rem', flexWrap: 'wrap' } },
									el(MediaUpload, {
										onSelect: onSelectCover,
										allowedTypes: ['image'],
										value: coverId || undefined,
										render: function (obj) {
											return el(
												Button,
												{ variant: 'secondary', onClick: obj.open },
												S.setCover || 'Set cover'
											);
										},
									}),
									coverId
										? el(
												Button,
												{
													variant: 'tertiary',
													isDestructive: true,
													onClick: function () {
														setCoverId(0);
														setCoverPreview('');
													},
												},
												S.removeCover || 'Remove cover'
										  )
										: null
								)
							)
						),
						el(
							'div',
							{ className: 'construction-project-edit-gallery', style: { marginBottom: '1.25rem' } },
							el(
								'p',
								{ style: { fontWeight: 600, marginBottom: '0.5rem' } },
								S.gallery || 'Gallery (all languages)'
							),
							el(
								'ul',
								{
									style: {
										display: 'flex',
										flexWrap: 'wrap',
										gap: '10px',
										listStyle: 'none',
										margin: '0 0 0.75rem',
										padding: 0,
									},
								},
								galleryIds.map(function (id) {
									var nid = Number(id);
									var item = galleryItems.find(function (g) {
										return Number(g.id) === nid;
									});
									var src = mediaThumb(item);
									return el(
										'li',
										{
											key: nid,
											style: {
												position: 'relative',
												width: '88px',
												height: '88px',
												borderRadius: '6px',
												overflow: 'hidden',
												background: '#f0f0f1',
												border: '1px solid #c3c4c7',
											},
										},
										src
											? el('img', {
													src: src,
													alt: '',
													style: { width: '100%', height: '100%', objectFit: 'cover' },
											  })
											: el('span', { style: { fontSize: '11px', padding: '4px' } }, '#' + nid),
										el(
											Button,
											{
												variant: 'primary',
												isDestructive: true,
												className: 'construction-project-edit-gallery-remove',
												style: {
													position: 'absolute',
													top: '2px',
													right: '2px',
													minWidth: '24px',
													height: '24px',
													padding: 0,
												},
												onClick: function () {
													removeGallery(nid);
												},
												label: S.remove || 'Remove',
											},
											'×'
										)
									);
								})
							),
							el(MediaUploadCheck, null, el(MediaUpload, {
								onSelect: onSelectGallery,
								allowedTypes: ['image'],
								multiple: true,
								gallery: true,
								value: galleryIds,
								render: function (obj) {
									return el(
										Button,
										{ variant: 'secondary', onClick: obj.open },
										S.addImages || 'Add images'
									);
								},
							}))
						),
						el(
							'div',
							{
								style: {
									display: 'flex',
									gap: '0.5rem',
									flexWrap: 'wrap',
									justifyContent: 'flex-end',
									marginTop: '1rem',
								},
							},
							fullEdit
								? el(
										Button,
										{
											variant: 'link',
											href: fullEdit,
											target: '_blank',
											rel: 'noopener noreferrer',
										},
										S.openFull || 'Open full editor'
								  )
								: null,
							el(Button, { variant: 'tertiary', onClick: onClose }, S.close || 'Close'),
							el(
								Button,
								{
									variant: 'primary',
									onClick: save,
									isBusy: saving,
									disabled: saving,
								},
								saving ? S.saving || 'Saving…' : S.save || 'Save project'
							)
						)
				  )
		);
	}

	function ProjectsEditableGrid(props) {
		var label = props.label;
		var blockProps = useBlockProps({ className: 'construction-block-preview construction-projects-editor' });

		var _list = useState([]);
		var projects = _list[0];
		var setProjects = _list[1];
		var _loading = useState(true);
		var loading = _loading[0];
		var setLoading = _loading[1];
		var _error = useState('');
		var error = _error[0];
		var setError = _error[1];
		var _editId = useState(0);
		var editId = _editId[0];
		var setEditId = _editId[1];
		var _tick = useState(0);
		var tick = _tick[0];
		var setTick = _tick[1];
		var _busyId = useState(0);
		var busyId = _busyId[0];
		var setBusyId = _busyId[1];
		var _adding = useState(false);
		var adding = _adding[0];
		var setAdding = _adding[1];

		var reload = useCallback(function () {
			setLoading(true);
			setError('');
			fetchProjects()
				.then(function (items) {
					setProjects(Array.isArray(items) ? items : []);
				})
				.catch(function () {
					setError(S.loadError || 'Could not load projects.');
					setProjects([]);
				})
				.finally(function () {
					setLoading(false);
				});
		}, []);

		useEffect(
			function () {
				reload();
			},
			[reload, tick]
		);

		var bump = useCallback(function () {
			setTick(function (n) {
				return n + 1;
			});
		}, []);

		var addProject = useCallback(
			function () {
				if (adding) {
					return;
				}
				setAdding(true);
				setError('');
				var titleLv = S.newTitleLv || 'Jauns projekts';
				var titleEn = S.newTitleEn || 'New project';
				var titleRu = S.newTitleRu || 'Новый проект';
				apiFetch({
					path: '/wp/v2/construction-projects',
					method: 'POST',
					data: {
						title: titleLv,
						status: 'publish',
						i18n: {
							lv: { title: titleLv, excerpt: '' },
							en: { title: titleEn, excerpt: '' },
							ru: { title: titleRu, excerpt: '' },
						},
					},
				})
					.then(function (created) {
						bump();
						if (created && created.id) {
							setEditId(created.id);
						}
					})
					.catch(function () {
						setError(S.saveError || 'Could not save project.');
					})
					.finally(function () {
						setAdding(false);
					});
			},
			[adding, bump]
		);

		var setProjectStatus = useCallback(
			function (project, nextStatus) {
				if (!project || !project.id || busyId) {
					return;
				}
				setBusyId(project.id);
				apiFetch({
					path: '/wp/v2/construction-projects/' + project.id,
					method: 'POST',
					data: { status: nextStatus },
				})
					.then(function () {
						bump();
					})
					.catch(function () {
						setError(S.statusError || 'Could not update project status.');
					})
					.finally(function () {
						setBusyId(0);
					});
			},
			[busyId, bump]
		);

		var removeProject = useCallback(
			function (project) {
				if (!project || !project.id || busyId) {
					return;
				}
				var name = displayTitle(project) || '#' + project.id;
				var msg = (S.removeConfirm || 'Remove this project permanently?').replace('%s', name);
				if (!window.confirm(msg)) {
					return;
				}
				setBusyId(project.id);
				apiFetch({
					path: addQueryArgs('/wp/v2/construction-projects/' + project.id, { force: true }),
					method: 'DELETE',
				})
					.then(function () {
						if (editId === project.id) {
							setEditId(0);
						}
						bump();
					})
					.catch(function () {
						setError(S.removeError || 'Could not remove project.');
					})
					.finally(function () {
						setBusyId(0);
					});
			},
			[busyId, bump, editId]
		);

		var toolbar = el(
			'div',
			{
				style: {
					display: 'flex',
					flexWrap: 'wrap',
					alignItems: 'center',
					justifyContent: 'space-between',
					gap: '0.75rem',
					marginBottom: '1rem',
				},
			},
			el(
				'p',
				{
					className: 'construction-projects-editor__hint',
					style: { margin: 0, color: '#646970', fontSize: '13px', flex: '1 1 220px' },
				},
				S.clickToEdit || 'Manage projects here.'
			),
			el(
				Button,
				{
					variant: 'primary',
					onClick: addProject,
					isBusy: adding,
					disabled: adding || loading,
				},
				adding ? S.adding || 'Adding…' : S.addProject || 'Add project'
			)
		);

		return el(
			'div',
			blockProps,
			toolbar,
			loading ? el('div', { style: { padding: '2rem', textAlign: 'center' } }, el(Spinner)) : null,
			error ? el(Notice, { status: 'error', isDismissible: false }, error) : null,
			!loading && !error && projects.length === 0
				? el(
						'div',
						{ style: { padding: '1.5rem', border: '1px dashed #c3c4c7', borderRadius: '8px', textAlign: 'center' } },
						el('p', { style: { margin: '0 0 1rem', color: '#646970' } }, S.empty || 'No projects yet.'),
						el(
							Button,
							{ variant: 'primary', onClick: addProject, isBusy: adding, disabled: adding },
							adding ? S.adding || 'Adding…' : S.addProject || 'Add project'
						)
				  )
				: null,
			!loading && projects.length
				? el(
						'div',
						{
							className: 'construction-projects-editor__grid',
							style: {
								display: 'grid',
								gridTemplateColumns: 'repeat(auto-fill, minmax(200px, 1fr))',
								gap: '1rem',
							},
						},
						projects.map(function (project) {
							var src = coverUrl(project);
							var title = displayTitle(project);
							var text = displayExcerpt(project);
							var status = project.status || 'publish';
							var isDisabled = status !== 'publish';
							var isBusy = busyId === project.id;
							return el(
								'div',
								{
									key: project.id,
									className:
										'construction-projects-editor__card' +
										(isDisabled ? ' is-disabled' : ''),
									style: {
										display: 'flex',
										flexDirection: 'column',
										width: '100%',
										border: '1px solid #dcdcde',
										borderRadius: '12px',
										overflow: 'hidden',
										background: isDisabled ? '#f6f7f7' : '#fff',
										opacity: isDisabled ? 0.78 : 1,
										boxShadow: 'none',
									},
								},
								el(
									'button',
									{
										type: 'button',
										onClick: function () {
											setEditId(project.id);
										},
										disabled: isBusy,
										style: {
											display: 'block',
											width: '100%',
											textAlign: 'left',
											padding: 0,
											border: 0,
											background: 'transparent',
											cursor: isBusy ? 'wait' : 'pointer',
										},
									},
									src
										? el('img', {
												src: src,
												alt: '',
												style: {
													display: 'block',
													width: '100%',
													aspectRatio: '4 / 3',
													objectFit: 'cover',
													filter: isDisabled ? 'grayscale(0.35)' : 'none',
												},
										  })
										: el('div', {
												style: {
													width: '100%',
													aspectRatio: '4 / 3',
													background: '#f0f0f1',
												},
										  }),
									el(
										'div',
										{ style: { padding: '0.75rem 0.85rem 0.5rem' } },
										isDisabled
											? el(
													'span',
													{
														style: {
															display: 'inline-block',
															marginBottom: '0.35rem',
															padding: '0.1rem 0.4rem',
															borderRadius: '3px',
															background: '#dba617',
															color: '#1d2327',
															fontSize: '11px',
															fontWeight: 600,
															lineHeight: 1.4,
														},
													},
													S.disabled || 'Disabled'
											  )
											: null,
										el(
											'strong',
											{
												style: {
													display: 'block',
													fontSize: '14px',
													lineHeight: 1.3,
													marginBottom: '0.25rem',
												},
											},
											title || '#' + project.id
										),
										text
											? el(
													'span',
													{
														style: {
															display: 'block',
															fontSize: '12px',
															color: '#646970',
															lineHeight: 1.4,
														},
													},
													text.length > 90 ? text.slice(0, 90) + '…' : text
											  )
											: null
									)
								),
								el(
									'div',
									{
										style: {
											display: 'flex',
											flexWrap: 'wrap',
											gap: '0.35rem',
											padding: '0 0.75rem 0.75rem',
											marginTop: 'auto',
										},
									},
									el(
										Button,
										{
											variant: 'secondary',
											size: 'small',
											disabled: isBusy,
											onClick: function () {
												setEditId(project.id);
											},
										},
										S.editProject || 'Edit'
									),
									el(
										Button,
										{
											variant: 'secondary',
											size: 'small',
											disabled: isBusy,
											isBusy: isBusy,
											onClick: function () {
												setProjectStatus(project, isDisabled ? 'publish' : 'draft');
											},
										},
										isDisabled ? S.enable || 'Enable' : S.disable || 'Disable'
									),
									el(
										Button,
										{
											variant: 'tertiary',
											size: 'small',
											isDestructive: true,
											disabled: isBusy,
											onClick: function () {
												removeProject(project);
											},
										},
										S.remove || 'Remove'
									)
								)
							);
						})
				  )
				: null,
			editId
				? el(ProjectEditModal, {
						projectId: editId,
						onClose: function () {
							setEditId(0);
						},
						onSaved: bump,
				  })
				: null
		);
	}

	registerBlockType('construction/projects-grid', {
		edit: function () {
			return el(ProjectsEditableGrid, { label: S.projectsGrid || 'Projects grid' });
		},
		save: function () {
			return null;
		},
	});

	registerBlockType('construction/home-projects', {
		edit: function () {
			return el(ProjectsEditableGrid, { label: S.homeProjects || 'Home projects' });
		},
		save: function () {
			return null;
		},
	});
})(window.wp);
