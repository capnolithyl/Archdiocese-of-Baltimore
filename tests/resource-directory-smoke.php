<?php
declare(strict_types=1);
define('ABSPATH', __DIR__);
define('BALT_VERSION','test');
function add_action(...$args):void{}
function absint($v):int{return abs((int)$v);}
function esc_html($v):string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function esc_attr($v):string{return esc_html($v);}
function esc_url($v):string{return preg_match('~^(?:https?://|/|#)~',(string)$v)?esc_html($v):'';}
function __($v,$domain):string{return $v;}
function esc_html_e($v,$d):void{echo esc_html($v);}
function esc_attr_e($v,$d):void{echo esc_attr($v);}
function get_post($id){return $GLOBALS['pages'][$id]??null;}
function get_the_title($p):string{return is_object($p)?$p->post_title:(get_post($p)->post_title??'');}
function wp_get_post_parent_id($id):int{return (int)(get_post($id)->post_parent??0);}
function get_pages($args):array{$GLOBALS['query']=$args;return array_values(array_filter($GLOBALS['pages'],fn($p)=>$p->post_type==='page'&&$p->post_parent===$args['parent']));}
function get_permalink($p):string{return '/page/'.$p->ID.'/';}
function wp_get_attachment_url($id){return $id===7?'/uploads/test.pdf':false;}
function get_field($k){return $GLOBALS['fixture'][$k]??null;}
function get_the_ID():int{return 2;}
function sanitize_title($s):string{return strtolower(str_replace(' ','-',$s));}
function wp_unique_id($p):string{return $p.'1';}
function get_block_wrapper_attributes($a):string{return 'class="'.esc_attr($a['class']).'"';}
function wp_enqueue_script(...$args):void{$GLOBALS['script']=$args;}
function get_template_directory_uri():string{return '/theme';}
function check($ok,$msg):void{if(!$ok)throw new RuntimeException($msg);}
require dirname(__DIR__).'/theme/inc/resource-directory.php';
function page($id,$parent,$name,$status='publish',$password='',$type='page'){return (object)['ID'=>$id,'post_parent'=>$parent,'post_title'=>$name,'post_status'=>$status,'post_password'=>$password,'post_type'=>$type];}
$GLOBALS['pages']=[1=>page(1,0,'Resources'),2=>page(2,1,'Crisis Resources'),3=>page(3,1,'Campaigns'),4=>page(4,1,'Private','private'),5=>page(5,1,'Protected','publish','secret'),6=>page(6,2,'Grandchild'),7=>page(7,2,'PDF Guide','inherit','','attachment'),8=>page(8,4,'Private PDF','inherit','','attachment')];
$root=balt_resource_directory_categories(1,'landing');
check(count($root)===2&&!$root[0]['current'],'Landing lists only published unprotected direct children');
check($GLOBALS['query']['sort_column']==='menu_order,post_title','Menu order');
$nav=balt_resource_directory_categories(2,'category');
check(count($nav)===2&&$nav[0]['current'],'Category highlights current page');
$rows=[['heading'=>'Articles & Reflections','links'=>[['type'=>'file','file'=>7],['type'=>'file','file'=>8],['link'=>['url'=>'https://example.org','title'=>'Article','target'=>'_blank']],['link'=>['url'=>'javascript:alert(1)','title'=>'Bad']]]],['heading'=>'Prayers','column'=>'right','links'=>[['link'=>['url'=>'/prayers/','title'=>'Prayer']]]],['heading'=>'Empty','links'=>[]]];
$s=balt_resource_directory_sections($rows);
check(count($s)===2&&count($s[0]['links'])===2&&$s[0]['links'][0]['label']==='PDF Guide'&&$s[1]['column']==='right','Sections and safe link/file data');
function render_dir($fields,$post_id,$is_preview=false){$GLOBALS['fixture']=$fields;ob_start();require dirname(__DIR__).'/theme/blocks/resource-directory/render.php';return ob_get_clean();}
$landing=render_dir(['mode'=>'landing'],1);
check(str_contains($landing,'balt-resource-directory__intro')&&!str_contains($landing,'<details'),'Landing intro only');
$category=render_dir(['mode'=>'category','sections'=>$rows],2);
foreach(['aria-current="page"','Jump to Section...','Articles &amp; Reflections','Prayers','/uploads/test.pdf','target="_blank" rel="noopener noreferrer"','href="#balt-resource-1-1-articles-&amp;-reflections"'] as $t)check(str_contains($category,$t),'Missing '.$t);
foreach(['Private PDF','Protected','javascript:','Grandchild'] as $t)check(!str_contains($category,$t),'Unexpected '.$t);
check(!str_contains(render_dir(['mode'=>'category','sections'=>$rows],2,true),'href="/uploads/test.pdf"'),'Preview links inert');
check(str_contains(render_dir(['mode'=>'category'],2),'No resources are available'),'Empty category message');
echo "Resource directory landing, sibling navigation, grouped resources and safe links passed\n";
