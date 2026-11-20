<?php
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );

$in_admin = false;
$fa_url = '';
$enqueued_styles = array();
$enqueued_scripts = array();
$actions = array();

function is_admin(): bool {
	return $GLOBALS['in_admin'];
}
function add_action( string $name, $callback ): void {
	$GLOBALS['actions'][ $name ] = $callback;
}
function apply_filters( string $name, $value ) {
	return match ( $name ) {
		'ACFFA_get_fa_url' => $GLOBALS['fa_url'],
		'ACFFA_get_latest_version' => '7.3.0',
		default => $value,
	};
}
function wp_enqueue_style( string $handle, string $src, array $deps, $version ): void {
	$GLOBALS['enqueued_styles'][] = compact( 'handle', 'src', 'deps', 'version' );
}
function wp_enqueue_script( string $handle, string $src, array $deps, $version, $in_footer ): void {
	$GLOBALS['enqueued_scripts'][] = compact( 'handle', 'src', 'deps', 'version', 'in_footer' );
}

require dirname( __DIR__ ) . '/theme/inc/feature-grid.php';

if ( ( $actions['enqueue_block_assets'] ?? null ) !== 'balt_feature_grid_editor_icons' ) {
	throw new RuntimeException( 'Use enqueue_block_assets for iframe content' );
}

$fa_url = 'https://example.org/fontawesome/all.css';
balt_feature_grid_editor_icons();
if ( $enqueued_styles || $enqueued_scripts ) {
	throw new RuntimeException( 'Do not enqueue extra assets on the public site' );
}

$in_admin = true;
$fa_url = '';
balt_feature_grid_editor_icons();
if ( $enqueued_styles || $enqueued_scripts ) {
	throw new RuntimeException( 'Missing plugin URL must not enqueue empty stylesheets' );
}

$fa_url = 'https://example.org/fontawesome/all.css';
balt_feature_grid_editor_icons();
if ( count( $enqueued_styles ) !== 1 ||
     $enqueued_styles[0]['src'] !== $fa_url ||
     $enqueued_styles[0]['version'] !== '7.3.0' ||
     $enqueued_styles[0]['handle'] !== 'balt-feature-grid-fa' ) {
	throw new RuntimeException( 'Font Awesome stylesheet was not enqueued correctly' );
}

$fa_url = 'https://kit.fontawesome.com/test.js';
balt_feature_grid_editor_icons();
if ( count( $enqueued_scripts ) !== 1 ||
     $enqueued_scripts[0]['src'] !== $fa_url ||
     $enqueued_scripts[0]['handle'] !== 'balt-feature-grid-fa-kit' ) {
	throw new RuntimeException( 'Font Awesome Kit script was not enqueued correctly' );
}

echo "Feature Grid iframe icon assets passed\n";
