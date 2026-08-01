/**
 * Construction front-end script.
 */
(() => {
	'use strict';

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

	// Projekti page: open each project in an animated modal (grid stays put).
	const projectsRoot = document.querySelector('.construction-projects');
	if (projectsRoot && projectsRoot.querySelector('.construction-project-card')) {
		const grid = projectsRoot.querySelector('.construction-projects__grid');
		const cards = Array.from(projectsRoot.querySelectorAll('.construction-project-card'));
		const legacyViewer = projectsRoot.querySelector('.construction-project-viewer');
		if (legacyViewer) {
			legacyViewer.hidden = true;
			legacyViewer.innerHTML = '';
		}

		const labelClose = projectsRoot.getAttribute('data-label-close') || 'Close';
		const labelPrev = projectsRoot.getAttribute('data-label-prev') || 'Previous';
		const labelNext = projectsRoot.getAttribute('data-label-next') || 'Next';
		const reduceMotionProjects = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
		let activeIndex = -1;
		let activeImageIndex = 0;
		let lastFocus = null;

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
		const modalBody = modal.querySelector('.construction-project-modal__body');
		const modalDialog = modal.querySelector('.construction-project-modal__dialog');

		const getCardImages = (card) => {
			const links = Array.from(card.querySelectorAll('a.construction-lightbox[href]'));
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
		};

		const showImage = (imageIndex) => {
			if (activeIndex < 0 || !modalBody) {
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

		const buildDetailMarkup = (images) => {
			const thumbs = images
				.map(
					(item, i) => `<button type="button" class="construction-project-viewer__thumb" data-image-index="${i}" aria-label="${i + 1}">
						<img src="${item.thumb}" alt="">
					</button>`
				)
				.join('');
			return `
				<div class="construction-project-viewer__media">
					<figure class="construction-project-viewer__stage">
						<img src="" alt="">
					</figure>
					<div class="construction-project-viewer__thumbs">${thumbs}</div>
				</div>
				<div class="construction-project-viewer__meta">
					<div class="construction-project-viewer__controls">
						<button type="button" class="construction-project-viewer__nav" data-nav="prev" aria-label="${labelPrev}">‹</button>
						<button type="button" class="construction-project-viewer__nav" data-nav="next" aria-label="${labelNext}">›</button>
						<button type="button" class="construction-project-viewer__close" data-nav="close" aria-label="${labelClose}">×</button>
					</div>
					<h2 class="construction-project-viewer__title" id="construction-project-modal-title"></h2>
					<p class="construction-project-viewer__text"></p>
				</div>
			`;
		};

		const animateOpen = (fromCard, animate = true) => {
			const gsap = window.gsap;
			modal.hidden = false;
			modal.classList.add('is-open');
			setBodyLock(true);

			// Deep-link from homepage (or reduced motion): show instantly, no grow animation.
			if (!animate || !gsap || reduceMotionProjects || !modalDialog) {
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

			// Optional: nudge start toward the clicked card for a “grown from card” feel.
			if (fromCard) {
				const from = fromCard.getBoundingClientRect();
				const dialogRect = modalDialog.getBoundingClientRect();
				if (from.width > 40 && dialogRect.width > 40) {
					const x = from.left + from.width / 2 - (dialogRect.left + dialogRect.width / 2);
					const y = from.top + from.height / 2 - (dialogRect.top + dialogRect.height / 2);
					gsap.set(modalDialog, { x: x * 0.35, y: y * 0.35 + 24 });
				}
			}

			gsap.to(modal, { autoAlpha: 1, duration: 0.25, ease: 'power1.out' });
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
				modal.classList.remove('is-open', 'is-visible');
				modal.hidden = true;
				if (modalBody) {
					modalBody.innerHTML = '';
				}
				setBodyLock(false);
				if (typeof onDone === 'function') {
					onDone();
				}
			};

			if (!gsap || reduceMotionProjects || !modalDialog) {
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
			if (activeIndex < 0 && modal.hidden) {
				return;
			}
			cards.forEach((node) => node.classList.remove('is-active'));
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
			const card = cards[index];
			if (!card || !modalBody) {
				return;
			}
			const images = getCardImages(card);
			if (images.length === 0) {
				return;
			}
			if (activeIndex === index && modal.classList.contains('is-open')) {
				return;
			}

			lastFocus = document.activeElement;
			activeIndex = index;
			const titleEl = card.querySelector('.construction-project-card__title');
			const textEl = card.querySelector('.construction-project-card__text');
			const title = titleEl ? titleEl.textContent.trim() : '';
			const text = textEl ? textEl.textContent.trim() : '';
			const slug = card.id || card.getAttribute('data-project-slug') || '';

			cards.forEach((node, i) => {
				node.classList.toggle('is-active', i === index);
			});

			modalBody.innerHTML = buildDetailMarkup(images);
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

		modal.addEventListener('click', (event) => {
			if (event.target.closest('[data-modal-close], [data-nav="close"]')) {
				event.preventDefault();
				closeProject();
				return;
			}
			const thumb = event.target.closest('[data-image-index]');
			if (thumb) {
				event.preventDefault();
				showImage(Number(thumb.getAttribute('data-image-index')));
				return;
			}
			const nav = event.target.closest('[data-nav]');
			if (!nav) {
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
			if (!modal.classList.contains('is-open')) {
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
				const index = cards.indexOf(card);
				if (index >= 0) {
					openProject(index, card);
				}
			});
		}

		const syncFromHash = ({ animate = false } = {}) => {
			const slug = (location.hash || '').replace(/^#/, '');
			if (!slug) {
				if (modal.classList.contains('is-open')) {
					closeProject();
				}
				return;
			}
			const index = cards.findIndex((card) => card.id === slug || card.getAttribute('data-project-slug') === slug);
			if (index < 0) {
				return;
			}
			if (activeIndex === index && modal.classList.contains('is-open')) {
				return;
			}
			// From homepage / deep link: open instantly. Grid clicks still animate.
			openProject(index, null, { animate });
		};

		if (location.hash) {
			syncFromHash({ animate: false });
		}
		window.addEventListener('hashchange', () => syncFromHash({ animate: false }));
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

	const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	// Homepage Realizētie projekti: slow infinite horizontal marquee.
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

		const measure = () => {
			const styles = window.getComputedStyle(track);
			const gap = Number.parseFloat(styles.columnGap || styles.gap) || 18;
			const distance = set.getBoundingClientRect().width + gap;
			const duration = Math.max(45, distance / 32);
			track.style.setProperty('--bn-marquee-distance', `${distance}px`);
			track.style.setProperty('--bn-marquee-duration', `${duration}s`);
			track.classList.add('is-marquee-animated');
		};

		const runMeasure = () => window.requestAnimationFrame(measure);
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(runMeasure).catch(runMeasure);
		} else {
			runMeasure();
		}
		window.addEventListener('resize', runMeasure);
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
