import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';

const read = p => readFileSync(p, 'utf8');
const pattern = read('theme/patterns/media-text-section.php');
const styles = read('theme/inc/editorial-block-styles.php');
const css = read('tailwind/custom/components/media-text.css');

assert.match(pattern, /Slug: baltimore\/media-text-section/);
assert.match(pattern, /<!-- wp:media-text {"align":"wide","isStackedOnMobile":true,"verticalAlignment":"center"} -->/);
assert.match(pattern, /<figure class="wp-block-media-text__media"><\/figure>/, 'Image must be editor-selected, not a fixed demo URL');
for (const block of ['paragraph', 'heading', 'buttons', 'button']) {
	assert.ok(pattern.includes('<!-- wp:' + block), 'Use native Gutenberg ' + block);
}
assert.equal((pattern.match(/<!-- wp:button(?: | -->)/g) || []).length, 2);
assert.ok(!pattern.includes('href='), 'Do not publish fake button destinations');
for (const style of ['balt-eyebrow', 'balt-lead']) {
	assert.ok(styles.includes("'" + style + "'"));
	assert.ok(pattern.includes('is-style-' + style));
	assert.ok(css.includes('p.is-style-' + style));
}
for (const token of ['aspect-ratio: 4 / 3', '5rem', '6rem', '3rem', 'min-height: 48px', 'has-media-on-the-right', 'is-stacked-on-mobile', 'prefers-reduced-motion', 'is-style-outline', 'is-image-fill-element']) {
	assert.ok(css.includes(token), 'Missing design or native-control safeguard: ' + token);
}
for (const path of ['theme/style.css','theme/style-editor.css']) {
	const built = read(path);
	for (const token of ['.wp-block-media-text', '.is-style-balt-eyebrow', '.is-style-balt-lead', '--balt-media-gap']) {
		assert.ok(built.includes(token), 'Missing compiled style ' + token + ' in ' + path);
	}
}
console.log('Native Media & Text pattern, editorial styles, responsive controls and both CSS bundles passed');
