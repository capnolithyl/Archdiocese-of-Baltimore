<?php
/** Render both stats styles with stubbed WordPress and ACF functions. */
declare(strict_types=1);
define('ABSPATH', __DIR__);
$GLOBALS['stats_test_fields'] = [];
$GLOBALS['reads'] = [];
function get_field(string $key) {
	$GLOBALS['reads'][] = $key;
	return $GLOBALS['stats_test_fields'][$key] ?? null;
}
function absint($v): int { return abs((int) $v); }
function esc_html(string $v): string { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
function esc_attr(string $v): string { return esc_html($v); }
function esc_html__(string $v, string $domain): string { return esc_html($v); }
function esc_attr_e(string $v, string $domain): void { echo esc_attr($v); }
function get_block_wrapper_attributes(array $attrs = []): string {
	return 'class="' . esc_attr($attrs['class'] ?? '') . '"';
}
function wp_unique_id(string $prefix): string { return $prefix . '1'; }
function wp_get_attachment_image(int $id, string $size, bool $icon, array $attrs): string { return '<img src="/photo.jpg">'; }
function dpi_blocks_render_link(array $link, string $class, bool $preview): string { return '<a class="' . esc_attr($class) . '">CTA</a>'; }
function wp_kses_post(string $html): string { return $html; }
function check(bool $ok, string $message): void { if (!$ok) throw new RuntimeException($message); }
function render_stats(array $fields, bool $is_preview = false): string {
	$GLOBALS['stats_test_fields'] = $fields;
	$GLOBALS['reads'] = [];
	ob_start();
	require dirname(__DIR__) . '/theme/dpi-blocks/blocks/stats/render.php';
	return (string) ob_get_clean();
}
$fields = [
	'stats' => [
		['value'=>'150+', 'label'=>'Parishes Served'],
		['value'=>'6', 'label'=>'Ministry Offices'],
		['value'=>'12,000+', 'label'=>'Adults Formed Annually'],
		['value'=>'40+', 'label'=>'Years of Service'],
	],
	'eyebrow'=>'Impact',
	'heading'=>'Our Faith in Action',
	'description'=>'Supporting description',
	'background_image'=>42,
	'buttons'=>[['link'=>['url'=>'/about']]],
	'item_background'=>'tinted',
];
$default = render_stats($fields);
check(str_contains($default, 'Our Faith in Action'), 'Legacy stats must keep the heading');
check(str_contains($default, '/photo.jpg') && str_contains($default, 'CTA'), 'Legacy background and buttons must survive');
check(str_contains($default, 'bg-primary/25') && str_contains($default, 'bg-linear-[158deg]'), 'Legacy dark tinted treatment lost');
check(!str_contains($default, 'balt-stats-compact'), 'Missing style must default to dark');
check($default === render_stats($fields + ['style'=>'impact']), 'Explicit impact should match legacy output');
$compact = render_stats($fields + ['style'=>'compact']);
foreach (['balt-stats-compact', 'bg-[#F5F3EE]', 'border-[#E5E7EB]', 'desktop:grid-cols-4', 'py-10', '150+', '12,000+', 'Years of Service', 'aria-label="Statistics"'] as $token) {
	check(str_contains($compact, $token), 'Compact rendering missing ' . $token);
}
foreach (['Our Faith in Action','Supporting description','/photo.jpg','CTA','bg-linear-[158deg]','min-h-[160px]'] as $token) {
	check(!str_contains($compact, $token), 'Compact must omit impact-only content: ' . $token);
}
check(!in_array('background_image', $GLOBALS['reads'], true), 'Compact must not read impact fields');
for ($count = 1; $count <= 6; $count++) {
	$items = array_fill(0, $count, ['value'=>'0', 'label'=>'<Test>']);
	$html = render_stats(['style'=>'compact','stats'=>$items], true);
	check(substr_count($html, '<li ') === $count, 'Compact count mismatch');
	check(substr_count($html, '>0</strong>') === $count, 'Zero is a valid statistic');
	check(substr_count($html, '&lt;Test&gt;') === $count, 'Labels must be escaped');
}
check(render_stats(['style'=>'compact','stats'=>[]]) === '', 'Empty public block should be hidden');
check(str_contains(render_stats(['style'=>'compact','stats'=>[]], true),'Add at least one statistic'), 'Editor needs empty state');
echo "Compact and legacy impact stats rendering passed\n";
