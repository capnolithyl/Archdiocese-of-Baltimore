import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
const read=p=>readFileSync(p,'utf8');
const fields=JSON.parse(read('theme/acf-json/group_balt_interior_page.json'));
assert.equal(fields.fields.find(f=>f.name==='balt_section_nav_links')?.max,8);
assert.equal(fields.location.length,2);
for(const [name,kind] of [['interior-featured','featured'],['interior-title','title']]){
 const src=read('theme/page-templates/'+name+'.php');
 assert.ok(src.includes('Template Name: Interior'));
 assert.ok(src.includes("'hero' => '"+kind+"'"));
}
const common=read('theme/template-parts/layout/interior-content.php');
assert.ok(common.includes('the_content()'));
assert.ok(common.includes('section-navigation')&&common.includes('interior-hero'));
const css=read('tailwind/custom/components/interior-page.css');
for(const item of ['min-height: 340px','rgb(15 26 46 / 65%)','min-height: 49px','overflow-x: auto','scroll-margin-top','letter-spacing: 2px'])assert.ok(css.includes(item),'Missing design treatment '+item);
console.log('Interior Figma geometry, page templates and ACF configuration passed');
