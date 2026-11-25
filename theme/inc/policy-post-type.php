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
			'exclude_from_search' => false,
			'has_archive'         => 'policy-library',
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
