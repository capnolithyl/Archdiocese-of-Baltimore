import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';

const news = readFileSync('theme/template-parts/components/content-card-grid.php', 'utf8');
const rss = readFileSync('theme/blocks/rss-news/render.php', 'utf8');
const curated = readFileSync('theme/blocks/content-card-grid/render.php', 'utf8');
for (const renderer of [rss,curated]) assert.ok(renderer.includes("'template-parts/components/content-card-grid'"), 'Both blocks must use the shared grid');
const featureGrid = readFileSync('theme/blocks/feature-grid/render.php', 'utf8');

// RSS News cards should share the proven Feature Grid interaction pattern.
for (const effect of ['before:origin-center', 'hover:bg-surface', 'hover:before:scale-x-100']) {
  assert.ok(featureGrid.includes(effect), 'Feature grid reference missing ' + effect);
  assert.ok(news.includes(effect), 'RSS News card missing ' + effect);
}
for (const effect of [
  'group relative flex min-w-0',
  'before:scale-x-0',
  'before:h-[3px]',
  'before:bg-secondary',
  'focus-within:bg-surface',
  'focus-within:before:scale-x-100',
  'group-hover:scale-105',
  'group-focus-within:scale-105',
  'motion-reduce:transition-none',
  'motion-reduce:before:transition-none',
]) {
  assert.ok(news.includes(effect), 'RSS News hover/focus effect missing ' + effect);
}
assert.ok(!news.includes('duration-300 hover:scale-105'), 'Image-only hover would leave keyboard and non-image links without the shared effect');
assert.ok(news.includes('target="_blank" rel="noopener noreferrer"'), 'External RSS links must remain safe');
console.log('RSS News hover, keyboard focus, image zoom and reduced-motion safeguards passed');
