<?php
/**
 * WPCM_Cache_Helper class.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Classes
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Cache_Helper {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'create_plugin_transient_name', array( __CLASS__, 'create_plugin_transient_name' ) );
		add_action( 'update_plugin_transient_keys', array( __CLASS__, 'update_plugin_transient_keys' ) );
		add_action( 'delete_plugin_transients', array( __CLASS__, 'delete_plugin_transients' ) );
		add_action( 'wp_ajax_wpcm_clear_transients', array( __CLASS__, 'wpcm_clear_transients' ) );
	}

	public static function create_plugin_transient_name( $atts, $type = 'players' ) {

		$names = implode( '-', $atts);
		$transient_name = 'wpcm_' . $type . '_' . md5( $names );

		return $transient_name;
	}

	public static function update_plugin_transient_keys( $key ) {

		$transient_keys = get_option( 'wpcm_transient_keys' );
	  	$transient_keys[] = $key;
	  
	  	update_option( 'wpcm_transient_keys', $transient_keys );
	}

	public static function delete_plugin_transients() {

	  	$transient_keys = get_option( 'wpcm_transient_keys' );
	  	
	  	if( ! empty( $transient_keys ) ) {
		  	foreach( $transient_keys as $t ) {
		    	delete_transient( $t );
		  	}
		  	update_option( 'wpcm_transient_keys', array() );
		}
	}

	/**
	 * Clear transients ajax
	 */
	public function wpcm_clear_transients() {
		
		if( !isset( $_POST['wpcm_nonce'] ) || !wp_verify_nonce($_POST['wpcm_nonce'], 'wpcm-nonce') )
			die('Permissions check failed');	
		
		do_action( 'delete_plugin_transients' );
		
		exit();
	}
}

WPCM_Cache_Helper::init();