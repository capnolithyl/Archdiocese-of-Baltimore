<?php
/**
 * Data sources for the reusable Content Card Grid.
 * This intentionally does not depend on RSS or modify the global post loop.
 *
 * @package Archdiocese_of_Baltimore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_register_content_card_grid_block(): void {
	register_block_type( get_template_directory() . '/blocks/content-card-grid' );
}
add_action( 'init', 'balt_register_content_card_grid_block', 5 );

function balt_render_content_card_grid_block( $block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = null ): void {
	require get_template_directory() . '/blocks/content-card-grid/render.php';
}

/** Public, viewable WordPress content types supported by the grid. */
function balt_content_grid_post_types(): array {
	return array_values( array_filter( get_post_types( array( 'public' => true ), 'names' ), static fn( $type ) => 'attachment' !== $type && is_post_type_viewable( $type ) ) );
}

/** Populate the latest-content type selector from registered WP post types. */
function balt_content_grid_post_type_choices( array $field ): array {
	$field['choices'] = array();
	foreach ( balt_content_grid_post_types() as $type ) {
		$object = get_post_type_object( $type );
		$field['choices'][ $type ] = $object->labels->singular_name;
	}
	return $field;
}
add_filter( 'acf/load_field/key=field_balt_card_grid_post_type', 'balt_content_grid_post_type_choices' );

/** Normalize manually managed rows to the shared display shape. */
function balt_content_grid_manual_cards( array $rows ): array {
	$cards = array();
	foreach ( array_slice( $rows, 0, 12 ) as $row ) {
		if ( ! is_array( $row ) ) {
			continue;
		}
		$title = trim( (string) ( $row['title'] ?? '' ) );
		if ( '' === $title ) {
			continue;
		}
		$link = is_array( $row['link'] ?? null ) ? $row['link'] : array();
		$cards[] = array(
			'title'      => $title,
			'excerpt'    => trim( (string) ( $row['description'] ?? '' ) ),
			'image_id'   => absint( $row['image'] ?? 0 ),
			'link'       => esc_url_raw( $link['url'] ?? '' ),
			'link_label' => trim( (string) ( $link['title'] ?? '' ) ),
			'target'     => '_blank' === ( $link['target'] ?? '' ) ? '_blank' : '',
			'meta'       => trim( (string) ( $row['meta'] ?? '' ) ),
		);
	}
	return $cards;
}

/** Resolve selected or queried records without exposing private/password-protected posts. */
function balt_content_grid_post_cards( array $records, bool $show_date ): array {
	$cards = array();
	$seen = array();
	$types = balt_content_grid_post_types();
	foreach ( array_slice( $records, 0, 12 ) as $record ) {
		$post = get_post( $record );
		if ( ! $post || 'publish' !== $post->post_status || '' !== $post->post_password || ! in_array( $post->post_type, $types, true ) || isset( $seen[ $post->ID ] ) ) {
			continue;
		}
		$url = get_permalink( $post );
		if ( ! $url ) {
			continue;
		}
		$seen[ $post->ID ] = true;
		$cards[] = array(
			'title'      => get_the_title( $post ),
			'excerpt'    => wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), 24, '…' ),
			'image_id'   => balt_get_featured_image_id( $post->ID ),
			'link'       => $url,
			'date'       => $show_date ? (int) get_post_timestamp( $post ) : 0,
		);
	}
	return $cards;
}

/** Resolve latest published records; category is available for standard posts. */
function balt_content_grid_latest_cards( string $type, int $limit, int $category, bool $show_date ): array {
	if ( ! in_array( $type, balt_content_grid_post_types(), true ) ) {
		return array();
	}
	$query = array(
		'post_type'           => $type,
		'post_status'         => 'publish',
		'posts_per_page'      => max( 1, min( 12, $limit ) ),
		'orderby'             => 'date',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
		'has_password'        => false,
	);
	if ( 'post' === $type && $category > 0 ) {
		$query['cat'] = $category;
	}
	return balt_content_grid_post_cards( get_posts( $query ), $show_date );
}
