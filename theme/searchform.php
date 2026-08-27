<?php
/**
 * Search form template.
 *
 * @package Archdiocese_of_Baltimore
 */

$search_id = wp_unique_id( 'site-search-' );
?>

<form role="search" method="get" class="site-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="site-search-form__label" for="<?php echo esc_attr( $search_id ); ?>">
		<span class="sr-only"><?php esc_html_e( 'Search for:', 'baltimore' ); ?></span>
		<input
			id="<?php echo esc_attr( $search_id ); ?>"
			class="site-search-form__field"
			type="search"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php esc_attr_e( 'What are you looking for?', 'baltimore' ); ?>"
		>
	</label>
	<button class="site-search-form__submit" type="submit"><?php esc_html_e( 'Search', 'baltimore' ); ?></button>
</form>
