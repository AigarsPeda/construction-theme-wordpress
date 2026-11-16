/**
 * Request form: the dialog and inline "Request form" blocks.
 *
 * Any core Button whose wrapper has the class `construction-request` opens the
 * dialog; `construction-request--estimate` / `--assessment` preset the request
 * type. Without JavaScript (or <dialog> support) the button keeps its normal
 * link, e.g. to the contacts page.
 */
(() => {
	'use strict';

	const TRIGGER = '.wp-block-button[class*="construction-request"] > a';
	const TYPES = ['estimate', 'assessment'];
	const PHONE = /^\+?[0-9 ()\-.]{6,25}$/;
	const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

	const setup = (root) => {
		let config;
		try {
			config = JSON.parse(root.querySelector('.construction-request-form__config').textContent || '{}');
		} catch (error) {
			return null;
		}

		const messages = config.messages || {};
		const body = root.querySelector('[data-request-body]');
		const done = root.querySelector('[data-request-done]');
		const form = root.querySelector('form');
		const title = body.querySelector('.construction-request-form__title');
		const status = form.querySelector('.construction-request-form__status');
		const submit = form.querySelector('.construction-request-form__submit');
		const message = form.elements.message;

		let startedAt = Date.now();
		let completed = false;
		let busy = false;

		const fields = () => Array.from(form.querySelectorAll('input:not([type="hidden"]):not([name="website"]), textarea'));

		const setError = (field, text) => {
			const error = form.querySelector('#' + field.id + '-error');
			field.toggleAttribute('aria-invalid', Boolean(text));
			if (error) {
				error.textContent = text || '';
				error.hidden = !text;
			}
		};

		const clearErrors = () => {
			fields().forEach((field) => setError(field, ''));
			status.textContent = '';
			status.classList.remove('is-error');
		};

		const validate = (field) => {
			const value = field.value.trim();
			if (!value) {
				return field.required ? messages.required : '';
			}
			if (field.type === 'email' && !EMAIL.test(value)) {
				return messages.email;
			}
			if (field.type === 'tel' && !PHONE.test(value)) {
				return messages.phone;
			}
			return '';
		};

		const showErrors = (errors) => {
			let first = null;
			fields().forEach((field) => {
				const text = errors[field.name] || '';
				setError(field, text);
				if (text && !first) {
					first = field;
				}
			});
			if (first) {
				status.textContent = messages.form || '';
				status.classList.add('is-error');
				first.focus();
			}
			return Boolean(first);
		};

		// Re-check a field once the visitor leaves it, and clear errors while typing.
		form.addEventListener('focusout', (event) => {
			const field = event.target;
			if (field.matches && field.matches('input, textarea') && field.hasAttribute('aria-invalid')) {
				setError(field, validate(field));
			}
		});
		form.addEventListener('input', (event) => {
			const field = event.target;
			if (field.hasAttribute && field.hasAttribute('aria-invalid') && !validate(field)) {
				setError(field, '');
			}
		});

		form.addEventListener('submit', async (event) => {
			event.preventDefault();
			if (busy) {
				return;
			}

			clearErrors();
			const errors = {};
			fields().forEach((field) => {
				const text = validate(field);
				if (text) {
					errors[field.name] = text;
				}
			});
			if (showErrors(errors)) {
				return;
			}

			const payload = {
				type: form.elements.type.value,
				lang: config.lang,
				page: config.page,
				token: config.token,
				website: form.elements.website ? form.elements.website.value : '',
				elapsed: Date.now() - startedAt,
			};
			fields().forEach((field) => {
				payload[field.name] = field.value.trim();
			});

			busy = true;
			submit.disabled = true;
			submit.textContent = messages.sending || submit.textContent;

			try {
				const response = await fetch(config.endpoint, {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify(payload),
					credentials: 'same-origin',
				});
				const data = await response.json().catch(() => ({}));

				if (response.ok && data.ok) {
					completed = true;
					body.hidden = true;
					done.hidden = false;
					done.querySelector('.construction-request-form__title').focus();
					return;
				}
				if (data.errors && showErrors(data.errors)) {
					return;
				}
				status.textContent = data.message || messages.send || '';
				status.classList.add('is-error');
			} catch (error) {
				status.textContent = messages.send || '';
				status.classList.add('is-error');
			} finally {
				busy = false;
				submit.disabled = false;
				submit.textContent = messages.submit || submit.textContent;
			}
		});

		return {
			fields,
			isComplete: () => completed,
			reset() {
				form.reset();
				clearErrors();
				completed = false;
				body.hidden = false;
				done.hidden = true;
			},
			prepare(type, heading) {
				form.elements.type.value = type;
				title.textContent = heading || config.title || '';
				if (message) {
					message.placeholder = (config.hints && config.hints[type]) || '';
				}
				startedAt = Date.now();
			},
		};
	};

	// Inline blocks.
	document.querySelectorAll('.construction-request-form--inline[data-request-form]').forEach(setup);

	// Dialog opened by request buttons.
	const dialog = document.getElementById('construction-request');
	if (!dialog || typeof dialog.showModal !== 'function') {
		return;
	}
	const instance = setup(dialog);
	if (!instance) {
		return;
	}

	// Capture phase: run before link handlers such as the smooth scroll for
	// "#contact", so request buttons always open the form instead.
	document.addEventListener('click', (event) => {
		const link = event.target.closest(TRIGGER);
		if (!link || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
			return;
		}
		event.preventDefault();
		event.stopPropagation();

		if (instance.isComplete()) {
			instance.reset();
		}
		const wrapper = link.closest('.wp-block-button');
		const match = (wrapper ? wrapper.className : '').match(/construction-request--([a-z]+)/);
		instance.prepare(match && TYPES.includes(match[1]) ? match[1] : 'general', link.textContent.trim());

		dialog.showModal();
		const first = instance.fields()[0];
		if (first) {
			first.focus();
		}
	}, true);

	dialog.addEventListener('click', (event) => {
		// A click on the backdrop lands on the <dialog> itself.
		if (event.target === dialog || event.target.closest('[data-request-close]')) {
			dialog.close();
		}
	});

	dialog.addEventListener('close', () => {
		if (instance.isComplete()) {
			instance.reset();
		}
	});
})();
