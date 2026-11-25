<?php
/**
 * Extend the native Accordion block without changing its saved markup or
 * WordPress Interactivity API behavior.
 *
 * @package Archdiocese_of_Baltimore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_accordion_attributes( array $args, string $name ): array {
	if ( 'core/accordion' !== $name ) {
		return $args;
	}
	$args['attributes']['baltEyebrow'] = array( 'type' => 'string', 'default' => '' );
	$args['attributes']['baltHeading'] = array( 'type' => 'string', 'default' => '' );
	return $args;
}
add_filter( 'register_block_type_args', 'balt_accordion_attributes', 10, 2 );

/** The native accordion's HTML, including its directives and anchors, is unchanged. */
function balt_render_accordion_section( string $html, array $block ): string {
	if ( '' === trim( $html ) ) {
		return $html;
	}
	$attrs = $block['attrs'] ?? array();
	$eyebrow = is_string( $attrs['baltEyebrow'] ?? null ) ? trim( $attrs['baltEyebrow'] ) : '';
	$heading = is_string( $attrs['baltHeading'] ?? null ) ? trim( $attrs['baltHeading'] ) : '';
	$header = '';
	$heading_id = '';
	if ( '' !== $eyebrow || '' !== $heading ) {
		$header = '<header class="balt-accordion-section__header">';
		if ( '' !== $eyebrow ) {
			$header .= '<p class="balt-accordion-section__eyebrow">' . esc_html( $eyebrow ) . '</p>';
		}
		if ( '' !== $heading ) {
			$heading_id = wp_unique_id( 'balt-accordion-title-' );
			$header .= '<h2 class="balt-accordion-section__heading" id="' . esc_attr( $heading_id ) . '">' . esc_html( $heading ) . '</h2>';
		}
		$header .= '</header>';
	}
	wp_enqueue_script( 'balt-accordion-motion', get_template_directory_uri() . '/assets/js/accordion-motion.js', array(), BALT_VERSION, true );
	// A named section is useful to assistive technology; no empty landmark.
	$tag = $heading_id ? 'section' : 'div';
	$label = $heading_id ? ' aria-labelledby="' . esc_attr( $heading_id ) . '"' : '';
	return '<' . $tag . ' class="balt-accordion-section balt-full-bleed"' . $label . '><div class="balt-accordion-section__inner">' . $header . $html . '</div></' . $tag . '>';
}
add_filter( 'render_block_core/accordion', 'balt_render_accordion_section', 10, 2 );
