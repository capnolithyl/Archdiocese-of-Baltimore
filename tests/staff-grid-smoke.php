<?php
declare(strict_types=1);
define('ABSPATH',__DIR__);
function get_field($k){return $GLOBALS['staff_fixture'][$k]??null;}
function esc_html($s):string{return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function esc_attr($s):string{return esc_html($s);}
function esc_url($s):string{return preg_match('~^(?:https?://|/|#)~',(string)$s)?esc_html($s):'';}
function __($s,$d):string{return $s;}
function esc_html_e($s,$d):void{echo esc_html($s);}
function absint($v):int{return abs((int)$v);}
function wp_unique_id($s):string{return $s.'test';}
function get_block_wrapper_attributes($a):string{return 'class="'.esc_attr($a['class']).'"';}
function wp_get_attachment_image($id,$size,$icon,$a):string{return $id===42?'<img src="/portrait.jpg" alt="" class="'.esc_attr($a['class']).'">':'';}
function check($ok,$msg):void{if(!$ok)throw new RuntimeException($msg);}
function add_action(...$args):void{}
function add_filter(...$args):void{}
function register_post_type($name,$args):void{$GLOBALS['registered_staff']=[$name,$args];}
function register_taxonomy($name,$type,$args):void{$GLOBALS['registered_department']=[$name,$type,$args];}
function get_post($id){return $GLOBALS['staff_posts'][$id]??null;}
function get_the_title($post):string{return $post->post_title;}
function get_post_meta($id,$key,$single):string{return $GLOBALS['staff_roles'][$id]??'';}
function get_post_thumbnail_id($id):int{return $id===10?42:0;}
require dirname(__DIR__).'/theme/inc/staff-post-type.php';
function render_staff(array $fixture,bool $is_preview=false):string{
 $GLOBALS['staff_fixture']=$fixture;
 ob_start(); require dirname(__DIR__).'/theme/blocks/staff-grid/render.php'; return (string)ob_get_clean();
}
$members=[['name'=>'First & Last','role'=>'Director','photo'=>42],['name'=>'Other Person','role'=>'','photo'=>99],['name'=>'']];
$f=['eyebrow'=>'Meet the Team','heading'=>'Our Team','members'=>$members,'team_link'=>['url'=>'/team/','title'=>'Meet Everyone','target'=>'_blank']];
$html=render_staff($f);
check(substr_count($html,'<article ')===2,'Skip empty members');
foreach(['First &amp; Last','Director','Other Person','/portrait.jpg','border-[#F5F3EE]','desktop:size-56','Meet Everyone','href="/team/"','target="_blank" rel="noopener noreferrer"','fa-solid fa-users','aria-labelledby="balt-staff-grid-test"'] as $t)check(str_contains($html,$t),'Missing '.$t);
check(str_contains($html,'>O</span>'),'Missing photo fallback');
check(strpos($html,'First &amp; Last')<strpos($html,'Other Person'),'Editor order must be preserved');
check(!str_contains(render_staff($f,true),'<a '),'Preview link must be inert');
check(!str_contains(render_staff(['members'=>$members,'team_link'=>['url'=>'javascript:alert(1)']]),'fa-solid fa-users'),'Invalid CTA must be hidden');
check(!str_contains(render_staff(['members'=>$members]),'Meet the Whole Team'),'No CTA when omitted');
check(render_staff(['members'=>[]])==='','Empty public block should be hidden');
check(str_contains(render_staff(['members'=>[]],true),'Add at least one staff member'),'Empty preview');
check(substr_count(render_staff(['members'=>array_fill(0,25,$members[0])]),'<article ')===25,'No arbitrary repeater cap');

balt_register_staff_post_type();
check($GLOBALS['registered_staff'][0]==='balt_staff','Staff post type not registered');
check($GLOBALS['registered_staff'][1]['has_archive']===true && $GLOBALS['registered_staff'][1]['rewrite']['slug']==='staff','Archive must be enabled');
check(in_array('thumbnail',$GLOBALS['registered_staff'][1]['supports'],true) && in_array('editor',$GLOBALS['registered_staff'][1]['supports'],true),'Staff needs portrait and bio');
check($GLOBALS['registered_department'][0]==='balt_staff_department','Department taxonomy missing');
$GLOBALS['staff_posts']=[
 10=>(object)['post_type'=>'balt_staff','post_status'=>'publish','post_password'=>'','post_title'=>'Canonical Person'],
 11=>(object)['post_type'=>'balt_staff','post_status'=>'draft','post_password'=>'','post_title'=>'Draft Person'],
 12=>(object)['post_type'=>'page','post_status'=>'publish','post_password'=>'','post_title'=>'Not Staff'],
 13=>(object)['post_type'=>'balt_staff','post_status'=>'publish','post_password'=>'secret','post_title'=>'Protected Person']
];
$GLOBALS['staff_roles']=[10=>'Canonical Role'];
$mixed=['members'=>[
 ['source'=>'custom','name'=>'Custom Person'],
 ['source'=>'staff','staff_member'=>10,'name'=>'Stale Name','role'=>'Stale Role'],
 ['source'=>'staff','staff_member'=>11,'name'=>'Stale Draft'],
 ['source'=>'staff','staff_member'=>12],
 ['source'=>'staff','staff_member'=>13],
 ['source'=>'staff','staff_member'=>99],
 ['name'=>'Legacy Manual']
]];
$html=render_staff($mixed);
check(substr_count($html,'<article ')===3,'Only valid staff and manual rows should render');
check(strpos($html,'Custom Person')<strpos($html,'Canonical Person') && strpos($html,'Canonical Person')<strpos($html,'Legacy Manual'),'Mixed row order lost');
check(str_contains($html,'Canonical Role') && str_contains($html,'/portrait.jpg'),'Selected role and image not sourced from CPT');
foreach(['Stale Name','Stale Role','Draft Person','Stale Draft','Not Staff','Protected Person'] as $bad)check(!str_contains($html,$bad),'Private or stale row leaked: '.$bad);
$GLOBALS['staff_roles'][10]='Updated Role';
check(str_contains(render_staff($mixed),'Updated Role'),'Canonical edits must update the grid');
check(balt_staff_picker_query([])===['post_type'=>'balt_staff','post_status'=>'publish','has_password'=>false],'Picker must restrict records');
echo "Staff CPT registration, mixed rows, legacy compatibility and canonical updates passed\n";
