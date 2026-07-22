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
