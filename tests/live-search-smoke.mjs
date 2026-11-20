import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { Script } from 'node:vm';

const js = readFileSync('theme/js/live-search.js', 'utf8');
const php = readFileSync('theme/inc/live-search.php', 'utf8');
const template = readFileSync('theme/template-parts/layout/live-search.php', 'utf8');
const header = readFileSync('theme/template-parts/layout/header-content.php', 'utf8');
const pageHeader = readFileSync('theme/header.php', 'utf8');

new Script(js);
assert.match(template, /<dialog[\s\S]*id="balt-live-search-dialog"/);
assert.match(template, /role="combobox"/);
assert.match(template, /role="listbox"/);
assert.match(template, /name="s"/);
assert.match(template, /data-balt-search-topic=/);
assert.match(php, /register_rest_route\(/);
assert.match(php, /'post_status'\s*=>\s*'publish'/);
assert.match(php, /'has_password'\s*=>\s*false/);
assert.match(php, /'posts_per_page'\s*=>\s*8/);
assert.match(js, /new AbortController\(\)/);
assert.match(js, /dialog\.showModal\(\)/);
assert.match(js, /aria-activedescendant/);
assert.match(header, /aria-controls="balt-live-search-dialog"/);
assert.doesNotMatch(header, /id="balt-header-search"/);
assert.ok(pageHeader.indexOf('layout/live-search') < pageHeader.indexOf('<div id="page">'), 'Search dialog must precede #page so transparent header remains adjacent to #content');
console.log('Native search dialog, accessible listbox and public search endpoint smoke checks passed');
