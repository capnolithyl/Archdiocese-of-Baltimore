<?php
/**
 * Optional template-level gold section navigation, Figma 320:2184.
 *
 * @package Archdiocese_of_Baltimore
 *
 * @var array $args Template arguments.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$post_id = isset( $args['post_id'] ) ? (int) $args['post_id'] : get_the_ID();
$links   = balt_interior_section_links( $post_id );

if ( ! $links ) {
	return;
}
?>
<nav class="balt-section-nav" aria-label="<?php esc_attr_e( 'Section navigation', 'baltimore' ); ?>">
	<div class="balt-section-nav__scroll">
		<ul class="balt-section-nav__items">
			<?php foreach ( $links as $link ) : ?>
				<li class="balt-section-nav__item">
					<a class="balt-section-nav__link" href="<?php echo esc_url( $link['url'] ); ?>"<?php if ( '_blank' === $link['target'] ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html( $link['label'] ); ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>
