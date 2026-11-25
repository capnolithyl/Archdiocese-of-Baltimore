// A local filter only. No requests, indexing, or assumptions about linked documents.
(() => {
	const normalize = (text) => text.normalize('NFKD').replace(/\p{M}/gu, '').toLowerCase().trim();

	function init(root) {
		const control = root.querySelector('[data-card-search]');
		const input = control?.querySelector('input');
		const grid = root.querySelector('.balt-content-card-grid');
		const empty = root.querySelector('[data-card-empty]');
		const status = root.querySelector('[data-card-status]');
		if (!input || input.disabled || !grid || !empty || !status) return;
		const entries = Array.from(grid.querySelectorAll(':scope > article'), (card) => ({
			card,
			text: normalize(Array.from(card.querySelectorAll('h3, p, time'), (part) => part.textContent).join(' ')),
		}));
		const update = () => {
			const terms = normalize(input.value).split(/\s+/).filter(Boolean);
			let visible = 0;
			for (const { card, text } of entries) {
				const match = terms.every((term) => text.includes(term));
				card.hidden = !match;
				if (match) visible++;
			}
			grid.hidden = visible === 0;
			empty.hidden = visible !== 0;
			status.textContent = terms.length
				? status.dataset.countTemplate.replace('%1$s', String(visible)).replace('%2$s', String(entries.length))
				: '';
		};
		input.addEventListener('input', update);
		control.hidden = false;
		update();
	}

	const start = () => document.querySelectorAll('.balt-card-directory').forEach(init);
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, { once: true });
	else start();
})();
