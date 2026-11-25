<?php
/**
 * Title: Contact & Form
 * Slug: baltimore/contact-form
 * Categories: baltimore, text
 * Inserter: yes
 * Description: Dark two-column section with fully editable content and a Gravity Forms block.
 *
 * @package Archdiocese_of_Baltimore
 */
?>
<!-- wp:group {"align":"full","className":"balt-contact-form balt-contact-pattern balt-full-bleed not-prose","layout":{"type":"default"}} -->
<div class="wp-block-group alignfull balt-contact-form balt-contact-pattern balt-full-bleed not-prose">
<!-- wp:columns {"className":"balt-contact-form__columns"} -->
<div class="wp-block-columns balt-contact-form__columns">
<!-- wp:column {"className":"balt-contact-form__intro"} -->
<div class="wp-block-column balt-contact-form__intro">
<!-- wp:paragraph {"className":"is-style-balt-eyebrow"} -->
<p class="is-style-balt-eyebrow"><?php esc_html_e( "We're Here to Help", 'baltimore' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'How Can We Help You?', 'baltimore' ); ?></h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><?php esc_html_e( 'Let us know how we can help. Add your own introduction, contact information, or any other content here.', 'baltimore' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:group {"className":"balt-contact-form__details","layout":{"type":"default"}} -->
<div class="wp-block-group balt-contact-form__details">
<!-- wp:paragraph {"className":"balt-contact-detail-phone"} -->
<p class="balt-contact-detail-phone"><?php esc_html_e( 'Phone number', 'baltimore' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"balt-contact-detail-hours"} -->
<p class="balt-contact-detail-hours"><?php esc_html_e( 'Office hours', 'baltimore' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"balt-contact-detail-email"} -->
<p class="balt-contact-detail-email"><?php esc_html_e( 'Email address', 'baltimore' ); ?></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"balt-contact-detail-location"} -->
<p class="balt-contact-detail-location"><?php esc_html_e( 'Office address', 'baltimore' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:column -->

<!-- wp:column {"className":"balt-contact-form__card"} -->
<div class="wp-block-column balt-contact-form__card">
<!-- wp:gravityforms/form {"title":false,"description":false} /-->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->
</div>
<!-- /wp:group -->
