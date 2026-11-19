<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Media Center content types. Each has its own category taxonomy. */
function balt_media_types(): array {
	return array(
		'balt_campaign' => array( 'singular' => __( 'Campaign', 'baltimore' ), 'plural' => __( 'Campaigns', 'baltimore' ), 'slug' => 'campaigns', 'icon' => 'dashicons-megaphone' ),
		'balt_event'    => array( 'singular' => __( 'Event', 'baltimore' ), 'plural' => __( 'Events', 'baltimore' ), 'slug' => 'events', 'icon' => 'dashicons-calendar-alt' ),
		'balt_podcast'  => array( 'singular' => __( 'Podcast', 'baltimore' ), 'plural' => __( 'Podcasts', 'baltimore' ), 'slug' => 'podcasts', 'icon' => 'dashicons-microphone' ),
		'balt_video'    => array( 'singular' => __( 'Video', 'baltimore' ), 'plural' => __( 'Videos', 'baltimore' ), 'slug' => 'videos', 'icon' => 'dashicons-video-alt3' ),
		'balt_press'    => array( 'singular' => __( 'Press Release', 'baltimore' ), 'plural' => __( 'Press', 'baltimore' ), 'slug' => 'press', 'icon' => 'dashicons-media-document' ),
		'balt_letter'   => array( 'singular' => __( 'Letter', 'baltimore' ), 'plural' => __( 'Letters', 'baltimore' ), 'slug' => 'letters', 'icon' => 'dashicons-email-alt' ),
		'balt_homily'   => array( 'singular' => __( 'Homily', 'baltimore' ), 'plural' => __( 'Homilies', 'baltimore' ), 'slug' => 'homilies', 'icon' => 'dashicons-book-alt' ),
	);
}

function balt_register_media_post_types(): void {
	foreach ( balt_media_types() as $type => $data ) {
		$singular = $data['singular'];
		$plural   = $data['plural'];

		register_post_type(
			$type,
			array(
				'labels' => array(
					'name'               => $plural,
					'singular_name'      => $singular,
					'add_new_item'       => sprintf( __( 'Add New %s', 'baltimore' ), $singular ),
					'edit_item'          => sprintf( __( 'Edit %s', 'baltimore' ), $singular ),
					'all_items'          => sprintf( __( 'All %s', 'baltimore' ), $plural ),
					'search_items'       => sprintf( __( 'Search %s', 'baltimore' ), $plural ),
					'not_found'          => __( 'No items found.', 'baltimore' ),
					'not_found_in_trash' => __( 'No items found in Trash.', 'baltimore' ),
				),
				'public'       => true,
				'has_archive'  => true,
				'rewrite'      => array( 'slug' => $data['slug'], 'with_front' => false ),
				'show_in_rest' => true,
				'menu_icon'    => $data['icon'],
				'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions' ),
			)
		);

		register_taxonomy(
			$type . '_category',
			$type,
			array(
				'labels' => array(
					'name'          => sprintf( __( '%s Categories', 'baltimore' ), $plural ),
					'singular_name' => __( 'Category', 'baltimore' ),
				),
				'public'            => true,
				'hierarchical'      => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rewrite'           => array( 'slug' => $data['slug'] . '-category', 'with_front' => false ),
			)
		);
	}
}
add_action( 'init', 'balt_register_media_post_types', 0 );
