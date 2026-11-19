<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Register the Image CTA Grid and its ACF preview. */
function balt_register_image_cta_grid_block(): void {
	register_block_type( get_template_directory() . '/blocks/image-cta-grid' );
}
add_action( 'init', 'balt_register_image_cta_grid_block', 5 );

function balt_render_image_cta_grid_block( $block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = null ): void {
	require get_template_directory() . '/blocks/image-cta-grid/render.php';
}
