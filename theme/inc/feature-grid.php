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


/** Shared link resolver: old rows default to their original ACF link. */
function balt_feature_grid_destination( array $item ): array {
	if ( 'file' === ( $item['destination_type'] ?? 'link' ) ) {
		$file_id = absint( $item['file'] ?? 0 );
		$url = $file_id ? wp_get_attachment_url( $file_id ) : false;
		return array(
			'url'      => $url ? (string) $url : '',
			'title'    => trim( (string) ( $item['file_label'] ?? '' ) ) ?: __( 'Download', 'baltimore' ),
			'target'   => '',
			'download' => true,
		);
	}
	$link = is_array( $item['link'] ?? null ) ? $item['link'] : array();
	return array(
		'url'      => (string) ( $link['url'] ?? '' ),
		'title'    => trim( (string) ( $link['title'] ?? '' ) ) ?: __( 'Learn More', 'baltimore' ),
		'target'   => '_blank' === ( $link['target'] ?? '' ) ? '_blank' : '',
		'download' => false,
	);
}
