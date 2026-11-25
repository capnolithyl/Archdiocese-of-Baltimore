<?php
/** WordPress-free interior page rendering regression tests. */
declare(strict_types=1);
define('ABSPATH',__DIR__);
$GLOBALS['fields']=[];
$GLOBALS['image_id']=42;
function get_field(string $key,int $id){return $GLOBALS['fields'][$key]??null;}
function get_page_template_slug(int $id):string{return $GLOBALS['template_slug']??'';}
function get_post_type(int $id):string{return $GLOBALS['post_type']??'page';}
function current_user_can(string $cap,int $id):bool{return $GLOBALS['can_edit']??true;}
function update_post_meta(int $id,string $key,string $value):void{
 test_ok($key==='_wp_page_template','Only template assignment may be changed');
 $GLOBALS['template_slug']=$value;
}
function add_action(...$args):void{}
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
foreach(['page-templates/interior-featured.php','page-templates/interior-title.php'] as $retired){
 $GLOBALS['template_slug']=$retired;
 balt_reset_retired_page_template(22);
 test_ok($GLOBALS['template_slug']==='default','Retired template assignment must reset');
}
$GLOBALS['template_slug']='other-template.php';
balt_reset_retired_page_template(22);
test_ok($GLOBALS['template_slug']==='other-template.php','Unrelated template must stay unchanged');
$GLOBALS['template_slug']='page-templates/interior-title.php';
$GLOBALS['can_edit']=false;
balt_reset_retired_page_template(22);
test_ok($GLOBALS['template_slug']==='page-templates/interior-title.php','Unauthorized user must not change assignment');
$GLOBALS['can_edit']=true;
$GLOBALS['post_type']='post';
balt_reset_retired_page_template(22);
test_ok($GLOBALS['template_slug']==='page-templates/interior-title.php','Non-page records must not change');
$GLOBALS['post_type']='page';
$GLOBALS['fields']['balt_interior_eyebrow']='Institute for';
$hero=render_part('interior-hero',[]);
test_ok(substr_count($hero,'<h1 ')===1,'Hero has duplicate or missing H1');
test_ok(str_contains($hero,'/uploads/42.jpg'),'Default page must display WordPress featured image');
test_ok(str_contains($hero,'balt-interior-hero--image'),'Featured image should activate image appearance');
test_ok(str_contains($hero,'Institute for'),'Eyebrow missing');
test_ok(str_contains($hero,'>Evangelization</h1>'),'Blank custom title must default to native page title');
$GLOBALS['fields']['balt_interior_title']=' Faith & Formation ';
$custom=render_part('interior-hero',[]);
test_ok(str_contains($custom,'>Faith &amp; Formation</h1>'),'Custom hero title must be trimmed and escaped');
test_ok(substr_count($custom,'<h1 ')===1,'Custom hero title must not add another H1');
test_ok(!str_contains($custom,'>Evangelization</h1>'),'Custom heading should replace the visible title only');
test_ok(get_the_title(22)==='Evangelization','Native WordPress title must stay unchanged');
$GLOBALS['image_id']=0;
$custom_plain=render_part('interior-hero',[]);
$GLOBALS['image_id']=42;
test_ok(str_contains($custom_plain,'>Faith &amp; Formation</h1>'),'Custom heading must work in title-only variant');
$GLOBALS['fields']['balt_interior_title']='   ';
test_ok(str_contains(render_part('interior-hero',[]),'>Evangelization</h1>'),'Whitespace-only custom title must fall back');
unset($GLOBALS['fields']['balt_interior_title']);
$GLOBALS['image_id']=0;
$noimg=render_part('interior-hero',[]);
test_ok(!str_contains($noimg,'<img '),'Default page must render title-only hero when no image exists');
test_ok(str_contains($noimg,'balt-interior-hero--title'),'Default no-image page must use navy hero');
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
