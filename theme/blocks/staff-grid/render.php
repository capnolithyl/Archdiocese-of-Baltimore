<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = trim( (string) get_field( 'eyebrow' ) );
$heading = trim( (string) get_field( 'heading' ) );
$preview = ! empty( $is_preview );
$members = array();
foreach ( (array) get_field( 'members' ) as $member ) {
	if ( ! is_array( $member ) ) {
		continue;
	}
	$data = balt_staff_grid_member_data( $member );
	if ( null !== $data ) {
		$members[] = $data;
	}
}

if ( ! $members && ! $preview ) {
	return;
}

$link = get_field( 'team_link' );
$url = is_array( $link ) ? esc_url( $link['url'] ?? '' ) : '';
$label = is_array( $link ) ? trim( (string) ( $link['title'] ?? '' ) ) : '';
$label = $label ?: __( 'Meet the Whole Team', 'baltimore' );
$icon = get_field( 'team_icon' );
$icon = is_string( $icon ) && '' !== trim( $icon ) ? $icon : 'fa-solid fa-users';
$heading_id = wp_unique_id( 'balt-staff-grid-' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed balt-staff-grid relative mt-0! w-full max-w-none bg-white py-[clamp(3rem,5.62vw,6rem)] text-foreground' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
	<div class="mx-auto w-full max-w-wide px-gutter">
		<?php if ( $eyebrow || $heading ) : ?>
			<header class="text-center">
				<?php if ( $eyebrow ) : ?>
					<p class="font-body text-sm font-bold leading-5 tracking-[3px] text-tertiary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<?php if ( $heading ) : ?>
					<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="<?php echo $eyebrow ? 'mt-3 ' : ''; ?>font-heading text-[clamp(2rem,3vw,2.5rem)] font-extrabold leading-[1.5] text-foreground text-balance"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
			</header>
		<?php endif; ?>
		<?php if ( $members ) : ?>
			<div class="<?php echo $eyebrow || $heading ? 'mt-12 ' : ''; ?>flex flex-wrap justify-center gap-x-6 gap-y-12 desktop:gap-x-12">
				<?php foreach ( $members as $member ) : ?>
					<?php
					$photo = $member['photo'] ? wp_get_attachment_image(
						$member['photo'], 'medium_large', false,
						array( 'class' => 'm-0! size-full object-cover object-center', 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 640px) 192px, 224px' )
					) : '';
					?>
					<article class="flex w-full min-w-0 flex-col items-center text-center sm:w-[calc((100%_-_1.5rem)/2)] desktop:w-[calc((100%_-_9rem)/4)]">
						<div class="mb-5 flex size-48 max-w-full shrink-0 items-center justify-center overflow-hidden rounded-full border-4 border-[#F5F3EE] bg-[#F5F3EE] shadow-md desktop:size-56">
							<?php if ( $photo ) : ?>
								<?php echo $photo; ?>
							<?php else : ?>
								<span class="font-heading text-6xl font-bold text-primary/60" aria-hidden="true"><?php echo esc_html( function_exists( 'mb_substr' ) ? mb_substr( $member['name'], 0, 1 ) : preg_replace( '/^(.).*$/us', '$1', $member['name'] ) ); ?></span>
							<?php endif; ?>
						</div>
						<h3 class="font-body text-base font-bold leading-[22px] tracking-[0.025em] text-foreground uppercase wrap-break-word"><?php echo esc_html( $member['name'] ); ?></h3>
						<?php if ( $member['role'] ) : ?>
							<p class="mt-1 font-body text-sm leading-[22.75px] text-muted italic wrap-break-word"><?php echo esc_html( $member['role'] ); ?></p>
						<?php endif; ?>
					</article>
				<?php endforeach; ?>
				<?php if ( $url ) : ?>
					<div class="flex w-full min-w-0 flex-col items-center text-center sm:w-[calc((100%_-_1.5rem)/2)] desktop:w-[calc((100%_-_9rem)/4)]">
						<div class="mb-5 flex size-48 max-w-full shrink-0 items-center justify-center rounded-full border-2 border-[#E5E7EB] text-[#C2D0E1] desktop:size-56" aria-hidden="true">
							<i class="<?php echo esc_attr( $icon ); ?> text-[80px] leading-none"></i>
						</div>
						<?php $tag = $preview ? 'span' : 'a'; ?>
						<<?php echo $tag; ?> class="inline-flex max-w-full items-center justify-center bg-primary px-7 py-3 text-center font-body text-xs font-bold leading-4 tracking-[2px] text-white uppercase no-underline transition-colors hover:bg-primary-light focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary motion-reduce:transition-none"<?php if ( ! $preview ) : ?> href="<?php echo $url; ?>"<?php if ( '_blank' === ( $link['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?><?php endif; ?>><?php echo esc_html( $label ); ?></<?php echo $tag; ?>>
					</div>
				<?php endif; ?>
			</div>
		<?php elseif ( $preview ) : ?>
			<p class="mt-12 text-center text-muted"><?php esc_html_e( 'Add at least one staff member to populate this grid.', 'baltimore' ); ?></p>
		<?php endif; ?>
	</div>
</section>
