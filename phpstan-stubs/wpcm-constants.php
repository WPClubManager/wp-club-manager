<?php
/**
 * Stubs for WPCM constants defined at runtime in wpclubmanager.php.
 * PHPStan can't see these because they're set in the plugin constructor.
 *
 * @phpstan-type WPCMConstants array{
 *   WPCM_PLUGIN_FILE: string,
 *   WPCM_VERSION: string,
 *   WPCM_URL: string,
 *   WPCM_PATH: string,
 *   WPCM_TEMPLATE_PATH: string,
 *   WPCM_PLUGIN_BASENAME: string,
 *   WPCM_TEMPLATE_DEBUG_MODE: bool
 * }
 */

// phpcs:disable
define( 'WPCM_PLUGIN_FILE', '' );
define( 'WPCM_VERSION', '' );
define( 'WPCM_URL', '' );
define( 'WPCM_PATH', '' );
define( 'WPCM_TEMPLATE_PATH', 'wp-club-manager/' );
define( 'WPCM_PLUGIN_BASENAME', '' );
define( 'WPCM_TEMPLATE_DEBUG_MODE', false );
