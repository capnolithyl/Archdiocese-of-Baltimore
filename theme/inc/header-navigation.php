<?php
/**
 * Select the global header appearance based on the first visible page block.
 * Editors can override the automatic choice on individual pages/posts.
 */
function balt_header_variant(): string {
	// Policy templates own their hero; never infer transparency from document content.
	if ( is_singular( 'balt_policy' ) ) {
		return 'solid';
	}

	if ( ! is_singular() ) {
		return 'solid';
	}

	$post_id = get_queried_object_id();
	if ( function_exists( 'get_field' ) ) {
		$override = get_field( 'balt_header_variant', $post_id );
		if ( in_array( $override, array( 'solid', 'transparent' ), true ) ) {
			return $override;
		}
	}

	// Ordinary pages use the automatic title hero above Gutenberg content.
	// Preserve the separate homepage hero and the explicit override above.
	if ( is_page() && ! is_front_page() ) {
		return 'solid';
	}

	$blocks = parse_blocks( (string) get_post_field( 'post_content', $post_id ) );
	foreach ( $blocks as $block ) {
		$name = $block['blockName'] ?? '';
		if ( ! $name && ! trim( wp_strip_all_tags( $block['innerHTML'] ?? '' ) ) ) {
			continue;
		}

		if ( in_array( $name, array( 'dpi/hero', 'dpi/feature-banner', 'core/cover', 'balt/image-text-banner' ), true ) ) {
			return 'transparent';
		}

		if ( 'core/image' === $name && 'full' === ( $block['attrs']['align'] ?? '' ) ) {
			return 'transparent';
		}

		break;
	}

	return 'solid';
}

/**
 * A separate submenu toggle allows the parent item to remain a real link.
 * Applies to primary and utility navigation, not footer links.
 */
function balt_header_submenu_button( $item_output, $item, $depth, $args ) {
	if ( 0 !== (int) $depth || ! in_array( $args->theme_location ?? '', array( 'menu-1', 'dpi-top-bar' ), true ) || ! in_array( 'menu-item-has-children', (array) ( $item->classes ?? array() ), true ) ) {
		return $item_output;
	}

	$button_class = 'dpi-top-bar' === $args->theme_location ? 'site-header__utility-toggle' : 'site-header__submenu-toggle';
	$src = get_template_directory_uri() . '/assets/header/chevron.svg';
	return $item_output . '<button type="button" class="' . esc_attr( $button_class ) . '" aria-expanded="false" aria-label="' .
		esc_attr( sprintf( __( 'Toggle %s submenu', 'baltimore' ), $item->title ) ) . '">' .
		'<img src="' . esc_url( $src ) . '" width="10" height="10" alt="" aria-hidden="true"></button>';
}
add_filter( 'walker_nav_menu_start_el', 'balt_header_submenu_button', 10, 4 );

/** Light, dependency-free navigation interactions. */
function balt_enqueue_header_script(): void {
	wp_enqueue_script(
		'balt-header-navigation',
		get_template_directory_uri() . '/js/header-navigation.js',
		array(),
		BALT_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'balt_enqueue_header_script' );

/** Separate mobile drill-down drawer, sharing the same WordPress menu locations. */
function balt_enqueue_mobile_navigation_script(): void {
	wp_enqueue_script(
		'balt-mobile-navigation',
		get_template_directory_uri() . '/js/mobile-navigation.js',
		array(),
		BALT_VERSION,
		true
	);
}
add_action( 'wp_enqueue_scripts', 'balt_enqueue_mobile_navigation_script' );

/** Avoid duplicate DOM IDs when the same menu renders on desktop and mobile. */
function balt_mobile_navigation_item_id( $id, $item, $args, $depth ) {
	if ( in_array( $args->menu_id ?? '', array( 'balt-mobile-primary-menu', 'balt-mobile-utility-menu' ), true ) ) {
		return sanitize_html_class( $args->menu_id . '-item-' . (int) $item->ID );
	}
	return $id;
}
add_filter( 'nav_menu_item_id', 'balt_mobile_navigation_item_id', 10, 4 );

