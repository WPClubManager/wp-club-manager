<?php
/**
 * Match Details
 *
 * Displays the match details box.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Match_Details
 */
class WPCM_Meta_Box_Match_Details {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$wpcm_comp_status = get_post_meta( $post->ID, 'wpcm_comp_status', true );
		$neutral          = get_post_meta( $post->ID, 'wpcm_neutral', true );
		$referee          = get_post_meta( $post->ID, 'wpcm_referee', true );

		$comps = get_the_terms( $post->ID, 'wpcm_comp' );
		if ( is_array( $comps ) ) {
			$comp      = $comps[0]->term_id;
			$comp_slug = $comps[0]->slug;
		} else {
			$comp      = 0;
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

		$venues        = get_the_terms( $post->ID, 'wpcm_venue' );
		$default_club  = get_default_club();
		$default_venue = get_the_terms( $default_club, 'wpcm_venue' );
		if ( is_array( $venues ) ) {
			$venue = $venues[0]->term_id;
		} elseif ( is_array( $default_venue ) ) {
				$venue = $default_venue[0]->term_id;
		} else {
			$venue = -1;
		}
		$time = ( 'publish' === $post->post_status || 'future' === $post->post_status ? get_the_time( 'H:i' ) : get_option( 'wpcm_match_time', '15:00' ) );

		$date = get_the_date( 'Y-m-d' );

		$option_list = get_option( 'wpcm_referee_list', array() );

		wpclubmanager_wp_text_input( array(
			'id'                => 'wpcm_match_date',
			'label'             => __( 'Date', 'wp-club-manager' ),
			'placeholder'       => _x( 'YYYY-MM-DD', 'placeholder', 'wp-club-manager' ),
			'value'             => $date,
			'description'       => '',
			'class'             => 'wpcm-date-picker',
			'custom_attributes' => array( 'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ),
		) );

		wpclubmanager_wp_text_input( array(
			'id'    => 'wpcm_match_kickoff',
			'label' => __( 'Time', 'wp-club-manager' ),
			'value' => $time,
			'class' => 'wpcm-time-picker',
		) ); ?>

		<p>
			<label><?php esc_html_e( 'Competition', 'wp-club-manager' ); ?></label>
			<?php
			wp_dropdown_categories(array(
				'taxonomy'     => 'wpcm_comp',
				'hide_empty'   => false,
				'meta_key'     => 'tax_position',
				'meta_compare' => 'NUMERIC',
				'orderby'      => 'meta_value_num',
				'selected'     => $comp,
				'name'         => 'wpcm_comp',
				'class'        => 'chosen_select',
			));
			?>
			<input type="text" name="wpcm_comp_status" id="wpcm_comp_status" value="<?php echo esc_attr( $wpcm_comp_status ); ?>" placeholder="<?php esc_html_e( 'Round (Optional)', 'wp-club-manager' ); ?>" />
		</p>
		<p>
			<label><?php esc_html_e( 'Season', 'wp-club-manager' ); ?></label>
			<?php
			wp_dropdown_categories(array(
				'taxonomy'     => 'wpcm_season',
				'hide_empty'   => false,
				'meta_key'     => 'tax_position',
				'meta_compare' => 'NUMERIC',
				'orderby'      => 'meta_value_num',
				'selected'     => $season,
				'name'         => 'wpcm_season',
				'class'        => 'chosen_select',
			));
			?>
		</p>
		<?php
		if ( is_club_mode() && has_teams() ) {
			?>
			<p>
				<label><?php esc_html_e( 'Team', 'wp-club-manager' ); ?></label>
				<?php
				wp_dropdown_categories(array(
					'taxonomy'     => 'wpcm_team',
					'hide_empty'   => false,
					'meta_key'     => 'tax_position',
					'meta_compare' => 'NUMERIC',
					'orderby'      => 'meta_value_num',
					'selected'     => $team,
					'name'         => 'wpcm_match_team',
					'class'        => 'chosen_select',
				));
				?>
			</p>
			<?php
		}
		?>
		<p>
			<label><?php esc_html_e( 'Venue', 'wp-club-manager' ); ?></label>
			<?php
			wp_dropdown_categories( array(
				'show_option_none' => __( 'None' ),
				'orderby'          => 'title',
				'hide_empty'       => false,
				'taxonomy'         => 'wpcm_venue',
				'selected'         => $venue,
				'name'             => 'wpcm_venue',
				'class'            => 'chosen_select',
			) );
			?>
			<label class="selectit wpcm-cb-block">
				<input type="checkbox" name="wpcm_neutral" id="wpcm_neutral" value="1" <?php checked( true, $neutral ); ?> />
				<?php esc_html_e( 'Neutral?', 'wp-club-manager' ); ?>
			</label>
		</p>
		<?php

		if ( get_option( 'wpcm_results_show_attendance' ) == 'yes' ) {
			wpclubmanager_wp_text_input( array(
				'id'    => 'wpcm_attendance',
				'label' => __( 'Attendance', 'wp-club-manager' ),
			) );
		}

		if ( get_option( 'wpcm_results_show_referee' ) == 'yes' ) {
			?>

			<?php
			if ( $option_list ) {
				?>
				<p>
					<label><?php esc_html_e( 'Referee', 'wp-club-manager' ); ?></label>
					<select name='wpcm_referee' id="wpcm_referee" class="combify-input">
						<?php
						foreach ( $option_list as $option ) {
							?>
							<option value="<?php echo esc_attr( $option ); ?>"<?php echo ( $option == $referee ? ' selected' : null ); ?>><?php echo esc_html( $option ); ?></option>
							<?php
						}
						?>
					</select>
				</p>
				<?php
			} else {
				wpclubmanager_wp_text_input( array(
					'id'    => 'wpcm_referee',
					'label' => __( 'Referee', 'wp-club-manager' ),
					'class' => 'regular-text',
				) );
			}
		}

		wpclubmanager_wp_checkbox( array(
			'id'    => 'wpcm_friendly',
			'label' => __( 'Friendly', 'wp-club-manager' ),
		) );

		do_action( 'wpclubmanager_admin_match_details', $post->ID );
	}

	/**
	 * Save meta box data
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {
		if ( ! check_admin_referer( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' ) ) {
			return;
		}

		$wpcm_match_date = filter_input( INPUT_POST, 'wpcm_match_date', FILTER_UNSAFE_RAW );
		if ( $wpcm_match_date ) {
			$date               = sanitize_text_field( $wpcm_match_date );
			$wpcm_match_kickoff = filter_input( INPUT_POST, 'wpcm_match_kickoff', FILTER_UNSAFE_RAW );
			$kickoff            = sanitize_text_field( $wpcm_match_kickoff );
			$datetime           = $date . ' ' . $kickoff . ':00';
			update_post_meta( $post_id, '_wpcm_match_datetime', $datetime );
		}

		$comp = filter_input( INPUT_POST, 'wpcm_comp', FILTER_VALIDATE_INT );
		if ( $comp ) {
			wp_set_post_terms( $post_id, $comp, 'wpcm_comp' );
		}

		$season = filter_input( INPUT_POST, 'wpcm_season', FILTER_VALIDATE_INT );
		if ( $season ) {
			wp_set_post_terms( $post_id, $season, 'wpcm_season' );
		}

		$team = filter_input( INPUT_POST, 'wpcm_match_team', FILTER_VALIDATE_INT );
		if ( $team ) {
			wp_set_post_terms( $post_id, $team, 'wpcm_team' );
		}

		$venue = filter_input( INPUT_POST, 'wpcm_venue', FILTER_VALIDATE_INT );
		if ( $venue ) {
			wp_set_post_terms( $post_id, $venue, 'wpcm_venue' );
		}

		$wpcm_comp_status = filter_input( INPUT_POST, 'wpcm_comp_status', FILTER_UNSAFE_RAW );
		if ( $wpcm_comp_status ) {
			update_post_meta( $post_id, 'wpcm_comp_status', sanitize_text_field( $wpcm_comp_status ) );
		}

		$wpcm_neutral = filter_input( INPUT_POST, 'wpcm_neutral', FILTER_UNSAFE_RAW );
		if ( $wpcm_neutral ) {
			update_post_meta( $post_id, 'wpcm_neutral', sanitize_text_field( $wpcm_neutral ) );
		}

		$wpcm_referee = filter_input( INPUT_POST, 'wpcm_referee', FILTER_UNSAFE_RAW );
		if ( $wpcm_referee ) {
			$wpcm_referee = sanitize_text_field( $wpcm_referee );
			update_post_meta( $post_id, 'wpcm_referee', $wpcm_referee );
			$options = get_option( 'wpcm_referee_list', array() );
			if ( ! in_array( $wpcm_referee, $options ) ) {
				$options[] = $wpcm_referee;
				update_option( 'wpcm_referee_list', $options );
			}
		}

		$wpcm_attendance = filter_input( INPUT_POST, 'wpcm_attendance', FILTER_UNSAFE_RAW );
		if ( $wpcm_attendance ) {
			update_post_meta( $post_id, 'wpcm_attendance', sanitize_text_field( $wpcm_attendance ) );
		}

		$is_friendly = filter_input( INPUT_POST, 'wpcm_friendly', FILTER_UNSAFE_RAW );
		if ( $is_friendly ) {
			update_post_meta( $post_id, 'wpcm_friendly', sanitize_text_field( $is_friendly ) );
		} else {
			update_post_meta( $post_id, 'wpcm_friendly', '' );
		}

		do_action( 'wpclubmanager_after_admin_match_save', $post_id );
	}
}
