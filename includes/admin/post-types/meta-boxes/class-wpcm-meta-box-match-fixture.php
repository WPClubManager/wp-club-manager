<?php
/**
 * Match Fixture
 *
 * Displays the match fixture box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Match_Fixture {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$home_club = get_post_meta( $post->ID, 'wpcm_home_club', true );
		$away_club = get_post_meta( $post->ID, 'wpcm_away_club', true );
		$separator = get_option('wpcm_match_clubs_separator'); ?>
		
		<p>
			<label><?php _e( 'Home', 'wp-club-manager' ); ?></label>
			<?php
			wpcm_dropdown_posts(array(
				'name' 				=> 'wpcm_home_club',
				'id' 				=> 'wpcm_home_club',
				'post_type' 		=> 'wpcm_club',
				'limit' 			=> -1,
				'show_option_none'	=> __( 'Choose club', 'wp-club-manager' ),
				'class'				=> 'chosen_select',
				'echo' 				=> false,
				'selected' 			=> $home_club
			));
			?>
		</p>

		<p>
			<label><?php _e( 'Away', 'wp-club-manager' ); ?></label>
			<?php
			wpcm_dropdown_posts(array(
				'name' 				=> 'wpcm_away_club',
				'id' 				=> 'wpcm_away_club',
				'post_type' 		=> 'wpcm_club',
				'limit' 			=> -1,
				'show_option_none'	=> __( 'Choose club', 'wp-club-manager' ),
				'class'				=> 'chosen_select',
				'echo' 				=> false,
				'selected' 			=> $away_club
			));
			?>
		</p>
		<input type="hidden" name="post_title" value="" />
	<?php }

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		update_post_meta( $post_id, 'wpcm_home_club', $_POST['wpcm_home_club'] );
		update_post_meta( $post_id, 'wpcm_away_club', $_POST['wpcm_away_club'] );

		// add comps and seasons to clubs
		wp_set_post_terms( $_POST['wpcm_home_club'], $_POST['wpcm_comp'], 'wpcm_comp', true );
		wp_set_post_terms( $_POST['wpcm_home_club'], $_POST['wpcm_season'], 'wpcm_season', true );
		wp_set_post_terms( $_POST['wpcm_away_club'], $_POST['wpcm_comp'], 'wpcm_comp', true );
		wp_set_post_terms( $_POST['wpcm_away_club'], $_POST['wpcm_season'], 'wpcm_season', true );
	}
}