<?php
/**
 * WordPress-free header checks: automatic/overridden variant, markup and menu button.
 */
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );

$singular = true;
$override = '';
$blocks = array();
$logo_id = 0;

function add_filter( ...$args ): void {}
function add_action( ...$args ): void {}
function is_singular(): bool { return $GLOBALS['singular']; }
function is_page(): bool { return $GLOBALS['test_is_page'] ?? false; }
function is_front_page(): bool { return $GLOBALS['test_is_front_page'] ?? false; }
function get_queried_object_id(): int { return 123; }
function get_field( string $field, $location = null ) {
	return match ( $field ) {
		'balt_header_variant' => $GLOBALS['override'],
		'balt_site_logo' => $GLOBALS['logo_id'],
		'balt_footer_logo' => 0,
		default => null,
	};
}
function parse_blocks( string $content ): array { return $GLOBALS['blocks']; }
function get_post_field( string $field, int $post_id ): string { return 'stub content'; }
function wp_strip_all_tags( string $html ): string { return strip_tags( $html ); }
function __( string $text, string $domain ): string { return $text; }
function absint( $value ): int { return abs( (int) $value ); }
function esc_attr( string $value ): string { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_url( string $value ): string { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr_e( string $text, string $domain ): void { echo esc_attr( $text ); }
function esc_html_e( string $text, string $domain ): void { echo esc_attr( $text ); }
function get_nav_menu_locations(): array { return array( 'menu-1' => 10 ); }
function wp_get_nav_menu_items( $menu_id ): array { return array(); }
function get_template_directory_uri(): string { return '/theme'; }
function get_bloginfo( string $key ): string { return 'Archdiocese of Baltimore'; }
function home_url( string $path ): string { return 'https://example.org' . $path; }
function has_nav_menu( string $location ): bool { return in_array( $location, array( 'menu-1', 'dpi-top-bar' ), true ); }
function wp_nav_menu( array $args ): void {
	echo '<ul id="' . esc_attr( $args['menu_id'] ) . '" class="' . esc_attr( $args['menu_class'] ) . '"><li><a href="/test">Test</a></li></ul>';
}
function get_search_form(): void { echo '<form role="search"><input type="search"></form>'; }
function wp_get_attachment_image( int $id, string $size, bool $icon, array $attrs ): string {
	return '<img src="/uploads/logo-' . $id . '.png" alt="' . esc_attr( $attrs['alt'] ) . '">';
}

require dirname( __DIR__ ) . '/theme/inc/header-navigation.php';
require dirname( __DIR__ ) . '/theme/inc/interior-pages.php';

function check( bool $ok, string $why ): void {
	if ( ! $ok ) throw new RuntimeException( $why );
}

$GLOBALS['test_is_page'] = true;
$GLOBALS['test_is_front_page'] = false;
$blocks = array( array( 'blockName' => 'dpi/hero', 'attrs' => array() ) );
check( balt_header_variant() === 'solid', 'Default interior page should use solid header despite a hero-like first content block' );
$override = 'transparent';
check( balt_header_variant() === 'transparent', 'Manual transparent header override must work on Default pages' );
$override = '';
$GLOBALS['test_is_front_page'] = true;
check( balt_header_variant() === 'transparent', 'Front page must preserve transparent hero header' );
$GLOBALS['test_is_page'] = false;
$GLOBALS['test_is_front_page'] = false;
$blocks = array( array( 'blockName' => 'dpi/hero', 'attrs' => array() ) );
check( balt_header_variant() === 'transparent', 'First Hero block should be transparent' );

$blocks = array( array( 'blockName' => 'core/cover', 'attrs' => array() ) );
check( balt_header_variant() === 'transparent', 'Cover at top should be transparent' );

$blocks = array( array( 'blockName' => 'core/image', 'attrs' => array( 'align' => 'full' ) ) );
check( balt_header_variant() === 'transparent', 'Full width image at top should be transparent' );

$blocks = array( array( 'blockName' => 'core/paragraph', 'attrs' => array() ), array( 'blockName' => 'dpi/hero' ) );
check( balt_header_variant() === 'solid', 'Hero farther down should not force transparent' );

$override = 'transparent';
check( balt_header_variant() === 'transparent', 'Manual transparent override ignored' );
$override = 'solid';
$blocks = array( array( 'blockName' => 'dpi/hero' ) );
check( balt_header_variant() === 'solid', 'Manual blue override ignored' );

$override = '';
$singular = false;
check( balt_header_variant() === 'solid', 'Archives should use blue navbar' );

$singular = true;
$blocks = array( array( 'blockName' => 'dpi/hero' ) );
$args = (object) array( 'theme_location' => 'menu-1' );
$item = (object) array(
	'classes' => array( 'menu-item-has-children' ),
	'title' => 'Parishes',
);
$menu_item = balt_header_submenu_button( '<a href="/parishes">Parishes</a>', $item, 0, $args );
check( str_contains( $menu_item, 'site-header__submenu-toggle' ) && str_contains( $menu_item, 'chevron.svg' ), 'Primary menu toggle must render exact Figma chevron' );
$args->theme_location = 'dpi-top-bar';
$utility = balt_header_submenu_button( '<a href="/report">Report Misconduct</a>', $item, 0, $args );
check( str_contains( $utility, 'site-header__utility-toggle' ) && str_contains( $utility, 'chevron.svg' ), 'Utility navigation should have an accessible toggle' );
check( balt_header_submenu_button( '<a>Grandchild</a>', $item, 1, $args ) === '<a>Grandchild</a>', 'Utility navigation does not support nested flyouts' );
$args->theme_location = 'menu-2';
check( balt_header_submenu_button( '<a>Footer</a>', $item, 0, $args ) === '<a>Footer</a>', 'Footer menus should not receive header toggles' );

function render_header(): string {
	ob_start();
	require dirname( __DIR__ ) . '/theme/template-parts/layout/header-content.php';
	return (string) ob_get_clean();
}

$transparent = render_header();
check( str_contains( $transparent, 'data-header-variant="transparent"' ), 'Transparent header attribute missing' );
check( str_contains( $transparent, '/theme/assets/header/logo.png' ), 'Exact Figma header logo fallback missing' );
check( str_contains( $transparent, '/theme/assets/header/search.svg' ), 'Exact search icon missing' );
check( str_contains( $transparent, 'top-admin-bar' ), 'WP admin bar offset lost' );
check( str_contains( $transparent, 'site-navigation' ), 'WordPress primary menu not rendered' );
check( str_contains( $transparent, 'top-bar-menu' ), 'WordPress utility menu not rendered' );
check( ! str_contains( $transparent, 'h-20 xl:h-[124px]" aria-hidden' ), 'Transparent hero must not be pushed down' );

$override = 'solid';
$solid = render_header();
check( str_contains( $solid, 'data-header-variant="solid"' ), 'Blue header attribute missing' );
check( str_contains( $solid, 'h-20 xl:h-[124px]" aria-hidden' ), 'Blue pages require a content spacer to avoid overlap' );

$logo_id = 42;
check( str_contains( render_header(), '/uploads/logo-42.png' ), 'Configured sitewide logo not used' );
check( str_contains( render_header(), 'aria-controls="balt-mobile-navigation"' ), 'Mobile drawer button points to the dialog' );
check( str_contains( render_header(), 'site-header__search-toggle hidden' ), 'Mobile search icon must be hidden beside hamburger' );
ob_start();
require dirname( __DIR__ ) . '/theme/template-parts/layout/mobile-navigation.php';
$drawer = (string) ob_get_clean();
check( str_contains( $drawer, 'id="balt-mobile-navigation"' ), 'Mobile navigation dialog missing' );
check( str_contains( $drawer, 'balt-mobile-primary-menu' ) && str_contains( $drawer, 'balt-mobile-utility-menu' ), 'Both WP menus must be rendered in drawer' );
check( str_contains( $drawer, 'data-balt-mobile-search' ) && str_contains( $drawer, 'data-balt-mobile-back' ), 'Search and drill-down Back controls missing' );

echo "Header automatic and manual variants, navigation, Figma icons and spacers passed\n";
