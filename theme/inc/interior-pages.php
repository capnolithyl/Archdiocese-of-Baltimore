<?php
/**
 * Shared configuration for reusable interior page templates.
 *
 * @package Archdiocese_of_Baltimore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clear retired template assignments when an authorized editor opens a page.
 * WordPress already falls back to page.php when the old file is missing.
 * Only the two retired template values are changed; page content is untouched.
 */
function balt_reset_retired_page_template( int $post_id ): void {
	if ( 'page' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( in_array( get_page_template_slug( $post_id ), array( 'page-templates/interior-featured.php', 'page-templates/interior-title.php' ), true ) ) {
		update_post_meta( $post_id, '_wp_page_template', 'default' );
	}
}

function balt_reset_retired_page_template_on_edit(): void {
	$post_id = isset( $_GET['post'] ) && is_scalar( $_GET['post'] ) ? absint( $_GET['post'] ) : 0;
	if ( $post_id ) {
		balt_reset_retired_page_template( $post_id );
	}
}
add_action( 'load-post.php', 'balt_reset_retired_page_template_on_edit' );

/**
 * Return only usable, editor-managed section navigation links.
 * Keeping this separate from the template makes missing/partial rows harmless.
 *
 * @return array<int,array{label:string,url:string,target:string}>
 */
function balt_interior_section_links( int $post_id ): array {
	if ( ! function_exists( 'get_field' ) || ! get_field( 'balt_section_nav_enabled', $post_id ) ) {
		return array();
	}

	$rows = get_field( 'balt_section_nav_links', $post_id );
	if ( ! is_array( $rows ) ) {
		return array();
	}

	$links = array();
	foreach ( array_slice( $rows, 0, 8 ) as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}

		$label  = trim( (string) ( $row['label'] ?? '' ) );
		$link   = $row['link'] ?? null;
		$url    = is_array( $link ) ? trim( (string) ( $link['url'] ?? '' ) ) : '';
		$target = is_array( $link ) && '_blank' === ( $link['target'] ?? '' ) ? '_blank' : '';

		if ( '' === $label || '' === $url || '' === esc_url( $url ) ) {
			continue;
		}

		$links[] = array( 'label' => $label, 'url' => $url, 'target' => $target );
	}

	return $links;
}
