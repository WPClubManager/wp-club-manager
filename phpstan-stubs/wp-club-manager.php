<?php
/**
 * Stub for WP Club Manager constants to satisfy PHPStan analysis.
 * These constants are defined at runtime in class-wp-club-manager.php
 * but PHPStan cannot see them during static analysis.
 */

// phpcs:disable

define( 'WPCM_PLUGIN_FILE', '' );
define( 'WPCM_VERSION', '' );
define( 'WPCM_TEMPLATE_PATH', '' );
define( 'WPCM_URL', '' );
define( 'WPCM_PATH', '' );
define( 'WPCM_PLUGIN_BASENAME', '' );
define( 'WPCM_BASENAME', '' );
define( 'WPCM_TEMPLATE_DEBUG_MODE', false );
define( 'WPCM_INSTALLING', false );

// WordPress constants that may not be available during static analysis.
if ( ! defined( 'WP_MEMORY_LIMIT' ) ) {
	define( 'WP_MEMORY_LIMIT', '256M' );
}

// Third-party constants referenced in the codebase.
if ( ! defined( 'W3TC_FILE' ) ) {
	define( 'W3TC_FILE', '' );
}

if ( ! defined( 'INPUT_REQUEST' ) ) {
	define( 'INPUT_REQUEST', 99 );
}
