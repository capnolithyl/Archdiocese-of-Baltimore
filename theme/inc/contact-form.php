<?php
/**
 * Contact & Form block, using Gravity Forms' own rendering and submission.
 *
 * @package Archdiocese_of_Baltimore
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function balt_register_contact_form_block(): void {
	register_block_type( get_template_directory() . '/blocks/contact-form' );
}
add_action( 'init', 'balt_register_contact_form_block', 5 );

function balt_render_contact_form_block( $block, $content = '', $is_preview = false, $post_id = 0, $wp_block = null, $context = null ): void {
	require get_template_directory() . '/blocks/contact-form/render.php';
}

/** Accept the add-on's ID or form-array return, including raw stored values. */
function balt_contact_form_id( $value ): int {
	if ( is_array( $value ) ) {
		$value = $value['id'] ?? ( $value[0] ?? 0 );
	}
	if ( is_object( $value ) ) {
		$value = $value->id ?? 0;
	}
	return is_numeric( $value ) && (int) $value > 0 ? (int) $value : 0;
}

function balt_contact_form_ajax( $value ): bool {
	// A missing value (legacy/unconfigured block) uses the default AJAX setting.
	return null === $value || '' === $value ? true : (bool) $value;
}

function balt_contact_form_get( int $id ): ?array {
	if ( ! $id || ! class_exists( 'GFAPI' ) ) {
		return null;
	}
	$form = GFAPI::get_form( $id );
	return is_array( $form ) && ! empty( $form['is_active'] ) && empty( $form['is_trash'] ) ? $form : null;
}

/**
 * ACF block data is stored in block comments. Find forms before wp_head,
 * including nested blocks and synced patterns, without rendering the content.
 */
function balt_contact_form_collect( array $blocks, array &$forms, array &$seen ): void {
	foreach ( $blocks as $block ) {
		$name = $block['blockName'] ?? '';
		if ( 'balt/contact-form' === $name ) {
			$data = $block['attrs']['data'] ?? array();
			$id = balt_contact_form_id( $data['form'] ?? $data['field_balt_contact_form_form'] ?? 0 );
			if ( $id ) {
				$ajax = balt_contact_form_ajax( $data['ajax'] ?? $data['field_balt_contact_form_ajax'] ?? null );
				$forms[ $id ] = ( $forms[ $id ] ?? false ) || $ajax;
			}
		}
		if ( 'core/block' === $name ) {
			$ref = absint( $block['attrs']['ref'] ?? 0 );
			if ( $ref && ! isset( $seen[ $ref ] ) ) {
				$seen[ $ref ] = true;
				$pattern = get_post( $ref );
				if ( $pattern && 'wp_block' === $pattern->post_type && 'publish' === $pattern->post_status ) {
					balt_contact_form_collect( parse_blocks( $pattern->post_content ), $forms, $seen );
				}
			}
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			balt_contact_form_collect( $block['innerBlocks'], $forms, $seen );
		}
	}
}

function balt_contact_form_enqueue(): void {
	if ( ! function_exists( 'gravity_form_enqueue_scripts' ) || ! is_singular() ) {
		return;
	}
	$post = get_queried_object();
	if ( ! $post instanceof WP_Post || post_password_required( $post ) ) {
		return;
	}
	$forms = array();
	$seen = array();
	balt_contact_form_collect( parse_blocks( $post->post_content ), $forms, $seen );
	foreach ( $forms as $id => $ajax ) {
		if ( balt_contact_form_get( $id ) ) {
			gravity_form_enqueue_scripts( $id, $ajax );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'balt_contact_form_enqueue', 20 );
