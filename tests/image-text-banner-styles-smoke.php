<?php
declare(strict_types=1);
define('ABSPATH', __DIR__);
function get_field(string $k) { return $GLOBALS['banner_fixture'][$k] ?? null; }
function absint($v): int { return abs((int)$v); }
function esc_html($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function esc_attr($v): string { return esc_html($v); }
function esc_url($v): string { return preg_match('~^(?:https?://|/|#)~', (string)$v) ? esc_attr($v) : ''; }
function wp_unique_id(string $prefix): string { return $prefix.'test'; }
function get_block_wrapper_attributes(array $attrs): string { return 'class="'.esc_attr($attrs['class']).'"'; }
function wp_get_attachment_image($id,$size,$icon,$attrs): string { return '<img src="/image/'.$id.'.jpg" alt="" class="'.esc_attr($attrs['class']).'">'; }
function wp_kses_post(string $s): string { return $s; }
function dpi_blocks_render_link(array $link,string $class,bool $preview): string {
 $tag=$preview?'span':'a';
 $attrs=$preview?'':' href="'.esc_url($link['url']).'"'.(('_blank'===($link['target']??''))?' target="_blank" rel="noopener noreferrer"':'');
 return '<'.$tag.' class="'.esc_attr($class).'"'.$attrs.'>'.esc_html($link['title']).'</'.$tag.'>';
}
function verify(bool $ok,string $msg): void { if (!$ok) throw new RuntimeException($msg); }
function render_banner(array $fixture,bool $is_preview=false): string {
 $GLOBALS['banner_fixture']=$fixture;
 ob_start();
 require dirname(__DIR__).'/theme/blocks/image-text-banner/render.php';
 return (string)ob_get_clean();
}
$fixture=['eyebrow'=>'A Message','heading'=>'Faith & Formation','subheading'=>'Split subtitle','quote'=>'Split quote','description'=>'Body <copy>','image'=>42,'buttons'=>[['link'=>['url'=>'/journey/','title'=>'Begin Your Journey']],['link'=>['url'=>'https://example.org','title'=>'More','target'=>'_blank']]]];
$legacy=render_banner($fixture);
verify($legacy===render_banner($fixture+['style'=>'split']),'Explicit split must match legacy');
verify($legacy===render_banner($fixture+['style'=>'unknown']),'Unknown style must fall back');
foreach(['lg:grid-cols-2','Split subtitle','Split quote','btn btn-red','btn btn-outline-subtle','/image/42.jpg'] as $token) verify(str_contains($legacy,$token),'Legacy markup missing '.$token);
verify(!str_contains($legacy,'balt-image-text-banner--background'),'Default must remain split');
$bg=render_banner($fixture+['style'=>'background']);
foreach(['balt-image-text-banner--background','from-primary/95','via-primary/80','to-primary/0','desktop:hidden','bg-secondary','694px','max-w-[600px]','3.5rem','29.25px','Faith &amp; Formation','Body &lt;copy&gt;','/image/42.jpg','btn btn-gold rounded-[4px]','target="_blank" rel="noopener noreferrer"','aria-labelledby="balt-image-text-banner-test"'] as $token) verify(str_contains($bg,$token),'Background missing '.$token);
foreach(['Split subtitle','Split quote','btn btn-red','lg:grid-cols-2'] as $token) verify(!str_contains($bg,$token),'Split content leaked '.$token);
verify(!str_contains(render_banner($fixture+['style'=>'background'],true),'<a '),'Preview links must be inert');
$no_image=render_banner(['style'=>'background','heading'=>'Fallback']);
verify(!str_contains($no_image,'<img ')&&str_contains($no_image,'bg-primary'),'Missing image fallback');
verify(!str_contains($no_image,'mt-8 flex'),'No empty button wrapper');
$unsafe=render_banner(['style'=>'background','heading'=>'Test','buttons'=>[['link'=>['url'=>'javascript:alert(1)','title'=>'Invalid']],['link'=>['url'=>'/valid/','title'=>'Valid']]]]);
verify(!str_contains($unsafe,'javascript:')&&str_contains($unsafe,'btn btn-gold'),'Filter unsafe link, first valid link gold');
echo "Split and full-background banner rendering passed\n";
