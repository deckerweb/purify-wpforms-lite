=== Purify WPForms Lite ===
Contributors: deckerweb
Tags: forms, wpforms, cleanup, purify
Requires at least: 6.7
Tested up to: 7.1.3
Requires PHP: 8.0
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Remove known promotional elements from WPForms Lite while keeping useful free features accessible.

== Description ==

* Audited cleanup for WPForms Lite 1.9.4.2 and 2.0.2.2.
* Keeps free fields, templates, form import/export and available payment controls; Lite 2.0.2.2 includes Stripe, PayPal Commerce and Square.
* Removes entries, setup checklist, coupon upsells and cross-product advertising from navigation.
* Plugin edition includes the deckerweb catalog, GitHub updater and local changelog.
* Separate snippet imports for Code Snippets/Advanced Scripts and WPCode Lite/Pro.

== Installation ==

Upload the plugin ZIP, activate WPForms Lite and Purify, and check the Plugins-page status.

== Frequently Asked Questions ==

= Does it unlock Pro features? =

No. Premium features remain unavailable. Purify only removes known promotional elements.

= Which versions are supported? =

Cleanup runs with WPForms Lite 1.9.4.2 and 2.0.2.2. Unknown versions, missing WPForms and WPForms Pro pause cleanup. The plugin list explains the status.

= Where are the settings? =

Cleanup is automatic. Purify adds no separate settings page. WPForms settings and Lite Connect backup controls remain accessible. Checklist progress is preserved; its menu entry and automatic onboarding are hidden. Entry transfer/automation and AI/MCP promotional views are omitted; existing settings are unchanged.

= Does it change forms or notices? =

No form data or WPForms settings are changed. Notifications, errors, security notices and real payment-fee disclosures remain. WPConsent installation through WPForms is blocked; native manual installation remains available. Purify does not remove installed plugins or enable/disable Lite Connect or existing AI access settings.

= Can I use a snippet? =

Yes. Import the Code Snippets JSON in Code Snippets or Advanced Scripts, or the separate WPCode JSON in WPCode Lite/Pro. Run one edition only. Snippets contain cleanup without the shared catalog, updater or plugin-history dialog.

= Does it support Multisite and what happens on uninstall? =

Cleanup follows the current website. Network activation is supported. Purify stores no form content or cleanup settings. Deactivation preserves data; uninstall cleans shared temporary Library data only after its final host is removed. Shared settings remain unless the separate Library deletion option was enabled. WPForms data is never deleted.

= What external connections are used? =

The plugin updater checks GitHub through WordPress update checks. Optional Library online refresh is initially off and contacts the approved GitHub catalog only when enabled. User-triggered catalog installation downloads approved packages. Purify adds no telemetry. Snippets add no external requests.

Full FAQ: https://github.com/deckerweb/purify-wpforms-lite/wiki/FAQ-by-topic

== Changelog ==

= 1.1.0 · 2026-10-10 =

* New: Shared catalog, GitHub release updater and accessible local changelog.
* Improved: More thorough cleanup removes premium entry tools, coupon upsells, setup checklist links, WPVibe/WPCode promotion and advertising widgets while preserving usable Lite controls.
* Improved: Original localized banners and icons use a clear form illustration.
* Fixed: Unknown target versions pause cleanup safely and bundled German translations load.
* Misc: PHP 8.0 is now required. Newsletter links contain no personal account data. Snippet imports remain available.

= 1.0.0 · 2025-04-05 =

* New: Initial public release.
