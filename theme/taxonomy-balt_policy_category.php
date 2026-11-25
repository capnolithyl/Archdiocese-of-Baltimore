<?php
/** Policy category index (Figma 357:8327). */
get_header();
$term = get_queried_object();
global $wp_query;
$groups = balt_policy_groups( $wp_query->posts );
wp_enqueue_script( 'balt-policy-filter', get_template_directory_uri() . '/assets/js/policy-filter.js', array(), BALT_VERSION, true );
?>
<main id="main" class="balt-policy-page">
	<?php get_template_part( 'template-parts/layout/interior-hero', null, array( 'hero_title' => $term->name, 'eyebrow' => __( 'Archdiocese Policies', 'baltimore' ), 'image_id' => balt_policy_term_image( $term ) ) ); ?>
	<div class="balt-policy-index" data-policy-filter>
		<div class="balt-policy-index__header">
			<a class="balt-policy-back" href="<?php echo esc_url( balt_policy_directory_url( $term ) ); ?>"><?php esc_html_e( '← Policy Directory', 'baltimore' ); ?></a>
			<?php get_template_part( 'template-parts/components/policy-filter', null, array( 'placeholder' => sprintf( __( 'Search %s...', 'baltimore' ), $term->name ) ) ); ?>
		</div>
		<div class="balt-policy-groups">
			<?php foreach ( $groups as $group ) : ?>
				<section class="balt-policy-group" data-policy-group>
					<h2><a data-policy-item href="<?php echo esc_url( get_permalink( $group['parent'] ) ); ?>"><span class="balt-policy-star" aria-hidden="true">★</span><span><?php echo esc_html( get_the_title( $group['parent'] ) ); ?></span></a></h2>
					<?php if ( $group['children'] ) : ?>
						<ul>
							<?php foreach ( $group['children'] as $child ) : ?>
								<li data-policy-item><a href="<?php echo esc_url( get_permalink( $child ) ); ?>"><?php echo esc_html( get_the_title( $child ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</section>
			<?php endforeach; ?>
		</div>
		<p class="balt-policy-empty" data-policy-empty<?php if ( $groups ) : ?> hidden<?php endif; ?>><?php esc_html_e( 'No matching policies found.', 'baltimore' ); ?></p>
	</div>
</main>
<?php get_footer(); ?>
