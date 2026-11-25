<?php
/**
 * Central staff records, separate from the manually editable Staff Grid.
 *
 * @package Archdiocese_of_Baltimore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_register_staff_post_type(): void {
	register_post_type(
		'balt_staff',
		array(
			'labels' => array(
				'name'          => __( 'Staff', 'baltimore' ),
				'singular_name' => __( 'Staff Member', 'baltimore' ),
				'add_new_item'  => __( 'Add Staff Member', 'baltimore' ),
				'edit_item'     => __( 'Edit Staff Member', 'baltimore' ),
				'all_items'     => __( 'All Staff', 'baltimore' ),
				'search_items'  => __( 'Search Staff', 'baltimore' ),
			),
			'public'       => true,
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'staff', 'with_front' => false ),
			'show_in_rest' => true,
			'menu_icon'    => 'dashicons-groups',
			'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
		)
	);

	register_taxonomy(
		'balt_staff_department',
		'balt_staff',
		array(
			'labels' => array(
				'name'          => __( 'Departments', 'baltimore' ),
				'singular_name' => __( 'Department', 'baltimore' ),
				'add_new_item'  => __( 'Add Department', 'baltimore' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'staff-department', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'balt_register_staff_post_type', 0 );

/** One canonical data shape for selected staff and a future directory. */
function balt_staff_member_data( int $id ): ?array {
	if ( $id <= 0 ) {
		return null;
	}
	$post = get_post( $id );
	if ( ! $post || 'balt_staff' !== $post->post_type || 'publish' !== $post->post_status || '' !== $post->post_password ) {
		return null;
	}
	$name = trim( (string) get_the_title( $post ) );
	if ( '' === $name ) {
		return null;
	}
	return array(
		'name'  => $name,
		'role'  => trim( (string) get_post_meta( $id, 'balt_staff_role', true ) ),
		'photo' => (int) get_post_thumbnail_id( $id ),
	);
}

/** Preserve old manual rows; explicitly selected records never fall back to stale manual fields. */
function balt_staff_grid_member_data( array $row ): ?array {
	if ( 'staff' === ( $row['source'] ?? 'custom' ) ) {
		return balt_staff_member_data( absint( $row['staff_member'] ?? 0 ) );
	}
	$name = trim( (string) ( $row['name'] ?? '' ) );
	if ( '' === $name ) {
		return null;
	}
	return array(
		'name'  => $name,
		'role'  => trim( (string) ( $row['role'] ?? '' ) ),
		'photo' => absint( $row['photo'] ?? 0 ),
	);
}

/** Restrict the editor's staff picker as well as the public renderer. */
function balt_staff_picker_query( array $args ): array {
	$args['post_type'] = 'balt_staff';
	$args['post_status'] = 'publish';
	$args['has_password'] = false;
	return $args;
}
add_filter( 'acf/fields/post_object/query/key=field_balt_staff_grid_member_staff', 'balt_staff_picker_query' );
