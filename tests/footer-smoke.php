<?php
/**
 * Check the global footer using lightweight WordPress/ACF stubs.
 */
declare(strict_types=1);

define( 'ABSPATH', __DIR__ . '/' );

$options = array(
	'balt_footer_logo'              => 84,
	'balt_footer_blurb'             => "A welcome statement\nfor our visitors.",
	'balt_social_media'             => array(
		array(
			'icon' => 'fa-brands fa-facebook',
			'link' => array( 'url' => 'https://facebook.com/example', 'title' => 'Facebook', 'target' => '_blank' ),
		),
	),
	'balt_contact_address'          => "320 Cathedral Street\nBaltimore, MD 21201",
	'balt_contact_phone'            => '(410) 547-5437',
	'balt_contact_phone_availability' => 'Mon–Fri, 8am–6pm',
	'balt_contact_email'            => 'info@example.org',
	'balt_privacy_policy_link'      => array( 'url' => '/privacy/', 'title' => 'Privacy Policy', 'target' => '' ),
	'balt_terms_link'               => array( 'url' => 'https://example.org/terms', 'title' => 'Terms of Service', 'target' => '_blank' ),
	'balt_accessibility_link'       => array( 'url' => '/accessibility/', 'title' => 'Accessibility', 'target' => '' ),
);

function get_field( string $field, string $location, bool $formatted = true ) {
	if ( 'option' !== $location ) {
		throw new RuntimeException( 'Footer fields must be sitewide options' );
	}
	return $GLOBALS['options'][ $field ] ?? null;
}
function absint( $value ): int { return abs( (int) $value ); }
function sanitize_email( string $value ): string { return filter_var( $value, FILTER_SANITIZE_EMAIL ); }
function esc_url( string $value ): string { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_attr( string $value ): string { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function esc_html( string $value ): string { return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' ); }
function __( string $text, string $domain ): string { return $text; }
function esc_html_e( string $text, string $domain ): void { echo esc_html( $text ); }
function esc_attr_e( string $text, string $domain ): void { echo esc_attr( $text ); }
function get_bloginfo( string $field ): string { return 'Archdiocese of Baltimore'; }
function wp_date( string $format ): string { return '2026'; }
function home_url( string $path ): string { return 'https://example.org' . $path; }
function get_template_directory_uri(): string { return '/theme'; }
function wp_get_attachment_image( int $id, string $size, bool $icon, array $attrs ): string {
	return '<img src="/uploads/' . $id . '.png" alt="' . esc_attr( $attrs['alt'] ) . '">';
}
function has_nav_menu( string $location ): bool { return in_array( $location, array( 'menu-2', 'footer-services' ), true ); }
function wp_nav_menu( array $args ): void {
	echo '<ul class="' . esc_attr( $args['menu_class'] ) . '"><li><a href="/example">' . esc_html( $args['theme_location'] ) . '</a></li></ul>';
}

function render_footer(): string {
	ob_start();
	require dirname( __DIR__ ) . '/theme/template-parts/layout/footer-content.php';
	return (string) ob_get_clean();
}

function must_contain( string $html, string $token ): void {
	if ( ! str_contains( $html, $token ) ) {
		throw new RuntimeException( 'Footer missing ' . $token );
	}
}

$html = render_footer();
foreach (
	array(
		'<footer id="colophon"', 'lg:grid-cols-4', '/uploads/84.png',
		'A welcome statement', 'for our visitors.',
		'fa-brands fa-facebook', 'facebook.com/example', 'Quick Links', 'Our Services',
		'menu-2', 'footer-services', '320 Cathedral Street', 'Baltimore, MD 21201',
		'tel:4105475437', 'Mon–Fri, 8am–6pm', 'mailto:info@example.org',
		'/theme/assets/footer/location.svg', '/theme/assets/footer/phone.svg',
		'/theme/assets/footer/email.svg', '/privacy/', 'https://example.org/terms',
		'/accessibility/', '© 2026 Archdiocese of Baltimore',
	) as $token
) {
	must_contain( $html, $token );
}
must_contain( $html, 'target="_blank" rel="noopener noreferrer"' );

$options['balt_footer_logo'] = 0;
$options['balt_site_logo'] = 0;
$options['balt_social_media'] = array();
$options['balt_contact_address'] = '';
$options['balt_contact_phone'] = '';
$options['balt_contact_email'] = '';
$options['balt_privacy_policy_link'] = null;
$options['balt_terms_link'] = null;
$options['balt_accessibility_link'] = null;

$empty = render_footer();
must_contain( $empty, '/theme/assets/footer/logo.png' );
if ( str_contains( $empty, 'href="tel:' ) || str_contains( $empty, 'href="mailto:' ) ||
	str_contains( $empty, 'Social media profile' ) || str_contains( $empty, 'href="/privacy/"' ) ) {
	throw new RuntimeException( 'Missing optional settings should not produce broken links' );
}
echo "Footer global settings, menus, links, logo fallback and responsive markup passed\n";
