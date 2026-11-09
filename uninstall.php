<?php
/** Remove this host's update cache and, for the final Library host, shared temporary data. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }
// Core retains the first plugin basename in a batch; this file owns only this host cleanup.
/**
 * Clear this host's repository cache and release shared resources only when unowned.
 * @return void Preserves WPForms data and other installed Library hosts.
 */
(static function (): void {
$purify_wpforms_lite_cache = 'ddw_ghru_' . substr( md5( 'https://github.com/deckerweb/purify-wpforms-lite' ), 0, 24 );
delete_site_transient( $purify_wpforms_lite_cache );
if ( is_multisite() ) {
    foreach ( get_networks( array( 'fields' => 'ids', 'number' => 0 ) ) as $purify_wpforms_lite_network ) {
        delete_network_option( $purify_wpforms_lite_network, '_site_transient_' . $purify_wpforms_lite_cache );
        delete_network_option( $purify_wpforms_lite_network, '_site_transient_timeout_' . $purify_wpforms_lite_cache );
    }
}
require_once __DIR__ . '/includes/deckerweb-plugin-library/lifecycle.php';
deckerweb_library_uninstall_v4( __DIR__ . '/purify-wpforms-lite.php' );

$purify_wpforms_lite_updater_class = require __DIR__ . '/includes/deckerweb-github-release-updater-v2.php';
if ( is_string( $purify_wpforms_lite_updater_class ) && is_callable( array( $purify_wpforms_lite_updater_class, 'clear_repository_cache' ) ) ) {
    $purify_wpforms_lite_updater_class::clear_repository_cache( 'https://github.com/deckerweb/purify-wpforms-lite' );
}

})();
