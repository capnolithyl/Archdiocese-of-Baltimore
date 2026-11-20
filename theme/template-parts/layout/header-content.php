<?php
/**
 * Sitewide navigation. Transparent over a leading hero, solid blue elsewhere.
 * A small script applies the solid/compact state after scrolling.
 */

$variant  = balt_header_variant();
$logo_id  = function_exists( 'get_field' ) ? absint( get_field( 'balt_site_logo', 'option' ) ) : 0;
if ( ! $logo_id && function_exists( 'get_field' ) ) {
	$logo_id = absint( get_field( 'balt_footer_logo', 'option' ) );
}
$asset_base = get_template_directory_uri() . '/assets';
?>
<header
	id="masthead"
	class="site-header fixed top-admin-bar right-0 left-0 z-50 bg-transparent transition-[background-color,box-shadow] duration-300"
	data-header-variant="<?php echo esc_attr( $variant ); ?>"
	data-compact="false"
	data-menu-open="false"
>
	<?php if ( has_nav_menu( 'dpi-top-bar' ) ) : ?>
		<div class="site-header__utility hidden xl:block">
			<nav class="mx-auto flex h-full w-full max-w-[1504px] items-center justify-end px-6 sm:px-10" aria-label="<?php esc_attr_e( 'Utility Navigation', 'baltimore' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'dpi-top-bar',
						'container'      => false,
						'menu_id'        => 'top-bar-menu',
						'menu_class'     => 'site-header__utility-menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		</div>
	<?php endif; ?>

	<div class="site-header__main relative mx-auto flex h-20 w-full max-w-[1504px] items-center gap-4 px-6 sm:px-10 xl:h-[88px]">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-header__brand relative z-10 shrink-0 no-underline" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php if ( $logo_id ) : ?>
				<?php echo wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'site-header__logo h-14 w-auto max-w-[150px] object-contain xl:h-20 xl:max-w-[184px]', 'alt' => get_bloginfo( 'name' ) ) ); ?>
			<?php else : ?>
				<img class="site-header__logo h-14 w-auto max-w-[150px] object-contain xl:h-20 xl:max-w-[184px]" src="<?php echo esc_url( $asset_base . '/header/logo.png' ); ?>" width="184" height="80" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
			<?php endif; ?>
		</a>

		<?php if ( has_nav_menu( 'menu-1' ) ) : ?>
			<nav id="site-navigation" class="site-header__nav absolute top-full right-0 left-0 hidden max-h-[calc(100dvh-90px)] overflow-y-auto bg-primary px-4 pb-5 shadow-xl xl:static xl:ml-auto xl:block xl:max-h-none xl:min-w-0 xl:overflow-visible xl:bg-transparent xl:p-0 xl:shadow-none" aria-label="<?php esc_attr_e( 'Main Navigation', 'baltimore' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'menu-1',
						'container'      => false,
						'menu_id'        => 'primary-menu',
						'menu_class'     => 'site-header__menu',
						'depth'          => 2,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>

		<div class="ml-auto flex shrink-0 items-center gap-1 xl:ml-0">
			<button type="button" class="site-header__search-toggle flex size-11 items-center justify-center rounded-sm text-white hover:bg-white/10" aria-label="<?php esc_attr_e( 'Search', 'baltimore' ); ?>" aria-expanded="false" aria-haspopup="dialog" aria-controls="balt-live-search-dialog">
				<img src="<?php echo esc_url( $asset_base . '/header/search.svg' ); ?>" width="18" height="18" alt="" aria-hidden="true">
			</button>
			<?php if ( has_nav_menu( 'menu-1' ) ) : ?>
				<button type="button" class="site-header__mobile-toggle flex size-11 items-center justify-center rounded-sm text-white hover:bg-white/10 xl:hidden" aria-controls="site-navigation" aria-expanded="false" aria-label="<?php esc_attr_e( 'Open navigation menu', 'baltimore' ); ?>">
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
				</button>
			<?php endif; ?>
		</div>

	</div>
</header>
<?php if ( 'solid' === $variant ) : ?>
	<div class="h-20 xl:h-[124px]" aria-hidden="true"></div>
<?php endif; ?>
