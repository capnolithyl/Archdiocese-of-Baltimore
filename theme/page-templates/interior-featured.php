<?php
/**
 * Template Name: Interior | Featured Image
 * Template Post Type: page
 *
 * Reusable interior page with a featured-image title hero.
 *
 * @package Archdiocese_of_Baltimore
 */

get_header();
get_template_part( 'template-parts/layout/interior-content', null, array( 'hero' => 'featured' ) );
get_footer();
