(() => {
	const selector = '[data-balt-media-center]';

	const initialize = (root = document) => {
		// ACF passes a jQuery wrapper; the same initializer also works on the frontend.
		const element = root?.jquery ? root[0] : root?.[0] ?? root;
		if (!element) return;

		const centers = [];
		if (element.matches?.(selector)) centers.push(element);
		centers.push(...(element.querySelectorAll?.(selector) ?? []));

		centers.forEach((center) => {
			if (center.dataset.mediaReady) return;

			const tabs = [...center.querySelectorAll('[data-balt-media-tab]')];
			const panels = [...center.querySelectorAll('[data-balt-media-panel]')];
			if (!tabs.length || tabs.length !== panels.length) return;
			center.dataset.mediaReady = 'true';

			const activate = (index, focus = false) => {
				tabs.forEach((tab, i) => {
					const active = i === index;
					tab.setAttribute('aria-selected', String(active));
					tab.tabIndex = active ? 0 : -1;
					panels[i].hidden = !active;
					panels[i].classList.toggle('hidden', !active);
				});
				if (focus) tabs[index].focus();
			};

			tabs.forEach((tab, index) => {
				tab.addEventListener('click', () => activate(index));
				tab.addEventListener('keydown', (event) => {
					let next = index;
					if (event.key === 'ArrowRight') next = (index + 1) % tabs.length;
					else if (event.key === 'ArrowLeft') next = (index - 1 + tabs.length) % tabs.length;
					else if (event.key === 'Home') next = 0;
					else if (event.key === 'End') next = tabs.length - 1;
					else return;
					event.preventDefault();
					activate(next, true);
				});
			});
			activate(0);
		});
	};

	const ready = () => {
		initialize();

		// Gutenberg replaces ACF previews when fields change, so reinitialize each render.
		window.acf?.addAction?.('render_block_preview', ($block) => initialize($block));
	};

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', ready);
	} else {
		ready();
	}
})();
