<?php
/**
 * Template Name: Interior | Title Only
 * Template Post Type: page
 *
 * Reusable interior page with a solid-color title hero.
 *
 * @package Archdiocese_of_Baltimore
 */

get_header();
get_template_part( 'template-parts/layout/interior-content', null, array( 'hero' => 'title' ) );
get_footer();
