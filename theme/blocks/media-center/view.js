(() => {
	const init = () => {
		document.querySelectorAll('[data-balt-media-center]').forEach((center) => {
			if (center.dataset.mediaReady) return;
			center.dataset.mediaReady = 'true';

			const tabs = [...center.querySelectorAll('[data-balt-media-tab]')];
			const panels = [...center.querySelectorAll('[data-balt-media-panel]')];
			if (!tabs.length || tabs.length !== panels.length) return;

			const activate = (index, focus = false) => {
				tabs.forEach((tab, i) => {
					const active = i === index;
					tab.setAttribute('aria-selected', String(active));
					tab.tabIndex = active ? 0 : -1;
					panels[i].hidden = !active;
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

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
