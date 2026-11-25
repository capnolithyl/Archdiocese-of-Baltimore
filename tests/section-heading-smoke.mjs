import assert from 'node:assert/strict';
import {readFileSync} from 'node:fs';
const read=p=>readFileSync(p,'utf8');
const blocks=[
 ['content-card-grid','balt_content_card_grid'],
 ['rss-news','balt_rss_news'],
 ['feature-grid','balt_feature_grid'],
 ['staff-grid','balt_staff_grid'],
 ['media-center','balt_media_center'],
 ['image-cta-grid','balt_image_cta_grid'],
 ['stats','dpi_stats'],
];
for(const [name,group] of blocks){
 const f=JSON.parse(read('theme/acf-json/group_'+group+'.json')).fields.find(f=>f.name==='heading_alignment');
 assert.equal(f?.type,'select',name);
 assert.equal(f.default_value,'default',name+' must preserve existing default');
 assert.deepEqual(Object.keys(f.choices),['default','left','center']);
 const render=read('theme/'+(name==='stats'?'dpi-blocks/blocks/':'blocks/')+name+'/render.php');
 assert.ok(render.includes("get_field( 'heading_alignment' )"),name);
 assert.ok(render.includes('text-left')&&render.includes('text-center'),name);
}
const grid=read('theme/blocks/content-card-grid/render.php');
assert.ok(grid.includes('items-end justify-between')&&grid.includes('! $header_link')&&grid.includes("'compact_spacing' => $left_heading"));
const shared=read('theme/template-parts/components/content-card-grid.php');
assert.ok(shared.includes("'mt-10' : 'mt-14'"));
const sectionLink=read('theme/template-parts/components/section-heading-link.php');
assert.ok(sectionLink.includes('target="_blank" rel="noopener noreferrer"')&&sectionLink.includes("'a' : 'span'"));
console.log('Shared heading alignment controls and default compatibility passed');
