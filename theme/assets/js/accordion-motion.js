/**
 * Animate native Accordion panels without replacing WordPress's toggle logic.
 * Core owns hidden, aria-expanded, autoclose, anchors and find-in-page.
 */
(() => {
	const duration = 260;
	const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

	function enhance(panel) {
		if (typeof panel.animate !== 'function') return;
		let closed = panel.hasAttribute('hidden');
		let running = null;
		let ownsInert = false;

		const clear = () => {
			panel.classList.remove('balt-accordion-animating');
			if (ownsInert) {
				panel.removeAttribute('inert');
				ownsInert = false;
			}
		};

		const stop = () => {
			if (running) {
				const animation = running;
				running = null;
				animation.cancel();
			}
			clear();
		};

		new MutationObserver(() => {
			const nextClosed = panel.hasAttribute('hidden');
			if (closed === nextClosed) return;
			closed = nextClosed;

			// Capture an interrupted transition's current visual height.
			const current = running ? panel.getBoundingClientRect().height : null;
			stop();
			if (reducedMotion.matches) return;

			if (closed && panel.contains(document.activeElement)) {
				panel.closest('.wp-block-accordion-item')
					?.querySelector(':scope > .wp-block-accordion-heading .wp-block-accordion-heading__toggle')
					?.focus({ preventScroll: true });
			}
			if (closed && !panel.hasAttribute('inert')) {
				panel.setAttribute('inert', '');
				ownsInert = true;
			}

			// Temporarily display the panel while measuring and animating.
			// Never remove or rewrite the hidden attribute.
			panel.classList.add('balt-accordion-animating');
			const height = panel.getBoundingClientRect().height;
			const from = current ?? (closed ? height : 0);
			const to = closed ? 0 : height;
			if (Math.abs(from - to) < 1) {
				clear();
				return;
			}
			const animation = panel.animate(
				[{ height: from + 'px' }, { height: to + 'px' }],
				{ duration, easing: 'cubic-bezier(.22,.61,.36,1)', fill: 'both' }
			);
			running = animation;
			animation.onfinish = () => {
				if (running === animation) stop();
			};
			animation.oncancel = () => {
				if (running === animation) {
					running = null;
					clear();
				}
			};
		}).observe(panel, { attributes: true, attributeFilter: ['hidden'] });

		reducedMotion.addEventListener?.('change', () => {
			if (reducedMotion.matches) stop();
		});
	}

	const init = () => document.querySelectorAll('.balt-accordion-section .wp-block-accordion-panel').forEach(enhance);
	if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init, { once: true });
	else init();
})();
