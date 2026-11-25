<?php
/**
 * Search form template.
 *
 * @package Archdiocese_of_Baltimore
 */

$search_id = wp_unique_id( 'site-search-' );
$policy_only = is_search() && 'balt_policy' === get_query_var( 'post_type' );
?>

<form role="search" method="get" class="site-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<?php if ( $policy_only ) : ?><input type="hidden" name="post_type" value="balt_policy"><?php endif; ?>
	<label class="site-search-form__label" for="<?php echo esc_attr( $search_id ); ?>">
		<span class="sr-only"><?php esc_html_e( 'Search for:', 'baltimore' ); ?></span>
		<input
			id="<?php echo esc_attr( $search_id ); ?>"
			class="site-search-form__field"
			type="search"
			name="s"
			value="<?php echo esc_attr( get_search_query() ); ?>"
			placeholder="<?php echo esc_attr( $policy_only ? __( 'Search policies...', 'baltimore' ) : __( 'What are you looking for?', 'baltimore' ) ); ?>"
		>
	</label>
	<button class="site-search-form__submit" type="submit"><?php esc_html_e( 'Search', 'baltimore' ); ?></button>
</form>
