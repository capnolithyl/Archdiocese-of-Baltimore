import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
const read=p=>readFileSync(p,'utf8');
const b=JSON.parse(read('theme/blocks/staff-grid/block.json'));
const g=JSON.parse(read('theme/acf-json/group_balt_staff_grid.json'));
assert.equal(b.name,'balt/staff-grid');
assert.equal(g.location[0][0].value,b.name);
const m=g.fields.find(f=>f.name==='members');
assert.equal(m.min,1);
assert.equal(m.max,0);
assert.equal(m.sub_fields.find(f=>f.name==='photo').return_format,'id');
assert.equal(m.sub_fields.find(f=>f.name==='name').required,1);
assert.equal(g.fields.find(f=>f.name==='team_link').type,'link');
assert.ok(read('theme/inc/staff-grid.php').includes(b.acf.renderCallback));
assert.ok(read('tailwind.css').includes('@source "./theme/blocks/staff-grid"'));
for(const p of ['theme/style.css','theme/style-editor.css']){
 const css=read(p);
 for(const token of ['22.75px','9rem','size-56','80px','C2D0E1'.toLowerCase()])assert.ok(css.toLowerCase().includes(token),p+' missing '+token);
}
console.log('Staff Grid schema, registration and compiled styles passed');
