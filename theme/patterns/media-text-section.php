<?php
/**
 * Title: Media & Text Section
 * Slug: baltimore/media-text-section
 * Categories: baltimore, text
 * Inserter: yes
 * Description: A wide image and text section with an eyebrow, heading, supporting copy and two buttons.
 *
 * @package Archdiocese_of_Baltimore
 */
?>
<!-- wp:media-text {"align":"wide","isStackedOnMobile":true,"verticalAlignment":"center"} -->
<div class="wp-block-media-text alignwide is-stacked-on-mobile is-vertically-aligned-center"><figure class="wp-block-media-text__media"></figure><div class="wp-block-media-text__content">
<!-- wp:paragraph {"className":"is-style-balt-eyebrow"} -->
<p class="is-style-balt-eyebrow"><?php esc_html_e( 'Section eyebrow', 'baltimore' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Introduce your mission or story', 'baltimore' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"is-style-balt-lead"} -->
<p class="is-style-balt-lead"><?php esc_html_e( 'Use this introductory paragraph to explain the purpose of this section and why it matters to your readers.', 'baltimore' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Add supporting details here, or remove this paragraph if you do not need it.', 'baltimore' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons">
<!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Learn More', 'baltimore' ); ?></a></div>
<!-- /wp:button -->

<!-- wp:button {"className":"is-style-outline"} -->
<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button"><?php esc_html_e( 'Get Involved', 'baltimore' ); ?></a></div>
<!-- /wp:button -->
</div>
<!-- /wp:buttons -->
</div></div>
<!-- /wp:media-text -->
