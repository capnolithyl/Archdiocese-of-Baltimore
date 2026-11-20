<?php
/** WordPress-free interior page rendering regression tests. */
declare(strict_types=1);
define('ABSPATH',__DIR__);
$GLOBALS['fields']=[];
$GLOBALS['image_id']=42;
function get_field(string $key,int $id){return $GLOBALS['fields'][$key]??null;}
function get_page_template_slug(int $id):string{return $GLOBALS['template_slug']??'';}
function get_the_ID():int{return 22;}
function get_the_title(int $id):string{return 'Evangelization';}
function get_post_thumbnail_id(int $id):int{return $GLOBALS['image_id'];}
function esc_attr(string $s):string{return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
function esc_html(string $s):string{return esc_attr($s);}
function esc_attr_e(string $s,string $d):void{echo esc_attr($s);}
function esc_url(string $s):string{return preg_match('~^(?:https?://|/|#[A-Za-z])~i',$s)?esc_attr($s):'';}
function wp_get_attachment_image(int $id,string $size,bool $icon,array $attrs):string{return '<img src="/uploads/'.$id.'.jpg" alt="'.esc_attr($attrs['alt']).'">';}
function test_ok(bool $v,string $msg):void{if(!$v)throw new RuntimeException($msg);}
function render_part(string $name,array $data):string{$args=$data;ob_start();require dirname(__DIR__).'/theme/template-parts/layout/'.$name.'.php';return ob_get_clean();}
require dirname(__DIR__).'/theme/inc/interior-pages.php';
$GLOBALS['template_slug']='page-templates/interior-featured.php';
test_ok(balt_is_interior_page_template(22),'Featured template missing');
$GLOBALS['template_slug']='page-templates/interior-title.php';
test_ok(balt_is_interior_page_template(22),'Title-only template missing');
$GLOBALS['template_slug']='default';
test_ok(!balt_is_interior_page_template(22),'Default should be excluded');
$GLOBALS['fields']['balt_interior_eyebrow']='Institute for';
$hero=render_part('interior-hero',['variant'=>'featured']);
test_ok(substr_count($hero,'<h1 ')===1,'Hero has duplicate or missing H1');
test_ok(str_contains($hero,'/uploads/42.jpg'),'WordPress featured image missing');
test_ok(str_contains($hero,'Institute for'),'Eyebrow missing');
$noimg=render_part('interior-hero',['variant'=>'title']);
test_ok(!str_contains($noimg,'<img '),'Title-only hero displays image');
$GLOBALS['image_id']=0;
$noimg=render_part('interior-hero',['variant'=>'featured']);
test_ok(!str_contains($noimg,'<img '),'Missing image fallback broken');
$GLOBALS['fields']['balt_section_nav_enabled']=1;
$GLOBALS['fields']['balt_section_nav_links']=[
 ['label'=>'About','link'=>['url'=>'#mission']],
 ['label'=>'Offices','link'=>['url'=>'/offices/']],
 ['label'=>'Resources','link'=>['url'=>'https://example.org/','target'=>'_blank']],
 ['label'=>'Unsafe','link'=>['url'=>'javascript:alert(1)']],
 ['label'=>'','link'=>['url'=>'/empty/']]
];
test_ok(count(balt_interior_section_links(22))===3,'Unsafe/incomplete links not filtered');
$nav=render_part('section-navigation',['post_id'=>22]);
test_ok(substr_count($nav,'class="balt-section-nav__item"')===3,'Nav should render three links');
test_ok(str_contains($nav,'href="#mission"'),'Anchor lost');
test_ok(str_contains($nav,'rel="noopener noreferrer"'),'External rel missing');
$GLOBALS['fields']['balt_section_nav_enabled']=0;
test_ok(render_part('section-navigation',['post_id'=>22])==='','Disabled nav not hidden');
echo "Interior hero variants and editable section navigation passed\n";
