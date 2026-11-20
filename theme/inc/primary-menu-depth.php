<?php
/**
 * Both primary and utility navigation allow one dropdown level, never nested flyouts.
 * Return the nearest top-level ancestor without losing existing links.
 *
 * @param int   $item_id Menu-item post ID.
 * @param array $parents Map of item IDs to their current parent IDs.
 */
function balt_primary_menu_allowed_parent( int $item_id, array $parents ): int {
	$parent = (int) ( $parents[ $item_id ] ?? 0 );
	if ( ! $parent ) {
		return 0;
	}

	$visited = array( $item_id => true );
	while ( $parent && isset( $parents[ $parent ] ) && ! isset( $visited[ $parent ] ) ) {
		$visited[ $parent ] = true;
		$next = (int) $parents[ $parent ];
		if ( ! $next ) {
			return $parent;
		}
		$parent = $next;
	}

	// Invalid or circular ancestry: favor a safe top-level item.
	return 0;
}

/**
 * Immediately render existing deeper entries as first-level dropdown links.
 * Clone items so other menus reusing the same objects aren't affected.
 * This is non-destructive; the database is normalized upon the next save.
 */
function balt_primary_menu_limit_render_depth( $items, $args ) {
	if ( ! in_array( $args->theme_location ?? '', array( 'menu-1', 'dpi-top-bar' ), true ) || ! is_array( $items ) ) {
		return $items;
	}

	$parents = array();
	foreach ( $items as $item ) {
		$parents[ (int) $item->ID ] = (int) $item->menu_item_parent;
	}

	$normalized = array();
	$has_children = array();
	foreach ( $items as $item ) {
		$copy = clone $item;
		$copy->menu_item_parent = balt_primary_menu_allowed_parent( (int) $item->ID, $parents );
		$copy->classes = array_values( array_diff( (array) ( $copy->classes ?? array() ), array( 'menu-item-has-children' ) ) );
		if ( $copy->menu_item_parent ) {
			$has_children[ $copy->menu_item_parent ] = true;
		}
		$normalized[] = $copy;
	}
	foreach ( $normalized as $item ) {
		if ( isset( $has_children[ (int) $item->ID ] ) ) {
			$item->classes[] = 'menu-item-has-children';
		}
	}
	return $normalized;
}
add_filter( 'wp_nav_menu_objects', 'balt_primary_menu_limit_render_depth', 10, 2 );

/**
 * Restrict saves as well as drag/drop. Core saves each menu item separately;
 * normalize on shutdown, when all changes in the request have finished.
 * Items are reparented, never deleted, and their menu order is preserved.
 */
function balt_primary_menu_normalize_saved_items( int $menu_id ): void {
	$locations = get_nav_menu_locations();
	if ( $menu_id <= 0 || ! in_array( $menu_id, array_filter( array( (int) ( $locations['menu-1'] ?? 0 ), (int) ( $locations['dpi-top-bar'] ?? 0 ) ) ), true ) ) {
		return;
	}

	$items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'any' ) );
	if ( ! is_array( $items ) || ! $items ) {
		return;
	}

	$parents = array();
	foreach ( $items as $item ) {
		$parents[ (int) $item->ID ] = (int) $item->menu_item_parent;
	}
	foreach ( $items as $item ) {
		$current = (int) $item->menu_item_parent;
		$allowed = balt_primary_menu_allowed_parent( (int) $item->ID, $parents );
		if ( $current !== $allowed ) {
			update_post_meta( (int) $item->ID, '_menu_item_menu_item_parent', (string) $allowed );
		}
	}
}

function balt_primary_menu_schedule_normalization( $menu_id ): void {
	static $scheduled = array();
	$menu_id = (int) $menu_id;
	if ( ! $menu_id || isset( $scheduled[ $menu_id ] ) ) {
		return;
	}

	$locations = get_nav_menu_locations();
	if ( ! in_array( $menu_id, array_filter( array( (int) ( $locations['menu-1'] ?? 0 ), (int) ( $locations['dpi-top-bar'] ?? 0 ) ) ), true ) ) {
		return;
	}

	$scheduled[ $menu_id ] = true;
	add_action( 'shutdown', static function () use ( $menu_id ) {
		balt_primary_menu_normalize_saved_items( $menu_id );
	}, 20 );
}
add_action( 'wp_update_nav_menu_item', 'balt_primary_menu_schedule_normalization', 10, 1 );
add_action( 'wp_add_nav_menu_item', 'balt_primary_menu_schedule_normalization', 10, 1 );

/**
 * Limit dragging/keyboard indenting in Appearance > Menus for only the
 * menus assigned to the primary or utility locations. Other menus unrestricted.
 */
function balt_primary_menu_limit_admin_editor( $hook_suffix ): void {
	if ( 'nav-menus.php' !== $hook_suffix ) {
		return;
	}
	$locations = get_nav_menu_locations();
	$restricted_menu_ids = array_filter( array( (int) ( $locations['menu-1'] ?? 0 ), (int) ( $locations['dpi-top-bar'] ?? 0 ) ) );
	global $nav_menu_selected_id;
	$selected_id = (int) ( $nav_menu_selected_id ?? 0 );
	if ( ! $selected_id && isset( $_GET['menu'] ) ) {
		$selected_id = absint( $_GET['menu'] );
	}

	if ( ! in_array( $selected_id, $restricted_menu_ids, true ) ) {
		return;
	}

	wp_add_inline_script(
		'nav-menu',
		'if (window.wpNavMenu && window.wpNavMenu.options) { window.wpNavMenu.options.globalMaxDepth = 1; }',
		'after'
	);
}
add_action( 'admin_enqueue_scripts', 'balt_primary_menu_limit_admin_editor' );

/** Explain the restriction to editors rather than leaving them guessing. */
function balt_primary_menu_depth_admin_notice(): void {
	global $nav_menu_selected_id;
	$locations = get_nav_menu_locations();
	$selected = (int) ( $nav_menu_selected_id ?? 0 );
	$primary = (int) ( $locations['menu-1'] ?? 0 );
	$utility = (int) ( $locations['dpi-top-bar'] ?? 0 );
	if ( ! $selected || ( $selected !== $primary && $selected !== $utility ) ) {
		return;
	}
	$name = $selected === $utility ? __( 'Utility Navigation', 'baltimore' ) : __( 'Primary Navigation', 'baltimore' );
	echo '<div class="notice notice-info"><p>' .
		esc_html( sprintf( __( '%s supports main links and one dropdown level only. Any older third-level links move directly under their top-level section when you save this menu; no links will be deleted.', 'baltimore' ), $name ) ) .
		'</p></div>';
}
add_action( 'admin_notices', 'balt_primary_menu_depth_admin_notice' );
