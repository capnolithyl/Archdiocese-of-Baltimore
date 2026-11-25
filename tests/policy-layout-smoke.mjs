import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
const read=p=>readFileSync(p,'utf8');
const category=read('theme/taxonomy-balt_policy_category.php');
const single=read('theme/single-balt_policy.php');
assert.ok(category.includes('balt_policy_groups')&&category.includes('balt-policy-star'));
assert.ok(single.includes('the_content()')&&single.includes('aria-current="page"')&&single.includes('balt_policy_resources')&&single.includes("'heading_tag' => 'p'"));
assert.equal(JSON.parse(read('theme/acf-json/group_balt_policy_category.json')).location[0][0].value,'balt_policy_category');
assert.equal(JSON.parse(read('theme/acf-json/group_balt_policy_details.json')).fields[0].max,0);
for(const p of ['theme/style.css','theme/style-editor.css']){
 const css=read(p);
 for(const token of ['balt-policy-groups','balt-policy-detail','balt-policy-resources','balt-policy-sidebar','300px','48px'])assert.ok(css.includes(token),p+' missing '+token);
}
console.log('Policy category and single layout schema and compiled CSS passed');

const {runInNewContext}=await import('node:vm');
const make=(text)=>({textContent:text,hidden:false,closest(){return this.group??null;}});
const parent=make('★ 100 Governance'), child=make('101 Budgeting'), other=make('200 Ethics');
const group={hidden:false,querySelector:()=>parent,querySelectorAll:()=>[child]};
const group2={hidden:false,querySelector:()=>other,querySelectorAll:()=>[]};
parent.group=child.group=group;other.group=group2;
const input={value:'',addEventListener(_,cb){this.update=cb;}};
const control={hidden:true,querySelector:()=>input},empty={hidden:true},status={dataset:{countTemplate:'%1$s of %2$s policies shown.'}};
const root={querySelector:s=>({'[data-policy-search]':control,'[data-policy-empty]':empty,'[data-policy-status]':status})[s],querySelectorAll:s=>s==='[data-policy-item]'?[parent,child,other]:[group,group2]};
runInNewContext(read('theme/assets/js/policy-filter.js'),{document:{readyState:'complete',querySelectorAll:()=>[root]}});
assert.equal(control.hidden,false);
input.value='budgeting';input.update();assert.equal(group.hidden,false);assert.equal(parent.hidden,false);assert.equal(child.hidden,false);assert.equal(group2.hidden,true);assert.equal(status.textContent,'2 of 3 policies shown.');
input.value='governance';input.update();assert.equal(child.hidden,false);
input.value='none';input.update();assert.equal(empty.hidden,false);
input.value='';input.update();assert.equal(empty.hidden,true);assert.ok(!group.hidden&&!group2.hidden);
console.log('Policy filtering keeps matching group context and accurate counts');
