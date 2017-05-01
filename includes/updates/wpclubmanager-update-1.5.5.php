<?php
/**
 * Update WPClubManager to 1.5.5
 *
 * @author 		Clubpress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Updates
 * @version     1.5.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$id = get_option( 'wpcm_default_club' );
if( $id ) {
	$date = get_the_date( 'Y-m-d h:i:s', $id );
	add_option( 'wpcm_install_date', date( 'Y-m-d h:i:s', strtotime( $date ) ) );
} else {
	add_option( 'wpcm_install_date', date( 'Y-m-d h:i:s' ) );
}
add_option( 'wpcm_rating', 'no' );