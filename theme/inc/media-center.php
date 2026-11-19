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
/**
 * Selectable Media Center sources: public posts/CPTs or their hierarchical categories.
 * Entries use a stable post type and taxonomy/term ID to avoid ambiguous term names.
 */
function balt_media_center_source_choices(): array {
	static $choices = null;
	if ( null !== $choices ) {
		return $choices;
	}

	$choices = array();
	foreach ( get_post_types( array( 'public' => true ), 'objects' ) as $type => $object ) {
		if ( in_array( $type, array( 'attachment', 'page' ), true ) ) {
			continue;
		}

		$plural = $object->labels->name;
		$choices[ 'type:' . $type ] = sprintf( __( '%s (all)', 'baltimore' ), $plural );

		foreach ( get_object_taxonomies( $type, 'objects' ) as $taxonomy => $taxonomy_object ) {
			if ( ! $taxonomy_object->public || ! $taxonomy_object->hierarchical ) {
				continue;
			}

			$terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false ) );
			if ( is_wp_error( $terms ) ) {
				continue;
			}
			foreach ( $terms as $term ) {
				$choices[ 'term:' . $type . ':' . $taxonomy . ':' . $term->term_id ] = sprintf( '%s / %s', $plural, $term->name );
			}
		}
	}

	return $choices;
}

/** Populate the ACF repeater's searchable select from currently registered content. */
function balt_media_center_load_source_field( array $field ): array {
	$field['choices'] = balt_media_center_source_choices();
	return $field;
}
add_filter( 'acf/load_field/key=field_balt_media_center_tab_source', 'balt_media_center_load_source_field' );

/** Validate a saved tab source and resolve its label, query scope and archive link. */
function balt_media_center_resolve_source( string $source ): ?array {
	$parts = explode( ':', $source );
	if ( ! in_array( $parts[0] ?? '', array( 'type', 'term' ), true ) ) {
		return null;
	}

	$is_term = 'term' === $parts[0];
	if ( count( $parts ) !== ( $is_term ? 4 : 2 ) ) {
		return null;
	}

	$type = $parts[1];
	$object = get_post_type_object( $type );
	if ( ! $object || ! $object->public || in_array( $type, array( 'attachment', 'page' ), true ) ) {
		return null;
	}

	$details = array(
		'post_type'  => $type,
		'type_label' => $object->labels->singular_name,
		'label'      => $object->labels->name,
		'archive_url' => get_post_type_archive_link( $type ),
		'taxonomy'   => '',
		'term_id'    => 0,
	);
	if ( ! $is_term ) {
		return $details;
	}

	$taxonomy = $parts[2];
	$id       = $parts[3];
	if ( ! ctype_digit( $id ) || 0 === (int) $id || ! is_object_in_taxonomy( $type, $taxonomy ) ) {
		return null;
	}

	$tax_object = get_taxonomy( $taxonomy );
	if ( ! $tax_object || ! $tax_object->public || ! $tax_object->hierarchical ) {
		return null;
	}

	$term = get_term( (int) $id, $taxonomy );
	if ( ! $term instanceof WP_Term ) {
		return null;
	}

	$link = get_term_link( $term );
	$details['label']       = $term->name;
	$details['archive_url'] = is_wp_error( $link ) ? false : $link;
	$details['taxonomy']    = $taxonomy;
	$details['term_id']     = $term->term_id;
	return $details;
}

