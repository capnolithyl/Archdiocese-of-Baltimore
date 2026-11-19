<?php

/**
 * Site-wide header; final layout and mobile behavior are part of the homepage sprint.
 */

$variant = "transparent";
?>
<header id="masthead" class="fixed top-admin-bar left-0 right-0 bg-transparent z-50">
	<?php if (has_nav_menu('dpi-top-bar')) : ?>
		<div class="hidden lg:block">
			<nav class="max-w-wide mx-auto px-6 lg:px-10 flex items-center justify-end gap-6 h-9"
				aria-label="<?php esc_attr_e('Utility Navigation', 'baltimore'); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'dpi-top-bar',
						'menu_id'        => 'top-bar-menu',
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		</div>
	<?php endif; ?>
	<nav id="site-navigation" aria-label="<?php esc_attr_e('Main Navigation', 'baltimore'); ?>">
		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'menu-1',
				'menu_id'        => 'primary-menu',
				'fallback_cb'    => false,
			)
		);
		?>
	</nav>
</header>