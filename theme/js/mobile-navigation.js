(() => {
	const drawer = document.getElementById('balt-mobile-navigation');
	const trigger = document.querySelector('#masthead .site-header__mobile-toggle');
	if (!drawer || !trigger || typeof drawer.showModal !== 'function') return;

	const closeButton = drawer.querySelector('[data-balt-mobile-close]');
	const backButton = drawer.querySelector('[data-balt-mobile-back]');
	const root = drawer.querySelector('[data-balt-mobile-root]');
	const detail = drawer.querySelector('[data-balt-mobile-detail]');
	const title = drawer.querySelector('[data-balt-mobile-title]');
	const detailItems = drawer.querySelector('[data-balt-mobile-detail-items]');
	const primary = drawer.querySelector('.balt-mobile-primary-menu');
	const utility = drawer.querySelector('.balt-mobile-utility-menu');
	const desktop = window.matchMedia('(min-width: 1280px)');
	if (!root || !detail || !detailItems || !backButton) return;

	let restoreFocus = true;
	let previousControl = null;

	const direct = (item, selector) => item.querySelector(':scope > ' + selector);

	const closeUtility = () => {
		utility?.querySelectorAll(':scope > li.menu-item-has-children').forEach((item) => {
			item.classList.remove('is-open');
			direct(item, '.site-header__utility-toggle')?.setAttribute('aria-expanded', 'false');
		});
	};

	const showRoot = (focusBack = true) => {
		drawer.dataset.view = 'root';
		root.inert = false;
		root.removeAttribute('aria-hidden');
		if (focusBack) (previousControl && previousControl.isConnected ? previousControl : closeButton)?.focus();
		detail.inert = true;
		detail.setAttribute('aria-hidden', 'true');
		detailItems.replaceChildren();
		primary?.querySelectorAll(':scope > li.menu-item-has-children').forEach((item) => {
			direct(item, 'a')?.setAttribute('aria-expanded', 'false');
			direct(item, '.site-header__submenu-toggle')?.setAttribute('aria-expanded', 'false');
		});
		previousControl = null;
	};

	const showDetail = (item, control) => {
		const submenu = direct(item, '.sub-menu');
		if (!submenu) return;

		const link = direct(item, 'a');
		const label = (link?.textContent || '').trim();
		const clone = submenu.cloneNode(true);
		clone.removeAttribute('id');
		clone.className = 'balt-mobile-drawer__detail-menu';
		clone.querySelectorAll('[id]').forEach((element) => element.removeAttribute('id'));
		const fragment = document.createDocumentFragment();

		// Primary parent destinations remain available from the drill-down.
		// Do not create fake URLs for empty placeholder menu links.
		const href = link?.getAttribute('href') || '';
		if (href && href !== '#' && !href.toLowerCase().startsWith('javascript:')) {
			const overview = document.createElement('a');
			overview.className = 'balt-mobile-drawer__overview';
			overview.href = href;
			overview.textContent = 'View ' + label;
			fragment.appendChild(overview);
		}
		fragment.appendChild(clone);
		detailItems.replaceChildren(fragment);
		title.textContent = label;
		previousControl = control;
		control?.setAttribute('aria-expanded', 'true');
		link?.setAttribute('aria-expanded', 'true');
		detail.inert = false;
		detail.removeAttribute('aria-hidden');
		drawer.dataset.view = 'detail';
		backButton.focus();
		root.inert = true;
		root.setAttribute('aria-hidden', 'true');
	};

	primary?.querySelectorAll(':scope > li.menu-item-has-children').forEach((item) => {
		const link = direct(item, 'a');
		const toggle = direct(item, '.site-header__submenu-toggle');
		if (!direct(item, '.sub-menu')) return;

		toggle?.addEventListener('click', () => showDetail(item, toggle));
		link?.setAttribute('role', 'button');
		link?.setAttribute('aria-label', 'Explore ' + link.textContent.trim());
		link?.setAttribute('aria-controls', 'balt-mobile-navigation-detail');
		link?.setAttribute('aria-expanded', 'false');
		toggle?.setAttribute('aria-controls', 'balt-mobile-navigation-detail');
		link?.addEventListener('click', (event) => {
			if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
			event.preventDefault();
			showDetail(item, link);
		});
		link?.addEventListener('keydown', (event) => {
			if (event.key === ' ') {
				event.preventDefault();
				showDetail(item, link);
			}
		});
	});

	utility?.querySelectorAll(':scope > li.menu-item-has-children').forEach((item, index) => {
		const toggle = direct(item, '.site-header__utility-toggle');
		const submenu = direct(item, '.sub-menu');
		if (!toggle || !submenu) return;
		submenu.id = 'balt-mobile-utility-submenu-' + (index + 1);
		toggle.setAttribute('aria-controls', submenu.id);
		toggle.addEventListener('click', () => {
			const opening = !item.classList.contains('is-open');
			closeUtility();
			item.classList.toggle('is-open', opening);
			toggle.setAttribute('aria-expanded', String(opening));
		});
	});

	const closeDrawer = () => {
		if (drawer.open) drawer.close();
	};

	trigger.addEventListener('click', () => {
		if (desktop.matches) return;
		if (drawer.open) {
			closeDrawer();
			return;
		}
		restoreFocus = true;
		drawer.showModal();
		document.documentElement.classList.add('balt-mobile-menu-open');
		trigger.setAttribute('aria-expanded', 'true');
		closeButton?.focus();
	});

	closeButton?.addEventListener('click', closeDrawer);
	backButton.addEventListener('click', () => showRoot(true));

	drawer.addEventListener('click', (event) => {
		if (event.target === drawer) closeDrawer();
	});

	drawer.addEventListener('close', () => {
		document.documentElement.classList.remove('balt-mobile-menu-open');
		trigger.setAttribute('aria-expanded', 'false');
		showRoot(false);
		closeUtility();
		if (restoreFocus && !desktop.matches) trigger.focus();
		restoreFocus = true;
	});

	drawer.querySelectorAll('[data-balt-mobile-search]').forEach((button) => {
		button.addEventListener('click', () => {
			const query = button.dataset.baltMobileSearchQuery || '';
			restoreFocus = false;
			closeDrawer();
			// Reuse the existing live-search overlay instead of duplicating search.
			document.querySelector('#masthead .site-header__search-toggle')?.click();
			if (query) {
				const input = document.getElementById('balt-live-search-input');
				if (input) {
					input.value = query;
					input.dispatchEvent(new Event('input', { bubbles: true }));
				}
			}
		});
	});

	desktop.addEventListener('change', () => {
		if (desktop.matches) closeDrawer();
	});
})();
