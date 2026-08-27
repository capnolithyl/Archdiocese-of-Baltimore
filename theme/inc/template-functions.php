<?php
/**
 * Functions which enhance the theme by hooking into WordPress.
 *
 * @package Archdiocese_of_Baltimore
 */

/**
 * Keep archive titles clean and let the template provide the archive context
 * separately as an eyebrow label.
 *
 * @return string
 */
function balt_get_the_archive_title() {
	if ( is_category() || is_tag() || is_tax() ) {
		return single_term_title( '', false );
	}

	if ( is_author() ) {
		return get_the_author_meta( 'display_name' );
	}

	if ( is_year() ) {
		return get_the_date( _x( 'Y', 'yearly archives date format', 'baltimore' ) );
	}

	if ( is_month() ) {
		return get_the_date( _x( 'F Y', 'monthly archives date format', 'baltimore' ) );
	}

	if ( is_day() ) {
		return get_the_date();
	}

	if ( is_post_type_archive() ) {
		return post_type_archive_title( '', false );
	}

	return __( 'Archives', 'baltimore' );
}
add_filter( 'get_the_archive_title', 'balt_get_the_archive_title' );

/**
 * Return a short archive-context label for the visual eyebrow treatment.
 *
 * @return string
 */
function balt_get_archive_eyebrow() {
	if ( is_category() ) {
		return __( 'Category', 'baltimore' );
	}

	if ( is_tag() ) {
		return __( 'Topic', 'baltimore' );
	}

	if ( is_tax() ) {
		$taxonomy = get_taxonomy( get_queried_object()->taxonomy ?? '' );
		return $taxonomy ? $taxonomy->labels->singular_name : __( 'Archive', 'baltimore' );
	}

	if ( is_author() ) {
		return __( 'Author', 'baltimore' );
	}

	if ( is_date() ) {
		return __( 'Archive', 'baltimore' );
	}

	if ( is_post_type_archive() ) {
		$post_type = get_post_type_object( get_queried_object()->name ?? '' );
		return $post_type ? $post_type->labels->name : __( 'Archive', 'baltimore' );
	}

	return __( 'Archive', 'baltimore' );
}

/**
 * Return the configured global fallback featured image attachment ID.
 *
 * @return int
 */
function balt_get_default_featured_image_id() {
	if ( ! function_exists( 'get_field' ) ) {
		return 0;
	}

	return absint( get_field( 'balt_default_featured_image', 'option' ) );
}

/**
 * Return a post's featured image, optionally falling back to Theme Settings.
 *
 * @param int  $post_id        Post ID. Defaults to the current post.
 * @param bool $allow_fallback Whether to use the global fallback image.
 * @return int
 */
function balt_get_featured_image_id( $post_id = 0, $allow_fallback = true ) {
	$post_id  = $post_id ? absint( $post_id ) : get_the_ID();
	$image_id = $post_id ? get_post_thumbnail_id( $post_id ) : 0;

	if ( ! $image_id && $allow_fallback ) {
		$image_id = balt_get_default_featured_image_id();
	}

	return absint( $image_id );
}

/**
 * Return a human-readable content type label for cards and single headers.
 *
 * @param int $post_id Post ID. Defaults to the current post.
 * @return string
 */
function balt_get_content_type_label( $post_id = 0 ) {
	$post_id   = $post_id ? absint( $post_id ) : get_the_ID();
	$post_type = $post_id ? get_post_type( $post_id ) : '';
	$object    = $post_type ? get_post_type_object( $post_type ) : null;

	return $object ? $object->labels->singular_name : __( 'Content', 'baltimore' );
}

/**
 * Determines whether the post thumbnail can be displayed.
 */
function balt_can_show_post_thumbnail() {
	return apply_filters( 'balt_can_show_post_thumbnail', ! post_password_required() && ! is_attachment() && has_post_thumbnail() );
}

/**
 * Create the continue reading link.
 *
 * @param string $more_string The string shown within the more link.
 */
function balt_continue_reading_link( $more_string ) {
	if ( ! is_admin() ) {
		$continue_reading = sprintf(
			/* translators: %s: Name of current post. */
			wp_kses( __( 'Continue reading %s', 'baltimore' ), array( 'span' => array( 'class' => array() ) ) ),
			the_title( '<span class="sr-only">"', '"</span>', false )
		);

		$more_string = '<a href="' . esc_url( get_permalink() ) . '">' . $continue_reading . '</a>';
	}

	return $more_string;
}

// Filter the excerpt more link.
add_filter( 'excerpt_more', 'balt_continue_reading_link' );

// Filter the content more link.
add_filter( 'the_content_more_link', 'balt_continue_reading_link' );
