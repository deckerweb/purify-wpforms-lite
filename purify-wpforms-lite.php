<?php # -*- coding: utf-8 -*-
/*
Plugin Name:       Purify WPForms Lite
Plugin URI:        https://github.com/deckerweb/purify-wpforms-lite
Description:       Cleanup the (free) Lite version of WPForms to make it usable. Purify the admin screens to speed up your daily form building :-)
Project:           Code Snippet: DDW Purify WPForms Lite
Version:           1.0.0
Author:            David Decker – DECKERWEB
Author URI:        https://deckerweb.de/
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:       purify-wpforms-lite
Domain Path:       /languages/
Requires WP:       6.7
Requires PHP:      7.4
GitHub Plugin URI: https://github.com/deckerweb/purify-wpforms-lite
GitHub Branch:     master
Copyright:         © 2025, David Decker – DECKERWEB

TESTED WITH:
Product			Versions
--------------------------------------------------------------------------------------------------------------
PHP 			8.0, 8.3
WordPress		6.7.2 ... 6.8 Beta
WPForms Lite	1.9.4.2
--------------------------------------------------------------------------------------------------------------

VERSION HISTORY:
Date        Version     Description
--------------------------------------------------------------------------------------------------------------
2025-04-??	1.0.0       Initial public release
2025-04-04	0.0.0	    Development start
--------------------------------------------------------------------------------------------------------------
*/

/** Prevent direct access */
if ( ! defined( 'ABSPATH' ) ) exit;  // Exit if accessed directly.

if ( ! class_exists( 'DDW_Purify_WPForms_Lite' ) ) :

class DDW_Purify_WPForms_Lite {

	/** Class constants & variables */
	private const VERSION = '1.0.0';

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'init',                       array( $this, 'init' ), 1 );
		add_action( 'admin_menu',                 array( $this, 'remove_submenus' ), 200 );
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_nodes' ) );
		add_action( 'admin_enqueue_scripts',      array( $this, 'enqueue_admin_styles' ), 200 );  // for Admin
	}
	
	/**
	 * Check if WPForms Lite is activated or not.
	 *
	 * @return bool TRUE when WPForms Lite is active, FALSE otherwise.
	 */
	private function is_wpforms_lite() {
		if ( class_exists( 'WPForms_Lite' ) ) return TRUE;
	}
	
	/**
	 * Remove WPForms Lite Dashboard widget.
	 */
	public function init() {
		add_filter( 'wpforms_admin_dashboardwidget', '__return_false' );
	}
	
	/**
	 * Load translations.
	 *   Normally we wouldn't do that since WP 6.5, but since this plugin does not come from wordpress.org plugin repository, we have to care for loading ourselves. We first look in wp-content/languages subfolder, then in plugin subfolder. That way translations can also be used for code snippet version of this plugin.
	 *
	 * @uses get_user_locale() | load_textdomain() | load_plugin_textdomain()
	 */
	public function load_translations() {
		
		/** Set unique textdomain string */
		$pwfl_textdomain = 'purify-wpforms-lite';
		
		/** The 'plugin_locale' filter is also used by default in load_plugin_textdomain() */
		$locale = apply_filters( 'plugin_locale', get_user_locale(), $pwfl_textdomain );
		
		/**
		 * WordPress languages directory
		 *   Will default to: wp-content/languages/purify-wpforms-lite/purify-wpforms-lite-{locale}.mo
		 */
		$pwfl_wp_lang_dir = trailingslashit( WP_LANG_DIR ) . trailingslashit( $pwfl_textdomain ) . $pwfl_textdomain . '-' . $locale . '.mo';
		
		/** Translations: First, look in WordPress' "languages" folder = custom & update-safe! */
		load_textdomain( $pwfl_textdomain, $pwfl_wp_lang_dir );
		
		/** Secondly, look in plugin's "languages" subfolder = default */
		load_plugin_textdomain( $pwfl_textdomain, FALSE, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . 'languages' );
	}
	
	/**
	 * Remove promotional submenus which have no value at all.
	 */
	public function remove_submenus() {
		if ( ! $this->is_wpforms_lite() ) return;
		
		remove_submenu_page( 'wpforms-overview', 'wpforms-entries' );
		remove_submenu_page( 'wpforms-overview', 'wpforms-community' );
		remove_submenu_page( 'wpforms-overview', 'wpforms-addons' );
		remove_submenu_page( 'wpforms-overview', 'wpforms-analytics' );
		remove_submenu_page( 'wpforms-overview', 'wpforms-smtp' );
		remove_submenu_page( 'wpforms-overview', 'wpforms-about' );
		remove_submenu_page( 'wpforms-overview', esc_url( 'https://wpforms.com/lite-upgrade/?utm_campaign=liteplugin&utm_medium=admin-menu&utm_source=WordPress&utm_content=Upgrade+to+Pro' ) );
	}
	
	/**
	 * Remove promotional Admin Bar nodes which have no value at all.
	 *   ALSO: Remove some nodes here, only to re-add them later on but with
	 *         tweaked properties.
	 */
	public function remove_admin_bar_nodes() {
		
		if ( ! $this->is_wpforms_lite() ) return;
		
		global $wp_admin_bar;
		
		$wp_admin_bar->remove_node( 'wpforms-upgrade' );
		$wp_admin_bar->remove_node( 'wpforms-tools-wpcode' );
		$wp_admin_bar->remove_node( 'wpforms-geolocation-settings' );
		$wp_admin_bar->remove_node( 'wpforms-access-settings' );
	}
	
	/**
	 * Add CSS styling for the Admin.
	 */
	public function enqueue_admin_styles() {
		
		if ( ! $this->is_wpforms_lite() ) return;
		
		/** Inline styles for the Admin Area */
		$inline_css_wpadmin = sprintf(
			'
				/** Remove stuff */
				.wp-submenu .wpforms-sidebar-upgrade-pro,
				.wpforms-dyk,
				.wpforms-education-lite-connect-setting,
				#wpforms-notifications,
				#wpforms-flyout,
				#wpforms-setup-templates-list .wpforms-template.pro,
				#wpforms-builder-lite-connect-top-bar,
				#wpforms-setting-row-license-heading,
				#wpforms-setting-row-license-key,
				#wpforms-setting-row-lite-connect-enabled,
				.wpforms-context-menu-list .wpforms-context-menu-list-item[data-action="upgrade"],
				.wpforms-admin-tabs li:has(a[href*="view=geolocation"]),
				.wpforms-admin-tabs li:has(a[href*="view=access"]),
				.wpforms-admin-tabs li:has(a[href*="view=wpcode"]),
				.wpforms-settings-provider.education-modal,
				.wpforms-settings-field-radio-wrapper.education-modal,
				.wpforms-smtp-education-notice.wpforms-dismiss-container,
				#toplevel_page_wpforms-overview span.wpforms-menu-new,
				.plugins-php tr[data-slug="wpforms-lite"] .wpforms-pro,
				.wp-submenu li.wpforms-sidebar-upgrade-pro,
				.wpforms-challenge.wpforms-challenge-start,
				.wpforms-challenge.paused,
				.wpforms-challenge,
				.wpforms-admin-page .settings-lite-cta,
				#wpforms-notice-bar,
				.wpforms-context-menu-list .wpforms-context-menu-list-item.education-modal {
					content: none !important;
					display: none !important;
				}
				
				@media screen and (min-width: 1024px) {
					.wpforms-admin-page #wpforms-notice-bar + #wpforms-header-temp {
						top: inherit;
					}
				}
			'
		);
		
		wp_add_inline_style( 'wp-admin', $inline_css_wpadmin );

		/** Inline styles for the Form Builder */
		wp_register_style( 'pwfl-builder', false );
		wp_enqueue_style( 'pwfl-builder' );
		
		$builder_inline_styles = sprintf(
			'
				/** Remove stuff */
				.wpforms-challenge.wpforms-challenge-start,
				.wpforms-challenge.paused,
				.wpforms-challenge,
				.wpforms-context-menu-list .wpforms-context-menu-list-item.education-modal,
				.wpforms-add-fields-group:has(a[data-group="fancy"]),
				.wpforms-add-fields-group .wpforms-not-available,
				.wpforms-field-option-group.wpforms-field-option-group-conditionals,
				.wpforms-panel-sidebar-section.education-modal,
				.wpforms-panel-content-also-available-item:has(div a.wpforms-panel-content-also-available-item-upgrade-to-pro),
				.wpforms-alert-ai.wpforms-educational-alert,
				.wpforms-field-option-row.education-modal,
				.wpforms-dyk,
				#wpforms-panel-field-confirmations-1-message_entry_preview-wrap {
					content: none !important;
					display: none !important;
				}
			'
		);

		wp_add_inline_style( 'pwfl-builder', $builder_inline_styles );
	}
	
}  // end of class

new DDW_Purify_WPForms_Lite();
	
endif;


if ( ! function_exists( 'ddw_pwfl_pluginrow_meta' ) ) :
	
add_filter( 'plugin_row_meta', 'ddw_pwfl_pluginrow_meta', 10, 2 );
/**
* Add plugin related links to plugin page.
*
* @param array  $ddwp_meta (Default) Array of plugin meta links.
* @param string $ddwp_file File location of plugin.
* @return array $ddwp_meta (Modified) Array of plugin links/ meta.
*/
function ddw_pwfl_pluginrow_meta( $ddwp_meta, $ddwp_file ) {

	if ( ! current_user_can( 'install_plugins' ) ) return $ddwp_meta;
	
	/** Get current user */
	$user = wp_get_current_user();
	
	/** Build Newsletter URL */
	$url_nl = sprintf(
		'https://deckerweb.us2.list-manage.com/subscribe?u=e09bef034abf80704e5ff9809&amp;id=380976af88&amp;MERGE0=%1$s&amp;MERGE1=%2$s',
		esc_attr( $user->user_email ),
		esc_attr( $user->user_firstname )
	);
	
	/** List additional links only for this plugin */
	if ( $ddwp_file === trailingslashit( dirname( plugin_basename( __FILE__ ) ) ) . basename( __FILE__ ) ) {
		$ddwp_meta[] = sprintf(
			'<a class="button button-inline" href="https://ko-fi.com/deckerweb" target="_blank" rel="nofollow noopener noreferrer" title="%1$s">❤ <b>%1$s</b></a>',
			esc_html_x( 'Donate', 'Plugins page listing', 'purify-wpforms-lite' )
		);
		
		$ddwp_meta[] = sprintf(
			'<a class="button-primary" href="%1$s" target="_blank" rel="nofollow noopener noreferrer" title="%2$s">⚡ <b>%2$s</b></a>',
			$url_nl,
			esc_html_x( 'Join our Newsletter', 'Plugins page listing', 'purify-wpforms-lite' )
		);
	}  // end if
	
	return apply_filters( 'ddw/admin_extras/pluginrow_meta', $ddwp_meta );

}  // end function

endif;