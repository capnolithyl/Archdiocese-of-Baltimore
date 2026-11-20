<?php
/**
 * Full-height mobile drawer. Both navigation lists come from the same WordPress
 * menus as desktop. Main items drill into a detail screen; utility items expand
 * inline. Only one submenu level is allowed by the theme's menu-depth policy.
 *
 * @package Archdiocese_of_Baltimore
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$help_link   = function_exists( 'get_field' ) ? get_field( 'balt_mobile_help_link', 'option' ) : null;
$donate_link = function_exists( 'get_field' ) ? get_field( 'balt_mobile_donate_link', 'option' ) : null;

$help_link   = is_array( $help_link ) && ! empty( $help_link['url'] ) ? $help_link : null;
$donate_link = is_array( $donate_link ) && ! empty( $donate_link['url'] ) ? $donate_link : null;

/* Reuse the real Give menu destination rather than inventing a donation URL. */
if ( ! $donate_link && has_nav_menu( 'menu-1' ) ) {
	$locations = get_nav_menu_locations();
	$menu_id   = (int) ( $locations['menu-1'] ?? 0 );
	$items     = $menu_id ? wp_get_nav_menu_items( $menu_id ) : array();
	if ( is_array( $items ) ) {
		foreach ( $items as $item ) {
			if ( 0 === (int) $item->menu_item_parent && in_array( strtolower( trim( (string) $item->title ) ), array( 'give', 'donate' ), true ) && ! empty( $item->url ) && '#' !== $item->url ) {
				$donate_link = array( 'url' => $item->url, 'target' => '' );
				break;
			}
		}
	}
}
?>
<dialog
	id="balt-mobile-navigation"
	class="balt-mobile-drawer"
	aria-label="<?php esc_attr_e( 'Mobile navigation', 'baltimore' ); ?>"
	data-view="root"
>
	<div class="balt-mobile-drawer__shell">
		<div class="balt-mobile-drawer__toolbar">
			<button class="balt-mobile-drawer__close" type="button" data-balt-mobile-close aria-label="<?php esc_attr_e( 'Close navigation', 'baltimore' ); ?>">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M5 5 19 19M19 5 5 19"/></svg>
			</button>
		</div>
		<div class="balt-mobile-drawer__viewport">
			<div class="balt-mobile-drawer__track">
				<div class="balt-mobile-drawer__root" data-balt-mobile-root>
					<?php if ( has_nav_menu( 'menu-1' ) ) : ?>
						<nav aria-label="<?php esc_attr_e( 'Primary mobile navigation', 'baltimore' ); ?>">
							<?php
							wp_nav_menu( array(
								'theme_location' => 'menu-1',
								'container'      => false,
								'menu_id'        => 'balt-mobile-primary-menu',
								'menu_class'     => 'balt-mobile-primary-menu',
								'depth'          => 2,
								'fallback_cb'    => false,
							) );
							?>
						</nav>
					<?php endif; ?>

					<?php if ( has_nav_menu( 'dpi-top-bar' ) ) : ?>
						<nav aria-label="<?php esc_attr_e( 'Utility mobile navigation', 'baltimore' ); ?>">
							<?php
							wp_nav_menu( array(
								'theme_location' => 'dpi-top-bar',
								'container'      => false,
								'menu_id'        => 'balt-mobile-utility-menu',
								'menu_class'     => 'balt-mobile-utility-menu',
								'depth'          => 2,
								'fallback_cb'    => false,
							) );
							?>
						</nav>
					<?php endif; ?>
				</div>
				<div id="balt-mobile-navigation-detail" class="balt-mobile-drawer__detail" data-balt-mobile-detail inert aria-hidden="true">
					<div class="balt-mobile-drawer__detail-header">
						<button type="button" class="balt-mobile-drawer__back" data-balt-mobile-back>
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m14.5 5-7 7 7 7"/></svg>
							<?php esc_html_e( 'Back', 'baltimore' ); ?>
						</button>
						<span class="balt-mobile-drawer__detail-title" data-balt-mobile-title></span>
					</div>
					<nav aria-label="<?php esc_attr_e( 'Section pages', 'baltimore' ); ?>" data-balt-mobile-detail-items></nav>
				</div>
			</div>
		</div>
		<div class="balt-mobile-drawer__footer">
			<div class="balt-mobile-drawer__actions">
				<?php if ( $help_link ) : ?>
					<a class="balt-mobile-drawer__action balt-mobile-drawer__action--help" href="<?php echo esc_url( $help_link['url'] ); ?>"<?php if ( '_blank' === ( $help_link['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?php esc_html_e( 'Get Help', 'baltimore' ); ?></a>
				<?php else : ?>
					<button type="button" class="balt-mobile-drawer__action balt-mobile-drawer__action--help" data-balt-mobile-search data-balt-mobile-search-query="help"><?php esc_html_e( 'Get Help', 'baltimore' ); ?></button>
				<?php endif; ?>
				<?php if ( $donate_link ) : ?>
					<a class="balt-mobile-drawer__action balt-mobile-drawer__action--donate" href="<?php echo esc_url( $donate_link['url'] ); ?>"<?php if ( '_blank' === ( $donate_link['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?php esc_html_e( 'Donate', 'baltimore' ); ?></a>
				<?php else : ?>
					<button type="button" class="balt-mobile-drawer__action balt-mobile-drawer__action--donate" data-balt-mobile-search data-balt-mobile-search-query="donate"><?php esc_html_e( 'Donate', 'baltimore' ); ?></button>
				<?php endif; ?>
			</div>
			<button type="button" class="balt-mobile-drawer__search" data-balt-mobile-search aria-label="<?php esc_attr_e( 'Search website', 'baltimore' ); ?>">
				<svg width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.55" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.8"/><path d="m15.5 15.5 5 5"/></svg>
			</button>
		</div>
	</div>
</dialog>
