<?php
/**
 * Admin Editor
 *
 * Methods which tweak the WP Editor.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Admin_Editor {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'add_shortcode_button' ) );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ) );
	}

	/**
	* Add buttons for shortcodes to the WP editor.
	*/
	public function add_shortcode_button() {

		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) return;

		if ( get_user_option('rich_editing') == 'true' ) :
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ) );
			add_filter( 'mce_buttons_3', array( $this, 'register_shortcode_button' ) );
		endif;
	}

	/**
	 * Register the shortcode buttons.
	 *
	 * @param array $buttons
	 * @return array
	 */
	public function register_shortcode_button($buttons) {

		array_push( $buttons, "wpcm_matches_button", "wpcm_standings_button", "|", "wpcm_players_button", "wpcm_staff_button", "|", "wpcm_map_button" );

		return $buttons;
	}

	/**
	 * Add the shortcode buttons to TinyMCE.
	 *
	 * @param array $plugin_array
	 * @return array
	 */
	public function add_shortcode_tinymce_plugin($plugin_array) {

		$plugin_array['matches']   = WPCM()->plugin_url() . '/assets/js/admin/editor-matches.js';
		$plugin_array['standings'] = WPCM()->plugin_url() . '/assets/js/admin/editor-standings.js';
		$plugin_array['players']   = WPCM()->plugin_url() . '/assets/js/admin/editor-players.js';
		$plugin_array['staff']     = WPCM()->plugin_url() . '/assets/js/admin/editor-staff.js';
		$plugin_array['map']       = WPCM()->plugin_url() . '/assets/js/admin/editor-map.js';

		return $plugin_array;
	}

	/**
	 * Force TinyMCE to refresh.
	 *
	 * @param int $ver
	 * @return int
	 */
	public function refresh_mce( $ver ) {
		
		$ver += 3;
		return $ver;
	}
}

new WPCM_Admin_Editor();