import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

const code = readFileSync('theme/js/header-navigation.js', 'utf8');
const listeners = {};
const cls = (starting = []) => {
	const values = new Set(starting);
	return {
		contains: name => values.has(name),
		add: name => values.add(name),
		remove: name => values.delete(name),
		toggle(name, force) { if (force) values.add(name); else values.delete(name); }
	};
};
const element = (classes = []) => ({
	classList: cls(classes), attributes: {}, events: {}, focused: false,
	setAttribute(name, value) { this.attributes[name] = value; },
	addEventListener(name, fn) { this.events[name] = fn; },
	focus() { this.focused = true; },
	querySelector() { return null; }
});
const nav = element(['hidden']);
const menuButton = element();
const searchButton = element();
const keyMap = {
	'.site-header__mobile-toggle': menuButton,
	'.site-header__nav': nav,
	'.site-header__search-toggle': searchButton
};
const header = {
	dataset: { compact: 'false', menuOpen: 'false' },
	querySelector: key => keyMap[key] || null,
	querySelectorAll: () => [],
	contains: target => [menuButton, nav, searchButton].includes(target)
};
const mql = { matches: false, addEventListener(name, callback) { this.onChange = callback; } };
const win = {
	scrollY: 0,
	matchMedia: query => {
		assert.equal(query, '(min-width: 1280px)');
		return mql;
	},
	requestAnimationFrame: callback => callback(),
	addEventListener(name, callback) { listeners[name] = callback; }
};
const doc = {
	getElementById: id => id === 'masthead' ? header : null,
	addEventListener(name, callback) { listeners['document-' + name] = callback; }
};

runInNewContext(code, { window: win, document: doc });

assert.equal(header.dataset.compact, 'false');
menuButton.events.click();
assert.equal(header.dataset.menuOpen, 'true');
assert.equal(menuButton.attributes['aria-expanded'], 'true');
assert.equal(nav.classList.contains('hidden'), false);

searchButton.events.click();
assert.equal(nav.classList.contains('hidden'), true, 'Search closes mobile navigation');
assert.equal(header.dataset.menuOpen, 'false', 'Search trigger closes mobile menu');

win.scrollY = 100;
listeners.scroll();
assert.equal(header.dataset.compact, 'true', 'Scroll should compact navbar');
win.scrollY = 0;
listeners.scroll();
assert.equal(header.dataset.compact, 'false', 'At top navbar should return to original size');

let prevented = false;
listeners['document-keydown']({ key: 'Escape', preventDefault() { prevented = true; } });
assert.equal(header.dataset.menuOpen, 'false', 'Escape leaves menu closed');

menuButton.events.click();
mql.matches = true;
mql.onChange();
assert.equal(nav.classList.contains('hidden'), true, 'Resizing to desktop resets mobile state');
assert.equal(header.dataset.menuOpen, 'false');

console.log('Header scroll transition, responsive menu, search-trigger and keyboard behavior passed');
