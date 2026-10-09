<?php
/** Remove this host's update cache and, for the final Library host, shared temporary data. */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) { exit; }
if ( WP_UNINSTALL_PLUGIN !== 'purify-wpforms-lite/purify-wpforms-lite.php' ) { return; }
$pwfl_cache = 'ddw_ghru_' . substr( md5( 'https://github.com/deckerweb/purify-wpforms-lite' ), 0, 24 );
delete_site_transient( $pwfl_cache );
if ( is_multisite() ) {
    foreach ( get_networks( array( 'fields' => 'ids', 'number' => 0 ) ) as $pwfl_network ) {
        delete_network_option( $pwfl_network, '_site_transient_' . $pwfl_cache );
        delete_network_option( $pwfl_network, '_site_transient_timeout_' . $pwfl_cache );
    }
}
require_once __DIR__ . '/includes/deckerweb-plugin-library/lifecycle.php';
deckerweb_library_uninstall_v3( __DIR__ . '/purify-wpforms-lite.php' );
