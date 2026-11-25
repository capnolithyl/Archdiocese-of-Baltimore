<?php
/**
 * Reusable editorial text styles for the native WordPress Paragraph block.
 *
 * @package Archdiocese_of_Baltimore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_register_editorial_block_styles(): void {
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'balt-eyebrow',
			'label' => __( 'Eyebrow', 'baltimore' ),
		)
	);
	register_block_style(
		'core/paragraph',
		array(
			'name'  => 'balt-lead',
			'label' => __( 'Lead', 'baltimore' ),
		)
	);
}
add_action( 'init', 'balt_register_editorial_block_styles' );
