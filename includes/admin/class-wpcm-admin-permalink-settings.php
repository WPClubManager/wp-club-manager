<?php
/**
 * Adds settings to the permalinks admin settings page.
 *
 * @author      WPClubManager
 * @category    Admin
 * @package     WPClubManager/Admin
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Permalink_Settings' ) ) :

/**
 * WPCM_Admin_Permalink_Settings Class
 */
class WPCM_Admin_Permalink_Settings {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		$this->slugs = apply_filters( 'wpclubmanager_permalink_slugs', array(
			array( 'club', __( 'Clubs', 'wp-club-manager' ) ),
			array( 'player', __( 'Players', 'wp-club-manager' ) ),
			array( 'staff', __( 'Staff', 'wp-club-manager' ) ),
			array( 'match', __( 'Matches', 'wp-club-manager' ) ),
		) );

		add_action( 'admin_init', array( $this, 'settings_init' ) );
		add_action( 'admin_init', array( $this, 'settings_save' ) );
	}

	/**
	 * Init our settings.
	 */
	public function settings_init() {
		// Add a section to the permalinks page
		add_settings_section( 'wpclubmanager-permalink', __( 'WP Club Manager Permalinks', 'wp-club-manager' ), array( $this, 'settings' ), 'permalink' );

		// Add our settings
		foreach ( $this->slugs as $slug ):
			add_settings_field(	
				$slug[0],								// id
				$slug[1],								// setting title
				array( $this, 'slug_input' ),			// display callback
				'permalink',							// settings page
				'wpclubmanager-permalink'				// settings section
			);
		endforeach;
	}

	/**
	 * Show a slug input box.
	 */
	public function slug_input() {
		$slug = array_shift( $this->slugs );
		$key = $slug[0];
		$text = get_option( 'wpclubmanager_' . $key . '_slug', null );
		?><fieldset><input id="wpclubmanager_<?php echo $key; ?>_slug" name="wpclubmanager_<?php echo $key; ?>_slug" type="text" class="regular-text code" value="<?php echo $text; ?>" placeholder="<?php echo $key; ?>"></fieldset><?php
	}

	/**
	 * Show the settings
	 */
	public function settings() {
		echo wpautop( __( 'These settings control the permalinks used for WP Club Manager. These settings only apply when <strong>not using "Plain" permalinks above</strong>.', 'wp-club-manager' ) );
	}

	/**
	 * Save the settings
	 */
	public function settings_save() {
		if ( ! is_admin() )
			return;

		if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['wpclubmanager_club_slug'] ) ):
			foreach ( $this->slugs as $slug ):
				$key = 'wpclubmanager_' . $slug[0] . '_slug';
				$value = null;
				if ( isset( $_POST[ $key ] ) )
					$value = sanitize_text_field( $_POST[ $key ] );
				if ( empty( $value ) )
					delete_option( $key );
				else
					update_option( $key, $value );
			endforeach;
			wpcm_flush_rewrite_rules();
		endif;
	}

}

endif;

return new WPCM_Admin_Permalink_Settings();
