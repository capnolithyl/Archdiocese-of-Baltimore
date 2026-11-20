(() => {
	const header = document.getElementById('masthead');
	if (!header) return;

	const menuButton = header.querySelector('.site-header__mobile-toggle');
	const menu = header.querySelector('.site-header__nav');
	const searchButton = header.querySelector('.site-header__search-toggle');
	const searchPanel = header.querySelector('.site-header__search');
	const desktop = window.matchMedia('(min-width: 1280px)');
	let pending = false;

	const updateScroll = () => {
		header.dataset.compact = String(window.scrollY > 48);
		pending = false;
	};

	const updateOpenState = () => {
		const mobileOpen = menu && !menu.classList.contains('hidden') && !desktop.matches;
		const searchOpen = searchPanel && !searchPanel.classList.contains('hidden');
		header.dataset.menuOpen = String(Boolean(mobileOpen || searchOpen));
	};

	const closeMenus = () => {
		menu?.classList.add('hidden');
		menuButton?.setAttribute('aria-expanded', 'false');
		searchPanel?.classList.add('hidden');
		searchButton?.setAttribute('aria-expanded', 'false');
		header.querySelectorAll('.site-header__menu li.is-open').forEach((item) => {
			item.classList.remove('is-open');
			item.querySelector(':scope > .site-header__submenu-toggle')?.setAttribute('aria-expanded', 'false');
		});
		updateOpenState();
	};

	menuButton?.addEventListener('click', () => {
		const opening = menu?.classList.contains('hidden');
		closeMenus();
		if (opening && menu) {
			menu.classList.remove('hidden');
			menuButton.setAttribute('aria-expanded', 'true');
			updateOpenState();
		}
	});

	searchButton?.addEventListener('click', () => {
		const opening = searchPanel?.classList.contains('hidden');
		closeMenus();
		if (opening && searchPanel) {
			searchPanel.classList.remove('hidden');
			searchButton.setAttribute('aria-expanded', 'true');
			searchPanel.querySelector('input[type="search"]')?.focus();
			updateOpenState();
		}
	});

	header.querySelectorAll('.site-header__menu li.menu-item-has-children').forEach((item) => {
		const toggle = item.querySelector(':scope > .site-header__submenu-toggle');
		const submenu = item.querySelector(':scope > .sub-menu');
		if (!toggle || !submenu) return;
		const id = 'balt-submenu-' + (item.id || Math.random().toString(36).slice(2));
		submenu.id = id;
		toggle.setAttribute('aria-controls', id);
		toggle.addEventListener('click', () => {
			const opening = !item.classList.contains('is-open');
			item.classList.toggle('is-open', opening);
			toggle.setAttribute('aria-expanded', String(opening));
		});
	});

	document.addEventListener('click', (event) => {
		if (!header.contains(event.target)) closeMenus();
	});

	document.addEventListener('keydown', (event) => {
		if (event.key !== 'Escape') return;
		const open = header.dataset.menuOpen === 'true' || header.querySelector('.site-header__menu li.is-open');
		if (open) {
			closeMenus();
			menuButton?.focus();
		}
	});

	desktop.addEventListener('change', closeMenus);
	window.addEventListener('scroll', () => {
		if (pending) return;
		pending = true;
		window.requestAnimationFrame(updateScroll);
	}, { passive: true });

	updateScroll();
})();
