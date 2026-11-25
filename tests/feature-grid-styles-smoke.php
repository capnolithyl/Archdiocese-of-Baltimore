<?php
declare(strict_types=1);
define('ABSPATH', __DIR__);
function add_action(...$a):void{}
function get_field($k){return $GLOBALS['feature_fixture'][$k]??null;}
function wp_unique_id($s):string{return $s.'test';}
function esc_html($s):string{return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function esc_attr($s):string{return esc_html($s);}
function esc_url($s):string{return preg_match('~^(?:https?://|/|#|mailto:)~',(string)$s)?esc_html($s):'';}
function esc_html_e($s,$d):void{echo esc_html($s);}
function __($s,$d):string{return $s;}
function absint($v):int{return abs((int)$v);}
function wp_get_attachment_url($id){return $id===42?'/uploads/guide.pdf':false;}
function get_block_wrapper_attributes($a):string{return 'class="'.esc_attr($a['class']).'"';}
function check($ok,$message):void{if(!$ok)throw new RuntimeException($message);}
require dirname(__DIR__).'/theme/inc/feature-grid.php';
function render_features(array $fixture,bool $is_preview=false):string{
 $GLOBALS['feature_fixture']=$fixture;
 ob_start(); require dirname(__DIR__).'/theme/blocks/feature-grid/render.php'; return (string)ob_get_clean();
}
$items=[
 ['heading'=>'Page & link','icon'=>'fa-solid fa-book','description'=>'','eyebrow'=>'Guide','icon_color'=>'tertiary','link'=>['url'=>'/page/','title'=>'Explore','target'=>'_blank']],
 ['heading'=>'File','icon'=>'fa-regular fa-file','destination_type'=>'file','file'=>42,'icon_color'=>'secondary','link'=>['url'=>'/stale/']],
 ['heading'=>'Invalid','link'=>['url'=>'javascript:alert(1)']],
 ['heading'=>'Missing file','destination_type'=>'file','file'=>9],
];
$base=['heading'=>'Featured Links','items'=>$items];
$classic=render_features($base);
check($classic===render_features($base+['style'=>'classic']),'Explicit classic must match default');
check(str_contains($classic,'min-h-[336px]')&&!str_contains($classic,'balt-feature-grid--compact'),'Classic appearance changed');
check(str_contains($classic,'href="/page/"'),'Classic link lost');
$compact=render_features($base+['style'=>'compact','view_all'=>['url'=>'/all/','title'=>'View All']]);
foreach(['balt-feature-grid--compact','desktop:grid-cols-4','min-h-[196px]','rounded-[14px]','bg-[#F5F3EE]','bg-tertiary text-white','bg-secondary text-primary','Page &amp; link','Guide','Explore','href="/uploads/guide.pdf" download','Download','View All','target="_blank" rel="noopener noreferrer"'] as $t)check(str_contains($compact,$t),'Missing compact token '.$t);
check(!str_contains($compact,'/stale/')&&!str_contains($compact,'javascript:'),'Invalid or stale link leaked');
check(!str_contains(render_features($base+['style'=>'compact','view_all'=>['url'=>'/all/','title'=>'View All']],true),'<a '),'Editor preview must be inert');
check(render_features(['style'=>'compact','items'=>[]])==='','Empty public grid should be hidden');
check(str_contains(render_features(['style'=>'compact','items'=>[]],true),'Add at least one feature'),'Missing empty preview');
$many=render_features(['style'=>'compact','items'=>array_fill(0,14,$items[0])]);
check(substr_count($many,'href="/page/"')===12,'Maximum must be twelve');
echo "Classic and compact feature grid, safe URLs, file links and preview passed\n";
