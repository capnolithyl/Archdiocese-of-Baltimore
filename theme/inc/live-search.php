<?php
/**
 * WordPress public live-search suggestions.
 *
 * Only published, non-passworded content in searchable public post types is
 * returned. The existing WordPress search.php remains the full-results fallback.
 *
 * @package Archdiocese_of_Baltimore
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Describe the item's location within its content type and public taxonomy. */
function balt_live_search_context( WP_Post $post ): string {
	$type    = $post->post_type;
	$object  = get_post_type_object( $type );
	$trail   = array( 'post' === $type ? __( 'News & Events', 'baltimore' ) : ( $object->labels->name ?? __( 'Pages', 'baltimore' ) ) );

	if ( 'page' === $type ) {
		foreach ( array_slice( array_reverse( get_post_ancestors( $post ) ), -2 ) as $parent_id ) {
			$trail[] = get_the_title( $parent_id );
		}
	} else {
		foreach ( get_object_taxonomies( $type, 'objects' ) as $taxonomy ) {
			if ( ! $taxonomy->public || ! $taxonomy->hierarchical ) {
				continue;
			}
			$terms = get_the_terms( $post->ID, $taxonomy->name );
			if ( ! is_array( $terms ) || ! $terms ) {
				continue;
			}
			$term = reset( $terms );
			foreach ( array_slice( array_reverse( get_ancestors( $term->term_id, $taxonomy->name, 'taxonomy' ) ), -1 ) as $ancestor_id ) {
				$ancestor = get_term( $ancestor_id, $taxonomy->name );
				if ( $ancestor instanceof WP_Term ) {
					$trail[] = $ancestor->name;
				}
			}
			$trail[] = $term->name;
			break;
		}
	}

	return implode( ' › ', array_map( 'wp_strip_all_tags', array_filter( $trail ) ) );
}

function balt_live_search_rest( WP_REST_Request $request ): WP_REST_Response {
	$query = trim( sanitize_text_field( (string) $request->get_param( 'q' ) ) );
	if ( strlen( $query ) < 2 ) {
		return rest_ensure_response( array( 'results' => array() ) );
	}

	$types = get_post_types( array( 'public' => true, 'exclude_from_search' => false ), 'names' );
	unset( $types['attachment'] );
	if ( ! $types ) {
		return rest_ensure_response( array( 'results' => array() ) );
	}

	$search = new WP_Query(
		array(
			'post_type'              => array_values( $types ),
			'post_status'            => 'publish',
			'has_password'           => false,
			'posts_per_page'         => 8,
			's'                      => $query,
			'orderby'                => 'relevance',
			'order'                  => 'DESC',
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
		)
	);
	$results = array();
	foreach ( $search->posts as $post ) {
		$url = get_permalink( $post );
		if ( $url ) {
			$results[] = array(
				'title'   => wp_specialchars_decode( wp_strip_all_tags( get_the_title( $post ) ), ENT_QUOTES ),
				'url'     => esc_url_raw( $url ),
				'context' => balt_live_search_context( $post ),
			);
		}
	}
	wp_reset_postdata();
	return rest_ensure_response( array( 'results' => $results ) );
}

function balt_register_live_search_route(): void {
	register_rest_route(
		'balt/v1',
		'/live-search',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'balt_live_search_rest',
			'permission_callback' => '__return_true',
			'args'                => array(
				'q' => array(
					'required'          => true,
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_text_field',
					'validate_callback' => static function ( $value ) {
						return is_string( $value ) && strlen( trim( $value ) ) <= 100;
					},
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'balt_register_live_search_route' );

function balt_enqueue_live_search_script(): void {
	wp_enqueue_script(
		'balt-live-search',
		get_template_directory_uri() . '/js/live-search.js',
		array(),
		BALT_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'balt_enqueue_live_search_script' );
