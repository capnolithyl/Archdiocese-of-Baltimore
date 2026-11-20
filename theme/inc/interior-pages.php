<?php
/**
 * Shared configuration for reusable interior page templates.
 *
 * @package Archdiocese_of_Baltimore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Which page templates own the shared interior hero/section navigation? */
function balt_is_interior_page_template( int $post_id ): bool {
	$template = get_page_template_slug( $post_id );

	return in_array(
		$template,
		array( 'page-templates/interior-featured.php', 'page-templates/interior-title.php' ),
		true
	);
}

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
