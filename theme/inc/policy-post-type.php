<?php
/**
 * Canonical policy records and policy-only search.
 *
 * @package Archdiocese_of_Baltimore
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_register_policy_post_type(): void {
	register_post_type(
		'balt_policy',
		array(
			'labels' => array(
				'name'          => __( 'Policies', 'baltimore' ),
				'singular_name' => __( 'Policy', 'baltimore' ),
				'add_new_item'  => __( 'Add Policy', 'baltimore' ),
				'edit_item'     => __( 'Edit Policy', 'baltimore' ),
				'all_items'     => __( 'All Policies', 'baltimore' ),
				'search_items'  => __( 'Search Policies', 'baltimore' ),
			),
			'public'              => true,
			'hierarchical'        => true,
			'exclude_from_search' => false,
			'has_archive'         => false,
			'rewrite'             => array( 'slug' => 'policy', 'with_front' => false ),
			'show_in_rest'        => true,
			'menu_icon'           => 'dashicons-media-document',
			'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
		)
	);
	register_taxonomy(
		'balt_policy_category',
		'balt_policy',
		array(
			'labels' => array(
				'name'          => __( 'Policy Categories', 'baltimore' ),
				'singular_name' => __( 'Policy Category', 'baltimore' ),
				'add_new_item'  => __( 'Add Policy Category', 'baltimore' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'policy-category', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'balt_register_policy_post_type', 0 );

/** A separate post type query keeps general site search unchanged. */
function balt_policy_search_query( WP_Query $query ): void {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() || 'balt_policy' !== $query->get( 'post_type' ) ) {
		return;
	}
	$query->set( 'post_status', 'publish' );
	$query->set( 'has_password', false );
}
add_action( 'pre_get_posts', 'balt_policy_search_query', 20 );


/** Stable ordering for numbered policies; use native Order to override title order. */
function balt_policy_category_posts( int $term_id ): array {
	if ( ! $term_id ) {
		return array();
	}
	return get_posts( array(
		'post_type' => 'balt_policy',
		'post_status' => 'publish',
		'has_password' => false,
		'posts_per_page' => -1,
		'orderby' => array( 'menu_order' => 'ASC', 'title' => 'ASC', 'ID' => 'ASC' ),
		'tax_query' => array( array(
			'taxonomy' => 'balt_policy_category',
			'field' => 'term_id',
			'terms' => $term_id,
			'include_children' => true,
		) ),
	) );
}

/** Group only records visible within this category. Missing parents become roots. */
function balt_policy_groups( array $posts ): array {
	$valid = array();
	foreach ( $posts as $post ) {
		if ( 'balt_policy' === $post->post_type && 'publish' === $post->post_status && '' === $post->post_password ) {
			$valid[ $post->ID ] = $post;
		}
	}
	$children = array();
	$roots = array();
	foreach ( $valid as $post ) {
		$parent = (int) $post->post_parent;
		if ( $parent !== (int) $post->ID && isset( $valid[ $parent ] ) ) {
			$children[ $parent ][] = $post;
		} else {
			$roots[] = $post;
		}
	}
	$seen = array();
	$walk = static function ( $post ) use ( &$walk, &$seen, $children ): array {
		if ( isset( $seen[ $post->ID ] ) ) {
			return array();
		}
		$seen[ $post->ID ] = true;
		$rows = array( $post );
		foreach ( $children[ $post->ID ] ?? array() as $child ) {
			$rows = array_merge( $rows, $walk( $child ) );
		}
		return $rows;
	};
	$groups = array();
	foreach ( array_merge( $roots, array_values( $valid ) ) as $root ) {
		if ( isset( $seen[ $root->ID ] ) ) {
			continue;
		}
		$rows = $walk( $root );
		$groups[] = array( 'parent' => array_shift( $rows ), 'children' => $rows );
	}
	return $groups;
}

/** Use a deterministic assigned category; editors should assign one category per policy. */
function balt_policy_term( int $post_id ): ?WP_Term {
	$terms = wp_get_object_terms( $post_id, 'balt_policy_category', array( 'orderby' => 'term_id', 'order' => 'ASC' ) );
	if ( is_wp_error( $terms ) || ! $terms ) {
		return null;
	}
	return $terms[0] instanceof WP_Term ? $terms[0] : null;
}

/** Link only to an actual published, unprotected Gutenberg landing page. */
function balt_policy_directory_url( ?WP_Term $term = null ): string {
	$id = $term && function_exists( 'get_field' ) ? absint( get_field( 'balt_policy_directory_page', $term ) ) : 0;
	$pages = array( $id ? get_post( $id ) : null );
	foreach ( array( 'policies', 'archdiocese-policies', 'policy-library' ) as $slug ) {
		$pages[] = get_page_by_path( $slug );
	}
	foreach ( $pages as $page ) {
		if ( $page && 'page' === $page->post_type && 'publish' === $page->post_status && '' === $page->post_password ) {
			$url = get_permalink( $page );
			if ( $url ) {
				return (string) $url;
			}
		}
	}
	// Until the landing page exists, do not generate a dead archive link.
	return '';
}

function balt_policy_term_image( ?WP_Term $term ): int {
	if ( ! $term || ! function_exists( 'get_field' ) ) {
		return 0;
	}
	$id = absint( get_field( 'balt_policy_category_image', $term ) );
	if ( ! $id && $term->parent ) {
		$parent = get_term( $term->parent, 'balt_policy_category' );
		if ( $parent instanceof WP_Term ) {
			return balt_policy_term_image( $parent );
		}
	}
	return $id;
}

/** Top-level categories supply the existing Content Card Grid. */
function balt_policy_category_cards( array $ids = array() ): array {
	$args = array( 'taxonomy' => 'balt_policy_category', 'hide_empty' => false, 'parent' => 0, 'orderby' => 'name', 'order' => 'ASC' );
	if ( $ids ) {
		$args['include'] = array_map( 'absint', $ids );
		$args['orderby'] = 'include';
		unset( $args['parent'] );
	}
	$terms = get_terms( $args );
	if ( is_wp_error( $terms ) ) {
		return array();
	}
	$cards = array();
	foreach ( $terms as $term ) {
		$url = get_term_link( $term );
		if ( is_wp_error( $url ) ) {
			continue;
		}
		$cards[] = array(
			'title' => $term->name,
			'excerpt' => wp_strip_all_tags( $term->description ),
			'image_id' => balt_policy_term_image( $term ),
			'link' => $url,
		);
	}
	return $cards;
}

function balt_policy_resources( int $id ): array {
	$rows = function_exists( 'get_field' ) ? (array) get_field( 'balt_policy_resources', $id ) : array();
	$links = array();
	foreach ( $rows as $row ) {
		$link = is_array( $row['link'] ?? null ) ? $row['link'] : array();
		$url = esc_url( $link['url'] ?? '' );
		$title = trim( (string) ( $link['title'] ?? '' ) );
		if ( $url && $title ) {
			$links[] = array( 'url' => $url, 'title' => $title, 'target' => '_blank' === ( $link['target'] ?? '' ) ? '_blank' : '' );
		}
	}
	return $links;
}

/** Category archives are a complete index, not paginated fragments. */
function balt_policy_archive_query( WP_Query $query ): void {
	if ( ! is_admin() && $query->is_main_query() && $query->is_tax( 'balt_policy_category' ) && ! $query->is_search() ) {
		$query->set( 'posts_per_page', -1 );
		$query->set( 'post_status', 'publish' );
		$query->set( 'has_password', false );
		$query->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC', 'ID' => 'ASC' ) );
	}
}
add_action( 'pre_get_posts', 'balt_policy_archive_query', 20 );
