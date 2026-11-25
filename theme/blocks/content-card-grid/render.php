<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow    = trim( (string) get_field( 'eyebrow' ) );
$heading    = trim( (string) get_field( 'heading' ) );
$subheading = trim( (string) get_field( 'subheading' ) );
$source     = get_field( 'source' ) ?: 'manual';
$show_date  = (bool) get_field( 'show_date' );
$cards = match ( $source ) {
	'selected' => balt_content_grid_post_cards( (array) get_field( 'selected_posts' ), $show_date ),
	'latest'   => balt_content_grid_latest_cards( (string) ( get_field( 'post_type' ) ?: 'post' ), (int) ( get_field( 'post_count' ) ?: 6 ), (int) get_field( 'category' ), $show_date ),
	default    => balt_content_grid_manual_cards( (array) get_field( 'cards' ) ),
};
if ( ! $cards && empty( $is_preview ) ) {
	return;
}
$view_all = get_field( 'view_all_link' );
$left_heading = 'left' === get_field( 'heading_alignment' );
$header_link = $left_heading && is_array( $view_all ) && ! empty( $view_all['url'] ) && '' !== esc_url( $view_all['url'] ) && '' !== trim( (string) ( $view_all['title'] ?? '' ) );
$heading_id = wp_unique_id( 'balt-content-card-grid-' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed relative mt-0! w-full max-w-none border-y border-border bg-white py-[clamp(3rem,5.62vw,6rem)] text-foreground' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
	<div class="mx-auto w-full max-w-wide px-gutter">
		<?php if ( $eyebrow || $heading || $subheading || $header_link ) : ?>
			<header class="<?php echo $left_heading ? 'flex flex-wrap items-end justify-between gap-4 text-left' : 'text-center'; ?>">
				<div class="min-w-0">
				<?php if ( $eyebrow ) : ?>
					<p class="eyebrow leading-5 tracking-[3px]"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( $heading ) : ?>
					<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="<?php echo $left_heading ? 'mt-2 text-[clamp(2rem,3vw,2.2rem)] leading-[1.5]' : 'mt-3 text-[clamp(2rem,3vw,2.5rem)] leading-[1.25]'; ?> font-heading font-extrabold text-foreground text-balance"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
				<?php if ( $subheading ) : ?>
					<p class="<?php echo $left_heading ? '' : 'mx-auto '; ?>mt-3 max-w-[520px] font-body text-base leading-[26px] text-muted"><?php echo esc_html( $subheading ); ?></p>
				<?php endif; ?>
				</div>
				<?php if ( $header_link ) : ?>
					<?php get_template_part( 'template-parts/components/section-heading-link', null, array( 'link' => $view_all, 'preview' => ! empty( $is_preview ) ) ); ?>
				<?php endif; ?>
			</header>
		<?php endif; ?>

		<?php if ( $cards ) : ?>
			<?php get_template_part( 'template-parts/components/content-card-grid', null, array( 'cards' => $cards, 'accent' => true, 'preview' => ! empty( $is_preview ), 'compact_spacing' => $left_heading ) ); ?>
		<?php else : ?>
			<p class="mt-10 text-center text-body"><?php esc_html_e( 'Add cards or select published content to display.', 'baltimore' ); ?></p>
		<?php endif; ?>

		<?php if ( ! $header_link && is_array( $view_all ) && ! empty( $view_all['url'] ) ) : ?>
			<div class="mt-12 text-center">
				<?php echo wp_kses_post( dpi_blocks_render_link( $view_all, 'btn btn-primary', ! empty( $is_preview ) ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
