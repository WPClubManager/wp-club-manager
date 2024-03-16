<?php
/**
 * Club importer - import matches into WP Club Manager.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Importers
 * @version     2.2.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( class_exists( 'WP_Importer' ) ) {

	/**
	 *  WPCM_Match_Importer
	 */
	class WPCM_Match_Importer extends WPCM_Importer {

		/**
		 * __construct function.
		 */
		public function __construct() {
			$this->import_page  = 'wpclubmanager_match_csv';
			$this->import_label = __( 'Import Matches', 'wp-club-manager' );
			$this->columns      = array(
				'post_date'       => __( 'Date', 'wp-club-manager' ),
				'post_time'       => __( 'Time', 'wp-club-manager' ),
				'wpcm_home_club'  => __( 'Home Club', 'wp-club-manager' ),
				'wpcm_away_club'  => __( 'Away Club', 'wp-club-manager' ),
				'wpcm_result'     => __( 'Result', 'wp-club-manager' ),
				'wpcm_comp'       => __( 'Competition', 'wp-club-manager' ),
				'wpcm_season'     => __( 'Season', 'wp-club-manager' ),
				'wpcm_team'       => __( 'Team', 'wp-club-manager' ),
				'wpcm_venue'      => __( 'Venue', 'wp-club-manager' ),
				'wpcm_attendance' => __( 'Attendance', 'wp-club-manager' ),
				'wpcm_referee'    => __( 'Referee', 'wp-club-manager' ),
				'wpcm_players'    => __( 'Lineup', 'wp-club-manager' ),
			);
		}

		/**
		 * import function.
		 *
		 * @param array $array
		 * @param array $columns
		 */
		public function import( $array = array(), $columns = array( 'post_title' ) ) {
			$this->imported = 0;
			$this->skipped  = 0;

			if ( ! is_array( $array ) || ! count( $array ) ) :
				$this->footer();
				die();
			endif;

			$rows = array_chunk( $array, count( $columns ) );

			$date_format              = 'yyyy/mm/dd';
			$wpcm_player_stats_labels = wpcm_get_preset_labels();
			foreach ( $wpcm_player_stats_labels as $key => $val ) :
				if ( get_option( 'wpcm_show_stats_' . $key ) == 'yes' ) :
					$labels[] = $key;
				endif;
			endforeach;

			foreach ( $rows as $row ) :

				$row = array_filter( $row );

				if ( empty( $row ) ) {
					continue;
				}

				$meta = array();

				foreach ( $columns as $index => $key ) :
					$meta[ $key ] = wpcm_array_value( $row, $index );
				endforeach;

				// Get home club ID
				$home_club   = wpcm_array_value( $meta, 'wpcm_home_club' );
				$home_object = get_page_by_title( $home_club, OBJECT, 'wpcm_club' );
				if ( $home_object ) :
					$home_id = $home_object->ID;
				else :
					// Create club if doesn't exist
					$home_id = wp_insert_post( array(
						'post_type'   => 'wpcm_club',
						'post_status' => 'publish',
						'post_title'  => $home_club,
					) );
					// Flag as import
					update_post_meta( $home_id, '_wpcm_import', 1 );
				endif;

				// Get away club ID
				$away_club   = wpcm_array_value( $meta, 'wpcm_away_club' );
				$away_object = get_page_by_title( $away_club, OBJECT, 'wpcm_club' );
				if ( $away_object ) :
					$away_id = $away_object->ID;
				else :
					// Create club if doesn't exist
					$away_id = wp_insert_post( array(
						'post_type'   => 'wpcm_club',
						'post_status' => 'publish',
						'post_title'  => $away_club,
					) );
					// Flag as import
					update_post_meta( $away_id, '_wpcm_import', 1 );
				endif;

				// Format date and time
				$date       = wpcm_array_value( $meta, 'post_date' );
				$time       = wpcm_array_value( $meta, 'post_time' );
				$date       = str_replace( '/', '-', trim( $date ) );
				$date_array = explode( '-', $date );
				$date       = substr( str_pad( wpcm_array_value( $date_array, 0, '0000' ), 4, '0', STR_PAD_LEFT ), 0, 4 ) . '-' .
						substr( str_pad( wpcm_array_value( $date_array, 1, '00' ), 2, '0', STR_PAD_LEFT ), 0, 2 ) . '-' .
						substr( str_pad( wpcm_array_value( $date_array, 2, '00' ), 2, '0', STR_PAD_LEFT ), 0, 2 );

				$date .= ' ' . trim( $time );

				// Insert match data
				$separator   = get_option( 'wpcm_match_clubs_separator' );
				$match_title = $home_club . ' ' . $separator . ' ' . $away_club;

				$args = array(
					'post_type'   => 'wpcm_match',
					'post_status' => 'publish',
					'post_date'   => $date,
					'post_title'  => $match_title,
					'post_name'   => 'importing',
				);
				$id   = wp_insert_post( $args );

				$post_name = sanitize_title_with_dashes( $id . '-' . $home_club . '-' . $separator . '-' . $away_club );

				// $match_name = $id . '-' . $home_title . '-' . $separator . '-' . $away_title;
				wp_update_post( array(
					'ID'         => $id,
					'post_name'  => $post_name,
					'post_title' => $match_title,
				) );

				// Flag as import
				update_post_meta( $id, '_wpcm_import', 1 );

				// Update home club
				update_post_meta( $id, 'wpcm_home_club', $home_id );

				// Update away club
				update_post_meta( $id, 'wpcm_away_club', $away_id );

				// Update result
				$result = wpcm_array_value( $meta, 'wpcm_result' );
				if ( $result ) :
					$scores     = explode( '-', wpcm_array_value( $meta, 'wpcm_result' ) );
					$home_goals = trim( $scores[0] );
					$away_goals = trim( $scores[1] );
					$goals      = array(
						'total' => array(
							'home' => $home_goals,
							'away' => $away_goals,
						),
					);

					if ( $home_goals >= '0' && $away_goals >= '0' ) :
						update_post_meta( $id, 'wpcm_home_goals', $home_goals );
						update_post_meta( $id, 'wpcm_away_goals', $away_goals );
						update_post_meta( $id, 'wpcm_goals', serialize( $goals ) );
						update_post_meta( $id, 'wpcm_played', 1 );
					endif;

				endif;

				// Update competitions
				$comps = wpcm_array_value( $meta, 'wpcm_comp' );
				$comp  = sanitize_title_with_dashes( $comps );
				wp_set_object_terms( $id, $comp, 'wpcm_comp', false );

				// Update seasons
				$seasons = wpcm_array_value( $meta, 'wpcm_season' );
				$season  = sanitize_title_with_dashes( $seasons );
				wp_set_object_terms( $id, $season, 'wpcm_season', false );

				// Update teams
				$teams = wpcm_array_value( $meta, 'wpcm_team' );
				$team  = sanitize_title_with_dashes( $teams );
				wp_set_object_terms( $id, $team, 'wpcm_team', false );

				// Update venues
				$venues = wpcm_array_value( $meta, 'wpcm_venue' );
				$venue  = sanitize_title_with_dashes( $venues );
				wp_set_object_terms( $id, $venue, 'wpcm_venue', false );

				// Update Attendance
				$attendance = wpcm_array_value( $meta, 'wpcm_attendance' );
				update_post_meta( $id, 'wpcm_attendance', $attendance );

				// Update Referee
				$referee = wpcm_array_value( $meta, 'wpcm_referee' );
				update_post_meta( $id, 'wpcm_referee', $referee );

				$players = wpcm_array_value( $meta, 'wpcm_players' );

				if ( $players ) :
					$lineup = explode( '|', $players );
					// unset($stats);
					$players_array = array();
					$players       = array();
					foreach ( $lineup as $player ) {
						$player_array = explode( '-', $player );
						$player_name  = trim( $player_array[0] );
						$player_stats = trim( $player_array[1] );

						$player_title = get_page_by_title( $player_name, OBJECT, 'wpcm_player' );
						if ( $player_title ) :
							$player_id = $player_title->ID;
						else :
							$player_id = wp_insert_post(
								array(
									'post_type'   => 'wpcm_player',
									'post_status' => 'publish',
									'post_title'  => $player_name,
								)
							);
							update_post_meta( $player_id, '_wpcm_import', 1 );
							$parts     = explode( ' ', $player_name );
							$lastname  = array_pop( $parts );
							$firstname = implode( ' ', $parts );
							update_post_meta( $player_id, '_wpcm_firstname', $firstname );
							update_post_meta( $player_id, '_wpcm_lastname', $lastname );
						endif;
						wp_set_object_terms( $player_id, $season, 'wpcm_season', false );
						if ( is_club_mode() ) :
							wp_set_object_terms( $player_id, $team, 'wpcm_team', false );
						endif;

						$player_stats = explode( ' ', $player_stats );
						foreach ( $player_stats as $player_stat => $value ) :
							$stats[] = $value;
						endforeach;
						$stats_combine = array_combine( $labels, $stats );
						unset( $stats );

						$cards = wpcm_stats_cards();
						foreach ( $cards as $card ) {
							if ( array_key_exists( $card, $stats_combine ) && '0' == $stats_combine[ $card ] ) {
								unset( $stats_combine[ $card ] );
							}
						}

						$selected                    = array( 'checked' => '1' );
						$stats_array                 = array_merge( $selected, $stats_combine );
						$players_array[ $player_id ] = $stats_array;
					}
					$players = array(
						'lineup' => $players_array,
					);
					update_post_meta( $id, 'wpcm_players', serialize( $players ) );
				endif;

				++$this->imported;

			endforeach;

			// Show import result
			echo '<div class="updated settings-error below-h2"><p>';
			/* translators: 1: imported total 2: skipped total */
			echo wp_kses_post( sprintf( __( 'Import complete - imported <strong>%1$s</strong> matches and skipped <strong>%2$s</strong>.', 'wp-club-manager' ), $this->imported, $this->skipped ) );
			echo '</p></div>';

			$this->import_end();
		}

		/**
		 * Performs post-import cleanup of files and the cache
		 */
		public function import_end() {
			echo '<p>' . esc_html__( 'All done!', 'wp-club-manager' ) . ' <a href="' . esc_url( admin_url( 'edit.php?post_type=wpcm_match' ) ) . '">' . esc_html__( 'View Matches', 'wp-club-manager' ) . '</a></p>';

			do_action( 'import_end' );
		}

		/**
		 * header function.
		 */
		public function header() {
			echo '<h2>' . esc_html__( 'Import Matches', 'wp-club-manager' ) . '</h2>';
		}

		/**
		 * greet function.
		 */
		public function greet() {
			echo '<div class="narrow">';
			echo '<p>' . esc_html__( 'Choose a .csv file to upload, then click "Upload file and import".', 'wp-club-manager' ) . '</p>';
			/* translators: 1: match-sample.csv URL */
			echo '<p>' . wp_kses_post( sprintf( __( 'Matches need to be defined with columns in a specific order (12 columns). <a href="%s">Click here to download a sample</a>.', 'wp-club-manager' ), esc_url( plugin_dir_url( WPCM_PLUGIN_FILE ) . 'dummy-data/match-sample.csv' ) ) ) . '</p>';
			wp_import_upload_form( 'admin.php?import=wpclubmanager_match_csv&step=1' );
			echo '</div>';
		}
	}
}
