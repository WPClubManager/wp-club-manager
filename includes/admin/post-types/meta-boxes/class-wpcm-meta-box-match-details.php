<?php
/**
 * Match Details
 *
 * Displays the match details box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.5.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Match_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$comps = get_the_terms( $post->ID, 'wpcm_comp' );
		$wpcm_comp_status = get_post_meta( $post->ID, 'wpcm_comp_status', true );
		$team = get_post_meta( $post->ID, 'wpcm_team', true );
		$neutral = get_post_meta( $post->ID, 'wpcm_neutral', true );

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

		$teams = get_the_terms( $post->ID, 'wpcm_team' );

		if ( is_array( $teams ) ) {
			$team = $teams[0]->term_id;
		} else {
			$team = -1;
		}

		$venues = get_the_terms( $post->ID, 'wpcm_venue' );
		$default_club = get_default_club();
		$default_venue = get_the_terms( $default_club, 'wpcm_venue' );

		if ( is_array( $venues ) ) {
			$venue = $venues[0]->term_id;
		} else {
			if( is_array( $default_venue ) ) {
				$venue = $default_venue[0]->term_id;
			} else {
				$venue = -1;
			}
		} ?>

		<p>
			<label><?php _e( 'Competition', 'wp-club-manager' ); ?></label>
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
			<input type="text" name="wpcm_comp_status" id="wpcm_comp_status" value="<?php echo $wpcm_comp_status; ?>" placeholder="Round (Optional)" />
		</p>
		<p>
			<label><?php _e( 'Season', 'wp-club-manager' ); ?></label>
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
			<label><?php _e( 'Team', 'wp-club-manager' ); ?></label>
			<?php
			wp_dropdown_categories(array(
				'show_option_all' => __( 'All' ),
				'orderby' => 'title',
				'hide_empty' => false,
				'taxonomy' => 'wpcm_team',
				'selected' => $team,
				'name' => 'wpcm_match_team',
				'class' => 'chosen_select'
			));
			?>
		</p>
		<p>
			<label><?php _e( 'Venue', 'wp-club-manager' ); ?></label>
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
			<label class="selectit wpcm-cb-block">
				<input type="checkbox" name="wpcm_neutral" id="wpcm_neutral" value="1" <?php checked( true, $neutral ); ?> />
				<?php _e( 'Neutral?', 'wp-club-manager' ); ?>
			</label>
		</p> <?php

		wpclubmanager_wp_text_input( array( 'id' => 'wpcm_attendance', 'label' => __( 'Attendance', 'wp-club-manager' ), 'class' => 'small-text' ) );

		wpclubmanager_wp_text_input( array( 'id' => 'wpcm_referee', 'label' => __( 'Referee', 'wp-club-manager' ), 'class' => 'regular-text' ) );

		do_action('wpclubmanager_admin_match_details', $post->ID );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if( isset( $_POST['wpcm_comp'] ) ){
			wp_set_post_terms( $post_id, $_POST['wpcm_comp'], 'wpcm_comp' );
		}
		if( isset( $_POST['wpcm_season'] ) ){
			wp_set_post_terms( $post_id, $_POST['wpcm_season'], 'wpcm_season' );
		}
		if( isset( $_POST['wpcm_match_team'] ) ){
			wp_set_post_terms( $post_id, $_POST['wpcm_match_team'], 'wpcm_team' );
		}
		if( isset( $_POST['wpcm_comp_status'] ) ){
			wp_set_post_terms( $post_id, $_POST['wpcm_venue'], 'wpcm_venue' );
		}

		if( isset( $_POST['wpcm_comp_status'] ) ){
			update_post_meta( $post_id, 'wpcm_comp_status', $_POST['wpcm_comp_status'] );
		}
		if( isset( $_POST['wpcm_neutral'] ) ){
			update_post_meta( $post_id, 'wpcm_neutral', $_POST['wpcm_neutral'] );
		}
		if( isset( $_POST['wpcm_referee'] ) ){
			update_post_meta( $post_id, 'wpcm_referee', $_POST['wpcm_referee'] );
		}
		if( isset( $_POST['wpcm_attendance'] ) ){
			update_post_meta( $post_id, 'wpcm_attendance', $_POST['wpcm_attendance'] );
		}

		do_action('wpclubmanager_after_admin_match_save', $post_id );
	}
}