<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hide WordPress's basic RSS block from the inserter.
 * Keep existing core/rss content renderable and leave balt/rss-news available.
 */
function balt_hide_core_rss_block( $allowed_blocks ) {
	if ( false === $allowed_blocks ) {
		return false;
	}

	if ( true === $allowed_blocks ) {
		$allowed_blocks = array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() );
	}

	return array_values( array_diff( $allowed_blocks, array( 'core/rss' ) ) );
}
add_filter( 'allowed_block_types_all', 'balt_hide_core_rss_block', PHP_INT_MAX );
