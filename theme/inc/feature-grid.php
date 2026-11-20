<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the theme's Navigate / Feature Grid ACF block. */
function balt_register_feature_grid_block(): void {
	register_block_type( get_template_directory() . '/blocks/feature-grid' );
}
add_action( 'init', 'balt_register_feature_grid_block', 5 );

/** Render an ACF preview without depending on the separate DPI Blocks plugin. */
function balt_render_feature_grid_block( $block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = null ): void {
	require get_template_directory() . '/blocks/feature-grid/render.php';
}


/**
 * ACF Font Awesome's admin stylesheet lives outside Gutenberg's preview iframe.
 * Enqueue its configured Font Awesome assets within the editor canvas as well.
 */
function balt_feature_grid_editor_icons(): void {
	if ( ! is_admin() ) {
		return;
	}

	$url = apply_filters( 'ACFFA_get_fa_url', '' );
	if ( ! is_string( $url ) || '' === $url ) {
		return;
	}

	if ( str_contains( $url, 'https://kit.fontawesome.com/' ) ) {
		wp_enqueue_script( 'balt-feature-grid-fa-kit', $url, array(), null, true );
		return;
	}

	$version = apply_filters( 'ACFFA_get_latest_version', '6.0.0' );
	wp_enqueue_style( 'balt-feature-grid-fa', $url, array(), $version );
}
add_action( 'enqueue_block_assets', 'balt_feature_grid_editor_icons' );
