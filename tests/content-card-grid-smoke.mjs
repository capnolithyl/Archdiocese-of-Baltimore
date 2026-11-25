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

assert.equal(fields.style.default_value,'default');
assert.equal(fields.style.choices.directory,'Directory');
assert.equal(fields.enable_card_search.type,'true_false');
assert.ok(fields.enable_card_search.instructions.includes('Does not search linked pages'));
for(const p of ['theme/style.css','theme/style-editor.css']){
 const css=read(p);
 for(const token of ['balt-card-directory__search','balt-card-directory__intro','1100px','382px','320px'])assert.ok(css.includes(token),p+' missing '+token);
}
const {runInNewContext}=await import('node:vm');
const cards=['Facilities & Real Estate','Fiscal Services','Human Resources'].map(t=>({hidden:false,querySelectorAll:()=>[{textContent:t}]}));
const input={value:'',disabled:false,addEventListener(name,cb){this.change=cb;}};
const control={hidden:true,querySelector:()=>input};
const grid={hidden:false,querySelectorAll:()=>cards};
const empty={hidden:true},status={dataset:{countTemplate:'%1$s of %2$s cards shown.'},textContent:''};
const root={querySelector:s=>({'[data-card-search]':control,'.balt-content-card-grid':grid,'[data-card-empty]':empty,'[data-card-status]':status})[s]};
runInNewContext(read('theme/assets/js/card-directory-filter.js'),{document:{readyState:'complete',querySelectorAll:()=>[root]}});
assert.equal(control.hidden,false);
input.value='real FACILITIES';input.change();assert.deepEqual(cards.map(c=>c.hidden),[false,true,true]);assert.equal(status.textContent,'1 of 3 cards shown.');
input.value='not found';input.change();assert.equal(grid.hidden,true);assert.equal(empty.hidden,false);
input.value='';input.change();assert.equal(grid.hidden,false);assert.equal(empty.hidden,true);assert.ok(cards.every(c=>!c.hidden));
console.log('Directory CSS and local search behavior passed');
