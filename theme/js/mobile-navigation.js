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
			const submenu = direct(item, '.sub-menu');
			if (submenu) {
				submenu.inert = true;
				submenu.setAttribute('aria-hidden', 'true');
			}
		});
	};

	const showRoot = (focusBack = true) => {
		root.inert = false;
		root.removeAttribute('aria-hidden');
		detail.inert = true;
		detail.setAttribute('aria-hidden', 'true');
		drawer.dataset.view = 'root';
		primary?.querySelectorAll(':scope > li.menu-item-has-children').forEach((item) => {
			direct(item, '.site-header__submenu-toggle')?.setAttribute('aria-expanded', 'false');
		});
		if (focusBack) (previousControl?.isConnected ? previousControl : closeButton)?.focus();
		previousControl = null;
		// Keep links painted as the detail view slides out. Clear on close,
		// rather than blanking the panel in the middle of the animation.
	};

	const showDetail = (item, control) => {
		const submenu = direct(item, '.sub-menu');
		if (!submenu) return;

		const link = direct(item, 'a');
		const label = (link?.textContent || '').trim();
		// Construct a fresh, visible list from real WordPress child links.
		// Cloning the hidden source <ul> retained layout styles/identifiers
		// that could leave the detail screen looking empty.
		const childLinks = submenu.querySelectorAll(':scope > li > a');
		if (!childLinks.length) return false;
		const list = document.createElement('ul');
		list.className = 'balt-mobile-drawer__detail-menu';
		childLinks.forEach((child) => {
			const row = document.createElement('li');
			row.appendChild(child.cloneNode(true));
			list.appendChild(row);
		});
		// The parent's link is available on the root screen. Only child
		// destinations belong here; no duplicate "View About" row.
		detailItems.replaceChildren(list);
		title.textContent = label;
		previousControl = control;
		control?.setAttribute('aria-expanded', 'true');
		detail.inert = false;
		detail.removeAttribute('aria-hidden');
		detail.scrollTop = 0;
		drawer.dataset.view = 'detail';
		root.inert = true;
		root.setAttribute('aria-hidden', 'true');
		backButton.focus();
		return true;
	};

	primary?.querySelectorAll(':scope > li.menu-item-has-children').forEach((item) => {
		const toggle = direct(item, '.site-header__submenu-toggle');
		if (!toggle || !direct(item, '.sub-menu')) return;

		// Parent labels remain regular navigation links. Only the separate
		// chevron button drills into a new screen.
		toggle.setAttribute('aria-controls', 'balt-mobile-navigation-detail');
		toggle.addEventListener('click', () => showDetail(item, toggle));
	});

	utility?.querySelectorAll(':scope > li.menu-item-has-children').forEach((item, index) => {
		const toggle = direct(item, '.site-header__utility-toggle');
		const submenu = direct(item, '.sub-menu');
		if (!toggle || !submenu) return;
		submenu.id = 'balt-mobile-utility-submenu-' + (index + 1);
		submenu.inert = true;
		submenu.setAttribute('aria-hidden', 'true');
		toggle.setAttribute('aria-controls', submenu.id);
		toggle.addEventListener('click', () => {
			const opening = !item.classList.contains('is-open');
			closeUtility();
			if (!opening) return;

			item.classList.add('is-open');
			submenu.inert = false;
			submenu.removeAttribute('aria-hidden');
			toggle.setAttribute('aria-expanded', 'true');
			// Match the height to the actual WordPress links, so short and
			// long accordions animate smoothly without arbitrary max-heights.
			submenu.style.setProperty('--balt-utility-height', (submenu.scrollHeight + 12) + 'px');
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
		// Always reopen on the root screen, independent of the last visit.
		showRoot(false);
		root.scrollTop = 0;
		detail.scrollTop = 0;
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
		detailItems.replaceChildren();
		detail.scrollTop = 0;
		root.scrollTop = 0;
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
