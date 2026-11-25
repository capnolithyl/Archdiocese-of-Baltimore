<?php
/** Single policy document (Figma 357:5230). */
get_header();
while ( have_posts() ) :
	the_post();
	$id = get_the_ID();
	$term = balt_policy_term( $id );
	$name = $term ? $term->name : __( 'Policies', 'baltimore' );
	$term_url = $term ? get_term_link( $term ) : balt_policy_directory_url();
	if ( is_wp_error( $term_url ) ) { $term_url = balt_policy_directory_url( $term ); }
	$groups = $term ? balt_policy_groups( balt_policy_category_posts( $term->term_id ) ) : array();
	$resources = post_password_required() ? array() : balt_policy_resources( $id );
	wp_enqueue_script( 'balt-policy-filter', get_template_directory_uri() . '/assets/js/policy-filter.js', array(), BALT_VERSION, true );
	?>
	<main id="main" class="balt-policy-page">
		<?php get_template_part( 'template-parts/layout/interior-hero', null, array( 'hero_title' => $name, 'eyebrow' => $term ? __( 'Archdiocese Policies', 'baltimore' ) . ' · ' . $name : __( 'Archdiocese Policies', 'baltimore' ), 'image_id' => balt_policy_term_image( $term ), 'heading_tag' => 'p' ) ); ?>
		<div class="balt-policy-detail<?php echo $resources ? ' balt-policy-detail--resources' : ''; ?>">
			<nav class="balt-policy-sidebar" aria-label="<?php esc_attr_e( 'Policies in this category', 'baltimore' ); ?>" data-policy-filter>
				<div class="balt-policy-sidebar__header">
					<a class="balt-policy-back" href="<?php echo esc_url( $term_url ); ?>"><?php echo esc_html( sprintf( __( '← All %s Policies', 'baltimore' ), $name ) ); ?></a>
					<?php get_template_part( 'template-parts/components/policy-filter' ); ?>
				</div>
				<ul class="balt-policy-sidebar__list">
					<?php foreach ( $groups as $group ) : ?>
						<?php foreach ( array_merge( array( $group['parent'] ), $group['children'] ) as $item ) : ?>
							<li data-policy-item><a href="<?php echo esc_url( get_permalink( $item ) ); ?>"<?php if ( (int) $item->ID === $id ) : ?> aria-current="page"<?php endif; ?>><?php echo esc_html( get_the_title( $item ) ); ?></a></li>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</ul>
				<p class="balt-policy-empty" data-policy-empty<?php if ( $groups ) : ?> hidden<?php endif; ?>><?php esc_html_e( 'No matching policies found.', 'baltimore' ); ?></p>
			</nav>
			<div class="balt-policy-detail__main">
				<article class="balt-policy-document">
					<?php the_title( '<h1 class="balt-policy-document__title">', '</h1>' ); ?>
					<div class="balt-policy-document__body">
						<?php the_content(); wp_link_pages(); ?>
					</div>
				</article>
				<?php if ( $resources ) : ?>
					<aside class="balt-policy-resources" aria-label="<?php esc_attr_e( 'Additional Resources', 'baltimore' ); ?>">
						<h2><?php esc_html_e( 'Additional Resources', 'baltimore' ); ?></h2>
						<ul>
							<?php foreach ( $resources as $link ) : ?>
								<li><a href="<?php echo $link['url']; ?>"<?php if ( '_blank' === $link['target'] ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html( $link['title'] ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</aside>
				<?php endif; ?>
			</div>
		</div>
	</main>
	<?php
endwhile;
get_footer();
