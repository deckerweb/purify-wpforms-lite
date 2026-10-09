<?php
/**
 * Plugin Name: Purify WPForms Lite
 * Plugin URI: https://github.com/deckerweb/purify-wpforms-lite
 * Description: Remove promotional elements from WPForms Lite while preserving useful free features.
 * Version: 1.1.0
 * Requires at least: 6.7
 * Requires PHP: 8.0
 * Author: David Decker – DECKERWEB
 * Author URI: https://github.com/deckerweb
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: purify-wpforms-lite
 * Domain Path: /languages/
 * Update URI: https://github.com/deckerweb/purify-wpforms-lite
 * GitHub Plugin URI: https://github.com/deckerweb/purify-wpforms-lite
 *
 * Copyright © 2022–2026 David Decker – DECKERWEB.
 * SPDX-License-Identifier: GPL-2.0-or-later
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'DDW_Purify_WPForms_Lite', false ) ) {
/** Clean source-audited Lite promotions without changing form data or settings. */
final class DDW_Purify_WPForms_Lite {
    private const VERSION = '1.1.0';

    /**
     * Register host translations and narrowly scoped admin cleanup.
     * @return void Registers hooks without changing persisted data.
     */
    public function __construct() {
        add_action( 'init', array( $this, 'load_translations' ), 0 );
        add_action( 'admin_menu', array( $this, 'remove_submenus' ), 999 );
        add_action( 'admin_bar_menu', array( $this, 'remove_admin_bar_nodes' ), 1300 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ), 200 );
        add_filter( 'wpforms_settings_license_output', array( $this, 'license_output' ) );
        add_filter( 'wpforms_settings_tabs', array( $this, 'settings_tabs' ), 100 );
        add_filter( 'wpforms_overview_row_actions', array( $this, 'form_row_actions' ), 1000 );
        add_filter( 'wpforms_admin_dashboard_widget_pipeline_get_widgets', array( $this, 'dashboard_widgets' ), 1000 );
        add_filter( 'wpforms_tools_views', array( $this, 'tool_views' ), 1000 );
        add_filter( 'wpforms_admin_tools_views_import_get_sub_views', array( $this, 'entry_transfer_views' ), 1000 );
        add_filter( 'wpforms_admin_tools_views_export_get_sub_views', array( $this, 'entry_transfer_views' ), 1000 );
        add_filter( 'wpforms_admin_payments_payments_get_views', array( $this, 'payment_views' ), 1000 );
        add_filter( 'wpforms_builder_fields_buttons', array( $this, 'field_buttons' ), 1000 );
        add_filter( 'wpforms_admin_flyoutmenu', array( $this, 'quiet_flyout' ) );
        add_filter( 'wpforms_admin_dashboardwidget', array( $this, 'quiet_flyout' ) );
        add_filter( 'wpforms_admin_builder_preview_dropdown_education_items_get_items', array( $this, 'preview_promotions' ), 1000 );
        add_action( 'init', array( $this, 'remove_promotion_callbacks' ), 999 );
        add_action( 'admin_init', array( $this, 'remove_promotion_callbacks' ), -100 );
        add_action( 'current_screen', array( $this, 'remove_promotion_callbacks' ), 1000 );
        add_action( 'wpforms_builder_init', array( $this, 'remove_promotion_callbacks' ), 1000 );
        add_action( 'admin_enqueue_scripts', array( $this, 'remove_promotion_callbacks' ), 1 );
        add_filter( 'rest_pre_dispatch', array( $this, 'block_consent_wizard_install' ), 10, 3 );
        add_filter( 'upgrader_pre_download', array( $this, 'block_consent_download' ), 10, 4 );
    }

    /**
     * Omit the paid entries shortcut from each Lite form row.
     * @param array $actions Existing form row action links.
     * @return array Actions with editing, preview, duplication and other native operations retained.
     */
    public function form_row_actions( $actions ) {
        if ( self::supported() && is_array( $actions ) ) { unset( $actions['entries'] ); }
        return $actions;
    }

    /**
     * Keep operational dashboard widgets and remove known promotional cards.
     * @param array $ids Native dashboard widget registry identifiers.
     * @return array Widget IDs without marketing or unavailable Lite location cards.
     */
    public function dashboard_widgets( $ids ) {
        if ( ! self::supported() || ! is_array( $ids ) ) { return $ids; }
        return array_values( array_diff( $ids, array(
            'dashboard_widget_locations', 'dashboard_widget_whats_new', 'dashboard_widget_license',
            'dashboard_widget_wp_mail_smtp', 'dashboard_widget_features_addons', 'dashboard_widget_integrations',
            'dashboard_widget_growth_tools', 'dashboard_widget_getting_started',
        ) ) );
    }

    /**
     * Remove entry-automation education and the WPCode promotion from native tools.
     * @param array $views Registered tools view objects keyed by slug.
     * @return array Native usable tools with the named Lite promotion views omitted.
     */
    public function tool_views( $views ) {
        if ( ! self::supported() || ! is_array( $views ) ) { return $views; }
        $rules = array( 'entry-automation' => 'WPForms\Admin\Tools\Views\EntryAutomation', 'wpcode' => 'WPForms\Admin\Tools\Views\CodeSnippets', 'ai-mcp' => 'WPForms\Admin\Tools\Views\AiMcp' );
        foreach ( $rules as $key => $class ) {
            if ( isset( $views[ $key ] ) && is_object( $views[ $key ] ) && $class === get_class( $views[ $key ] ) ) { unset( $views[ $key ] ); }
        }
        return $views;
    }

    /**
     * Remove entry import/export upsell tabs, preserving form and template transfers.
     * @param array $views Registered import or export sub-view objects.
     * @return array Sub-views without the audited entry education classes.
     */
    public function entry_transfer_views( $views ) {
        if ( self::supported() && is_array( $views ) && isset( $views['entries'] ) && is_object( $views['entries'] )
            && in_array( get_class( $views['entries'] ), array( 'WPForms\Admin\Education\Admin\Tools\ImportEntries', 'WPForms\Admin\Education\Admin\Tools\ExportEntries' ), true ) ) {
            unset( $views['entries'] );
        }
        return $views;
    }

    /**
     * Remove the coupon upsell view while preserving real payment gateways and reports.
     * @param array $views Registered payment view objects keyed by slug.
     * @return array Views without the audited Lite coupon education class.
     */
    public function payment_views( $views ) {
        if ( self::supported() && is_array( $views ) && isset( $views['coupons'] ) && is_object( $views['coupons'] )
            && 'WPForms\Admin\Payments\Views\Coupons\Education' === get_class( $views['coupons'] ) ) {
            unset( $views['coupons'] );
        }
        return $views;
    }

    /**
     * Keep usable builder field buttons and omit marked premium placeholders.
     * @param array $groups Registered field groups containing field definitions.
     * @return array Field groups with only audited education placeholders removed.
     */
    public function field_buttons( $groups ) {
        if ( ! self::supported() || ! is_array( $groups ) ) { return $groups; }
        foreach ( $groups as $key => $group ) {
            if ( ! is_array( $group ) || ! isset( $group['fields'] ) || ! is_array( $group['fields'] ) ) { continue; }
            foreach ( $group['fields'] as $index => $field ) {
                if ( ! is_array( $field ) ) { continue; }
                $classes = $field['class'] ?? array();
                $classes = is_string( $classes ) ? preg_split( '/\s+/', $classes ) : (array) $classes;
                if ( in_array( 'education-modal', $classes, true ) ) { unset( $groups[ $key ]['fields'][ $index ] ); }
            }
            if ( empty( $groups[ $key ]['fields'] ) ) { unset( $groups[ $key ] ); }
        }
        return $groups;
    }

    /**
     * Suppress optional floating links and the redundant WordPress dashboard widget.
     * @param bool $show Whether upstream should render its optional admin panel.
     * @return bool False for audited Lite; unchanged for every other target.
     */
    public function quiet_flyout( $show ) {
        return self::supported() ? false : $show;
    }

    /**
     * Omit unavailable addon preview cards without affecting native form previews.
     * @param array $items Upstream preview-dropdown education items.
     * @return array No premium education cards for audited Lite, otherwise unchanged.
     */
    public function preview_promotions( $items ) {
        return self::supported() ? array() : $items;
    }

    /**
     * Unhook named marketing renderers before output while retaining operational callbacks.
     * @return void Removes only audited class/method pairs; never changes persisted data.
     */
    public function remove_promotion_callbacks() {
        if ( ! self::supported() || ! is_admin() ) { return; }
        $rules = array(
            'WPForms_Review' => array( 'review_request', 'admin_footer', 'promote_wpforms' ),
            'WPForms_Lite' => array( 'settings_cta' ),
            'WPForms\Admin\FlyoutMenu' => array( 'output' ),
            'WPForms\Lite\Admin\Education\Admin\NoticeBar' => array( 'display' ),
            'WPForms\Lite\Admin\Education\Builder\DidYouKnow' => array( 'notifications', 'confirmations' ),
            'WPForms\Lite\Admin\Education\Builder\Fields' => array( 'add_fields', 'field_conditional_logic', 'form_preview_notice' ),
            'WPForms\Lite\Admin\Education\Builder\Notifications' => array( 'advanced_section' ),
            'WPForms\Lite\Admin\Education\Builder\Confirmations' => array( 'entry_preview_settings' ),
            'WPForms\Admin\Education\Builder\Providers' => array( 'display_addons' ),
            'WPForms\Admin\Education\Builder\Payments' => array( 'display_addons' ),
            'WPForms\Admin\Education\Builder\Settings' => array( 'display' ),
            'WPForms\Admin\Settings\Gdpr\WPConsentCallout' => array( 'add_setting', 'enqueue_assets' ),
            'WPForms\Admin\Education\Builder\Gdpr' => array( 'maybe_render_notice', 'enqueue_assets' ),
            'WPForms\SetupChecklist\Menu' => array( 'register', 'reorder', 'print_styles' ),
            'WPForms\Admin\Education\Admin\Settings\Integrations' => array( 'display_addons' ),
            'WPForms\Admin\Education\Builder\Calculations' => array( 'basic_options', 'advanced_options' ),
            'WPForms\Admin\Education\Builder\QrCode' => array( 'logo_settings' ),
            'WPForms\Admin\Education\Builder\AddonBanners' => array( 'maybe_render_banner' ),
            'WPForms\Admin\Education\Promo\SugarCalendar\FieldsPanel' => array( 'maybe_render_banner' ),
            'WPForms\Admin\Education\Promo\SugarCalendar\TemplatesGrid' => array( 'print_banner_template' ),
        );
        // Automatic onboarding is optional; explicit manual launches remain available.
        if ( empty( $_GET['wpforms_setup_wizard'] ) ) { // Read-only presentation context.
            $rules['WPForms\SetupWizard\SetupWizard'] = array( 'maybe_launch' );
            $rules['WPForms_Welcome'] = array( 'redirect' );
        }
        global $wp_filter;
        foreach ( $wp_filter as $tag => $hook ) {
            if ( ! $hook instanceof WP_Hook ) { continue; }
            foreach ( $hook->callbacks as $priority => $callbacks ) {
                foreach ( $callbacks as $callback ) {
                    $function = $callback['function'];
                    if ( ! is_array( $function ) || ! is_object( $function[0] ) ) { continue; }
                    $class = get_class( $function[0] );
                    if ( isset( $rules[ $class ] ) && in_array( $function[1], $rules[ $class ], true ) ) {
                        remove_filter( $tag, $function, $priority );
                    }
                }
            }
        }
    }

    /**
     * Block the wizard's WPConsent bundle installation without altering existing plugins.
     * @param mixed $result Previously intercepted REST result, or null.
     * @param WP_REST_Server $server Current REST server, unused.
     * @param WP_REST_Request $request Current request with the wizard's selected plugin files.
     * @return mixed Existing result, or a localized rejection of WPConsent installation.
     */
    public function block_consent_wizard_install( $result, $server, $request ) {
        if ( null !== $result || ! self::supported() || '/wpforms/v1/setup-wizard/install-plugins' !== $request->get_route() || 'POST' !== $request->get_method() ) { return $result; }
        if ( in_array( 'wpconsent-cookies-banner-privacy-suite/wpconsent.php', (array) $request->get_param( 'plugins' ), true ) ) {
            return new WP_Error( 'pwfl_consent_install_blocked', __( 'WPConsent installation through WPForms is blocked by Purify. Manual installation through WordPress remains available.', 'purify-wpforms-lite' ), array( 'status' => 403 ) );
        }
        return $result;
    }

    /**
     * Stop WPForms-initiated WPConsent downloads, preserving native manual installations.
     * @param mixed $reply Prior download override, or false.
     * @param string $package Package URL requested by the native upgrader.
     * @param WP_Upgrader $upgrader Native upgrader instance, unused.
     * @param array $context Native operation context, unused.
     * @return mixed Prior result, or a localized error for the named cross-promotion package.
     */
    public function block_consent_download( $reply, $package, $upgrader, $context ) {
        if ( false !== $reply || ! self::supported() || ! is_string( $package ) ) { return $reply; }
        $page = isset( $_GET['page'] ) && is_string( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
        $action = isset( $_REQUEST['action'] ) && is_string( $_REQUEST['action'] ) ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : '';
        if ( 0 !== strpos( $page, 'wpforms-' ) && 0 !== strpos( $action, 'wpforms_' ) ) { return $reply; }
        $url = wp_parse_url( $package );
        if ( is_array( $url ) && 'downloads.wordpress.org' === ( $url['host'] ?? '' )
            && preg_match( '~^/plugin/wpconsent-cookies-banner-privacy-suite(?:\.(?:[0-9.]+|latest-stable))?\.zip$~D', $url['path'] ?? '' ) ) {
            return new WP_Error( 'pwfl_consent_install_blocked', __( 'WPConsent installation through WPForms is blocked by Purify. Manual installation through WordPress remains available.', 'purify-wpforms-lite' ) );
        }
        return $reply;
    }

    /**
     * Limit structural cleanup to the source-audited free release.
     * @return bool Whether the active target is supported Lite rather than Pro.
     */
    public static function supported() {
        return class_exists( 'WPForms_Lite' ) && ! class_exists( 'WPForms_Pro' )
            && defined( 'WPFORMS_VERSION' ) && in_array( WPFORMS_VERSION, array( '1.9.4.2', '2.0.2.2' ), true );
    }

    /**
     * Remove only audited promotional paragraphs, retaining license activation controls.
     * @param string $output Upstream license field HTML.
     * @return string Original HTML with named promotional paragraphs removed.
     */
    public function license_output( $output ) {
        if ( ! self::supported() || ! is_string( $output ) ) { return $output; }
        $clean = preg_replace( '~<p\b[^>]*>(?:(?!</p>).)*class="wpforms-upgrade-modal"(?:(?!</p>).)*</p>~s', '', $output );
        return preg_replace( '~<p class="discount-note">(?:(?!</p>).)*</p>~s', '', $clean ?? $output ) ?? $output;
    }

    /**
     * Omit audited premium-only education tabs without changing saving logic.
     * @param array $tabs Upstream settings tab definitions.
     * @return array Usable settings tabs, with unknown definitions preserved.
     */
    public function settings_tabs( $tabs ) {
        if ( self::supported() && is_array( $tabs ) ) {
            foreach ( array( 'geolocation', 'access', 'wpcode' ) as $key ) {
                if ( isset( $tabs[ $key ] ) && false === ( $tabs[ $key ]['form'] ?? null ) ) { unset( $tabs[ $key ] ); }
            }
        }
        return $tabs;
    }

    /**
     * Load custom, update-safe translations before the bundled catalog.
     * @return void Registers this host's locale resources on init.
     */
    public function load_translations() {
        $domain = 'purify-wpforms-lite';
        $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
        $custom = trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo';
        $bundled = __DIR__ . '/languages/' . $domain . '-' . $locale . '.mo';
        if ( is_readable( $custom ) ) {
            load_textdomain( $domain, $custom, $locale );
        } elseif ( is_readable( $bundled ) ) {
            load_textdomain( $domain, $bundled, $locale );
        }
        load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Remove promotional destinations, preserving forms, payments and backup access.
     * @return void Removes only named promotion menus and the exact upgrade origin.
     */
    public function remove_submenus() {
        if ( ! self::supported() ) { return; }
        foreach ( array( 'wpforms-entries', 'wpforms-setup-checklist', 'wpforms-wpconsent', 'wpforms-sugar-calendar', 'wpforms-duplicator', 'wpforms-uncanny-automator', 'wpforms-community', 'wpforms-addons', 'wpforms-analytics', 'wpforms-smtp', 'wpforms-about' ) as $slug ) {
            remove_submenu_page( 'wpforms-overview', $slug );
        }
        global $submenu;
        foreach ( $submenu['wpforms-overview'] ?? array() as $item ) {
            $url = isset( $item[2] ) ? wp_parse_url( html_entity_decode( $item[2] ) ) : false;
            if ( is_array( $url ) && isset( $url['host'], $url['path'] ) && 'wpforms.com' === strtolower( $url['host'] ) && '/lite-upgrade/' === trailingslashit( $url['path'] ) ) {
                remove_submenu_page( 'wpforms-overview', $item[2] );
            }
        }
    }

    /**
     * Remove the upgrade toolbar entry after WPForms registers its nodes.
     * @param WP_Admin_Bar $bar Current toolbar instance.
     * @return void Preserves operational toolbar links and notifications.
     */
    public function remove_admin_bar_nodes( $bar ) {
        if ( ! self::supported() ) { return; }
        foreach ( array( 'wpforms-upgrade', 'wpforms-tools-wpcode', 'wpforms-geolocation-settings', 'wpforms-access-settings' ) as $id ) { $bar->remove_node( $id ); }
    }

    /**
     * Load local cleanup CSS only on audited WPForms screens or its plugin row.
     * @return void Enqueues scoped CSS without direct style output.
     */
    public function enqueue_admin_styles() {
        if ( ! self::supported() ) { return; }
        $screen = get_current_screen();
        if ( ! $screen || ( false === strpos( $screen->id, 'wpforms' ) && 'plugins' !== $screen->base ) ) { return; }
        wp_enqueue_style( 'pwfl-cleanup', plugins_url( 'assets/css/cleanup.css', __FILE__ ), array(), self::VERSION );
        if ( false !== strpos( $screen->id, 'wpforms' ) ) {
            wp_enqueue_script( 'pwfl-cleanup', plugins_url( 'assets/js/cleanup.js', __FILE__ ), array(), self::VERSION, true );
            wp_localize_script( 'pwfl-cleanup', 'pwflCleanup', array( 'fees' => __( '%s platform fees apply in addition to payment provider fees.', 'purify-wpforms-lite' ) ) );
        }
    }
}
new DDW_Purify_WPForms_Lite();
}
require_once __DIR__ . '/includes/integration.php';
DDW_PWFL_Integration::register( __FILE__ );
add_filter( 'plugin_row_meta', 'ddw_pwfl_pluginrow_meta', 10, 2 );

/**
 * Add local history, status and optional project links on this plugin's row.
 * @param array $meta Existing escaped plugin links.
 * @param string $file Plugin basename being rendered.
 * @return array Escaped metadata links; other plugin rows remain unchanged.
 */
function ddw_pwfl_pluginrow_meta( $meta, $file ) {
    if ( $file !== plugin_basename( __FILE__ ) || ! current_user_can( 'activate_plugins' ) ) { return $meta; }
    $meta[] = '<button type="button" class="button-link" data-pwfl-history>' . esc_html__( 'Changelog', 'purify-wpforms-lite' ) . '</button>';
    $status = DDW_PWFL_Integration::status_label();
    if ( $status ) { $meta[] = $status; }
    $meta[] = '<a href="https://ko-fi.com/deckerweb" target="_blank" rel="nofollow noopener noreferrer">' . esc_html__( 'Donate', 'purify-wpforms-lite' ) . '</a>';
    $meta[] = '<a href="https://eepurl.com/gbAUUn" target="_blank" rel="nofollow noopener noreferrer">' . esc_html__( 'Join the newsletter', 'purify-wpforms-lite' ) . '</a>';
    /**
     * Filter escaped metadata links for deckerweb admin integrations.
     * @since 1.0.0
     * @param array $meta Escaped metadata links to modify and return.
     */
    return apply_filters( 'ddw/admin_extras/pluginrow_meta', $meta );
}
