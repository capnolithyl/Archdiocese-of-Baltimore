import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
const read=p=>readFileSync(p,'utf8');
const b=JSON.parse(read('theme/blocks/resource-directory/block.json'));
const g=JSON.parse(read('theme/acf-json/group_balt_resource_directory.json'));
assert.equal(b.name,'balt/resource-directory');
assert.equal(g.location[0][0].value,b.name);
const m=g.fields.find(f=>f.name==='mode');
assert.deepEqual(Object.keys(m.choices),['landing','category']);
const sections=g.fields.find(f=>f.name==='sections');
assert.equal(sections.max,0);
const links=sections.sub_fields.find(f=>f.name==='links');
assert.equal(links.max,0);
assert.equal(links.sub_fields.find(f=>f.name==='file').return_format,'id');
for(const p of ['theme/style.css','theme/style-editor.css']){
 const css=read(p);
 for(const token of ['balt-resource-directory','balt-resource-directory__jump','balt-resource-directory__category','240px','600px'])assert.ok(css.includes(token),p+' missing '+token);
}
assert.ok(read('theme/assets/js/resource-directory.js').includes('menu.open = false'));
console.log('Resource directory schema and compiled CSS passed');
