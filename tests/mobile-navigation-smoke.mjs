import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { runInNewContext, Script } from 'node:vm';

const js = readFileSync('theme/js/mobile-navigation.js', 'utf8');
const css = readFileSync('tailwind/custom/components/mobile-navigation.css', 'utf8');
const template = readFileSync('theme/template-parts/layout/mobile-navigation.php', 'utf8');
const pageHeader = readFileSync('theme/header.php', 'utf8');
const acf = JSON.parse(readFileSync('theme/acf-json/group_balt_header_settings.json', 'utf8'));

new Script(js);
assert.ok(pageHeader.indexOf('layout/mobile-navigation') < pageHeader.indexOf('<div id="page">'));
for (const field of ['balt_mobile_help_link', 'balt_mobile_donate_link']) {
	assert.ok(acf.fields.some((item) => item.name === field), field + ' must be editable');
}
for (const item of ['<dialog', 'data-balt-mobile-back', 'data-balt-mobile-search', 'balt-mobile-primary-menu', 'balt-mobile-utility-menu']) {
	assert.ok(template.includes(item), 'Missing mobile template element ' + item);
}
for (const rule of ['width: min(400px, 100%)', 'balt-mobile-drawer-enter', 'data-view="detail"', 'prefers-reduced-motion']) {
	assert.ok(css.includes(rule), 'Missing responsive style ' + rule);
}

const classList = () => {
	const values = new Set();
	return {
		add: name => values.add(name), remove: name => values.delete(name),
		contains: name => values.has(name),
		toggle: (name, on) => on ? values.add(name) : values.delete(name)
	};
};
const element = (overrides = {}) => ({
	dataset: {}, events: {}, attributes: {}, classList: classList(),
	inert: false, isConnected: true, focused: false,
	addEventListener(name, callback) { this.events[name] = callback; },
	setAttribute(name, value) { this.attributes[name] = value; },
	removeAttribute(name) { delete this.attributes[name]; },
	getAttribute(name) { return this.attributes[name] || null; },
	focus() { this.focused = true; },
	querySelector() { return null; }, querySelectorAll() { return []; },
	replaceChildren(...children) { this.children = children; },
	appendChild(child) { (this.children ||= []).push(child); },
	...overrides
});

const trigger = element();
const headerSearch = element({ click() { this.clicked = true; } });
const drawer = element({
	open: false,
	showModal() { this.open = true; },
	close() { this.open = false; this.events.close?.(); }
});
const closeButton = element();
const backButton = element();
const root = element();
const detail = element({inert: true});
const detailItems = element();
const title = element();
const parentLink = element({textContent:'About',attributes:{href:'/about/'}});
const parentToggle = element();
const sourceSubmenu = element({cloneNode: () => element()});
const aboutItem = element({querySelector(selector) {
	return ({
		':scope > a': parentLink,
		':scope > .site-header__submenu-toggle': parentToggle,
		':scope > .sub-menu': sourceSubmenu
	})[selector] || null;
}});
const primary = element({querySelectorAll:()=>[aboutItem]});
const utilityToggle = element();
const utilitySubmenu = element();
const utilityItem = element({querySelector(selector) {
	return ({
		':scope > .site-header__utility-toggle': utilityToggle,
		':scope > .sub-menu': utilitySubmenu
	})[selector] || null;
}});
const utility = element({querySelectorAll:()=>[utilityItem]});
const searchButton = element({dataset:{baltMobileSearchQuery:'baptism'}});
const lookup = {
	'[data-balt-mobile-close]': closeButton,
	'[data-balt-mobile-back]': backButton,
	'[data-balt-mobile-root]': root,
	'[data-balt-mobile-detail]': detail,
	'[data-balt-mobile-title]': title,
	'[data-balt-mobile-detail-items]': detailItems,
	'.balt-mobile-primary-menu': primary,
	'.balt-mobile-utility-menu': utility
};
drawer.querySelector = selector => lookup[selector] || null;
drawer.querySelectorAll = selector => selector === '[data-balt-mobile-search]' ? [searchButton] : [];

const input = element({dispatchEvent() { this.dispatched = true; }});
const media = {matches:false,addEventListener(_,callback) {this.changed = callback;}};
const document = {
	documentElement:{classList:classList()},
	getElementById:id=>id==='balt-mobile-navigation'?drawer:id==='balt-live-search-input'?input:null,
	querySelector:selector=>selector.includes('mobile-toggle')?trigger:selector.includes('search-toggle')?headerSearch:null,
	createDocumentFragment:()=>element(),
	createElement:()=>element()
};
runInNewContext(js, {document,window:{matchMedia:()=>media},Event:class{}});

trigger.events.click();
assert.equal(drawer.open, true, 'Hamburger must open native modal');
assert.equal(trigger.attributes['aria-expanded'], 'true');
assert.equal(document.documentElement.classList.contains('balt-mobile-menu-open'), true);

let prevented = false;
parentLink.events.click({button:0,preventDefault() {prevented = true;}});
assert.equal(prevented, true);
assert.equal(drawer.dataset.view, 'detail', 'Main submenu should shift screens');
assert.equal(root.inert, true);
assert.equal(detail.inert, false);
assert.equal(title.textContent, 'About');
assert.equal(backButton.focused, true);
assert.ok(detailItems.children.length);

backButton.events.click();
assert.equal(drawer.dataset.view, 'root');
assert.equal(root.inert, false);
assert.equal(detail.inert, true);
assert.equal(parentLink.focused, true);

utilityToggle.events.click();
assert.equal(utilityItem.classList.contains('is-open'), true);
assert.equal(utilityToggle.attributes['aria-expanded'], 'true');
utilityToggle.events.click();
assert.equal(utilityItem.classList.contains('is-open'), false);

searchButton.events.click();
assert.equal(drawer.open, false);
assert.equal(headerSearch.clicked, true, 'Bottom search must reuse existing search overlay');
assert.equal(input.value, 'baptism');
assert.equal(input.dispatched, true);
assert.equal(trigger.attributes['aria-expanded'], 'false');

trigger.events.click();
media.matches = true;
media.changed();
assert.equal(drawer.open, false, 'Drawer must close on desktop breakpoint');
console.log('Mobile dialog, drill-down, Back, utility accordion and search handoff passed');
