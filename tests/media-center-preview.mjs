import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

const js = readFileSync('theme/blocks/media-center/view.js', 'utf8');

function createCenter(count = 3) {
	const tabs = Array.from({ length: count }, () => {
		const events = {};
		return {
			events,
			attributes: {},
			tabIndex: -1,
			focused: false,
			setAttribute(key, value) { this.attributes[key] = value; },
			addEventListener(name, cb) { (events[name] ??= []).push(cb); },
			focus() { this.focused = true; }
		};
	});
	const panels = Array.from({ length: count }, () => {
		const classes = new Set(['hidden']);
		return {
			hidden: true,
			classList: {
				contains: (name) => classes.has(name),
				toggle(name, enabled) { if (enabled) classes.add(name); else classes.delete(name); }
			}
		};
	});
	const root = {
		dataset: {},
		matches: selector => selector === '[data-balt-media-center]',
		querySelectorAll(selector) {
			if (selector === '[data-balt-media-tab]') return tabs;
			if (selector === '[data-balt-media-panel]') return panels;
			return [];
		}
	};
	return { root, tabs, panels };
}

const first = createCenter();
const actions = {};
const doc = {
	readyState: 'complete',
	querySelectorAll: () => [first.root]
};
runInNewContext(js, {
	document: doc,
	window: { acf: { addAction(name, cb) { actions[name] = cb; } } }
});

assert.equal(typeof actions.render_block_preview, 'function', 'ACF preview hook registered');
assert.deepEqual(first.tabs.map(tab => tab.attributes['aria-selected']), ['true', 'false', 'false']);
assert.deepEqual(first.panels.map(panel => panel.hidden), [false, true, true]);
assert.equal(first.panels[0].classList.contains('hidden'), false);
assert.equal(first.panels[1].classList.contains('hidden'), true);

first.tabs[1].events.click[0]();
assert.deepEqual(first.panels.map(panel => panel.hidden), [true, false, true]);
assert.equal(first.panels[0].classList.contains('hidden'), true);
assert.equal(first.panels[1].classList.contains('hidden'), false);

let prevented = false;
first.tabs[1].events.keydown[0]({ key: 'ArrowRight', preventDefault() { prevented = true; } });
assert.equal(prevented, true);
assert.deepEqual(first.panels.map(panel => panel.hidden), [true, true, false]);
assert.equal(first.tabs[2].focused, true);

const replaced = createCenter(2);
actions.render_block_preview({ jquery: true, 0: replaced.root });
assert.deepEqual(replaced.panels.map(panel => panel.hidden), [false, true], 'New ACF preview initialized');
replaced.tabs[1].events.click[0]();
assert.deepEqual(replaced.panels.map(panel => panel.hidden), [true, false], 'New ACF preview interactive');

actions.render_block_preview({ jquery: true, 0: replaced.root });
assert.equal(replaced.tabs[0].events.click.length, 1, 'Preview is not initialized twice');

console.log('Media Center frontend tabs and ACF preview reinitialization passed');
