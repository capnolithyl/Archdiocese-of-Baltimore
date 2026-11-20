<?php
/**
 * Verify the primary menu is limited to one dropdown without dropping links.
 * No real WordPress database required.
 */
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );
$GLOBALS['primary_menu'] = 17;
$GLOBALS['locations'] = array( 'menu-1' => 17, 'menu-2' => 28, 'dpi-top-bar' => 29 );
$GLOBALS['updated_parents'] = array();
$GLOBALS['hooks'] = array();
$GLOBALS['inline_js'] = array();

function add_action( $tag, $callback, ...$args ): void {
	$GLOBALS['hooks'][ $tag ][] = $callback;
}
function add_filter( ...$args ): void {}
function get_nav_menu_locations(): array { return $GLOBALS['locations']; }
function wp_get_nav_menu_items( $menu_id, ...$args ): array { return $GLOBALS['menu_items']; }
function update_post_meta( $id, $key, $value ): void {
	$GLOBALS['updated_parents'][ $id ] = array( $key, $value );
}
function wp_add_inline_script( $handle, $script, $position ): void {
	$GLOBALS['inline_js'][] = array( $handle, $script, $position );
}
function absint( $value ): int { return abs( (int) $value ); }
function esc_html__( $text, $domain ) { return $text; }

require dirname( __DIR__ ) . '/theme/inc/primary-menu-depth.php';

$original = array(
	(object) array( 'ID' => 10, 'menu_item_parent' => 0, 'classes' => array( 'menu-item' ), 'title' => 'About' ),
	(object) array( 'ID' => 11, 'menu_item_parent' => 10, 'classes' => array( 'menu-item-has-children' ), 'title' => 'Our Story' ),
	(object) array( 'ID' => 12, 'menu_item_parent' => 11, 'classes' => array(), 'title' => 'Archbishop Lori' ),
	(object) array( 'ID' => 13, 'menu_item_parent' => 12, 'classes' => array(), 'title' => 'Leadership' ),
	(object) array( 'ID' => 14, 'menu_item_parent' => 10, 'classes' => array(), 'title' => 'Departments & Offices' ),
);
$GLOBALS['menu_items'] = $original;

function assert_equal( $expected, $actual, $message ) {
	if ( $expected !== $actual ) {
		throw new RuntimeException( $message . ': ' . var_export( $actual, true ) );
	}
}
$args = (object) array( 'theme_location' => 'menu-1' );
$flat = balt_primary_menu_limit_render_depth( $original, $args );
assert_equal( 5, count( $flat ), 'No menu links may be discarded' );
assert_equal( array( 0, 10, 10, 10, 10 ), array_map( static fn( $item ) => $item->menu_item_parent, $flat ), 'Grandchildren become direct dropdown links' );
assert_equal( array( 0, 10, 11, 12, 10 ), array_map( static fn( $item ) => $item->menu_item_parent, $original ), 'Rendering must not mutate menu records' );
assert_equal( true, in_array( 'menu-item-has-children', $flat[0]->classes, true ), 'Top-level parent needs the dropdown chevron' );
assert_equal( false, in_array( 'menu-item-has-children', $flat[1]->classes, true ), 'Nested child must not show a flyout chevron' );
assert_equal( $original, balt_primary_menu_limit_render_depth( $original, (object) array( 'theme_location' => 'menu-2' ) ), 'Other menus are not constrained' );
assert_equal( array( 0, 10, 10, 10, 10 ), array_map( static fn( $item ) => $item->menu_item_parent, balt_primary_menu_limit_render_depth( $original, (object) array( 'theme_location' => 'dpi-top-bar' ) ) ), 'Utility dropdown depth must be limited too' );

balt_primary_menu_normalize_saved_items( 28 );
assert_equal( array(), $GLOBALS['updated_parents'], 'Other menus must never be rewritten' );
balt_primary_menu_normalize_saved_items( 17 );
assert_equal( array( 12, 13 ), array_keys( $GLOBALS['updated_parents'] ), 'Only deeper descendants should be updated' );
assert_equal( array( '_menu_item_menu_item_parent', '10' ), $GLOBALS['updated_parents'][12], 'Third-level link must move under root' );
assert_equal( array( '_menu_item_menu_item_parent', '10' ), $GLOBALS['updated_parents'][13], 'Fourth-level link must move under root' );
$GLOBALS['updated_parents'] = array();
balt_primary_menu_normalize_saved_items( 29 );
assert_equal( array( 12, 13 ), array_keys( $GLOBALS['updated_parents'] ), 'Utility menu saves should normalize deeper links' );

assert_equal( 0, balt_primary_menu_allowed_parent( 1, array( 1 => 2, 2 => 1 ) ), 'Cycles must collapse safely' );
assert_equal( 0, balt_primary_menu_allowed_parent( 42, array( 42 => 999 ) ), 'Orphaned items must be top-level' );

$GLOBALS['nav_menu_selected_id'] = 17;
balt_primary_menu_limit_admin_editor( 'nav-menus.php' );
assert_equal( 1, count( $GLOBALS['inline_js'] ), 'Primary menu editor should get a depth limit' );
assert_equal( 'nav-menu', $GLOBALS['inline_js'][0][0], 'Limit should target the native menu editor' );
assert_equal( true, str_contains( $GLOBALS['inline_js'][0][1], 'globalMaxDepth = 1' ), 'One child level max' );
$GLOBALS['nav_menu_selected_id'] = 28;
balt_primary_menu_limit_admin_editor( 'nav-menus.php' );
assert_equal( 1, count( $GLOBALS['inline_js'] ), 'Other menus should be unrestricted' );
$GLOBALS['nav_menu_selected_id'] = 29;
balt_primary_menu_limit_admin_editor( 'nav-menus.php' );
assert_equal( 2, count( $GLOBALS['inline_js'] ), 'Utility menu editor should share the one-level limit' );
assert_equal( true, str_contains( $GLOBALS['inline_js'][1][1], 'globalMaxDepth = 1' ), 'Utility menu maximum indentation should be one' );

echo "Primary and utility menu single-dropdown editor limits, safe reparenting and frontend fallbacks passed\n";
