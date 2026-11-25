<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
$id = wp_unique_id( 'balt-policy-filter-' );
?>
<div class="balt-card-directory__search balt-policy-filter" data-policy-search hidden>
	<label class="sr-only" for="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Filter policies by title', 'baltimore' ); ?></label>
	<span class="balt-card-directory__search-icon" aria-hidden="true"></span>
	<input type="search" id="<?php echo esc_attr( $id ); ?>" placeholder="<?php echo esc_attr( $args['placeholder'] ?? __( 'Search policies...', 'baltimore' ) ); ?>" autocomplete="off">
</div>
<p class="sr-only" role="status" data-policy-status data-count-template="<?php esc_attr_e( '%1$s of %2$s policies shown.', 'baltimore' ); ?>"></p>
