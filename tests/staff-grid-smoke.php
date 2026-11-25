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
echo "Staff Grid rendering, order, unlimited rows, fallback and CTA passed\n";
