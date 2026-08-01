/**
 * Construction front-end script.
 */
(() => {
	'use strict';

	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	document.querySelectorAll('a[href^="#"]').forEach((link) => {
		link.addEventListener('click', (event) => {
			const id = link.getAttribute('href');
			if (!id || id === '#') {
				return;
			}
			const target = document.querySelector(id);
			if (!target) {
				return;
			}
			event.preventDefault();
			target.scrollIntoView({ behavior: 'smooth', block: 'start' });
		});
	});

	// Mobile drawer: nav links + language switcher.
	(() => {
		const root = document.querySelector('[data-construction-drawer]');
		const toggle = document.querySelector('[data-construction-menu-open]');
		if (!root || !toggle) {
			return;
		}

		// Header uses backdrop-filter, which traps position:fixed children.
		// Keep the drawer on <body> so it covers the full viewport.
		if (root.parentElement !== document.body) {
			document.body.appendChild(root);
		}

		const closeButtons = root.querySelectorAll('[data-construction-menu-close]');
		const drawer = root.querySelector('.construction-drawer');
		const mq = window.matchMedia('(max-width: 899px)');
		let closeTimer = 0;

		const setOpen = (open) => {
			if (!mq.matches && open) {
				return;
			}

			window.clearTimeout(closeTimer);
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			document.body.classList.toggle('construction-menu-open', open);

			if (open) {
				root.hidden = false;
				// Next frame so the slide-in transition can run.
				requestAnimationFrame(() => {
					root.classList.add('is-open');
				});
				if (drawer) {
					const focusTarget = drawer.querySelector('[data-construction-menu-close]') || drawer;
					focusTarget.focus();
				}
				return;
			}

			root.classList.remove('is-open');
			closeTimer = window.setTimeout(() => {
				root.hidden = true;
			}, 320);
			toggle.focus();
		};

		toggle.addEventListener('click', () => {
			const willOpen = toggle.getAttribute('aria-expanded') !== 'true';
			setOpen(willOpen);
		});

		closeButtons.forEach((btn) => {
			btn.addEventListener('click', () => setOpen(false));
		});

		document.addEventListener('keydown', (event) => {
			if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
				setOpen(false);
			}
		});

		mq.addEventListener('change', (event) => {
			if (!event.matches) {
				setOpen(false);
			}
		});
	})();

	/**
	 * Shared project modal (projects page + homepage teaser).
	 */
	const ensureProjectModal = () => {
		let modal = document.querySelector('.construction-project-modal');
		if (!modal) {
			modal = document.createElement('div');
			modal.className = 'construction-project-modal';
			modal.hidden = true;
			modal.setAttribute('role', 'dialog');
			modal.setAttribute('aria-modal', 'true');
			modal.innerHTML = `
				<div class="construction-project-modal__backdrop" data-modal-close="1"></div>
				<div class="construction-project-modal__dialog" role="document">
					<div class="construction-project-modal__body"></div>
				</div>
			`;
			document.body.appendChild(modal);
		}
		return modal;
	};

	const bindProjectModal = ({
		getCards,
		titleSelector,
		textSelector,
		getImageLinks,
		labelClose,
		labelPrev,
		labelNext,
		hashSync = false,
		openPageUrl = '',
		openPageLabel = '',
		glass = false,
	}) => {
		const modal = ensureProjectModal();
		const modalBody = modal.querySelector('.construction-project-modal__body');
		const modalDialog = modal.querySelector('.construction-project-modal__dialog');
		let activeIndex = -1;
		let activeImageIndex = 0;
		let lastFocus = null;
		let ownsOpen = false;

		const getCardImages = (card) => {
			const links = getImageLinks(card);
			return links
				.map((link) => {
					const img = link.querySelector('img');
					return {
						full: link.getAttribute('href'),
						thumb: img ? img.getAttribute('src') : link.getAttribute('href'),
						alt: img ? img.getAttribute('alt') || '' : '',
					};
				})
				.filter((item) => item.full);
		};

		const setHash = (slug) => {
			if (!hashSync) {
				return;
			}
			const next = slug ? `#${slug}` : location.pathname + location.search;
			if (history.replaceState) {
				history.replaceState(null, '', next);
			} else if (slug) {
				location.hash = slug;
			}
		};

		const setBodyLock = (locked) => {
			document.documentElement.classList.toggle('has-project-modal', locked);
			document.body.classList.toggle('has-project-modal', locked);
			document.documentElement.classList.toggle('is-home-project-modal', locked && glass);
			document.body.classList.toggle('is-home-project-modal', locked && glass);
			modal.classList.toggle('is-glass', locked && glass);
		};

		const showImage = (imageIndex) => {
			const cards = getCards();
			if (activeIndex < 0 || !modalBody || !cards[activeIndex]) {
				return;
			}
			const images = getCardImages(cards[activeIndex]);
			if (images.length === 0) {
				return;
			}
			activeImageIndex = ((imageIndex % images.length) + images.length) % images.length;
			const current = images[activeImageIndex];
			const stageImg = modalBody.querySelector('.construction-project-viewer__stage img');
			if (stageImg) {
				stageImg.src = current.full;
				stageImg.alt = current.alt;
			}
			modalBody.querySelectorAll('.construction-project-viewer__thumb').forEach((btn, i) => {
				btn.classList.toggle('is-active', i === activeImageIndex);
			});
		};

		const escapeHtml = (value) =>
			String(value)
				.replace(/&/g, '&amp;')
				.replace(/</g, '&lt;')
				.replace(/"/g, '&quot;');

		const buildDetailMarkup = (images, slug) => {
			const thumbs = images
				.map(
					(item, i) => `<button type="button" class="construction-project-viewer__thumb" data-image-index="${i}" aria-label="${i + 1}">
						<img src="${item.thumb}" alt="">
					</button>`
				)
				.join('');
			const pageHref =
				openPageUrl && slug
					? `${openPageUrl.replace(/\/?$/, '/') }#${slug}`
					: openPageUrl;
			const openPage =
				pageHref && openPageLabel
					? `<p class="construction-project-viewer__open-page"><a href="${escapeHtml(pageHref)}">${escapeHtml(openPageLabel)} →</a></p>`
					: '';
			return `
				<div class="construction-project-viewer__media">
					<figure class="construction-project-viewer__stage">
						<img src="" alt="">
					</figure>
					<div class="construction-project-viewer__thumbs">${thumbs}</div>
				</div>
				<div class="construction-project-viewer__meta">
					<div class="construction-project-viewer__controls">
						<button type="button" class="construction-project-viewer__nav" data-nav="prev" aria-label="${escapeHtml(labelPrev)}">‹</button>
						<button type="button" class="construction-project-viewer__nav" data-nav="next" aria-label="${escapeHtml(labelNext)}">›</button>
						<button type="button" class="construction-project-viewer__close" data-nav="close" aria-label="${escapeHtml(labelClose)}">×</button>
					</div>
					<h2 class="construction-project-viewer__title" id="construction-project-modal-title"></h2>
					<p class="construction-project-viewer__text"></p>
					${openPage}
				</div>
			`;
		};

		const animateOpen = (fromCard, animate = true) => {
			const gsap = window.gsap;
			modal.hidden = false;
			modal.classList.add('is-open');
			setBodyLock(true);
			ownsOpen = true;

			if (!animate || !gsap || reduceMotion || !modalDialog) {
				if (gsap) {
					gsap.killTweensOf([modal, modalDialog]);
					gsap.set(modal, { clearProps: 'opacity,visibility' });
					gsap.set(modalDialog, { clearProps: 'transform' });
				}
				modal.style.opacity = '';
				modal.style.visibility = '';
				modal.classList.add('is-visible');
				return;
			}

			gsap.killTweensOf([modal, modalDialog]);
			gsap.set(modal, { autoAlpha: 0 });
			gsap.set(modalDialog, { scale: 0.92, y: 24, transformOrigin: '50% 50%' });

			if (fromCard) {
				const from = fromCard.getBoundingClientRect();
				const dialogRect = modalDialog.getBoundingClientRect();
				if (from.width > 40 && dialogRect.width > 40) {
					const x = from.left + from.width / 2 - (dialogRect.left + dialogRect.width / 2);
					const y = from.top + from.height / 2 - (dialogRect.top + dialogRect.height / 2);
					gsap.set(modalDialog, { x: x * 0.35, y: y * 0.35 + 24 });
				}
			}

			gsap.to(modal, { autoAlpha: 1, duration: 0.28, ease: 'power1.out' });
			gsap.to(modalDialog, {
				x: 0,
				y: 0,
				scale: 1,
				duration: 0.4,
				ease: 'power2.out',
				clearProps: 'transform',
				onComplete: () => modal.classList.add('is-visible'),
			});
		};

		const animateClose = (onDone) => {
			const gsap = window.gsap;
			const finish = () => {
				modal.classList.remove('is-open', 'is-visible', 'is-glass');
				modal.hidden = true;
				if (modalBody) {
					modalBody.innerHTML = '';
				}
				setBodyLock(false);
				ownsOpen = false;
				if (typeof onDone === 'function') {
					onDone();
				}
			};

			if (!gsap || reduceMotion || !modalDialog) {
				finish();
				return;
			}
			gsap.killTweensOf([modal, modalDialog]);
			gsap.to(modalDialog, {
				scale: 0.96,
				y: 12,
				duration: 0.2,
				ease: 'power1.in',
			});
			gsap.to(modal, {
				autoAlpha: 0,
				duration: 0.2,
				ease: 'power1.in',
				onComplete: finish,
			});
		};

		const closeProject = () => {
			if (!ownsOpen && activeIndex < 0 && modal.hidden) {
				return;
			}
			if (!ownsOpen) {
				return;
			}
			getCards().forEach((node) => node.classList.remove('is-active'));
			activeIndex = -1;
			activeImageIndex = 0;
			setHash('');
			animateClose(() => {
				if (lastFocus && typeof lastFocus.focus === 'function') {
					lastFocus.focus();
				}
			});
		};

		const openProject = (index, fromCard, { animate = true } = {}) => {
			const cards = getCards();
			const card = cards[index];
			if (!card || !modalBody) {
				return;
			}
			const images = getCardImages(card);
			if (images.length === 0) {
				return;
			}
			if (ownsOpen && activeIndex === index && modal.classList.contains('is-open')) {
				return;
			}

			lastFocus = document.activeElement;
			activeIndex = index;
			const titleEl = card.querySelector(titleSelector);
			const textEl = card.querySelector(textSelector);
			const title = titleEl ? titleEl.textContent.trim() : '';
			const text = textEl ? textEl.textContent.trim() : '';
			const slug = card.id || card.getAttribute('data-project-slug') || '';

			cards.forEach((node, i) => {
				node.classList.toggle('is-active', i === index);
			});

			modalBody.innerHTML = buildDetailMarkup(images, slug);
			modal.setAttribute('aria-labelledby', 'construction-project-modal-title');
			const titleNode = modalBody.querySelector('.construction-project-viewer__title');
			const textNode = modalBody.querySelector('.construction-project-viewer__text');
			if (titleNode) {
				titleNode.textContent = title;
			}
			if (textNode) {
				textNode.textContent = text;
			}
			showImage(0);
			setHash(slug);
			animateOpen(fromCard || card, animate);

			const closeBtn = modalBody.querySelector('[data-nav="close"]');
			if (closeBtn) {
				closeBtn.focus();
			}
		};

		const openBySlug = (slug, fromCard, options) => {
			const cards = getCards();
			const index = cards.findIndex(
				(card) => card.id === slug || card.getAttribute('data-project-slug') === slug
			);
			if (index < 0) {
				return false;
			}
			openProject(index, fromCard, options);
			return true;
		};

		modal.addEventListener('click', (event) => {
			if (!ownsOpen || !modal.classList.contains('is-open')) {
				return;
			}
			if (event.target.closest('[data-modal-close], [data-nav="close"]')) {
				event.preventDefault();
				closeProject();
				return;
			}
			const thumb = event.target.closest('[data-image-index]');
			if (thumb && modal.contains(thumb)) {
				event.preventDefault();
				showImage(Number(thumb.getAttribute('data-image-index')));
				return;
			}
			const nav = event.target.closest('[data-nav]');
			if (!nav || !modal.contains(nav)) {
				return;
			}
			const action = nav.getAttribute('data-nav');
			if (action === 'prev') {
				showImage(activeImageIndex - 1);
			} else if (action === 'next') {
				showImage(activeImageIndex + 1);
			}
		});

		document.addEventListener('keydown', (event) => {
			if (!ownsOpen || !modal.classList.contains('is-open')) {
				return;
			}
			if (event.key === 'Escape') {
				event.preventDefault();
				closeProject();
			} else if (event.key === 'ArrowLeft') {
				event.preventDefault();
				showImage(activeImageIndex - 1);
			} else if (event.key === 'ArrowRight') {
				event.preventDefault();
				showImage(activeImageIndex + 1);
			}
		});

		return {
			openProject,
			openBySlug,
			closeProject,
			syncFromHash: ({ animate = false } = {}) => {
				if (!hashSync) {
					return;
				}
				const slug = (location.hash || '').replace(/^#/, '');
				if (!slug) {
					if (ownsOpen && modal.classList.contains('is-open')) {
						closeProject();
					}
					return;
				}
				openBySlug(slug, null, { animate });
			},
		};
	};

	// Projekti page modal.
	const projectsRoot = document.querySelector('.construction-projects');
	if (projectsRoot && projectsRoot.querySelector('.construction-project-card')) {
		const grid = projectsRoot.querySelector('.construction-projects__grid');
		const legacyViewer = projectsRoot.querySelector('.construction-project-viewer');
		if (legacyViewer) {
			legacyViewer.hidden = true;
			legacyViewer.innerHTML = '';
		}

		const projectsModal = bindProjectModal({
			getCards: () => Array.from(projectsRoot.querySelectorAll('.construction-project-card')),
			titleSelector: '.construction-project-card__title',
			textSelector: '.construction-project-card__text',
			getImageLinks: (card) => Array.from(card.querySelectorAll('a.construction-lightbox[href]')),
			labelClose: projectsRoot.getAttribute('data-label-close') || 'Close',
			labelPrev: projectsRoot.getAttribute('data-label-prev') || 'Previous',
			labelNext: projectsRoot.getAttribute('data-label-next') || 'Next',
			hashSync: true,
			glass: true,
		});

		if (grid) {
			grid.addEventListener('click', (event) => {
				const card = event.target.closest('.construction-project-card');
				if (!card || !grid.contains(card)) {
					return;
				}
				const link = event.target.closest('a.construction-lightbox');
				if (link) {
					event.preventDefault();
				}
				const cards = Array.from(projectsRoot.querySelectorAll('.construction-project-card'));
				const index = cards.indexOf(card);
				if (index >= 0) {
					projectsModal.openProject(index, card);
				}
			});
		}

		if (location.hash) {
			projectsModal.syncFromHash({ animate: false });
		}
		window.addEventListener('hashchange', () => projectsModal.syncFromHash({ animate: false }));
	} else if (typeof window.GLightbox === 'function' && document.querySelector('.construction-projects__grid .construction-lightbox')) {
		// Legacy flat gallery fallback.
		const triggers = Array.from(document.querySelectorAll('.construction-projects__grid .construction-lightbox'));
		const realItems = triggers.map((node) => ({
			href: node.getAttribute('href'),
			type: 'image',
		}));

		if (realItems.length > 0) {
			const elements =
				realItems.length === 1
					? realItems
					: [realItems[realItems.length - 1], ...realItems, realItems[0]];

			const lightbox = window.GLightbox({
				elements,
				touchNavigation: true,
				loop: false,
				openEffect: 'fade',
				closeEffect: 'fade',
				slideEffect: 'slide',
				dragAutoSnap: true,
				preload: true,
			});

			let jumping = false;
			const lastCloneIndex = elements.length - 1;
			const firstRealIndex = realItems.length === 1 ? 0 : 1;
			const lastRealIndex = realItems.length === 1 ? 0 : realItems.length;

			const jumpWithoutSlide = (index) => {
				jumping = true;
				const previousEffect = lightbox.settings.slideEffect;
				lightbox.settings.slideEffect = 'none';
				lightbox.goToSlide(index);
				lightbox.settings.slideEffect = previousEffect;
				requestAnimationFrame(() => {
					jumping = false;
				});
			};

			lightbox.on('slide_changed', ({ current }) => {
				if (jumping || realItems.length < 2) {
					return;
				}
				if (current.index === lastCloneIndex) {
					jumpWithoutSlide(firstRealIndex);
				} else if (current.index === 0) {
					jumpWithoutSlide(lastRealIndex);
				}
			});

			triggers.forEach((node, index) => {
				node.addEventListener('click', (event) => {
					event.preventDefault();
					lightbox.openAt(realItems.length === 1 ? 0 : index + 1);
				});
			});
		}
	}

	// Homepage: open the same project modal over glass (stay on index).
	const homeProjects = document.querySelector('[data-home-projects]');
	if (homeProjects) {
		const getHomeCards = () => {
			const liveSet = homeProjects.querySelector(
				'.construction-home-projects__set:not([aria-hidden="true"])'
			);
			if (liveSet) {
				return Array.from(liveSet.querySelectorAll('.construction-home-projects__card'));
			}
			return Array.from(homeProjects.querySelectorAll('.construction-home-projects__card')).filter(
				(card) => !card.closest('[aria-hidden="true"]')
			);
		};

		const homeModal = bindProjectModal({
			getCards: getHomeCards,
			titleSelector: '.construction-home-projects__name',
			textSelector: '.construction-home-projects__blurb',
			getImageLinks: (card) => {
				const gallery = card.querySelector('.construction-home-projects__gallery');
				return gallery
					? Array.from(gallery.querySelectorAll('a.construction-lightbox[href]'))
					: [];
			},
			labelClose: homeProjects.getAttribute('data-label-close') || 'Close',
			labelPrev: homeProjects.getAttribute('data-label-prev') || 'Previous',
			labelNext: homeProjects.getAttribute('data-label-next') || 'Next',
			hashSync: false,
			openPageUrl: homeProjects.getAttribute('data-projects-url') || '',
			openPageLabel: homeProjects.getAttribute('data-label-open-page') || '',
			glass: true,
		});

		// Bubble phase so marquee drag can suppress the synthetic click first.
		document.addEventListener('click', (event) => {
			const link = event.target.closest('[data-project-open]');
			if (!link || !homeProjects.contains(link) || event.defaultPrevented) {
				return;
			}
			if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) {
				return;
			}
			const slug = link.getAttribute('data-project-open');
			if (!slug) {
				return;
			}
			event.preventDefault();
			const card = link.closest('.construction-home-projects__card');
			homeModal.openBySlug(slug, card, { animate: true });
		});
	}

	// Homepage Realizētie projekti: slow infinite marquee + drag/scrub.
	(() => {
		const marquee = document.querySelector('[data-home-projects-marquee]');
		const track = document.querySelector('[data-home-projects-track]');
		if (!marquee || !track || track.dataset.marqueeReady === '1') {
			return;
		}

		const cards = Array.from(track.children).filter((node) =>
			node.classList.contains('construction-home-projects__card')
		);
		if (cards.length === 0) {
			return;
		}

		const set = document.createElement('div');
		set.className = 'construction-home-projects__set';
		cards.forEach((card) => set.appendChild(card));
		track.appendChild(set);
		track.dataset.marqueeReady = '1';

		if (reduceMotion) {
			return;
		}

		const clone = set.cloneNode(true);
		clone.setAttribute('aria-hidden', 'true');
		clone.querySelectorAll('a').forEach((anchor) => {
			anchor.setAttribute('tabindex', '-1');
		});
		track.appendChild(clone);

		const speed = 32; // px per second — same calm pace as before
		const dragThreshold = 6;
		let loopWidth = 0;
		let offset = 0;
		let pointerActive = false;
		let dragging = false;
		let dragMoved = false;
		let pointerId = null;
		let startX = 0;
		let startOffset = 0;
		let lastTs = 0;

		const wrap = (value) => {
			if (loopWidth <= 0) {
				return value;
			}
			let next = value;
			while (next <= -loopWidth) {
				next += loopWidth;
			}
			while (next > 0) {
				next -= loopWidth;
			}
			return next;
		};

		const apply = () => {
			track.style.transform = `translate3d(${offset}px, 0, 0)`;
		};

		const measure = () => {
			const styles = window.getComputedStyle(track);
			const gap = Number.parseFloat(styles.columnGap || styles.gap) || 18;
			loopWidth = set.getBoundingClientRect().width + gap;
			offset = wrap(offset);
			apply();
		};

		const endPointer = (event) => {
			if (!pointerActive || (pointerId !== null && event.pointerId !== pointerId)) {
				return;
			}
			pointerActive = false;
			dragging = false;
			pointerId = null;
			marquee.classList.remove('is-dragging');
			lastTs = 0;
			// Only block navigation after a real scrub — plain clicks must open the project.
			if (dragMoved) {
				const suppressClick = (clickEvent) => {
					clickEvent.preventDefault();
					clickEvent.stopPropagation();
					marquee.removeEventListener('click', suppressClick, true);
				};
				marquee.addEventListener('click', suppressClick, true);
			}
		};

		const onPointerDown = (event) => {
			if (event.button !== undefined && event.button !== 0) {
				return;
			}
			// Do not capture yet — capturing on every down swallows card link clicks.
			pointerActive = true;
			dragging = false;
			dragMoved = false;
			pointerId = event.pointerId;
			startX = event.clientX;
			startOffset = offset;
		};

		const onPointerMove = (event) => {
			if (!pointerActive || (pointerId !== null && event.pointerId !== pointerId)) {
				return;
			}
			const delta = event.clientX - startX;
			if (!dragging) {
				if (Math.abs(delta) < dragThreshold) {
					return;
				}
				dragging = true;
				dragMoved = true;
				marquee.classList.add('is-dragging');
				if (marquee.setPointerCapture) {
					marquee.setPointerCapture(event.pointerId);
				}
			}
			offset = wrap(startOffset + delta);
			apply();
		};

		marquee.addEventListener('pointerdown', onPointerDown);
		marquee.addEventListener('pointermove', onPointerMove);
		marquee.addEventListener('pointerup', endPointer);
		marquee.addEventListener('pointercancel', endPointer);

		marquee.addEventListener(
			'wheel',
			(event) => {
				const delta =
					Math.abs(event.deltaX) > Math.abs(event.deltaY) ? event.deltaX : event.deltaY;
				if (!delta) {
					return;
				}
				event.preventDefault();
				offset = wrap(offset - delta);
				apply();
				lastTs = 0;
			},
			{ passive: false }
		);

		const tick = (ts) => {
			if (!lastTs) {
				lastTs = ts;
			}
			const dt = Math.min(0.064, (ts - lastTs) / 1000);
			lastTs = ts;
			if (!dragging) {
				offset = wrap(offset - speed * dt);
				apply();
			}
			window.requestAnimationFrame(tick);
		};

		const start = () => {
			measure();
			window.requestAnimationFrame(tick);
		};

		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(start).catch(start);
		} else {
			start();
		}
		window.addEventListener('resize', () => {
			window.requestAnimationFrame(measure);
		});
	})();

	const gsap = window.gsap;

	/**
	 * FAQ accordion via GSAP.
	 * Keep <details open> always so the browser never display:none's the panel
	 * (that unmount-style jump on close). Visual state is height + aria only.
	 */
	if (!gsap) {
		return;
	}

	document.querySelectorAll('.construction-faq-item').forEach((details) => {
		const summary = details.querySelector('summary');
		const panel = details.querySelector('.construction-faq-item__panel');
		if (!summary || !panel) {
			return;
		}

		let expanded = false;
		let animating = false;

		const setCollapsed = () => {
			expanded = false;
			details.classList.remove('is-open');
			summary.setAttribute('aria-expanded', 'false');
			panel.setAttribute('aria-hidden', 'true');
			panel.setAttribute('inert', '');
			gsap.set(panel, { height: 0, overflow: 'hidden' });
		};

		const setExpanded = () => {
			expanded = true;
			details.classList.add('is-open');
			summary.setAttribute('aria-expanded', 'true');
			panel.setAttribute('aria-hidden', 'false');
			panel.removeAttribute('inert');
			gsap.set(panel, { height: 'auto', overflow: 'hidden' });
		};

		// Always keep native open so content stays in the layout tree.
		details.open = true;
		summary.setAttribute('role', 'button');

		if (details.classList.contains('is-open')) {
			setExpanded();
		} else {
			setCollapsed();
		}

		summary.addEventListener('click', (event) => {
			event.preventDefault();
			event.stopPropagation();

			if (animating) {
				return;
			}

			if (reduceMotion) {
				if (expanded) {
					setCollapsed();
				} else {
					setExpanded();
				}
				return;
			}

			animating = true;
			gsap.killTweensOf(panel);

			if (!expanded) {
				details.classList.add('is-open');
				summary.setAttribute('aria-expanded', 'true');
				panel.setAttribute('aria-hidden', 'false');
				panel.removeAttribute('inert');
				expanded = true;

				gsap.fromTo(
					panel,
					{ height: 0, overflow: 'hidden' },
					{
						height: 'auto',
						duration: 0.4,
						ease: 'power2.out',
						onComplete: () => {
							animating = false;
						},
					}
				);
				return;
			}

			gsap.to(panel, {
				height: 0,
				duration: 0.35,
				ease: 'power2.inOut',
				overflow: 'hidden',
				onComplete: () => {
					details.classList.remove('is-open');
					summary.setAttribute('aria-expanded', 'false');
					panel.setAttribute('aria-hidden', 'true');
					panel.setAttribute('inert', '');
					expanded = false;
					animating = false;
				},
			});
		});
	});
})();
