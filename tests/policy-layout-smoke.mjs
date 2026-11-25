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
