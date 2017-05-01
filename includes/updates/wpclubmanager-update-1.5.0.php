<?php
/**
 * Update WPClubManager to 1.5.0
 *
 * @author 		Clubpress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Updates
 * @version     1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


$my_posts = get_posts( array('post_type' => 'wpcm_player', 'numberposts' => -1 ) );

$stats = array_merge( array( 'appearances' => __( 'Apps', 'wp-club-manager' ) ), wpcm_get_preset_labels() );

foreach( $stats as $key => $val ) {
	if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) {
		$stats[$key] = '';
	}
}

foreach ( $my_posts as $my_post ):

	update_post_meta( $my_post->ID, '_wpcm_custom_player_stats', $stats );

endforeach;

$stats_labels = wpcm_get_preset_labels();

foreach ( $stats_labels as $key => $value ) :

	update_option( 'wpcm_match_show_stats_' . $key, 'yes' );

endforeach;