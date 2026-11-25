<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$eyebrow = trim( (string) get_field( 'eyebrow' ) );
$heading = trim( (string) get_field( 'heading' ) );
$description = trim( (string) get_field( 'description' ) );
$center = 'center' === get_field( 'heading_alignment' );
$phone = trim( (string) get_field( 'phone' ) );
$hours = trim( (string) get_field( 'phone_hours' ) );
$email = sanitize_email( (string) get_field( 'email' ) );
$address = trim( (string) get_field( 'address' ) );
$tel = preg_replace( '/[^0-9+]/', '', $phone );
$id = balt_contact_form_id( get_field( 'form', false, false ) );
$ajax = balt_contact_form_ajax( get_field( 'ajax' ) );
$preview = ! empty( $is_preview );
$form = balt_contact_form_get( $id );
$available = $form && function_exists( 'gravity_form' );
$has_intro = $eyebrow || $heading || $description || $phone || $email || $address;
if ( ! $preview && ! $available && ! $has_intro ) {
	return;
}
$heading_id = wp_unique_id( 'balt-contact-form-' );
?>
<section <?php echo get_block_wrapper_attributes( array( 'class' => 'not-prose balt-full-bleed balt-contact-form relative mt-0! w-full max-w-none bg-foreground py-[clamp(3rem,5.62vw,6rem)] text-white' ) ); ?><?php if ( $heading ) : ?> aria-labelledby="<?php echo esc_attr( $heading_id ); ?>"<?php endif; ?>>
	<div class="mx-auto grid w-full max-w-wide grid-cols-1 items-start gap-y-10 px-gutter <?php echo $has_intro && ( $available || $preview ) ? 'desktop:grid-cols-2 desktop:gap-x-[clamp(2rem,4.68vw,5rem)]' : ''; ?>">
		<?php if ( $has_intro ) : ?>
			<div class="min-w-0">
				<div class="<?php echo $center ? 'text-center' : 'text-left'; ?>">
					<?php if ( $eyebrow ) : ?>
						<p class="font-body text-sm font-semibold leading-5 tracking-[3px] text-secondary uppercase"><?php echo esc_html( $eyebrow ); ?></p>
					<?php endif; ?>
					<?php if ( $heading ) : ?>
						<h2 id="<?php echo esc_attr( $heading_id ); ?>" class="<?php echo $eyebrow ? 'mt-4 ' : ''; ?>font-heading text-[clamp(2rem,3.6vw,3rem)] font-black leading-[1.25] text-white text-balance"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>
					<?php if ( $description ) : ?>
						<p class="mt-5 whitespace-pre-line font-body text-base leading-[26px] text-white/70"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( $phone || $email || $address ) : ?>
					<ul class="mt-8 flex list-none flex-col gap-4 p-0 font-body text-sm leading-5 text-white/75">
						<?php if ( $phone ) : ?>
							<li class="flex items-start gap-3">
								<span class="balt-contact-icon balt-contact-icon--phone mt-0.5" aria-hidden="true"></span>
								<div class="min-w-0">
									<?php if ( $tel && ! $preview ) : ?>
										<a href="<?php echo esc_url( 'tel:' . $tel ); ?>" class="text-inherit no-underline hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary"><?php echo esc_html( $phone ); ?></a>
									<?php else : ?>
										<?php echo esc_html( $phone ); ?>
									<?php endif; ?>
									<?php if ( $hours ) : ?><p class="mt-0.5 text-xs leading-4 text-white/60"><?php echo esc_html( $hours ); ?></p><?php endif; ?>
								</div>
							</li>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<li class="flex items-start gap-3">
								<span class="balt-contact-icon balt-contact-icon--email mt-0.5" aria-hidden="true"></span>
								<?php if ( $preview ) : ?><span class="min-w-0 wrap-break-word"><?php echo esc_html( $email ); ?></span><?php else : ?>
									<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="min-w-0 wrap-break-word text-inherit no-underline hover:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary"><?php echo esc_html( $email ); ?></a>
								<?php endif; ?>
							</li>
						<?php endif; ?>
						<?php if ( $address ) : ?>
							<li class="flex items-start gap-3">
								<span class="balt-contact-icon balt-contact-icon--location mt-0.5" aria-hidden="true"></span>
								<address class="min-w-0 whitespace-pre-line wrap-break-word not-italic"><?php echo esc_html( $address ); ?></address>
							</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<?php if ( $available || $preview ) : ?>
			<div class="balt-contact-form__card min-w-0 rounded-card border border-white/10 bg-white/5 p-5 sm:p-8">
				<?php if ( $preview ) : ?>
					<?php if ( $available ) : ?>
						<p class="font-body text-sm font-bold text-secondary"><?php esc_html_e( 'Gravity Form', 'baltimore' ); ?></p>
						<p class="mt-2 text-lg font-bold text-white"><?php echo esc_html( $form['title'] ?? '' ); ?></p>
						<p class="mt-2 text-sm text-white/75"><?php esc_html_e( 'The selected form is rendered on the published page. Edit its fields and submit button in Gravity Forms.', 'baltimore' ); ?></p>
					<?php else : ?>
						<p class="text-sm text-white/75"><?php esc_html_e( 'Select an active Gravity Form in the block settings. Gravity Forms and its ACF add-on must be installed.', 'baltimore' ); ?></p>
					<?php endif; ?>
				<?php else : ?>
					<?php gravity_form( $id, false, false, false, null, $ajax, 0, true ); ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>
</section>
