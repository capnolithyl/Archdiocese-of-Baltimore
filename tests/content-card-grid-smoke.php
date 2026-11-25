<?php
declare(strict_types=1);
define('ABSPATH',__DIR__);
define('BALT_VERSION','test');
function esc_html_e($v,$d):void{echo esc_html($v);}
function esc_attr_e($v,$d):void{echo esc_attr($v);}
function get_template_directory_uri():string{return '/theme';}
function home_url($path):string{return $path;}
function wp_enqueue_script(...$args):void{$GLOBALS['card_filter_script']=$args;}
function add_action(...$args):void{}
function add_filter(...$args):void{}
function absint($v):int{return abs((int)$v);}
function esc_html($v):string{return htmlspecialchars((string)$v,ENT_QUOTES,'UTF-8');}
function esc_attr($v):string{return esc_html($v);}
function __($v,$domain):string{return $v;}
function esc_url_raw($v):string{return preg_match('~^(?:https?://|/|#)~',$v)?$v:'';}
function esc_url($v):string{return esc_attr(esc_url_raw($v));}
function wp_get_attachment_image($id,$size,$icon,$attrs):string{return '<img src="/media/'.$id.'.jpg" alt="" class="'.esc_attr($attrs['class']).'">';}
function wp_date($format,$date):string{return gmdate($format,$date);}
function get_post_types($args,$format):array{return ['post'=>'post','page'=>'page','attachment'=>'attachment'];}
function is_post_type_viewable($type):bool{return true;}
function get_post($record){return is_object($record)?$record:($GLOBALS['posts'][$record]??null);}
function get_permalink($post):string{return '/post/'.$post->ID;}
function get_the_title($post):string{return $post->post_title;}
function get_the_excerpt($post):string{return $post->post_excerpt;}
function wp_strip_all_tags($s):string{return strip_tags($s);}
function wp_trim_words($s,$len,$more):string{return $s;}
function balt_get_featured_image_id($id):int{return 42;}
function get_post_timestamp($post):int{return 1795622400;}
function get_posts($query):array{$GLOBALS['query']=$query;return [1,2,3];}
function check(bool $ok,string $message):void{if(!$ok)throw new RuntimeException($message);}
function render_cards(array $args):string{ob_start();require dirname(__DIR__).'/theme/template-parts/components/content-card-grid.php';return ob_get_clean();}
require dirname(__DIR__).'/theme/inc/content-card-grid.php';
$rows=[
 ['title'=>'Family Formation','description'=>'Support & care','image'=>42,'link'=>['url'=>'/family/']],
 ['title'=>'Adult Formation','link'=>['url'=>'https://example.org/','title'=>'Explore','target'=>'_blank']],
 ['title'=>'No link','link'=>['url'=>'javascript:alert(1)']],
 ['title'=>'']
];
$cards=balt_content_grid_manual_cards($rows);
check(count($cards)===3,'Skip untitled rows');
check($cards[0]['image_id']===42 && $cards[0]['target']==='','Manual image and same-tab link');
check($cards[2]['link']==='','Unsafe URL must be rejected');
$html=render_cards(['cards'=>$cards,'accent'=>true]);
check(substr_count($html,'<article ')===3,'Card count incorrect');
check(str_contains($html,'/media/42.jpg'),'Attachment image missing');
check(str_contains($html,'Support &amp; care'),'Escape text');
check(str_contains($html,'target="_blank" rel="noopener noreferrer"'),'External target missing');
check(str_contains($html,'Learn More: Family Formation'),'Default CTA accessible label');
check(str_contains($html,'Explore: Adult Formation'),'Custom CTA missing');
check(!str_contains($html,'javascript:'),'Unsafe link leaked');
check(!str_contains(render_cards(['cards'=>$cards,'preview'=>true]),'<a '),'Preview links must be inert');
$rss=render_cards(['cards'=>[['title'=>'RSS','link'=>'https://example.org/news','image'=>'https://example.org/photo.jpg','date'=>1795622400,'excerpt'=>'Story']],'external'=>true]);
check(str_contains($rss,'target="_blank" rel="noopener noreferrer"'),'RSS should remain external');
check(str_contains($rss,'<time ') && str_contains($rss,'https://example.org/photo.jpg'),'RSS date and image missing');
check(str_contains($rss,'before:scale-x-0'),'RSS hover accent changed');
$GLOBALS['posts']=[
 1=>(object)['ID'=>1,'post_status'=>'publish','post_password'=>'','post_type'=>'post','post_title'=>'News','post_excerpt'=>'Excerpt'],
 2=>(object)['ID'=>2,'post_status'=>'draft','post_password'=>'','post_type'=>'post','post_title'=>'Hidden','post_excerpt'=>''],
 3=>(object)['ID'=>3,'post_status'=>'publish','post_password'=>'secret','post_type'=>'page','post_title'=>'Protected','post_excerpt'=>'']
];
$selected=balt_content_grid_post_cards([1,2,3,1],true);
check(count($selected)===1 && $selected[0]['date']>0,'Only unique, published, unprotected content should render');
check(balt_content_grid_post_cards([1],false)[0]['date']===0,'Date must be optional');
$latest=balt_content_grid_latest_cards('post',99,5,true);
check(count($latest)===1 && $GLOBALS['query']['posts_per_page']===12 && $GLOBALS['query']['cat']===5,'Latest post query/filter limits');
check(balt_content_grid_latest_cards('attachment',3,0,false)===[],'Disallowed type must not query');
echo "Manual, selected and latest content cards; RSS shared rendering and safe links passed\n";

function get_field($key){return $GLOBALS['grid_fixture'][$key]??null;}
function wp_unique_id($prefix){return $prefix.'test';}
function get_block_wrapper_attributes($attrs){return 'class="'.esc_attr($attrs['class']).'"';}
function get_template_part($slug,$name=null,$args=[]){require dirname(__DIR__).'/theme/'.$slug.'.php';}
function wp_kses_post($html){return $html;}
function dpi_blocks_render_link($link,$class,$preview){return '<span class="'.esc_attr($class).'">'.esc_html($link['title']).'</span>';}
function grid_render($fields,$is_preview=false){$GLOBALS['grid_fixture']=$fields;ob_start();require dirname(__DIR__).'/theme/blocks/content-card-grid/render.php';return ob_get_clean();}
$fixture=['eyebrow'=>'Stay Informed','heading'=>'Latest News','subheading'=>'Sample','cards'=>$rows,'view_all_link'=>['url'=>'/news/','title'=>'All News']];
$center=grid_render($fixture);
check($center===grid_render($fixture+['heading_alignment'=>'center']),'Explicit center should preserve original layout');
check(str_contains($center,'mt-14 grid') && str_contains($center,'btn btn-primary'),'Center retains bottom button and original grid spacing');
$left=grid_render($fixture+['heading_alignment'=>'left']);
check(str_contains($left,'items-end justify-between gap-4 text-left')&&str_contains($left,'mt-10 grid'),'Left heading layout and spacing');
check(str_contains($left,'href="/news/"')&&strpos($left,'All News')<strpos($left,'class="balt-content-card-grid '),'All News must appear above cards');
check(!str_contains($left,'btn btn-primary'),'Do not duplicate section CTA');
check(!str_contains(grid_render($fixture+['heading_alignment'=>'left'],true),'href="/news/"'),'Section CTA preview should be inert');
echo "Latest News header, CTA location and default grid layout passed\n";

$directory=grid_render(['style'=>'directory','eyebrow'=>'Policy Directory','subheading'=>'Policies & norms','cards'=>$rows,'enable_card_search'=>1,'search_placeholder'=>'Search policies...']);
foreach(['balt-card-directory__header','balt-card-directory__eyebrow','Policies &amp; norms','Search policies...','data-card-search hidden','data-card-empty hidden','Filter displayed cards','data-card-status','Family Formation'] as $token)check(str_contains($directory,$token),'Directory missing '.$token);
check($GLOBALS['card_filter_script'][0]==='balt-card-directory-filter','Local filter script enqueued');
check(!str_contains(grid_render(['style'=>'directory','cards'=>$rows]),'data-card-search'),'Search is optional');
check(str_contains(grid_render(['style'=>'directory','cards'=>$rows,'enable_card_search'=>1],true),'autocomplete="off" disabled'),'Preview input inert');
check(!str_contains($center,'balt-card-directory'),'Standard layout remains default');
echo "Directory header, optional search, editor and shared cards passed\n";

$policy=grid_render(['style'=>'directory','cards'=>$rows,'enable_card_search'=>1,'search_mode'=>'policies']);
foreach(['<form class="balt-card-directory__search"','name="post_type" value="balt_policy"','name="s"','role="search"'] as $token)check(str_contains($policy,$token),'Policy search missing '.$token);
check(!str_contains($policy,'data-card-search')&&!str_contains($policy,'data-card-empty'),'Policy search must not filter cards');
check(!str_contains(grid_render(['style'=>'directory','cards'=>$rows,'enable_card_search'=>1,'search_mode'=>'policies'],true),'<form '),'Preview should not submit');
echo "Policy search mode and compatibility passed\n";
