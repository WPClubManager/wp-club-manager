<?php
/**
 * WPClubManager Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.5.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * is_wpclubmanager - Returns true if on a page which uses WPClubManager templates (cart and checkout are standard pages with shortcodes and thus are not included)
 *
 * @access public
 * @return bool
 */
function is_wpclubmanager() {
	return apply_filters( 'is_wpclubmanager', ( is_match() || is_club() || is_player() || is_staff() ||is_sponsor() ) ? true : false );
}

if ( ! function_exists( 'is_club' ) ) {

	/**
	 * is_club - Returns true when viewing a single club.
	 *
	 * @access public
	 * @return bool
	 */
	function is_club() {
		return is_singular( array( 'wpcm_club' ) );
	}
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

if ( ! function_exists( 'is_staff' ) ) {

	/**
	 * is_staff - Returns true when viewing a single staff.
	 *
	 * @access public
	 * @return bool
	 */
	function is_staff() {
		return is_singular( array( 'wpcm_staff' ) );
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

if ( ! function_exists( 'is_plugins_page' ) ) {
	/**
	 * is_plugins_page - Returns true when the page is plugins.php
	 *
	 * @access public
	 * @return bool
	 */
	function is_plugins_page() {
	    global $pagenow;

	    return ( 'plugins.php' === $pagenow );
	}
}