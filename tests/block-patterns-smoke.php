<?php
/** Test actual pattern registration and rendered native block content. */
declare(strict_types=1);
define('ABSPATH',__DIR__);
$GLOBALS['patterns']=[];
$GLOBALS['categories']=[];
$GLOBALS['hooks']=[];
function __(string $s,string $domain):string{return $s;}
function esc_html_e(string $s,string $domain):void{echo htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
function get_template_directory():string{return dirname(__DIR__).'/theme';}
function add_action(...$args):void{$GLOBALS['hooks'][]=$args;}
function register_block_pattern_category(string $slug,array $data):void{$GLOBALS['categories'][$slug]=$data;}
function register_block_pattern(string $slug,array $data):void{
 if(isset($GLOBALS['patterns'][$slug]))throw new RuntimeException('Duplicate pattern registration');
 $GLOBALS['patterns'][$slug]=$data;
}
class WP_Block_Patterns_Registry {
 public static function get_instance():self{return new self();}
 public function is_registered(string $slug):bool{return isset($GLOBALS['patterns'][$slug]);}
}
function check(bool $v,string $message):void{if(!$v)throw new RuntimeException($message);}
require dirname(__DIR__).'/theme/inc/block-patterns.php';
check(in_array(['init','balt_register_block_patterns',9],$GLOBALS['hooks'],true),'Pattern must register before core discovery');
balt_register_block_patterns();
$p=$GLOBALS['patterns']['baltimore/media-text-section']??[];
check(isset($GLOBALS['categories']['baltimore']),'Baltimore category missing');
check(($p['inserter']??false)===true,'Pattern must show in inserter');
check(in_array('baltimore',$p['categories']??[],true),'Pattern must be categorized');
check(in_array('media',$p['keywords']??[],true),'Search keyword missing');
check(str_contains($p['content']??'','<!-- wp:media-text'),'Native Media & Text content missing');
check(str_contains($p['content']??'','<!-- wp:buttons -->'),'Native buttons missing');
check(!str_contains($p['content']??'','<?php'),'Pattern must contain rendered markup, not PHP');
balt_register_block_patterns();
check(count($GLOBALS['patterns'])===2,'Both theme patterns must register exactly once');
$c=$GLOBALS['patterns']['baltimore/contact-form']??[];
check(($c['inserter']??false)===true && in_array('baltimore',$c['categories']??[],true),'Contact pattern must be discoverable');
check(str_contains($c['content'],'<!-- wp:group')&&str_contains($c['content'],'<!-- wp:columns'),'Use native editable layout blocks');
check(substr_count($c['content'],'<!-- wp:column ')===2,'Two editable columns');
check(str_contains($c['content'],'<!-- wp:gravityforms/form {"title":false,"description":false} /-->'),'Native Gravity Forms selector with no fixed ID');
check(!str_contains($c['content'],'<!-- wp:balt/contact-form')&&!str_contains($c['content'],'"lock"'),'No legacy block or content locks');
check(!str_contains($c['content'],'<?php'),'Render pattern PHP before registration');
unset($GLOBALS['patterns']['baltimore/contact-form']);
balt_register_block_patterns();
check(count($GLOBALS['patterns'])===2,'Register missing contact pattern even when media pattern exists');
echo "Baltimore pattern registration and inserter content passed\n";
