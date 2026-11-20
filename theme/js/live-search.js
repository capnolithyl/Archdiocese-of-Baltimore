(() => {
	const dialog = document.getElementById('balt-live-search-dialog');
	const trigger = document.querySelector('#masthead .site-header__search-toggle');
	if (!dialog || !trigger || typeof dialog.showModal !== 'function') return;

	const input = dialog.querySelector('[data-balt-search-input]');
	const form = dialog.querySelector('[data-balt-search-form]');
	const close = dialog.querySelector('[data-balt-search-close]');
	const clear = dialog.querySelector('[data-balt-search-clear]');
	const popular = dialog.querySelector('[data-balt-search-popular]');
	const feedback = dialog.querySelector('[data-balt-search-feedback]');
	const hint = dialog.querySelector('[data-balt-search-hint]');
	const list = dialog.querySelector('[data-balt-search-results]');
	const message = dialog.querySelector('[data-balt-search-message]');
	const announcement = dialog.querySelector('[data-balt-search-announcement]');
	if (!input || !form || !list || !dialog.dataset.endpoint) return;

	let timer = 0;
	let controller = null;
	let generation = 0;
	let options = [];
	let active = -1;

	const select = (index) => {
		active = index;
		options.forEach((option, i) => option.setAttribute('aria-selected', String(i === index)));
		if (index < 0 || !options[index]) {
			input.removeAttribute('aria-activedescendant');
		} else {
			input.setAttribute('aria-activedescendant', options[index].id);
			options[index].scrollIntoView({ block: 'nearest' });
		}
	};

	const resetList = () => {
		options = [];
		list.replaceChildren();
		select(-1);
		input.setAttribute('aria-expanded', 'false');
	};

	const cancelPending = () => {
		window.clearTimeout(timer);
		controller?.abort();
		controller = null;
		generation++;
	};

	const showPopular = () => {
		popular.hidden = false;
		feedback.hidden = true;
		hint.hidden = true;
		message.hidden = true;
		announcement.textContent = '';
	};

	const render = (items) => {
		const fragment = document.createDocumentFragment();
		options = [];
		items.slice(0, 8).forEach((item) => {
			if (!item || typeof item.title !== 'string' || typeof item.url !== 'string') return;
			let url;
			try {
				url = new URL(item.url, window.location.href);
			} catch {
				return;
			}
			if (!['http:', 'https:'].includes(url.protocol) || url.origin !== window.location.origin) return;

			const link = document.createElement('a');
			link.className = 'balt-search-result';
			link.id = 'balt-search-result-' + options.length;
			link.href = url.href;
			link.tabIndex = -1;
			link.setAttribute('role', 'option');
			link.setAttribute('aria-selected', 'false');

			const title = document.createElement('span');
			title.className = 'balt-search-result-title';
			title.textContent = item.title;
			link.appendChild(title);
			if (typeof item.context === 'string' && item.context) {
				const context = document.createElement('span');
				context.className = 'balt-search-result-context';
				context.textContent = item.context;
				link.appendChild(context);
			}
			link.addEventListener('mouseenter', () => select(options.indexOf(link)));
			options.push(link);
			fragment.appendChild(link);
		});
		list.replaceChildren(fragment);
		select(-1);
		input.setAttribute('aria-expanded', String(options.length > 0));
		hint.hidden = !options.length;
		message.hidden = Boolean(options.length);
		message.textContent = options.length ? '' : 'No matches found. Press Enter to search the full website.';
		announcement.textContent = options.length
			? options.length + (options.length === 1 ? ' suggestion available.' : ' suggestions available.')
			: 'No suggestions found. Press Enter to search the full website.';
	};

	const fetchResults = async (query, version) => {
		const current = new AbortController();
		controller = current;
		try {
			const url = new URL(dialog.dataset.endpoint, window.location.href);
			url.searchParams.set('q', query);
			const response = await fetch(url, {
				signal: current.signal,
				credentials: 'same-origin',
				headers: { Accept: 'application/json' }
			});
			if (!response.ok) throw new Error('Search failed');
			const data = await response.json();
			if (version !== generation || !dialog.open) return;
			render(Array.isArray(data.results) ? data.results : []);
		} catch (error) {
			if (error.name === 'AbortError' || version !== generation || !dialog.open) return;
			resetList();
			hint.hidden = true;
			message.hidden = false;
			message.textContent = 'Suggestions unavailable. Press Enter to search the full website.';
			announcement.textContent = 'Search suggestions unavailable.';
		} finally {
			if (controller === current) controller = null;
		}
	};

	const update = () => {
		cancelPending();
		resetList();
		clear.hidden = !input.value;
		const query = input.value.trim();
		if (!query) {
			showPopular();
			return;
		}
		popular.hidden = true;
		feedback.hidden = false;
		hint.hidden = true;
		message.hidden = false;
		if (query.length < 2) {
			message.textContent = 'Type at least two characters to see suggestions.';
			announcement.textContent = '';
			return;
		}
		message.textContent = 'Searching...';
		announcement.textContent = 'Searching...';
		const version = generation;
		timer = window.setTimeout(() => fetchResults(query, version), 250);
	};

	trigger.addEventListener('click', () => {
		if (dialog.open) return dialog.close();
		dialog.showModal();
		document.documentElement.classList.add('balt-search-open');
		trigger.setAttribute('aria-expanded', 'true');
		input.focus();
	});
	close?.addEventListener('click', () => dialog.close());
	dialog.addEventListener('click', (event) => {
		if (event.target === dialog) dialog.close();
	});
	dialog.addEventListener('close', () => {
		cancelPending();
		input.value = '';
		clear.hidden = true;
		resetList();
		showPopular();
		document.documentElement.classList.remove('balt-search-open');
		trigger.setAttribute('aria-expanded', 'false');
		const mobileReturn = window.matchMedia('(max-width: 1279px)').matches
			? document.querySelector('#masthead .site-header__mobile-toggle')
			: null;
		(mobileReturn || trigger).focus();
	});

	input.addEventListener('input', update);
	clear?.addEventListener('click', () => {
		input.value = '';
		update();
		input.focus();
	});
	dialog.querySelectorAll('[data-balt-search-topic]').forEach((button) => {
		button.addEventListener('click', () => {
			input.value = button.dataset.baltSearchTopic || '';
			update();
			input.focus();
		});
	});
	input.addEventListener('keydown', (event) => {
		if (event.key === 'Escape') {
			event.preventDefault();
			dialog.close();
			return;
		}
		if ((event.key === 'ArrowDown' || event.key === 'ArrowUp') && options.length) {
			event.preventDefault();
			select(event.key === 'ArrowDown'
				? (active + 1) % options.length
				: (active - 1 + options.length) % options.length);
			return;
		}
		if (event.key === 'Enter' && active >= 0 && options[active]) {
			event.preventDefault();
			window.location.assign(options[active].href);
		}
	});
	// With no selected suggestion, Enter submits to the existing WP search.php.
	form.addEventListener('submit', (event) => {
		if (!input.value.trim()) {
			event.preventDefault();
			input.focus();
		}
	});
})();
