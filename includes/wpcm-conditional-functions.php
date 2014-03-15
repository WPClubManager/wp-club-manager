<?php
/**
 * WPClubManager Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * is_wpclubmanager - Returns true if on a page which uses WPClubManager templates (cart and checkout are standard pages with shortcodes and thus are not included)
 *
 * @access public
 * @return bool
 */
function is_wpclubmanager() {
	return apply_filters( 'is_wpclubmanager', ( is_match() || is_player() || is_sponsors() ) ? true : false );
}

if ( ! function_exists( 'is_player' ) ) {

	/**
	 * is_player - Returns true when viewing a single player.
	 *
	 * @access public
	 * @return bool
	 */
	function is_player() {
		return is_singular( array( 'wpcm_player' ) );
	}
}

if ( ! function_exists( 'is_match' ) ) {

	/**
	 * is_match - Returns true when viewing a single match.
	 *
	 * @access public
	 * @return bool
	 */
	function is_match() {
		return is_singular( array( 'wpcm_match' ) );
	}
}

if ( ! function_exists( 'is_sponsors' ) ) {

	/**
	 * is_sponsors - Returns true when viewing the sponsor type archive.
	 *
	 * @access public
	 * @return bool
	 */
	function is_sponsors() {
		return ( is_post_type_archive( 'wpcm_sponsor' ) ) ? true : false;
	}
}

if ( ! function_exists( 'is_sponsor' ) ) {

	/**
	 * is_sponsor - Returns true when viewing a single sponsor.
	 *
	 * @access public
	 * @return bool
	 */
	function is_sponsor() {
		return is_singular( array( 'wpcm_sponsor' ) );
	}
}

if ( ! function_exists( 'is_ajax' ) ) {

	/**
	 * is_ajax - Returns true when the page is loaded via ajax.
	 *
	 * @access public
	 * @return bool
	 */
	function is_ajax() {
		if ( defined('DOING_AJAX') )
			return true;

		return ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) ? true : false;
	}
}