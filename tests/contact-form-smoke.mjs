import assert from 'node:assert/strict';
import {readFileSync,statSync} from 'node:fs';
const read=p=>readFileSync(p,'utf8');
const b=JSON.parse(read('theme/blocks/contact-form/block.json'));
const g=JSON.parse(read('theme/acf-json/group_balt_contact_form.json'));
assert.equal(b.name,'balt/contact-form');
assert.equal(g.location[0][0].value,b.name);
const f=g.fields.find(x=>x.name==='form');
assert.equal(f.type,'forms');
assert.equal(f.return_format,'id');
assert.equal(f.multiple,0);
assert.equal(g.fields.find(x=>x.name==='ajax').default_value,1);
assert.ok(read('theme/inc/contact-form.php').includes('gravity_form_enqueue_scripts( $id, $ajax )'));
assert.ok(read('tailwind.css').includes('@source "./theme/blocks/contact-form"'));
for(const icon of ['phone','email','location'])assert.ok(statSync('theme/assets/footer/'+icon+'.svg').size>100);
for(const p of ['theme/style.css','theme/style-editor.css']){
 const css=read(p);
 for(const token of ['balt-contact-form','balt-contact-icon--phone','gform_wrapper','102px','42px','c9a840'])assert.ok(css.toLowerCase().includes(token),p+' missing '+token);
}
console.log('Contact form schema, GF integration and compiled scoped styles passed');
