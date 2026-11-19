<?php

/**
 * Template part for displaying pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Archdiocese_of_Baltimore
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <header class="entry-header">
        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
    </header>

    <?php balt_post_thumbnail(); ?>

    <div <?php balt_content_class('entry-content'); ?>>
        <?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div>' . __('Pages:', 'baltimore'),
				'after'  => '</div>',
			)
		);
		?>
    </div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->