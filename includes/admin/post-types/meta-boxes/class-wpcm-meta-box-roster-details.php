<?php
/**
 * Roster Details
 *
 * Displays the league table details box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Roster_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
        
		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

        $seasons = get_the_terms( $post->ID, 'wpcm_season' );
		if ( is_array( $seasons ) ) {
		 	$season = $seasons[0]->term_id;
		} else {
			$season = -1;
		}

		$teams = get_the_terms( $post->ID, 'wpcm_team' );
		if ( is_array( $teams ) ) {
			$team = $teams[0]->term_id;
		} else {
			$team = -1;
		} ?>
        
        <p>
            <label><?php _e( 'Season', 'wp-club-manager' ); ?></label>
            <?php
            wp_dropdown_categories(array(
                'orderby' => 'tax_position',
				'meta_key' => 'tax_position',
                'hide_empty' => false,
                'taxonomy' => 'wpcm_season',
                'selected' => $season,
                'name' => 'wpcm_roster_season',
                'class' => 'chosen_select'
            ));
            ?>
        </p>
        <p>
            <label><?php _e( 'Team', 'wp-club-manager' ); ?></label>
            <?php
            wp_dropdown_categories(array(
                'orderby' => 'tax_position',
				'meta_key' => 'tax_position',
                'hide_empty' => false,
                'taxonomy' => 'wpcm_team',
                'selected' => $team,
                'name' => 'wpcm_roster_team',
                'class' => 'chosen_select'
            ));
            ?>
        </p>

        <?php do_action('wpclubmanager_admin_roster_details', $post->ID );

    }

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if( isset( $_POST['wpcm_roster_season'] ) ) {
            wp_set_post_terms( $post_id, $_POST['wpcm_roster_season'], 'wpcm_season' );
        }
        if( isset( $_POST['wpcm_roster_team'] ) ) {
            wp_set_post_terms( $post_id, $_POST['wpcm_roster_team'], 'wpcm_team' );
        }

		do_action( 'delete_plugin_transients' );
	}
}