// Search the complete visible category index or sidebar, without changing URLs.
(() => {
	const normalize = (s) => s.normalize('NFKD').replace(/\p{M}/gu, '').toLowerCase().trim();
	function init(root) {
		const control = root.querySelector('[data-policy-search]');
		const input = control?.querySelector('input');
		const empty = root.querySelector('[data-policy-empty]');
		const status = root.querySelector('[data-policy-status]');
		if (!input || !empty || !status) return;
		const items = Array.from(root.querySelectorAll('[data-policy-item]'), item => ({ item, text: normalize(item.textContent) }));
		const groups = Array.from(root.querySelectorAll('[data-policy-group]'));
		function update() {
			const terms = normalize(input.value).split(/\s+/).filter(Boolean);
			
			for (const {item, text} of items) {
				const match = terms.every(term => text.includes(term));
				item.hidden = !match;
			}
			for (const group of groups) {
				const parent = group.querySelector('h2 [data-policy-item]');
				const children = Array.from(group.querySelectorAll('li[data-policy-item]'));
				// Keep group context for matching children, or show its children when its heading matches.
				const parentMatch = parent && !parent.hidden;
				if (parentMatch) for (const child of children) child.hidden = false;
				const visible = parentMatch || children.some(child => !child.hidden);
				group.hidden = !visible;
				if (parent && visible) parent.hidden = false;
			}
			const count = items.filter(({item}) => !item.hidden && !item.closest('[data-policy-group]')?.hidden).length;
			empty.hidden = count > 0;
			status.textContent = terms.length ? status.dataset.countTemplate.replace('%1$s', String(count)).replace('%2$s', String(items.length)) : '';
		}
		input.addEventListener('input', update);
		control.hidden = false;
		update();
	}
	const start = () => document.querySelectorAll('[data-policy-filter]').forEach(init);
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', start, {once:true});
	else start();
})();
