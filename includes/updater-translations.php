<?php
/** Copy into the host adapter and replace the purify-wpforms-lite textdomain with its literal domain. */
defined( 'ABSPATH' ) || exit;
/**
 * Resolve an updater source string in the host textdomain.
 * @param string $message English updater message.
 * @return string Translated host message, or original source text.
 */
return static function ( string $message ): string {
    // Literal calls let the host's normal translation extractor collect every source string.
    switch ( $message ) {
        case 'Private mode must be boolean.':
            return __( 'Private mode must be boolean.', 'purify-wpforms-lite' );
        case 'Invalid authentication provider.':
            return __( 'Invalid authentication provider.', 'purify-wpforms-lite' );
        case 'The plugin must be installed in a stable slug directory.':
            return __( 'The plugin must be installed in a stable slug directory.', 'purify-wpforms-lite' );
        case 'Invalid GitHub repository URL.':
            return __( 'Invalid GitHub repository URL.', 'purify-wpforms-lite' );
        case 'The private update could not be authorized. Check the repository credentials and refresh updates.':
            return __( 'The private update could not be authorized. Check the repository credentials and refresh updates.', 'purify-wpforms-lite' );
        case 'Could not create the update download file.':
            return __( 'Could not create the update download file.', 'purify-wpforms-lite' );
        case 'The private update download failed. Check credentials and try again.':
            return __( 'The private update download failed. Check credentials and try again.', 'purify-wpforms-lite' );
        case 'Could not access the update filesystem.':
            return __( 'Could not access the update filesystem.', 'purify-wpforms-lite' );
        case 'GitHub release does not contain the plugin main file.':
            return __( 'GitHub release does not contain the plugin main file.', 'purify-wpforms-lite' );
        case 'Could not prepare the GitHub release package.':
            return __( 'Could not prepare the GitHub release package.', 'purify-wpforms-lite' );
        case 'See the release on GitHub.':
            return __( 'See the release on GitHub.', 'purify-wpforms-lite' );
        case 'Git Updater is active. If a deckerweb installation or update fails, temporarily deactivate Git Updater and retry. Reactivate it afterwards if other plugins or themes need it. Only leave it disabled when those extensions have another reliable update provider.':
            return __( 'Git Updater is active. If a deckerweb installation or update fails, temporarily deactivate Git Updater and retry. Reactivate it afterwards if other plugins or themes need it. Only leave it disabled when those extensions have another reliable update provider.', 'purify-wpforms-lite' );
        case 'Manage installed plugins':
            return __( 'Manage installed plugins', 'purify-wpforms-lite' );
        case 'Dismiss this notice':
            return __( 'Dismiss this notice', 'purify-wpforms-lite' );
        case 'Permission denied.':
            return __( 'Permission denied.', 'purify-wpforms-lite' );
        case 'Public catalog callbacks require both providers and cannot be used in private mode.':
            return __( 'Public catalog callbacks require both providers and cannot be used in private mode.', 'purify-wpforms-lite' );
        case 'The approved release could not be verified.':
            return __( 'The approved release could not be verified.', 'purify-wpforms-lite' );
        case 'The approved release changed. Refresh updates first.':
            return __( 'The approved release changed. Refresh updates first.', 'purify-wpforms-lite' );
        case 'Catalog diagnostics require a public catalog provider.':
            return __( 'Catalog diagnostics require a public catalog provider.', 'purify-wpforms-lite' );
        case 'Catalog approval':
            return __( 'Catalog approval', 'purify-wpforms-lite' );
        case 'Package download':
            return __( 'Package download', 'purify-wpforms-lite' );
        case 'Checksum verification':
            return __( 'Checksum verification', 'purify-wpforms-lite' );
        case 'Archive verification':
            return __( 'Archive verification', 'purify-wpforms-lite' );
        case 'The update server could not be reached. Try again later.':
            return __( 'The update server could not be reached. Try again later.', 'purify-wpforms-lite' );
        case 'The update request timed out. Try again later.':
            return __( 'The update request timed out. Try again later.', 'purify-wpforms-lite' );
        case 'The update server address could not be resolved.':
            return __( 'The update server address could not be resolved.', 'purify-wpforms-lite' );
        case 'The secure connection to the update server failed.':
            return __( 'The secure connection to the update server failed.', 'purify-wpforms-lite' );
        case 'The update server returned an unsuccessful HTTP response.':
            return __( 'The update server returned an unsuccessful HTTP response.', 'purify-wpforms-lite' );
        case 'The update provider did not return valid update data.':
            return __( 'The update provider did not return valid update data.', 'purify-wpforms-lite' );
        case 'The package does not match the approved checksum. Installation was stopped.':
            return __( 'The package does not match the approved checksum. Installation was stopped.', 'purify-wpforms-lite' );
        case 'The archive or plugin identity could not be verified. Installation was stopped.':
            return __( 'The archive or plugin identity could not be verified. Installation was stopped.', 'purify-wpforms-lite' );
        case 'Update stopped at %1$s: %2$s':
            /* translators: 1: update stage, 2: fixed explanation. */
            return __( 'Update stopped at %1$s: %2$s', 'purify-wpforms-lite' );
        case 'HTTP status: %d.':
            /* translators: %d: HTTP error status. */
            return __( 'HTTP status: %d.', 'purify-wpforms-lite' );
        case 'Invalid private download limits.':
            return __( 'Invalid private download limits.', 'purify-wpforms-lite' );
        case 'Invalid package cleanup provider.':
            return __( 'Invalid package cleanup provider.', 'purify-wpforms-lite' );
        case 'Safe package cleanup is unavailable for this update provider.':
            return __( 'Safe package cleanup is unavailable for this update provider.', 'purify-wpforms-lite' );
        case 'The update package identity, version or requirements could not be verified.':
            return __( 'The update package identity, version or requirements could not be verified.', 'purify-wpforms-lite' );
        default:
            return $message;
    }
};
