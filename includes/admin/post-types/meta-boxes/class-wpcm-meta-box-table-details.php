<?php
/**
 * Table Details
 *
 * Displays the league table details box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Table_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
        
		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

        $comps = get_the_terms( $post->ID, 'wpcm_comp' );
        if ( is_array( $comps ) ) {
			$comp = $comps[0]->term_id;
			$comp_slug = $comps[0]->slug;
		} else {
			$comp = 0;
			$comp_slug = null;
        }
        
        $seasons = get_the_terms( $post->ID, 'wpcm_season' );
		if ( is_array( $seasons ) ) {
		 	$season = $seasons[0]->term_id;
		} else {
			$season = 0;
		}

        $default_club = get_default_club();
		if( $default_club !== null && has_teams() ){
            $teams = get_the_terms( $post->ID, 'wpcm_team' );
            if ( is_array( $teams ) ) {
                $team = $teams[0]->term_id;
            } else {
                $team = 0;
            }
        } ?>
        
        <p>
            <label><?php _e( 'Competition', 'wp-club-manager' ); ?></label>
            <?php
            wp_dropdown_categories(array(
                'taxonomy' => 'wpcm_comp',
                'meta_key' => 'tax_position',
                'orderby' => 'tax_position',
                'hide_empty' => false,
                'selected' => $comp,
                'name' => 'wpcm_table_comp',
                'class' => 'chosen_select'
            ));
            ?>
        </p>
        <p>
            <label><?php _e( 'Season', 'wp-club-manager' ); ?></label>
            <?php
            wp_dropdown_categories(array(
                'taxonomy' => 'wpcm_season',
                'meta_key' => 'tax_position',
                'orderby' => 'tax_position',
                'hide_empty' => false,
                'selected' => $season,
                'name' => 'wpcm_table_season',
                'class' => 'chosen_select'
            ));
            ?>
        </p>
        <?php
        if( $default_club !== null && has_teams() ){ ?>
            <p>
                <label><?php _e( 'Team', 'wp-club-manager' ); ?></label>
                <?php
                wp_dropdown_categories(array(
                    'taxonomy' => 'wpcm_team',
                    'meta_key' => 'tax_position',
                    'orderby' => 'tax_position',
                    'hide_empty' => false,
                    'selected' => $team,
                    'name' => 'wpcm_table_team',
                    'class' => 'chosen_select'
                ));
                ?>
            </p>
        <?php
        } ?>

        <?php do_action('wpclubmanager_admin_table_details', $post->ID );

    }

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if( isset( $_POST['wpcm_table_comp'] ) ){
            wp_set_post_terms( $post_id, $_POST['wpcm_table_comp'], 'wpcm_comp' );
		}
		if( isset( $_POST['wpcm_table_season'] ) ){
            wp_set_post_terms( $post_id, $_POST['wpcm_table_season'], 'wpcm_season' );
        }
        if( isset( $_POST['wpcm_table_team'] ) ){
            wp_set_post_terms( $post_id, $_POST['wpcm_table_team'], 'wpcm_team' );
        }

		do_action( 'delete_plugin_transients' );
	}
}