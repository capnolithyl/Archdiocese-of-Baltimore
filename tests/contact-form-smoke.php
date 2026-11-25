<?php
declare(strict_types=1);
define('ABSPATH',__DIR__);
function add_action(...$args):void{}
function esc_html($v):string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function esc_attr($v):string{return esc_html($v);}
function esc_html_e($v,$domain):void{echo esc_html($v);}
function esc_url($v):string{return esc_attr($v);}
function sanitize_email($v):string{return filter_var($v,FILTER_VALIDATE_EMAIL)?$v:'';}
function absint($v):int{return abs((int)$v);}
function get_field($name,...$args){return $GLOBALS['fixture'][$name]??null;}
function wp_unique_id($prefix):string{return $prefix.'1';}
function get_block_wrapper_attributes($attrs):string{return 'class="'.esc_attr($attrs['class']).'"';}
class GFAPI {static function get_form($id){return $id===7?['id'=>7,'title'=>'Office Contact','is_active'=>1,'is_trash'=>0]:($id===8?['id'=>8,'is_active'=>0]:false);}}
function gravity_form(...$args):void{$GLOBALS['gf_args']=$args;echo '<form id="gform_7">Real form</form>';}
function get_post($id){return $id===3?(object)['post_type'=>'wp_block','post_status'=>'publish','post_content'=>'pattern']:null;}
function parse_blocks($s){return $s==='pattern'?[['blockName'=>'balt/contact-form','attrs'=>['data'=>['form'=>7,'ajax'=>0]]],['blockName'=>'core/block','attrs'=>['ref'=>3]]]:[];}
function check($ok,$msg):void{if(!$ok)throw new RuntimeException($msg);}
require dirname(__DIR__).'/theme/inc/contact-form.php';
function render_contact($fields,$is_preview=false):string{$GLOBALS['fixture']=$fields;ob_start();require dirname(__DIR__).'/theme/blocks/contact-form/render.php';return ob_get_clean();}
check(balt_contact_form_id('7')===7&&balt_contact_form_id(['id'=>7])===7&&balt_contact_form_id(['7'])===7&&balt_contact_form_id('bad')===0,'Normalize form IDs');
check(balt_contact_form_ajax(null)&&!balt_contact_form_ajax('0'),'AJAX default and explicit off');
$fixture=['eyebrow'=>'Here to Help','heading'=>'Contact & Help','description'=>'Ask <us>','phone'=>'(410) 555-0100','phone_hours'=>'Mon–Fri','email'=>'test@example.org','address'=>'Example address','form'=>7,'ajax'=>1];
$html=render_contact($fixture);
foreach(['Contact &amp; Help','Ask &lt;us&gt;','tel:4105550100','mailto:test@example.org','Example address','balt-contact-form__card','gform_7','desktop:grid-cols-2','balt-contact-icon--phone'] as $token)check(str_contains($html,$token),'Missing '.$token);
check($GLOBALS['gf_args']===[7,false,false,false,null,true,0,true],'Native GF call parameters');
check(!str_contains(render_contact($fixture,true),'<form')&&str_contains(render_contact($fixture,true),'Office Contact'),'Editor must not render submit-capable form');
check(!str_contains(render_contact(['form'=>8,'heading'=>'Contact']),'balt-contact-form__card'),'Inactive form must not render');
check(render_contact(['form'=>8])==='','Empty public block hidden');
check(str_contains(render_contact([],true),'Select an active Gravity Form'),'Editor empty state');
check(str_contains(render_contact(['form'=>7,'heading'=>'Contact','heading_alignment'=>'center']),'text-center'),'Heading alignment');
$forms=[];$seen=[];
balt_contact_form_collect([['blockName'=>'core/group','innerBlocks'=>[['blockName'=>'balt/contact-form','attrs'=>['data'=>['form'=>7,'ajax'=>1]]],['blockName'=>'core/block','attrs'=>['ref'=>3]]]]],$forms,$seen);
check($forms===[7=>true]&&isset($seen[3]),'Nested and synced blocks must be collected, deduplicated and cycle-safe');
echo "Contact form rendering, preview, IDs, missing form and asset discovery passed\n";
