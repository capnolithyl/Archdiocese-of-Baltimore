<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_register_staff_grid_block(): void {
	register_block_type( get_template_directory() . '/blocks/staff-grid' );
}
add_action( 'init', 'balt_register_staff_grid_block', 5 );

function balt_render_staff_grid_block( $block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = null ): void {
	require get_template_directory() . '/blocks/staff-grid/render.php';
}
