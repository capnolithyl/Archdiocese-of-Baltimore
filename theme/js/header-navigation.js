(() => {
	const header = document.getElementById('masthead');
	if (!header) return;

	const menuButton = header.querySelector('.site-header__mobile-toggle');
	const menu = header.querySelector('.site-header__nav');
	const searchButton = header.querySelector('.site-header__search-toggle');
	const desktop = window.matchMedia('(min-width: 1280px)');
	const submenuItems = [...header.querySelectorAll('.site-header__menu > li.menu-item-has-children')];
	let pending = false;

	const updateScroll = () => {
		header.dataset.compact = String(window.scrollY > 48);
		pending = false;
	};

	const updateOpenState = () => {
		const mobileOpen = menu && !menu.classList.contains('hidden') && !desktop.matches;
		header.dataset.menuOpen = String(Boolean(mobileOpen));
	};

	const directChild = (item, selector) => item.querySelector(':scope > ' + selector);

	const closeBranch = (item) => {
		item.classList.remove('is-open');
		directChild(item, '.site-header__submenu-toggle')?.setAttribute('aria-expanded', 'false');
		item.querySelectorAll('li.is-open').forEach((child) => {
			child.classList.remove('is-open');
			directChild(child, '.site-header__submenu-toggle')?.setAttribute('aria-expanded', 'false');
		});
	};

	/**
	 * Open the first-level dropdown before measuring its viewport bounds.
	 * The narrow/mobile navigation stays under its existing behavior.
	 */
	const placeSubmenu = (item) => {
		if (!desktop.matches) return;
		const submenu = directChild(item, '.sub-menu');
		if (!submenu) return;

		submenu.dataset.flyoutDirection = 'right';
		submenu.style.removeProperty('--balt-submenu-shift');

		const viewport = document.documentElement?.clientWidth || window.innerWidth;
		const edge = 8;
		const firstRect = submenu.getBoundingClientRect();
		if (firstRect.right > viewport - edge) {
			// Align this single dropdown to its parent's right edge.
			submenu.dataset.flyoutDirection = 'left';
		}

		// Protect narrow windows and unusually wide translated labels.
		const rect = submenu.getBoundingClientRect();
		const shift = rect.left < edge
			? edge - rect.left
			: rect.right > viewport - edge
				? viewport - edge - rect.right
				: 0;
		if (shift) {
			submenu.style.setProperty('--balt-submenu-shift', Math.round(shift) + 'px');
		}
	};

	const openDesktop = (item) => {
		if (!desktop.matches) return;
		for (const sibling of item.parentElement?.children || []) {
			if (sibling !== item && sibling.classList?.contains('is-open')) closeBranch(sibling);
		}
		item.classList.add('is-open');
		directChild(item, '.site-header__submenu-toggle')?.setAttribute('aria-expanded', 'true');
		placeSubmenu(item);
	};

	const closeMenus = () => {
		menu?.classList.add('hidden');
		menuButton?.setAttribute('aria-expanded', 'false');
		submenuItems.forEach((item) => closeBranch(item));
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

	// The native search dialog owns its own open, close and focus lifecycle.
	searchButton?.addEventListener('click', closeMenus);

	submenuItems.forEach((item, index) => {
		const toggle = directChild(item, '.site-header__submenu-toggle');
		const submenu = directChild(item, '.sub-menu');
		if (!toggle || !submenu) return;

		const id = 'balt-submenu-' + (item.id || String(index + 1));
		submenu.id = id;
		toggle.setAttribute('aria-controls', id);

		toggle.addEventListener('click', () => {
			const opening = !item.classList.contains('is-open');
			if (desktop.matches) {
				if (opening) openDesktop(item);
				else closeBranch(item);
			} else {
				// Retain the existing mobile behavior until its design is ready.
				item.classList.toggle('is-open', opening);
				toggle.setAttribute('aria-expanded', String(opening));
			}
		});

		item.addEventListener('mouseenter', () => openDesktop(item));
		item.addEventListener('mouseleave', () => {
			if (desktop.matches && !item.contains(document.activeElement)) closeBranch(item);
		});

		item.addEventListener('focusin', (event) => {
			if (desktop.matches && event.target !== toggle) openDesktop(item);
		});
		item.addEventListener('focusout', (event) => {
			if (!desktop.matches) return;
			const focusMovedOutside = !item.contains(event.relatedTarget);
			if (focusMovedOutside && !item.matches(':hover')) closeBranch(item);
		});
	});

	document.addEventListener('click', (event) => {
		if (!header.contains(event.target)) closeMenus();
	});

	document.addEventListener('keydown', (event) => {
		if (event.key !== 'Escape') return;

		const opened = submenuItems.filter((item) => item.classList.contains('is-open'));
		if (desktop.matches && opened.length) {
			event.preventDefault();
			const deepest = opened[opened.length - 1];
			closeBranch(deepest);
			directChild(deepest, '.site-header__submenu-toggle')?.focus();
			return;
		}
		if (header.dataset.menuOpen === 'true' || opened.length) {
			event.preventDefault();
			closeMenus();
			menuButton?.focus();
		}
	});

	desktop.addEventListener('change', closeMenus);
	window.addEventListener('resize', () => {
		if (!desktop.matches) return;
		submenuItems.filter((item) => item.classList.contains('is-open')).forEach(placeSubmenu);
	});
	window.addEventListener('scroll', () => {
		if (pending) return;
		pending = true;
		window.requestAnimationFrame(updateScroll);
	}, { passive: true });

	updateScroll();
})();
