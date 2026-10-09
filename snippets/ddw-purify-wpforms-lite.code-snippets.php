<?php
/** Purify WPForms Lite 1.1.0 — David Decker – DECKERWEB. GPL-2.0-or-later. */
if ( ! defined( 'ABSPATH' )  ) { return; }
if ( ! class_exists( 'DDW_Purify_WPForms_Lite', false ) ) {
/** Clean source-audited Lite promotions without changing form data or settings. */
final class DDW_Purify_WPForms_Lite {
    /**
     * Translate this standalone snippet's two messages without a plugin directory.
     * @param string $message English message being displayed.
     * @return string Bundled neutral German text or the original English message.
     */
    private static function message( $message ) {
        $messages = array(
            'WPConsent installation through WPForms is blocked by Purify. Manual installation through WordPress remains available.' => 'Purify blockiert die WPConsent-Installation über WPForms. Die manuelle Installation über WordPress bleibt möglich.',
            '%s platform fees apply in addition to payment provider fees.' => '%s Plattformgebühren fallen zusätzlich zu den Gebühren des Zahlungsanbieters an.',
        );
        return 0 === strpos( determine_locale(), 'de' ) && isset( $messages[ $message ] ) ? $messages[ $message ] : $message;
    }

    private const VERSION = '1.1.0';

    /**
     * Register narrowly scoped admin cleanup for the snippet edition.
     * @return void Registers hooks without changing persisted data.
     */
    public function __construct() {
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
            return new WP_Error( 'pwfl_consent_install_blocked', self::message( 'WPConsent installation through WPForms is blocked by Purify. Manual installation through WordPress remains available.' ), array( 'status' => 403 ) );
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
            return new WP_Error( 'pwfl_consent_install_blocked', self::message( 'WPConsent installation through WPForms is blocked by Purify. Manual installation through WordPress remains available.' ) );
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
        wp_register_style( 'pwfl-cleanup', false, array(), self::VERSION );
        wp_enqueue_style( 'pwfl-cleanup' );
        wp_add_inline_style( 'pwfl-cleanup', '/* Only source-audited promotion markers; never blanket-hide notices or settings. */
.wpforms-admin-page #wpforms-notice-bar,
.wpforms-admin-page .settings-lite-cta,
.wpforms-admin-page #wpforms-setup-templates-list .wpforms-template.pro,
.wpforms-admin-page .wpforms-context-menu-list-item[data-action="upgrade"],
.wpforms-admin-page .wpforms-add-fields-group .wpforms-not-available,
.wpforms-admin-page .wpforms-field-option-group-conditionals:has(> a.education-modal),
.wpforms-admin-page .wpforms-panel-sidebar-section.education-modal,
.wpforms-admin-page .wpforms-panel-content-also-available-item:has(a.wpforms-panel-content-also-available-item-upgrade-to-pro),
.wpforms-admin-page .wpforms-field-option-row.education-modal,
.wpforms-admin-page .wpforms-alert-ai.wpforms-educational-alert,
.plugins-php tr[data-slug="wpforms-lite"] .wpforms-pro {
    display: none !important;
}
@media screen and (min-width: 1024px) {
    .wpforms-admin-page #wpforms-notice-bar + #wpforms-header-temp { top: inherit; }
}
/* Known promotion fallbacks, including dynamically inserted builder content. */
.wpforms-admin-page .wpforms-template.pro,
.wpforms-admin-page .wpforms-settings-provider.education-modal,
.wpforms-admin-page .wpforms-settings-field-radio-wrapper.education-modal,
.wpforms-admin-page .wpforms-footer-promotion,
.wpforms-admin-page #wpforms-flyout,
.wpforms-admin-page #wpforms-builder-lite-connect-top-bar,
.wpforms-admin-page #wpforms-panel-field-confirmations-1-message_entry_preview-wrap,
.wpforms-admin-page .wpforms-dyk:not(.wpforms-education-lite-connect-wrapper),
.wpforms-admin-page .wpforms-add-fields-group:has(> .wpforms-add-fields-buttons:empty) {
    display: none !important;
}
.wpforms-admin-page .wpforms-empty-payments-gateway:has(a.wpforms-upgrade-modal),
.wpforms-admin-page .wpforms-template-upgrade-button,
.wpforms-admin-page .wpforms-ai-mcp-card:has(.wpforms-ai-mcp-pro-badge.education-modal),
.wpforms-admin-page .wpforms-builder-payment-settings-fee-notice {
    display: none !important;
}

.wpforms-admin-page .wpforms-ai-mcp-page-builder { display: none !important; }

.wpforms-admin-page .wpforms-dashboard-widget-entries-promo,
.wpforms-admin-page .wpforms-education-lite-connect-enabled-info a.wpforms-upgrade-modal { display: none !important; }
.wpforms-admin-page .wpforms-dashboard-widget-entries-col-interactions,
.wpforms-admin-page .wpforms-dashboard-widget-entries-col-conversion,
.wpforms-admin-page .wpforms-dashboard-spam-security-row:has(a.education-modal[data-type="plugin"]),
.wpforms-admin-page .wpforms-dashboard-spam-security-row-or {
    display: none !important;
}
' );
        if ( false !== strpos( $screen->id, 'wpforms' ) ) {
            wp_register_script( 'pwfl-cleanup', false, array(), self::VERSION, true );
            wp_enqueue_script( 'pwfl-cleanup' );
            wp_add_inline_script( 'pwfl-cleanup', '/* Remove named upgrade CTAs while retaining upstream operational fee disclosures. */
(function () {
    \'use strict\';
    /**
     * Identify the audited upgrade destination.
     * @param {HTMLAnchorElement} link Candidate anchor.
     * @returns {boolean} Whether the link targets the exact WPForms upgrade page.
     */
    function upgradeLink(link) {
        try { var url = new URL(link.href, window.location.href); return url.hostname === \'wpforms.com\' && url.pathname === \'/lite-upgrade/\'; }
        catch (error) { return false; }
    }
    /**
     * Remove named marketing clauses without removing fee information or unrelated notices.
     * @param {Element} root Audited WPForms page root.
     * @returns {void} Changes only the recognized fee notice markup.
     */
    function clean(root) {
        root.querySelectorAll(\'.wpforms-stripe-notice-info p, p.wpforms-stripe-notice-info, .wpforms-square-notice-info p, p.wpforms-square-notice-info, .wpforms-paypal-commerce-notice-info p, p.wpforms-paypal-commerce-notice-info\').forEach(function (notice) {
            var link = Array.from(notice.querySelectorAll(\'a[href]\')).find(upgradeLink);
            if (!link || link.parentElement !== notice) return;
            var range = document.createRange(); range.setStartBefore(link); range.setEnd(notice, notice.childNodes.length); range.deleteContents();
        });
        root.querySelectorAll(\'.wpforms-empty-payments-fees\').forEach(function (notice) {
            var link = Array.from(notice.querySelectorAll(\'a[href]\')).find(upgradeLink);
            var amount = notice.textContent.match(/\\d+(?:[.,]\\d+)?\\s*%/);
            if (link && amount && window.pwflCleanup) notice.textContent = pwflCleanup.fees.replace(\'%s\', amount[0]);
        });
    }
    var roots = document.querySelectorAll(\'.wpforms-admin-page\');
    roots.forEach(function (root) {
        clean(root);
        var scheduled = false;
        new MutationObserver(function () {
            if (scheduled) return; scheduled = true;
            window.requestAnimationFrame(function () { scheduled = false; clean(root); });
        }).observe(root, { childList: true, subtree: true });
    });
}());
', 'after' );
            wp_localize_script( 'pwfl-cleanup', 'pwflCleanup', array( 'fees' => self::message( '%s platform fees apply in addition to payment provider fees.' ) ) );
        }
    }
}
new DDW_Purify_WPForms_Lite();
}
