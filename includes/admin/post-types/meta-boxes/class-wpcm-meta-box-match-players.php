<?php
/**
 * Match Players
 *
 * Displays the match players box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Match_Players {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		$post_id = $post->ID;

		$players = unserialize( get_post_meta( $post_id, 'wpcm_players', true ) );

		$club = get_option('wpcm_default_club'); ?>
		
		<div class="playersdiv" id="wpcm_players">
				
			<ul class="wpcm_stats-tabs">
				<li class="tabs"><a href="#wpcm_lineup" tabindex="3"><?php _e( 'Starting Lineup', 'wpclubmanager' ); ?></a></li>
				<li class="hide-if-no-js"><a href="#wpcm_subs" tabindex="3"><?php _e( 'Substitutes', 'wpclubmanager' ); ?></a></li>
			</ul>
			<div id="wpcm_lineup" class="tabs-panel">
				<?php wpcm_match_player_stats_table( $players, $club, 'lineup' ); ?>
				<p class="wpcm_counter"><?php _e('You have selected', 'wpclubmanager'); ?> <span class="counter"></span> <?php _e('players', 'wpclubmanager'); ?></p>
			</div>
			<div id="wpcm_subs" class="tabs-panel" style="display: none;">
				<?php wpcm_match_player_stats_table( $players, $club, 'subs' ); ?>
				<p class="wpcm_counter"><?php _e('You have selected', 'wpclubmanager'); ?> <span class="counter"></span> <?php _e('substitutes', 'wpclubmanager'); ?></p>
			</div>
		</div>
	<?php }

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if(isset($_POST['wpcm_players'])){
			$players = (array)$_POST['wpcm_players'];
		} else {
			$players = null;
		}
			
		if ( is_array( $players ) ) {
			if ( array_key_exists( 'lineup', $players ) && is_array( $players['lineup'] ) )
				$players['lineup'] = array_filter( $players['lineup'], 'wpcm_array_filter_checked' );
			if ( array_key_exists( 'subs', $players ) &&  is_array( $players['subs'] ) )
				$players['subs'] = array_filter( $players['subs'], 'wpcm_array_filter_checked' );
		}
		update_post_meta( $post_id, 'wpcm_players', serialize( $players ) );
	}
}