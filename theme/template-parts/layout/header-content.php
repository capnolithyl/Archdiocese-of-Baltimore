<?php

/**
 * Template part for displaying the header content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Archdiocese_of_Baltimore
 */

?>

<header id="masthead" class="fixed top-admin-bar left-0 right-0 bg-primary z-50">

    <div class="hidden lg:block">
        <nav class="max-w-wide mx-auto px-6 lg:px-10 flex items-center justify-end gap-6 h-9">
            <?php
			wp_nav_menu(
				array(
					'theme_location' => 'dpi-top-bar',
					'menu_id'        => 'top-bar-menu',
					'items_wrap'     => '<ul id="%1$s" class="%2$s" aria-label="submenu">%3$s</ul>',
				)
			);
			?>
        </nav>
    </div>

    <div>

    </div>

    <nav id="site-navigation" aria-label="<?php esc_attr_e('Main Navigation', 'baltimore'); ?>">

        <?php
		wp_nav_menu(
			array(
				'theme_location' => 'menu-1',
				'menu_id'        => 'primary-menu',
				'items_wrap'     => '<ul id="%1$s" class="%2$s" aria-label="submenu">%3$s</ul>',
			)
		);
		?>
    </nav>

</header>