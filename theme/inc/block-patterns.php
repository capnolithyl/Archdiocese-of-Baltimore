<?php
/**
 * Register Baltimore's reusable patterns explicitly, so newly deployed
 * pattern files are available even with a cached theme pattern inventory.
 *
 * @package Archdiocese_of_Baltimore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_register_block_patterns(): void {
	register_block_pattern_category(
		'baltimore',
		array( 'label' => __( 'Baltimore', 'baltimore' ) )
	);

	$slug = 'baltimore/media-text-section';
	$file = get_template_directory() . '/patterns/media-text-section.php';

	if ( ! is_readable( $file ) || WP_Block_Patterns_Registry::get_instance()->is_registered( $slug ) ) {
		return;
	}

	ob_start();
	include $file;
	$content = ob_get_clean();

	register_block_pattern(
		$slug,
		array(
			'title'       => __( 'Media & Text Section', 'baltimore' ),
			'description' => __( 'Wide image and text section with an eyebrow, heading, supporting copy and buttons.', 'baltimore' ),
			'categories'  => array( 'baltimore', 'text' ),
			'keywords'    => array( 'media', 'image', 'text', 'mission' ),
			'inserter'    => true,
			'content'     => $content,
		)
	);
}
// Run before core's theme pattern discovery; core skips already registered slugs.
add_action( 'init', 'balt_register_block_patterns', 9 );
