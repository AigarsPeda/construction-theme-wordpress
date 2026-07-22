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

	// Project gallery lightbox with a virtual ring (clone last/first)
	// so next/prev always slide in one direction — no awkward wrap.
	if (typeof window.GLightbox === 'function' && document.querySelector('.construction-projects__grid .construction-lightbox')) {
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
				// Allow GLightbox to finish DOM updates before accepting another jump.
				requestAnimationFrame(() => {
					jumping = false;
				});
			};

			lightbox.on('slide_changed', ({ current }) => {
				if (jumping || realItems.length < 2) {
					return;
				}
				if (current.index === lastCloneIndex) {
					// Landed on cloned first after sliding past last → snap to real first.
					jumpWithoutSlide(firstRealIndex);
				} else if (current.index === 0) {
					// Landed on cloned last after sliding before first → snap to real last.
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
