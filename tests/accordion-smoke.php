<?php
declare(strict_types=1);
define('ABSPATH',__DIR__);
define('BALT_VERSION','test');
function add_filter(...$args):void{}
function esc_html($s):string{return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function esc_attr($s):string{return esc_html($s);}
function wp_unique_id($prefix):string{return $prefix.'1';}
function get_template_directory_uri():string{return '/theme';}
function wp_enqueue_script(...$args):void{$GLOBALS['script']=$args;}
function check($ok,$message):void{if(!$ok)throw new RuntimeException($message);}
require dirname(__DIR__).'/theme/inc/accordion.php';
$a=balt_accordion_attributes(['attributes'=>['autoclose'=>['type'=>'boolean']]],'core/accordion');
check(isset($a['attributes']['autoclose'],$a['attributes']['baltEyebrow'],$a['attributes']['baltHeading']),'Preserve native attributes');
check(balt_accordion_attributes([],'core/paragraph')===[],'Only accordion extended');
$native='<div id="faq" class="wp-block-accordion" data-wp-interactive="core/accordion"><div class="wp-block-accordion-panel" hidden="until-found">Answer</div></div>';
$out=balt_render_accordion_section($native,['attrs'=>['baltEyebrow'=>' Common & Questions ','baltHeading'=>'FAQ <test>']]);
check(str_contains($out,$native),'Native markup and directives must remain unchanged');
check(str_contains($out,'Common &amp; Questions')&&str_contains($out,'FAQ &lt;test&gt;'),'Escaped fields');
check(str_contains($out,'aria-labelledby="balt-accordion-title-1"'),'Section landmark');
check($GLOBALS['script'][0]==='balt-accordion-motion','Animation script not enqueued');
$old=balt_render_accordion_section($native,[]);
check(!str_contains($old,'<header')&&!str_contains($old,'<section')&&str_contains($old,$native),'Old blocks must remain renderable without empty headings');
check(balt_render_accordion_section('',[])==='','Empty output');
echo "Native accordion attributes, escaped section heading and original markup passed\n";

check($a['attributes']['baltHeadingAlignment']['default']==='default','Alignment must preserve default');
check(str_contains(balt_render_accordion_section($native,['attrs'=>['baltHeading'=>'FAQ','baltHeadingAlignment'=>'left']]),'balt-accordion-section__header--left'),'Left heading alignment');
check(!str_contains(balt_render_accordion_section($native,['attrs'=>['baltHeading'=>'FAQ','baltHeadingAlignment'=>'center']]),'balt-accordion-section__header--left'),'Centered heading alignment');
