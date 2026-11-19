<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the reusable Image & Text Banner ACF block. */
function balt_register_image_text_banner_block(): void {
	register_block_type( get_template_directory() . '/blocks/image-text-banner' );
}
add_action( 'init', 'balt_register_image_text_banner_block', 5 );

function balt_render_image_text_banner_block( $block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = null ): void {
	require get_template_directory() . '/blocks/image-text-banner/render.php';
}
