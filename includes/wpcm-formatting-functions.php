<?php
/**
 * WPClubManager Formatting
 *
 * Functions for formatting data.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Sanitize taxonomy names. Slug format (no spaces, lowercase).
 *
 * Doesn't use sanitize_title as this destroys utf chars.
 *
 * @access public
 * @param mixed $taxonomy
 * @return string
 */
function wpcm_sanitize_taxonomy_name( $taxonomy ) {

	$filtered = strtolower( remove_accents( stripslashes( strip_tags( $taxonomy ) ) ) );
	$filtered = preg_replace( '/&.+?;/', '', $filtered ); // Kill entities
	$filtered = str_replace( array( '.', '\'', '"' ), '', $filtered ); // Kill quotes and full stops.
	$filtered = str_replace( array( ' ', '_' ), '-', $filtered ); // Replace spaces and underscores.

	return apply_filters( 'sanitize_taxonomy_name', $filtered, $taxonomy );
}

/**
 * Clean variables
 *
 * @access public
 * @param string $var
 * @return string
 */
function wpcm_clean( $var ) {

	return sanitize_text_field( $var );
}

/**
 * Merge two arrays
 *
 * @access public
 * @param array $a1
 * @param array $a2
 * @return array
 */
function wpcm_array_overlay( $a1, $a2 ) {

    foreach( $a1 as $k => $v ) {
        if ( ! array_key_exists( $k, $a2 ) ) {
        	continue;
        }
        if ( is_array( $v ) && is_array( $a2[ $k ] ) ) {
            $a1[ $k ] = wpcm_array_overlay( $v, $a2[ $k ] );
        } else {
            $a1[ $k ] = $a2[ $k ];
        }
    }
    return $a1;
}

/**
 * WooCommerce Date Format - Allows to change date format for everything WooCommerce
 *
 * @access public
 * @return string
 */
function wpcm_date_format() {

	return apply_filters( 'wpclubmanager_date_format', get_option( 'date_format' ) );
}

/**
 * WooCommerce Time Format - Allows to change time format for everything WooCommerce
 *
 * @access public
 * @return string
 */
function wpcm_time_format() {
	
	return apply_filters( 'wpclubmanager_time_format', get_option( 'time_format' ) );
}