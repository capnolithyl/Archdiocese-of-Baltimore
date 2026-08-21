<?php
/**
 * Disable WordPress comments, trackbacks, and pingbacks.
 *
 * Existing database records are preserved. This module only prevents them from
 * being exposed or used while the theme is active.
 *
 * @package Archdiocese_of_Baltimore
 */

/**
 * Remove comment and trackback support from a registered post type.
 *
 * @param string $post_type Post type name.
 */
function balt_disable_comments_registered_post_type_support( $post_type ) {
	remove_post_type_support( $post_type, 'comments' );
	remove_post_type_support( $post_type, 'trackbacks' );
}
add_action( 'registered_post_type', 'balt_disable_comments_registered_post_type_support' );

/**
 * Remove discussion support from post types registered before the theme loads.
 */
function balt_disable_comments_post_type_support() {
	foreach ( get_post_types() as $post_type ) {
		balt_disable_comments_registered_post_type_support( $post_type );
	}
}
add_action( 'init', 'balt_disable_comments_post_type_support', 100 );

/**
 * Return a closed status for default discussion settings.
 *
 * @return string
 */
function balt_disable_comments_default_status() {
	return 'closed';
}
add_filter( 'pre_option_default_comment_status', 'balt_disable_comments_default_status' );
add_filter( 'pre_option_default_ping_status', 'balt_disable_comments_default_status' );

/**
 * Ensure new or updated content is stored with discussion disabled.
 *
 * @param array $data Prepared post data.
 * @return array
 */
function balt_disable_comments_post_data( $data ) {
	$data['comment_status'] = 'closed';
	$data['ping_status']    = 'closed';

	return $data;
}
add_filter( 'wp_insert_post_data', 'balt_disable_comments_post_data' );

/**
 * Hide all existing comment query results.
 *
 * @return array
 */
function balt_disable_comments_query_results() {
	return array();
}
add_filter( 'comments_array', 'balt_disable_comments_query_results' );
add_filter( 'comments_pre_query', 'balt_disable_comments_query_results' );
add_filter( 'get_comments_number', '__return_zero' );
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );

/**
 * Remove comment administration screens from the menu.
 */
function balt_disable_comments_admin_menu() {
	remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}
add_action( 'admin_menu', 'balt_disable_comments_admin_menu', 999 );

/**
 * Prevent direct access to comment administration screens.
 */
function balt_disable_comments_admin_redirect() {
	global $pagenow;

	$comment_screens = array(
		'comment.php',
		'edit-comments.php',
		'options-discussion.php',
	);

	if ( in_array( $pagenow, $comment_screens, true ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'admin_init', 'balt_disable_comments_admin_redirect' );

/**
 * Remove the legacy recent-comments dashboard widget.
 */
function balt_disable_comments_dashboard_widget() {
	remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
}
add_action( 'wp_dashboard_setup', 'balt_disable_comments_dashboard_widget' );

/**
 * Remove the comments shortcut from the admin toolbar.
 *
 * @param WP_Admin_Bar $admin_bar Admin toolbar instance.
 */
function balt_disable_comments_admin_bar( $admin_bar ) {
	$admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'balt_disable_comments_admin_bar', 999 );

/**
 * Remove comment routes from the REST API index.
 *
 * @param array $endpoints Registered REST API endpoints.
 * @return array
 */
function balt_disable_comments_rest_endpoints( $endpoints ) {
	unset( $endpoints['/wp/v2/comments'] );
	unset( $endpoints['/wp/v2/comments/(?P<id>[\d]+)'] );

	return $endpoints;
}
add_filter( 'rest_endpoints', 'balt_disable_comments_rest_endpoints' );

/**
 * Remove comment and pingback methods from XML-RPC.
 *
 * @param array $methods Registered XML-RPC methods.
 * @return array
 */
function balt_disable_comments_xmlrpc_methods( $methods ) {
	$disabled_methods = array(
		'pingback.extensions.getPingbacks',
		'pingback.ping',
		'wp.deleteComment',
		'wp.editComment',
		'wp.getComment',
		'wp.getCommentCount',
		'wp.getComments',
		'wp.getCommentStatusList',
		'wp.newComment',
	);

	foreach ( $disabled_methods as $method ) {
		unset( $methods[ $method ] );
	}

	return $methods;
}
add_filter( 'xmlrpc_methods', 'balt_disable_comments_xmlrpc_methods' );

/**
 * Remove the pingback response header.
 *
 * @param array $headers HTTP response headers.
 * @return array
 */
function balt_disable_comments_pingback_header( $headers ) {
	unset( $headers['X-Pingback'] );

	return $headers;
}
add_filter( 'wp_headers', 'balt_disable_comments_pingback_header' );

/**
 * Return a 404 response for comment feed URLs.
 */
function balt_disable_comments_feed() {
	if ( ! is_comment_feed() ) {
		return;
	}

	global $wp_query;

	$wp_query->set_404();
	status_header( 404 );
	nocache_headers();
	include get_404_template();
	exit;
}
add_action( 'template_redirect', 'balt_disable_comments_feed' );

add_filter( 'feed_links_show_comments_feed', '__return_false' );
add_filter( 'feed_links_extra_show_post_comments_feed', '__return_false' );
