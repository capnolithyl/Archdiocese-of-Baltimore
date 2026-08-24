<?php

/**
 * Template part for displaying the header content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Archdiocese_of_Baltimore
 */

?>

<header id="masthead">

	<div>
	</div>

	<nav id="site-navigation" aria-label="<?php esc_attr_e('Main Navigation', 'baltimore'); ?>">
		<button aria-controls="primary-menu"
			aria-expanded="false"><?php esc_html_e('Primary Menu', 'baltimore'); ?></button>

		<?php
		wp_nav_menu(
			array(
				'theme_location' => 'menu-1',
				'menu_id'        => 'primary-menu',
				'items_wrap'     => '<ul id="%1$s" class="%2$s" aria-label="submenu">%3$s</ul>',
			)
		);
		?>
	</nav><!-- #site-navigation -->

</header><!-- #masthead -->