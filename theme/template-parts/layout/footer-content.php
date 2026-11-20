<?php
/**
 * Global footer: branding, menus and contact information.
 *
 * Contact details and social links belong to Theme Settings, not Footer Settings.
 *
 * @package Archdiocese_of_Baltimore
 */

$get_option = static function ( string $name, bool $formatted = true ) {
	return function_exists( 'get_field' ) ? get_field( $name, 'option', $formatted ) : null;
};

$logo_id = absint( $get_option( 'balt_footer_logo' ) );
if ( ! $logo_id ) {
	$logo_id = absint( $get_option( 'balt_site_logo' ) );
}

$blurb = trim( (string) $get_option( 'balt_footer_blurb', false ) );

$social_links = $get_option( 'balt_social_media' );
$social_links = is_array( $social_links ) ? $social_links : array();

$address  = trim( (string) $get_option( 'balt_contact_address', false ) );
$phone    = trim( (string) $get_option( 'balt_contact_phone' ) );
$hours    = trim( (string) $get_option( 'balt_contact_phone_availability' ) );
$email    = sanitize_email( (string) $get_option( 'balt_contact_email' ) );
$tel_href = preg_replace( '/[^0-9+]/', '', $phone );

$legal_links = array();
foreach (
	array(
		'balt_privacy_policy_link' => __( 'Privacy Policy', 'baltimore' ),
		'balt_terms_link'          => __( 'Terms of Service', 'baltimore' ),
		'balt_accessibility_link'  => __( 'Accessibility', 'baltimore' ),
	) as $field => $fallback
) {
	$link = $get_option( $field );
	if ( is_array( $link ) && ! empty( $link['url'] ) ) {
		$legal_links[] = array(
			'url'    => $link['url'],
			'title'  => ! empty( $link['title'] ) ? $link['title'] : $fallback,
			'target' => $link['target'] ?? '',
		);
	}
}

$asset_url = get_template_directory_uri() . '/assets/footer';
?>
<footer id="colophon" class="bg-primary font-body text-light-muted">
	<div class="mx-auto w-full max-w-wide px-6 sm:px-10 lg:px-[97px]">
		<div class="grid grid-cols-1 gap-x-12 gap-y-12 py-16 sm:grid-cols-2 lg:grid-cols-4">
			<div class="min-w-0">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="inline-block no-underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-secondary">
					<?php if ( $logo_id ) : ?>
						<?php echo wp_get_attachment_image( $logo_id, 'medium', false, array( 'class' => 'h-[60px] w-auto max-w-full object-contain', 'alt' => get_bloginfo( 'name' ) ) ); ?>
					<?php else : ?>
						<img src="<?php echo esc_url( $asset_url . '/logo.png' ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" width="138" height="60" class="h-[60px] w-auto object-contain">
					<?php endif; ?>
				</a>

				<?php if ( $blurb ) : ?>
					<p class="mt-5 max-w-[300px] text-base leading-[26px]"><?php echo nl2br( esc_html( $blurb ) ); ?></p>
				<?php endif; ?>

				<?php if ( $social_links ) : ?>
					<ul class="mt-6 flex list-none flex-wrap gap-3 p-0" aria-label="<?php esc_attr_e( 'Social media', 'baltimore' ); ?>">
						<?php foreach ( $social_links as $social ) : ?>
							<?php
							$icon = $social['icon'] ?? '';
							$link = $social['link'] ?? null;
							if ( ! is_string( $icon ) || ! $icon || ! is_array( $link ) || empty( $link['url'] ) ) {
								continue;
							}
							$label = ! empty( $link['title'] ) ? (string) $link['title'] : __( 'Social media profile', 'baltimore' );
							?>
							<li>
								<a href="<?php echo esc_url( $link['url'] ); ?>" aria-label="<?php echo esc_attr( $label ); ?>" target="_blank" rel="noopener noreferrer" class="flex size-9 items-center justify-center rounded-[10px] bg-primary-light text-base text-white no-underline transition-colors hover:bg-white/20 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-secondary">
									<i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="min-w-0">
				<h2 class="font-body text-lg font-medium leading-7 tracking-[-0.025em] text-white"><?php esc_html_e( 'Quick Links', 'baltimore' ); ?></h2>
				<?php if ( has_nav_menu( 'menu-2' ) ) : ?>
					<nav class="mt-6 [&_a]:text-light-muted [&_a]:no-underline [&_a:hover]:text-white [&_a:focus-visible]:text-white" aria-label="<?php esc_attr_e( 'Quick Links', 'baltimore' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'menu-2',
								'container'      => false,
								'menu_class'     => 'm-0 flex list-none flex-col gap-3 p-0 text-base leading-6',
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					</nav>
				<?php endif; ?>
			</div>

			<div class="min-w-0">
				<h2 class="font-body text-lg font-medium leading-7 tracking-[-0.025em] text-white"><?php esc_html_e( 'Our Services', 'baltimore' ); ?></h2>
				<?php if ( has_nav_menu( 'footer-services' ) ) : ?>
					<nav class="mt-6 [&_a]:text-light-muted [&_a]:no-underline [&_a:hover]:text-white [&_a:focus-visible]:text-white" aria-label="<?php esc_attr_e( 'Our Services', 'baltimore' ); ?>">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer-services',
								'container'      => false,
								'menu_class'     => 'm-0 flex list-none flex-col gap-3 p-0 text-base leading-6',
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					</nav>
				<?php endif; ?>
			</div>

			<div class="min-w-0">
				<h2 class="font-body text-lg font-medium leading-7 tracking-[-0.025em] text-white"><?php esc_html_e( 'Contact Us', 'baltimore' ); ?></h2>
				<?php if ( $address || $phone || $email ) : ?>
					<ul class="mt-6 flex list-none flex-col gap-4 p-0 text-base leading-[26px]">
						<?php if ( $address ) : ?>
							<li class="flex items-start gap-3">
								<img src="<?php echo esc_url( $asset_url . '/location.svg' ); ?>" alt="" aria-hidden="true" width="20" height="20" class="mt-0.5 size-5 shrink-0">
								<address class="min-w-0 not-italic"><?php echo nl2br( esc_html( $address ) ); ?></address>
							</li>
						<?php endif; ?>
						<?php if ( $phone ) : ?>
							<li class="flex items-start gap-3">
								<img src="<?php echo esc_url( $asset_url . '/phone.svg' ); ?>" alt="" aria-hidden="true" width="20" height="20" class="mt-0.5 size-5 shrink-0">
								<div class="min-w-0">
									<a href="<?php echo esc_url( 'tel:' . $tel_href ); ?>" class="text-light-muted no-underline hover:text-white"><?php echo esc_html( $phone ); ?></a>
									<?php if ( $hours ) : ?>
										<p class="text-sm leading-5 text-[#99A1AF]"><?php echo esc_html( $hours ); ?></p>
									<?php endif; ?>
								</div>
							</li>
						<?php endif; ?>
						<?php if ( $email ) : ?>
							<li class="flex items-start gap-3">
								<img src="<?php echo esc_url( $asset_url . '/email.svg' ); ?>" alt="" aria-hidden="true" width="20" height="20" class="mt-0.5 size-5 shrink-0">
								<a href="<?php echo esc_url( 'mailto:' . $email ); ?>" class="min-w-0 break-words text-light-muted no-underline hover:text-white"><?php echo esc_html( $email ); ?></a>
							</li>
						<?php endif; ?>
					</ul>
				<?php endif; ?>
			</div>
		</div>

		<div class="flex flex-col gap-4 border-t border-[#2D5180] py-6 text-sm leading-5 md:flex-row md:items-center md:justify-between">
			<p><?php echo esc_html( sprintf( __( '© %1$s %2$s. All rights reserved.', 'baltimore' ), wp_date( 'Y' ), get_bloginfo( 'name' ) ) ); ?></p>

			<?php if ( $legal_links ) : ?>
				<nav aria-label="<?php esc_attr_e( 'Legal and accessibility', 'baltimore' ); ?>">
					<ul class="flex list-none flex-wrap gap-x-6 gap-y-2 p-0">
						<?php foreach ( $legal_links as $link ) : ?>
							<li>
								<a href="<?php echo esc_url( $link['url'] ); ?>"<?php if ( '_blank' === $link['target'] ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?> class="text-light-muted no-underline hover:text-white"><?php echo esc_html( $link['title'] ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
		</div>
	</div>
</footer>
