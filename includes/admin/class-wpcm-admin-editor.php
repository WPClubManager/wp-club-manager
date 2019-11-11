<?php
/**
 * Admin Editor
 *
 * Methods which tweak the WP Editor.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     2.0.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Admin_Editor {

	/**
	 * Constructor
	 */
	public function __construct() {

		add_action( 'admin_init', array( $this, 'add_shortcode_button' ) );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_mce' ) );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_lang' ), 10, 1 );
	}

	public function add_shortcode_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ) );
			add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ) );
		}
	}

	public function add_tinymce_lang( $arr ) {
	    $arr['wpcm_shortcodes_button'] = WPCM()->plugin_path() . '/assets/js/admin/editor-lang.php';
	    return $arr;
	}

	/**
	 * Register the shortcode buttons.
	 *
	 * @param array $buttons
	 * @return array
	 */
	
	public function register_shortcode_button( $buttons ) {
		array_push( $buttons, 'wpcm_shortcodes_button' );
		return $buttons;
	}

	/**
	 * Add the shortcode buttons to TinyMCE.
	 *
	 * @param array $plugin_array
	 * @return array
	 */
	// 
	public function add_shortcode_tinymce_plugin( $plugin_array ) {
		$plugin_array['wpcm_shortcodes_button'] = WPCM()->plugin_url() . '/assets/js/admin/editor.js';
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