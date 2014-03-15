<?php
/**
 * Match Details
 *
 * Displays the match details box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Match_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$post_id = $post->ID;

		$comps = get_the_terms( $post_id, 'wpcm_comp' );

		$match_team = get_post_meta( $post_id, 'wpcm_team', true );

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
			$season = -1;
		}

		$venues = get_the_terms( $post->ID, 'wpcm_venue' );

		if ( is_array( $venues ) ) {
			$venue = $venues[0]->term_id;
		} else {
			$venue = -1;
		} ?>

		<p>
			<label><?php _e( 'Competition', 'wpclubmanager' ); ?></label>
			<?php
			wp_dropdown_categories(array(
				'show_option_none' => __( 'None' ),
				'orderby' => 'title',
				'hide_empty' => false,
				'taxonomy' => 'wpcm_comp',
				'selected' => $comp,
				'name' => 'wpcm_comp',
				'class' => 'chosen_select'
			));
			?>
		</p>
		<p>
			<label><?php _e( 'Season', 'wpclubmanager' ); ?></label>
			<?php
			wp_dropdown_categories(array(
				'show_option_none' => __( 'None' ),
				'orderby' => 'title',
				'hide_empty' => false,
				'taxonomy' => 'wpcm_season',
				'selected' => $season,
				'name' => 'wpcm_season',
				'class' => 'chosen_select'
			));
			?>
		</p>
		<p>
			<label><?php _e( 'Team', 'wpclubmanager' ); ?></label>
			<?php
			wp_dropdown_categories(array(
				'show_option_all' => __( 'All' ),
				'orderby' => 'title',
				'hide_empty' => false,
				'taxonomy' => 'wpcm_team',
				'selected' => $match_team,
				'name' => 'wpcm_match_team',
				'class' => 'chosen_select'
			));
			?>
		</p>
		<p>
			<label><?php _e( 'Venue', 'wpclubmanager' ); ?></label>
			<?php
			wp_dropdown_categories( array(
				'show_option_none' => __( 'None' ),
				'orderby' => 'title',
				'hide_empty' => false,
				'taxonomy' => 'wpcm_venue',
				'selected' => $venue,
				'name' => 'wpcm_venue',
				'class' => 'chosen_select'
			) );
			?>
		</p> <?php

		wpclubmanager_wp_text_input( array( 'id' => 'wpcm_attendance', 'label' => __( 'Attendance', 'wpclubmanager' ), 'class' => 'small-text' ) );

		wpclubmanager_wp_text_input( array( 'id' => 'wpcm_referee', 'label' => __( 'Referee', 'wpclubmanager' ), 'class' => 'regular-text' ) );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		$comp = $_POST['wpcm_comp'];
		$season = $_POST['wpcm_season'];
		wp_set_post_terms( $post_id, $comp, 'wpcm_comp' );
		wp_set_post_terms( $post_id, $season, 'wpcm_season' );

		if(isset($_POST['wpcm_teams'])){
			$teams = $_POST['wpcm_teams'];
		}

		if(isset($_POST['wpcm_match_team'])){
			$match_team = $_POST['wpcm_match_team'];
		} else {
			$match_team = null;
		}
		
		update_post_meta( $post_id, 'wpcm_team', $match_team );
		wp_set_post_terms( $post_id, $match_team, 'wpcm_team' );

		$venue = $_POST['wpcm_venue'];
		wp_set_post_terms( $post_id, $venue, 'wpcm_venue' );

		update_post_meta( $post_id, 'wpcm_referee', $_POST['wpcm_referee'] );
		update_post_meta( $post_id, 'wpcm_attendance', $_POST['wpcm_attendance'] );
	}
}