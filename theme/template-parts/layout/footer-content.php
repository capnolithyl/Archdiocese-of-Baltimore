<?php
/**
 * Footer shell; final footer presentation is part of the homepage sprint.
 */
?>
<footer id="colophon">
	<?php if ( has_nav_menu( 'menu-2' ) ) : ?>
		<nav aria-label="<?php esc_attr_e( 'Footer Menu', 'baltimore' ); ?>">
			<?php
			wp_nav_menu(
					array(
						'theme_location' => 'menu-2',
						'menu_class'     => 'footer-menu',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
		</nav>
	<?php endif; ?>
	<p class="site-copyright">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( get_bloginfo( 'name' ) ); ?></p>
</footer>
