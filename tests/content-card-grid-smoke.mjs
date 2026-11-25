import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
const read=p=>readFileSync(p,'utf8');
const group=JSON.parse(read('theme/acf-json/group_balt_content_card_grid.json'));
const block=JSON.parse(read('theme/blocks/content-card-grid/block.json'));
assert.equal(block.name,'balt/content-card-grid');
assert.equal(group.location[0][0].value,block.name);
const fields=Object.fromEntries(group.fields.map(f=>[f.name,f]));
assert.deepEqual(Object.keys(fields.source.choices),['manual','selected','latest']);
assert.equal(fields.cards.max,12);
assert.equal(fields.selected_posts.return_format,'id');
assert.equal(fields.cards.sub_fields.find(f=>f.name==='image').return_format,'id');
assert.equal(fields.show_date.default_value,0);
const shared=read('theme/template-parts/components/content-card-grid.php');
for(const s of ['h-[200px]','rounded-card','lg:grid-cols-3','group-hover:scale-105','before:scale-x-100','text-[#6B6B7B]']) assert.ok(shared.includes(s),s);
for(const p of ['theme/style.css','theme/style-editor.css']) {
 const css=read(p);
 for(const token of ['200px','22.75px','5.62vw']) assert.ok(css.includes(token),p+' missing '+token);
}
console.log('Content Card Grid ACF schema and compiled shared styles passed');
