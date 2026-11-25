import assert from 'node:assert/strict';
import {readFileSync,statSync} from 'node:fs';
const read=p=>readFileSync(p,'utf8');
const b=JSON.parse(read('theme/blocks/contact-form/block.json'));
const g=JSON.parse(read('theme/acf-json/group_balt_contact_form.json'));
assert.equal(b.name,'balt/contact-form');
assert.equal(b.supports.inserter,false,'Legacy block must not be offered for new inserts');
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

const pattern=read('theme/patterns/contact-form.php');
for(const token of ['balt-contact-pattern','balt-contact-form__columns','balt-contact-form__intro','balt-contact-form__card','<!-- wp:gravityforms/form {"title":false,"description":false} /-->'])assert.ok(pattern.includes(token),token);
assert.ok(!pattern.includes('formId')&&!pattern.includes('"lock"'),'No selected form or locked content');
for(const p of ['theme/style.css','theme/style-editor.css']){
 const css=read(p);
 for(const token of ['balt-contact-pattern','balt-contact-form__columns','balt-contact-detail-phone','balt-contact-detail-hours'])assert.ok(css.includes(token),p+' missing '+token);
}
console.log('Contact pattern and legacy block compatibility passed');

const source = read('tailwind/custom/components/contact-form.css');
for (const token of ['--gf-ctrl-textarea-height: 102px', 'min-block-size: 102px !important', 'block-size: 102px;', '--gf-form-gap-y: 16px', '--gf-ctrl-label-font-size-primary: 12px', 'color: #e4c76c !important', '.ginput_counter', '.gform_heading :is(.gform_title, .gform_description)']) assert.ok(source.includes(token), 'Missing scoped GF styling: '+token);
console.log('Contact form compact spacing, logical textarea sizing and accessible labels passed');
