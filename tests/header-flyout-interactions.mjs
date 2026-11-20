import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext } from 'node:vm';

const code = readFileSync('theme/js/header-navigation.js', 'utf8');
const css = readFileSync('tailwind/custom/components/header.css', 'utf8');
const listeners = {};
const classList = (initial = []) => {
	const classes = new Set(initial);
	return {
		contains: (name) => classes.has(name),
		add: (name) => classes.add(name),
		remove: (name) => classes.delete(name),
		toggle: (name, force) => force ? classes.add(name) : classes.delete(name)
	};
};
const node = (initial = []) => ({
	classList: classList(initial),
	dataset: {},
	style: {
		properties: {},
		removeProperty(name) { delete this.properties[name]; },
		setProperty(name, value) { this.properties[name] = value; }
	},
	attributes: {},
	events: {},
	focused: false,
	addEventListener(name, callback) { this.events[name] = callback; },
	setAttribute(name, value) { this.attributes[name] = value; },
	focus() { this.focused = true; },
	contains(target) { return target === this; },
	matches: () => false,
	querySelector: () => null,
	querySelectorAll: () => []
});
const menu = node(['hidden']);
const menuButton = node();
const searchButton = node();

const menuItem = (id, left, right, isNested = false) => {
	const item = node(['menu-item-has-children']);
	item.id = id;
	item.left = left;
	item.right = right;
	item.toggle = node();
	item.submenu = node(['sub-menu']);
	item.submenu.getBoundingClientRect = () => {
		const width = 230;
		const direction = item.submenu.dataset.flyoutDirection;
		const x = isNested
			? direction === 'left' ? left - width : right
			: direction === 'left' ? right - width : left;
		return { left: x, right: x + width, width };
	};
	item.querySelector = (selector) => {
		if (selector === ':scope > .site-header__submenu-toggle') return item.toggle;
		if (selector === ':scope > .sub-menu') return item.submenu;
		return null;
	};
	item.querySelectorAll = () => (item.descendant?.classList.contains('is-open') ? [item.descendant] : []);
	item.contains = (target) => target === item || target === item.toggle ||
		target === item.submenu || item.descendant?.contains(target) === true;
	return item;
};

const ministries = menuItem('ministries', 900, 1000);
const nested = menuItem('family', 1060, 1130, true);
const give = menuItem('give', 1140, 1240);
ministries.parentElement = { children: [ministries, give] };
give.parentElement = ministries.parentElement;
nested.parentElement = { children: [nested] };

const items = [ministries, give];
const header = {
	dataset: { compact: 'false', menuOpen: 'false' },
	querySelector(selector) {
		return ({
			'.site-header__mobile-toggle': menuButton,
			'.site-header__nav': menu,
			'.site-header__search-toggle': searchButton
		})[selector] || null;
	},
	querySelectorAll: () => items,
	contains: () => true
};
const match = { matches: true, addEventListener(_, callback) { this.onChange = callback; } };
const windowStub = {
	scrollY: 0,
	innerWidth: 1280,
	matchMedia: () => match,
	addEventListener(type, callback) { listeners[type] = callback; },
	requestAnimationFrame(callback) { callback(); }
};
const documentStub = {
	activeElement: null,
	documentElement: { clientWidth: 1280 },
	getElementById: () => header,
	addEventListener(type, callback) { listeners['document-' + type] = callback; }
};
runInNewContext(code, { window: windowStub, document: documentStub });

// The only supported submenu is the first dropdown beneath a top-level item.
ministries.events.mouseenter();
assert.equal(ministries.classList.contains('is-open'), true);
assert.equal(ministries.toggle.attributes['aria-expanded'], 'true');
assert.equal(ministries.submenu.dataset.flyoutDirection, 'right');

// Rightmost top-level navigation items still avoid viewport overflow.
give.events.mouseenter();
assert.equal(ministries.classList.contains('is-open'), false);
assert.equal(give.submenu.dataset.flyoutDirection, 'left');

let prevented = false;
listeners['document-keydown']({ key: 'Escape', preventDefault() { prevented = true; } });
assert.equal(prevented, true);
assert.equal(give.classList.contains('is-open'), false);
assert.equal(give.toggle.focused, true);

// Mobile toggle behavior remains unchanged until the mobile design arrives.
match.matches = false;
match.onChange();
ministries.toggle.events.click();
assert.equal(ministries.classList.contains('is-open'), true);
ministries.toggle.events.click();
assert.equal(ministries.classList.contains('is-open'), false);

assert.match(css, /@media \(min-width: 80rem\)/);
assert.match(css, /\.sub-menu\[data-flyout-direction="left"\]/);
assert.doesNotMatch(css, /\.sub-menu \.sub-menu \{/);
assert.match(css, /background: #f2f0ed/);
assert.match(css, /color: #e43d36/);
console.log('Single-level dropdown, viewport alignment and mobile regression tests passed');
