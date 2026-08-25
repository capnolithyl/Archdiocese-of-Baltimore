<?php
/**
 * ACF options pages and admin helpers.
 *
 * @package Archdiocese_of_Baltimore
 */

/**
 * Register the theme options pages.
 */
function balt_register_acf_options_pages() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => __( 'Theme Settings', 'baltimore' ),
			'menu_title' => __( 'Theme Settings', 'baltimore' ),
			'menu_slug'  => 'theme-settings',
			'capability' => 'edit_theme_options',
			'icon_url'   => 'dashicons-admin-generic',
			'redirect'   => false,
		)
	);

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Header Settings', 'baltimore' ),
			'menu_title'  => __( 'Header Settings', 'baltimore' ),
			'menu_slug'   => 'theme-header-settings',
			'parent_slug' => 'theme-settings',
			'capability'  => 'edit_theme_options',
		)
	);

	acf_add_options_sub_page(
		array(
			'page_title'  => __( 'Footer Settings', 'baltimore' ),
			'menu_title'  => __( 'Footer Settings', 'baltimore' ),
			'menu_slug'   => 'theme-footer-settings',
			'parent_slug' => 'theme-settings',
			'capability'  => 'edit_theme_options',
		)
	);
}
add_action( 'acf/init', 'balt_register_acf_options_pages' );

/**
 * Get the best admin URL for editing a registered menu location.
 *
 * If a menu is assigned, link directly to that menu. Otherwise link to the
 * menu-locations screen so an editor can assign one first.
 *
 * @param string $location Menu location slug.
 * @return string
 */
function balt_get_menu_admin_url( $location ) {
	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations[ $location ] ) ? absint( $locations[ $location ] ) : 0;

	if ( $menu_id ) {
		return admin_url( 'nav-menus.php?action=edit&menu=' . $menu_id );
	}

	return admin_url( 'nav-menus.php?action=locations' );
}

/**
 * Populate Footer Settings shortcut fields with environment-safe admin URLs.
 *
 * @param array $field ACF field configuration.
 * @return array
 */
function balt_load_footer_settings_shortcut( $field ) {
	$theme_settings_url = admin_url( 'admin.php?page=theme-settings' );

	switch ( $field['key'] ) {
		case 'field_balt_footer_social_shortcut':
			$field['message'] = sprintf(
				'<a class="button button-secondary" href="%1$s">%2$s</a><p class="description">%3$s</p>',
				esc_url( $theme_settings_url ),
				esc_html__( 'Edit Social Media', 'baltimore' ),
				esc_html__( 'Social links are managed under Theme Settings → Social Media.', 'baltimore' )
			);
			break;

		case 'field_balt_footer_menu_shortcut':
			$field['message'] = sprintf(
				'<a class="button button-secondary" href="%1$s">%2$s</a>',
				esc_url( balt_get_menu_admin_url( 'menu-2' ) ),
				esc_html__( 'Edit Footer Menu', 'baltimore' )
			);
			break;

		case 'field_balt_footer_services_shortcut':
			$field['message'] = sprintf(
				'<a class="button button-secondary" href="%1$s">%2$s</a>',
				esc_url( balt_get_menu_admin_url( 'footer-services' ) ),
				esc_html__( 'Edit Our Services Menu', 'baltimore' )
			);
			break;

		case 'field_balt_footer_contact_shortcut':
			$field['message'] = sprintf(
				'<a class="button button-secondary" href="%1$s">%2$s</a><p class="description">%3$s</p>',
				esc_url( $theme_settings_url ),
				esc_html__( 'Edit Contact Information', 'baltimore' ),
				esc_html__( 'Contact details are managed under Theme Settings → Contact.', 'baltimore' )
			);
			break;
	}

	return $field;
}
add_filter( 'acf/load_field/key=field_balt_footer_social_shortcut', 'balt_load_footer_settings_shortcut' );
add_filter( 'acf/load_field/key=field_balt_footer_menu_shortcut', 'balt_load_footer_settings_shortcut' );
add_filter( 'acf/load_field/key=field_balt_footer_services_shortcut', 'balt_load_footer_settings_shortcut' );
add_filter( 'acf/load_field/key=field_balt_footer_contact_shortcut', 'balt_load_footer_settings_shortcut' );
