<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_register_media_center_block(): void {
	register_block_type( get_template_directory() . '/blocks/media-center' );
}
add_action( 'init', 'balt_register_media_center_block', 5 );

function balt_render_media_center_block( $block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = null ): void {
	require get_template_directory() . '/blocks/media-center/render.php';
}
