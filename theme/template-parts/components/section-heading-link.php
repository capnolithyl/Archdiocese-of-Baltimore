<?php
/** Compact section-header CTA, using the existing Feature Grid arrow. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$link = $args['link'] ?? array();
$url = esc_url( $link['url'] ?? '' );
$label = trim( (string) ( $link['title'] ?? '' ) );
if ( ! $url || '' === $label ) {
	return;
}
$tag = empty( $args['preview'] ) ? 'a' : 'span';
?>
<<?php echo $tag; ?> class="inline-flex items-center gap-2 font-body text-sm font-bold leading-5 tracking-[0.025em] text-primary uppercase no-underline hover:underline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"<?php if ( 'a' === $tag ) : ?> href="<?php echo $url; ?>"<?php if ( '_blank' === ( $link['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?><?php endif; ?>>
	<?php echo esc_html( $label ); ?>
	<svg aria-hidden="true" viewBox="0 0 13 13" width="13" height="13" fill="none" stroke="currentColor" stroke-width="1.08333" stroke-linecap="round" stroke-linejoin="round"><path d="M2.7083 6.5h7.5834M6.5 2.7083l3.7917 3.7917-3.7917 3.7917"/></svg>
</<?php echo $tag; ?>>
