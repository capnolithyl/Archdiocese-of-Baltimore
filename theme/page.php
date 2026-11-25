<?php
/**
 * Default page layout: a featured image creates an image hero; when there is
 * no featured image, the shared hero becomes a navy title-only banner.
 *
 * @package Archdiocese_of_Baltimore
 */

get_header();
get_template_part( 'template-parts/layout/interior-content', null, array( 'hero' => 'auto' ) );
get_footer();
