<?php
/**
 * Post Types Admin
 *
 * @author   ClubPress
 * @category Admin
 * @package  WPClubManager/Admin
 * @version  2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPCM_Admin_Post_Types' ) ) :

	/**
	 * WPCM_Admin_Post_Types Class.
	 *
	 * Handles the edit posts views and some functionality on the edit post screen for WPCM post types.
	 */
	class WPCM_Admin_Post_Types {

		/**
		 * Constructor.
		 */
		public function __construct() {

			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

			add_filter( 'the_posts', array( $this, 'show_scheduled_matches' ) );
			add_filter( 'wp_insert_post_data', array( $this, 'wp_insert_post_data' ), 99, 2 );

			// WP List table columns. Defined here so they are always available for events such as inline editing.
			add_filter( 'manage_wpcm_match_posts_columns', array( $this, 'match_columns' ) );
			add_action( 'manage_wpcm_match_posts_custom_column', array( $this, 'render_match_columns' ), 2 );
			add_filter( 'manage_edit-wpcm_match_sortable_columns', array( $this, 'match_sortable_columns' ) );
			add_filter( 'manage_wpcm_club_posts_columns', array( $this, 'club_columns' ) );
			add_action( 'manage_wpcm_club_posts_custom_column', array( $this, 'render_club_columns' ), 2 );
			add_filter( 'manage_wpcm_player_posts_columns', array( $this, 'player_columns' ) );
			add_action( 'manage_wpcm_player_posts_custom_column', array( $this, 'render_player_columns' ), 2 );
			add_filter( 'manage_wpcm_staff_posts_columns', array( $this, 'staff_columns' ) );
			add_action( 'manage_wpcm_staff_posts_custom_column', array( $this, 'render_staff_columns' ), 2 );
			add_filter( 'manage_wpcm_roster_posts_columns', array( $this, 'roster_columns' ) );
			add_action( 'manage_wpcm_roster_posts_custom_column', array( $this, 'render_roster_columns' ), 2 );
			add_filter( 'manage_wpcm_table_posts_columns', array( $this, 'table_columns' ) );
			add_action( 'manage_wpcm_table_posts_custom_column', array( $this, 'render_table_columns' ), 2 );

			add_filter( 'bulk_actions-edit-wpcm_match', array( $this, 'wpcm_match_bulk_actions' ) );
			add_filter( 'list_table_primary_column', array( $this, 'list_table_primary_column' ), 10, 2 );
			add_filter( 'post_row_actions', array( $this, 'row_actions' ), 2, 100 );

			// Quick edit
			add_action( 'quick_edit_custom_box', array( $this, 'quick_edit' ), 10, 2 );
			add_action( 'save_post', array( $this, 'quick_edit_save_post' ), 10, 2 );

			// Filters
			add_action( 'restrict_manage_posts', array( $this, 'request_filter_dropdowns' ) );

			// Edit post screens
			add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
			add_action( 'admin_head', array( $this, 'title_styles' ) );
			add_filter( 'admin_post_thumbnail_html', array( $this, 'custom_admin_post_thumbnail_html' ) );
			add_filter( 'media_view_strings', array( $this, 'media_view_strings' ), 10, 2 );
			add_filter( 'gettext', array( $this, 'text_replace' ), 20, 3 );

			// Disable post type view mode options
			add_filter( 'view_mode_post_types', array( $this, 'disable_view_mode_options' ) );

			include_once 'class-wpcm-admin-meta-boxes.php';
		}

		/**
		 * Change messages when a post type is updated.
		 *
		 * @param  array $messages
		 * @return array
		 */
		public function post_updated_messages( $messages ) {
			global $post, $post_ID;

			$messages['wpcm_player']  = array(
				0  => '', // Unused. Messages start at index 1.
				/* translators: 1: preview url */
				1  => sprintf( __( 'Player updated. <a href="%s">View Player</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				2  => __( 'Custom field updated.', 'wp-club-manager' ),
				3  => __( 'Custom field deleted.', 'wp-club-manager' ),
				4  => __( 'Player updated.', 'wp-club-manager' ),
				/* translators: 1: revision title */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Player restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				/* translators: 1: preview url */
				6  => sprintf( __( 'Player published. <a href="%s">View Player</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				7  => __( 'Player saved.', 'wp-club-manager' ),
				/* translators: 1: preview url */
				8  => sprintf( __( 'Player submitted. <a target="_blank" href="%s">Preview Player</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
				/* translators: 1: date 2: preview url */
				9  => sprintf( __( 'Player scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Player</a>', 'wp-club-manager' ),
				date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
				/* translators: 1: preview url */
				10 => sprintf( __( 'Player draft updated. <a target="_blank" href="%s">Preview Player</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			);
			$messages['wpcm_staff']   = array(
				0  => '', // Unused. Messages start at index 1.
				/* translators: 1: preview url */
				1  => sprintf( __( 'Staff updated. <a href="%s">View Staff</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				2  => __( 'Custom field updated.', 'wp-club-manager' ),
				3  => __( 'Custom field deleted.', 'wp-club-manager' ),
				4  => __( 'Staff updated.', 'wp-club-manager' ),
				/* translators: 1: revision title */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Staff restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				/* translators: 1: preview url */
				6  => sprintf( __( 'Staff published. <a href="%s">View Staff</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				7  => __( 'Staff saved.', 'wp-club-manager' ),
				/* translators: 1: preview url */
				8  => sprintf( __( 'Staff submitted. <a target="_blank" href="%s">Preview Staff</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
				/* translators: 1: date 2: preview url */
				9  => sprintf( __( 'Staff scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Staff</a>', 'wp-club-manager' ),
				date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
				/* translators: 1: preview url */
				10 => sprintf( __( 'Staff draft updated. <a target="_blank" href="%s">Preview Staff</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			);
			$messages['wpcm_match']   = array(
				0  => '', // Unused. Messages start at index 1.
				/* translators: 1: preview url */
				1  => sprintf( __( 'Match updated. <a href="%s">View Match</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				2  => __( 'Custom field updated.', 'wp-club-manager' ),
				3  => __( 'Custom field deleted.', 'wp-club-manager' ),
				4  => __( 'Match updated.', 'wp-club-manager' ),
				/* translators: 1: revision title */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Match restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				/* translators: 1: preview url */
				6  => sprintf( __( 'Match published. <a href="%s">View Match</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				7  => __( 'Match saved.', 'wp-club-manager' ),
				/* translators: 1: preview url */
				8  => sprintf( __( 'Match submitted. <a target="_blank" href="%s">Preview Match</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
				/* translators: 1: date 2: preview url */
				9  => sprintf( __( 'Match scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Match</a>', 'wp-club-manager' ),
				date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
				/* translators: 1: preview url */
				10 => sprintf( __( 'Match draft updated. <a target="_blank" href="%s">Preview Match</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			);
			$messages['wpcm_club']    = array(
				0  => '', // Unused. Messages start at index 1.
				/* translators: 1: preview url */
				1  => sprintf( __( 'Club updated. <a href="%s">View Club</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				2  => __( 'Custom field updated.', 'wp-club-manager' ),
				3  => __( 'Custom field deleted.', 'wp-club-manager' ),
				4  => __( 'Club updated.', 'wp-club-manager' ),
				/* translators: 1: revision title */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Club restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				/* translators: 1: preview url */
				6  => sprintf( __( 'Club published. <a href="%s">View Club</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				7  => __( 'Club saved.', 'wp-club-manager' ),
				/* translators: 1: preview url */
				8  => sprintf( __( 'Club submitted. <a target="_blank" href="%s">Preview Club</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
				/* translators: 1: date 2: preview url */
				9  => sprintf( __( 'Club scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Club</a>', 'wp-club-manager' ),
				date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
				/* translators: 1: preview url */
				10 => sprintf( __( 'Club draft updated. <a target="_blank" href="%s">Preview Club</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			);
			$messages['wpcm_sponsor'] = array(
				0  => '', // Unused. Messages start at index 1.
				1  => __( 'Sponsor updated.', 'wp-club-manager' ),
				2  => __( 'Custom field updated.', 'wp-club-manager' ),
				3  => __( 'Custom field deleted.', 'wp-club-manager' ),
				4  => __( 'Sponsor updated.', 'wp-club-manager' ),
				/* translators: 1: revision title */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Sponsor restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				6  => __( 'Sponsor published.', 'wp-club-manager' ),
				7  => __( 'Sponsor saved.', 'wp-club-manager' ),
				8  => __( 'Sponsor submitted.', 'wp-club-manager' ),
				/* translators: 1: preview url */
				9  => sprintf( __( 'Sponsor scheduled for: <strong>%1$s</strong>.', 'wp-club-manager' ),
				date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ) ),
				10 => __( 'Sponsor draft updated.', 'wp-club-manager' ),
			);
			$messages['wpcm_roster']  = array(
				0  => '', // Unused. Messages start at index 1.
				/* translators: 1: preview url */
				1  => sprintf( __( 'Roster updated. <a href="%s">View Roster</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				2  => __( 'Custom field updated.', 'wp-club-manager' ),
				3  => __( 'Custom field deleted.', 'wp-club-manager' ),
				4  => __( 'Roster updated.', 'wp-club-manager' ),
				/* translators: 1: revision title */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'Roster restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				/* translators: 1: preview url */
				6  => sprintf( __( 'Roster published. <a href="%s">View Roster</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				7  => __( 'Roster saved.', 'wp-club-manager' ),
				/* translators: 1: preview url */
				8  => sprintf( __( 'Roster submitted. <a target="_blank" href="%s">Preview Roster</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
				/* translators: 1: date 2: preview url */
				9  => sprintf( __( 'Roster scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Roster</a>', 'wp-club-manager' ),
				date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
				/* translators: 1: preview url */
				10 => sprintf( __( 'Roster draft updated. <a target="_blank" href="%s">Preview Roster</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			);
			$messages['wpcm_table']   = array(
				0  => '', // Unused. Messages start at index 1.
				/* translators: 1: preview url */
				1  => sprintf( __( 'League Table updated. <a href="%s">View League Table</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				2  => __( 'Custom field updated.', 'wp-club-manager' ),
				3  => __( 'Custom field deleted.', 'wp-club-manager' ),
				4  => __( 'League Table updated.', 'wp-club-manager' ),
				/* translators: 1: revision title */
				5  => isset( $_GET['revision'] ) ? sprintf( __( 'League Table restored to revision from %s', 'wp-club-manager' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
				/* translators: 1: preview url */
				6  => sprintf( __( 'League Table published. <a href="%s">View League Table</a>', 'wp-club-manager' ), esc_url( get_permalink( $post_ID ) ) ),
				7  => __( 'League Table saved.', 'wp-club-manager' ),
				/* translators: 1: preview url */
				8  => sprintf( __( 'League Table submitted. <a target="_blank" href="%s">Preview League Table</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
				/* translators: 1: date 2: preview url */
				9  => sprintf( __( 'League Table scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview League Table</a>', 'wp-club-manager' ),
				date_i18n( __( 'M j, Y @ G:i', 'wp-club-manager' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
				/* translators: 1: preview url */
				10 => sprintf( __( 'League Table draft updated. <a target="_blank" href="%s">Preview League Table</a>', 'wp-club-manager' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			);

			return $messages;
		}

		/**
		 * Show future
		 *
		 * @param array $posts
		 *
		 * @return array
		 */
		public function show_scheduled_matches( $posts ) {

			global $wp_query, $wpdb;

			if ( is_single() && 0 == $wp_query->post_count && isset( $wp_query->query_vars['wpcm_match'] ) ) {
				$posts = $wpdb->get_results( $wp_query->request ); // phpcs:ignore
			}

			return $posts;
		}

		/**
		 * Insert post title data
		 *
		 * @param array $data
		 * @param array $postarr
		 *
		 * @return array
		 */
		public function wp_insert_post_data( $data, $postarr ) {

			if ( 'wpcm_match' == $data['post_type'] ) :

				$separator = get_option( 'wpcm_match_clubs_separator' );

				if ( '' == $data['post_title'] || ' ' . $separator . ' ' == $data['post_title'] || 'importing' == $data['post_name'] ) {

					// $default_club = get_default_club();
					$title_format = get_match_title_format();
					// $separator = get_option('wpcm_match_clubs_separator');
					$home_id = '';
					$home_club = filter_input( INPUT_POST, 'wpcm_home_club', FILTER_VALIDATE_INT );
					if ( $home_club ) {
						$home_id = $home_club;
					}
					$away_id = '';
					$away_club = filter_input( INPUT_POST, 'wpcm_away_club', FILTER_VALIDATE_INT );
					if ( $away_club ) {
						$away_id = $away_club;
					}
					$home_club = get_post( $home_id );
					$away_club = get_post( $away_id );

					if ( is_club_mode() ) {
						$home_club = wpcm_get_team_name( $home_club, $postarr['ID'] );
						$away_club = wpcm_get_team_name( $away_club, $postarr['ID'] );
					} else {
						$home_club = $home_club->post_name;
						$away_club = $away_club->post_name;
					}
					if ( '%home% vs %away%' == $title_format ) {
						$side1 = $home_club;
						$side2 = $away_club;
					} else {
						$side1 = $away_club;
						$side2 = $home_club;
					}

					$title     = $side1 . ' ' . $separator . ' ' . $side2;
					$post_name = sanitize_title_with_dashes( $postarr['ID'] . '-' . $title );

					$data['post_title'] = $title;
					$data['post_name']  = $post_name;
				}

				$kickoff = filter_input( INPUT_POST, 'wpcm_match_kickoff', FILTER_UNSAFE_RAW );
				$date = filter_input( INPUT_POST, 'wpcm_match_date', FILTER_UNSAFE_RAW );
				if ( $kickoff && $date ) {
					$date         = sanitize_text_field( $date );
					$kickoff      = sanitize_text_field( $kickoff );
					$datetime     = $date . ' ' . $kickoff . ':00';
					$datetime_gmt = get_gmt_from_date( $datetime );

					$data['post_date']     = $datetime;
					$data['post_date_gmt'] = $datetime_gmt;

					if ( $datetime_gmt > gmdate( 'Y-m-d H:i:59' ) ) {
						$data['post_status'] = 'future';
					}
				}

			endif;

			if ( 'wpcm_player' == $data['post_type'] ) :

				$firstname = filter_input( INPUT_POST, '_wpcm_firstname', FILTER_UNSAFE_RAW );
				if ( $firstname ) {
					$first_name = sanitize_text_field( $firstname );
				} else {
					$first_name = '';
				}
				$lastname = filter_input( INPUT_POST, '_wpcm_lastname', FILTER_UNSAFE_RAW );
				if ( $lastname ) {
					$last_name = sanitize_text_field( $lastname );
				} else {
					$last_name = '';
				}

				if ( $firstname || $lastname ) {
					$title = sanitize_title_with_dashes( $first_name . '-' . $last_name );

					$data['post_title'] = $first_name . ' ' . $last_name;
					$data['post_name']  = $title;
				}

			endif;

			if ( 'wpcm_staff' == $data['post_type'] ) :

				$firstname = '';
				$first_name = filter_input( INPUT_POST, '_wpcm_firstname', FILTER_UNSAFE_RAW );
				if ( $first_name ) {
					$firstname = sanitize_text_field( $first_name );
				}
				$lastname = '';
				$last_name = filter_input( INPUT_POST, '_wpcm_lastname', FILTER_UNSAFE_RAW );
				if ( $last_name ) {
					$lastname = sanitize_text_field( $last_name );
				}

				$title = sanitize_title_with_dashes( $firstname . '-' . $lastname );

				$data['post_title'] = $firstname . ' ' . $lastname;
				$data['post_name']  = $title;

			endif;

			return $data;
		}

		/**
		 * Define custom columns for matches.
		 *
		 * @param  array $existing_columns
		 * @return array
		 */
		public function match_columns( $existing_columns ) {

			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
				$existing_columns = array();
			}

			unset( $existing_columns['title'], $existing_columns['date'], $existing_columns['comments'] );

			$columns         = array();
			$columns['cb']   = '<input type="checkbox" />';
			$columns['name'] = __( 'Fixture', 'wp-club-manager' );

			if ( is_club_mode() ) {
				$columns['team'] = __( 'Team', 'wp-club-manager' );
			}

			$columns['comp']    = __( 'Competition', 'wp-club-manager' );
			$columns['season']  = __( 'Season', 'wp-club-manager' );
			$columns['dates']   = __( 'Date', 'wp-club-manager' );
			$columns['kickoff'] = __( 'Time', 'wp-club-manager' );
			$columns['score']   = __( 'Score', 'wp-club-manager' );

			return array_merge( $columns, $existing_columns );
		}

		/**
		 * Define custom columns for clubs.
		 *
		 * @param  array $existing_columns
		 * @return array
		 */
		public function club_columns( $existing_columns ) {

			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
				$existing_columns = array();
			}

			unset( $existing_columns['title'], $existing_columns['date'], $existing_columns['comments'] );

			$columns          = array();
			$columns['cb']    = '<input type="checkbox" />';
			$columns['name']  = __( 'Club', 'wp-club-manager' );
			$columns['image'] = __( 'Badge', 'wp-club-manager' );
			$columns['abbr']  = __( 'Abbreviation', 'wp-club-manager' );
			$columns['venue'] = __( 'Venue', 'wp-club-manager' );

			return array_merge( $columns, $existing_columns );
		}

		/**
		 * Define custom columns for players.
		 *
		 * @param array $existing_columns
		 *
		 * @return array
		 */
		public function player_columns( $existing_columns ) {

			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
				$existing_columns = array();
			}

			unset( $existing_columns['title'], $existing_columns['date'], $existing_columns['comments'] );

			$columns          = array();
			$columns['cb']    = '<input type="checkbox" />';
			$columns['name']  = __( 'Name', 'wp-club-manager' );
			$columns['image'] = __( 'Image', 'wp-club-manager' );
			if ( is_league_mode() ) {
				$columns['club'] = __( 'Club', 'wp-club-manager' );
			}
			if ( get_option( 'wpcm_player_profile_show_number' ) == 'yes' ) {
				$columns['number'] = __( 'Squad No.', 'wp-club-manager' );
			}
			if ( get_option( 'wpcm_player_profile_show_nationality' ) == 'yes' ) {
				$columns['flag'] = __( 'Nationality', 'wp-club-manager' );
			}
			if ( get_option( 'wpcm_player_profile_show_age' ) == 'yes' ) {
				$columns['age'] = __( 'Age', 'wp-club-manager' );
			}
			$columns['position'] = __( 'Positions', 'wp-club-manager' );

			return array_merge( $columns, $existing_columns );
		}

		/**
		 * Define custom columns for players.
		 *
		 * @param array $existing_columns
		 *
		 * @return array
		 */
		public function staff_columns( $existing_columns ) {

			if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
				$existing_columns = array();
			}

			unset( $existing_columns['title'], $existing_columns['date'], $existing_columns['comments'] );

			$columns          = array();
			$columns['cb']    = '<input type="checkbox" />';
			$columns['name']  = __( 'Name', 'wp-club-manager' );
			$columns['image'] = __( 'Image', 'wp-club-manager' );
			if ( is_league_mode() ) {
				$columns['club'] = __( 'Club', 'wp-club-manager' );
			}
			if ( get_option( 'wpcm_staff_profile_show_nationality' ) == 'yes' ) {
				$columns['flag'] = __( 'Nationality', 'wp-club-manager' );
			}
			if ( get_option( 'wpcm_staff_profile_show_age' ) == 'yes' ) {
				$columns['age'] = __( 'Age', 'wp-club-manager' );
			}
			if ( get_option( 'wpcm_show_staff_email' ) == 'yes' ) {
				$columns['email'] = __( 'Email', 'wp-club-manager' );
			}
			if ( get_option( 'wpcm_show_staff_phone' ) == 'yes' ) {
				$columns['phone'] = __( 'Phone', 'wp-club-manager' );
			}
			$columns['jobs'] = __( 'Jobs', 'wp-club-manager' );

			return array_merge( $columns, $existing_columns );
		}

		/**
		 * Define custom columns for rosters.
		 *
		 * @param array $columns
		 *
		 * @return array
		 */
		public function roster_columns( $columns ) {

			$columns = array(
				'cb'     => '<input type="checkbox" />',
				'title'  => __( 'Title', 'wp-club-manager' ),
				'season' => __( 'Season', 'wp-club-manager' ),
			);
			if ( is_club_mode() ) {
				$columns['team'] = __( 'Team', 'wp-club-manager' );
			}
			$columns['players'] = __( 'Players', 'wp-club-manager' );
			$columns['staff']   = __( 'Staff', 'wp-club-manager' );

			return $columns;
		}

		/**
		 * Define custom columns for tables.
		 *
		 * @param array $columns
		 *
		 * @return array
		 */
		public function table_columns( $columns ) {

			$columns = array(
				'cb'     => '<input type="checkbox" />',
				'title'  => __( 'Title', 'wp-club-manager' ),
				'comp'   => __( 'Competition', 'wp-club-manager' ),
				'season' => __( 'Season', 'wp-club-manager' ),
			);
			if ( is_club_mode() ) {
				$columns['team'] = __( 'Team', 'wp-club-manager' );
			}
			$columns['clubs'] = __( 'Clubs', 'wp-club-manager' );

			return $columns;
		}

		/**
		 * Ouput custom columns for matches.
		 *
		 * @param string $column
		 */
		public function render_match_columns( $column ) {

			global $post;

			// if ( empty( $the_product ) || $the_product->id != $post->ID ) {
			// $the_product = wpcm_get_product( $post );
			// }

			switch ( $column ) {
				case 'name':
					$edit_link = get_edit_post_link( $post->ID );
					$title     = _draft_or_post_title();

					echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

					_post_states( $post );

					echo '</strong>';

					if ( $post->post_parent > 0 ) {
						echo '&nbsp;&nbsp;&larr; <a href="' . esc_url( get_edit_post_link( $post->post_parent ) ) . '">' . esc_html( get_the_title( $post->post_parent ) ) . '</a>';
					}

					// Excerpt view
					if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
						echo esc_html( apply_filters( 'the_excerpt', $post->post_excerpt ) );
					}

					// $this->_render_match_row_actions( $post, $title );

					get_inline_data( $post );

					$played = get_post_meta( $post->ID, 'wpcm_played', true );
					$score  = wpcm_get_match_result( $post->ID );
					if ( taxonomy_exists( 'wpcm_team' ) ) {
						$team = get_the_terms( $post->ID, 'wpcm_team' );
					} else {
						$team = null;
					}
					$comp = get_the_terms( $post->ID, 'wpcm_comp' );
					if ( $comp ) {
						$comp = $comp[0]->name;
					} else {
						$comp = '';
					}
					$season = get_the_terms( $post->ID, 'wpcm_season' );
					if ( $season ) {
						$season = $season[0]->slug;
					} else {
						$season = '';
					}
					// $venue = wpcm_get_match_venue( $post->ID );
					$venue = get_the_terms( $post->ID, 'wpcm_venue' );
					$venue_slug = '';
					if ( $venue && isset( $venue[0]->slug ) ) {
						$venue_slug = $venue[0]->slug;
					}
					// $home_goals = get_post_meta( $post->ID, 'wpcm_home_goals', true );
					// $away_goals = get_post_meta( $post->ID, 'wpcm_away_goals', true );
					$referee    = get_post_meta( $post->ID, 'wpcm_referee', true );
					$attendance = get_post_meta( $post->ID, 'wpcm_attendance', true );
					$friendly   = get_post_meta( $post->ID, 'wpcm_friendly', true );
					$goals      = array_merge( array(
						'total' => array(
							'home' => 0,
							'away' => 0,
						),
					), (array) unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) ) );
					/* Custom inline data for wpclubmanager. */
					echo '
					<div class="hidden" id="wpclubmanager_inline_' . esc_attr( $post->ID ) . '">
						' . ( $team ? '<div class="team">' . esc_html( $team[0]->slug ) . '</div>' : '' ) . '
						<div class="comp">' . esc_html( $comp ) . '</div>
						<div class="season">' . esc_html( $season ) . '</div>
						<div class="venue">' . esc_html( $venue_slug ) . '</div>
						<div class="played">' . esc_html( $played ) . '</div>
						<div class="score">' . esc_html( $score[0] ) . '</div>
						<div class="home-goals">' . esc_html( $goals['total']['home'] ) . '</div>
						<div class="away-goals">' . esc_html( $goals['total']['away'] ) . '</div>
						<div class="referee">' . esc_html( $referee ) . '</div>
						<div class="attenance">' . esc_html( $attendance ) . '</div>
						<div class="friendly">' . esc_html( $friendly ) . '</div>
					</div>
				';

					break;
				case 'team':
					if ( taxonomy_exists( 'wpcm_team' ) ) {
						$terms = get_the_terms( $post->ID, 'wpcm_team' );
						if ( $terms ) {
							foreach ( $terms as $term ) {
								$teams[] = $term->name;
							}
							$output = join( ', ', $teams );
						} else {
							$output = '';
						}
						echo esc_html( $output );
					}
					break;
				case 'comp':
					$comp = get_the_terms( $post->ID, 'wpcm_comp' );
					if ( $comp ) {
						$comp = $comp[0]->name;
					} else {
						$comp = '';
					}
					echo esc_html( $comp );
					break;
				case 'season':
					$season = get_the_terms( $post->ID, 'wpcm_season' );
					if ( $season ) {
						$season = $season[0]->slug;
					} else {
						$season = '';
					}
					echo esc_html( $season );
					break;
				case 'dates':
					if ( get_post_status( $post->ID ) == 'future' ) {
						$date = __( 'Scheduled', 'wp-club-manager' );
					} elseif ( get_post_status( $post->ID ) == 'publish' ) {
						$played    = get_post_meta( $post->ID, 'wpcm_played', true );
						$postponed = get_post_meta( $post->ID, '_wpcm_postponed', true );
						if ( empty( $played ) ) {
							$date = '<span class="red">' . __( 'Awaiting result', 'wp-club-manager' ) . '</span>';
						} elseif ( $postponed ) {
								$date = '<span>' . __( 'Postponed', 'wp-club-manager' ) . '</span>';
						} else {
							$date = '<span class="green">' . __( 'Played', 'wp-club-manager' ) . '</span>';
						}
					} else {
						$date = ucfirst( get_post_status( $post->ID ) );
					}
					echo wp_kses_post( $date );
					?>
				<br>
				<abbr title="<?php echo get_the_date( 'Y/m/d' ) . ' ' . esc_html( get_the_time( 'H:i:s' ) ); ?>"><?php echo get_the_date( get_option( 'date_format' ) ); ?></abbr>
					<?php
					break;
				case 'kickoff':
					echo esc_html( get_the_time( get_option( 'time_format' ) ) );
					break;
				case 'score':
					$score = wpcm_get_match_result( $post->ID );
					echo esc_html( $score[0] );
					break;
				default:
					break;
			}
		}

		/**
		 * Ouput custom columns for clubs.
		 *
		 * @param string $column
		 */
		public function render_club_columns( $column ) {

			global $post;

			$defaults = get_club_details( $post );

			switch ( $column ) {
				case 'image':
					echo esc_url( $defaults['badge'] );
					break;
				case 'name':
					$edit_link    = get_edit_post_link( $post->ID );
					$title        = _draft_or_post_title();
					$default_club = get_default_club();

					echo '<strong>' . ( $post->ID == $default_club ? '<span class="list-table-club-default">' . esc_html__( 'Default', 'wp-club-manager' ) . '</span>' : '' ) . '<a class="row-title" href="' . esc_url( $edit_link ) . '">' . ( $post->post_parent > 0 ? '&mdash;' : '' ) . ' ' . esc_html( $title ) . '</a>';

					_post_states( $post );

					echo '</strong>';

					// Excerpt view
					if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
						echo esc_html( apply_filters( 'the_excerpt', $post->post_excerpt ) );
					}

					get_inline_data( $post );

					$venue = get_the_terms( $post->ID, 'wpcm_venue' );
					if ( $venue ) {
						$venue = $venue[0]->slug;
					} else {
						$venue = '';
					}
					/* Custom inline data for wpclubmanager. */
					echo '
					<div class="hidden" id="wpclubmanager_inline_' . esc_attr( $post->ID ) . '">
						<div class="venue">' . esc_html( $venue ) . '</div>
					</div>
				';

					break;
				case 'abbr':
					$abbr = get_club_abbreviation( $post->ID );
					echo esc_html( $abbr );
					break;
				case 'venue':
					if ( false == $defaults['venue'] ) {
						echo '';
					} else {
						echo esc_html( $defaults['venue']['name'] );
					}
					break;
			}
		}

		/**
		 * Ouput custom columns for players.
		 *
		 * @param string $column
		 */
		public function render_player_columns( $column ) {

			global $post;

			switch ( $column ) {
				case 'number':
					$number = get_post_meta( $post->ID, 'wpcm_number', true );
					echo esc_html( $number );
					break;
				case 'image':
					echo get_the_post_thumbnail( $post->ID, 'player_thumbnail' );
					break;
				case 'name':
					$edit_link = get_edit_post_link( $post->ID );
					$title     = _draft_or_post_title();

					echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

					_post_states( $post );

					echo '</strong>';

					if ( $post->post_parent > 0 ) {
						echo '&nbsp;&nbsp;&larr; <a href="' . esc_url( get_edit_post_link( $post->post_parent ) ) . '">' . esc_html( get_the_title( $post->post_parent ) ) . '</a>';
					}

					// Excerpt view
					if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
						echo esc_html( apply_filters( 'the_excerpt', $post->post_excerpt ) );
					}

					// $this->_render_match_row_actions( $post, $title );

					get_inline_data( $post );

					$fname = get_post_meta( $post->ID, '_wpcm_firstname', true );
					$lname = get_post_meta( $post->ID, '_wpcm_lastname', true );
					if ( is_league_mode() ) {
						$player_club = get_post_meta( $post->ID, '_wpcm_player_club', true );
					}

					// $positions = get_the_terms($post->ID, 'wpcm_position');
					// if( $positions ) {
					// foreach( $positions as $term ) {
					// $positions = $term->slug;
					// }
					// var_dump($positions);
					// $position = $positions;
					// } else {
					// $position = '';
					// }

					/* Custom inline data for wpclubmanager. */
					echo '
					<div class="hidden" id="wpclubmanager_inline_' . esc_attr( $post->ID ) . '">
						<div class="fname">' . esc_html( $fname ) . '</div>
						<div class="lname">' . esc_html( $lname ) . '</div>
						' . ( is_league_mode() ? '<div class="player_club">' . esc_html( $player_club ) . '</div>' : '' ) . '
					</div>
				';

					break;
				case 'position':
					$terms = get_the_terms( $post->ID, 'wpcm_position' );
					if ( $terms ) {
						foreach ( $terms as $term ) {
							$positions[] = $term->name;
						}
						$output = join( ', ', $positions );
						echo esc_html( $output );
					}
					break;
				case 'club':
					$club = get_post_meta( $post->ID, '_wpcm_player_club', true );
					echo esc_html( get_the_title( $club ) );
					break;
				case 'flag':
					$nationality = get_post_meta( $post->ID, 'wpcm_natl', true );
					echo '<img class="flag" src="' . esc_url( WPCM_URL . 'assets/images/flags/' . $nationality ) . '.png" />';
					break;
				case 'age':
					$dob = get_post_meta( $post->ID, 'wpcm_dob', true );
					if ( empty( $dob ) ) {
						echo '';
					} else {
						echo esc_html( get_age( $dob ) );
					}
					break;
			}
		}

		/**
		 * Ouput custom columns for staff.
		 *
		 * @param string $column
		 */
		public function render_staff_columns( $column ) {

			global $post;

			switch ( $column ) {
				case 'image':
					echo get_the_post_thumbnail( $post->ID, 'player_thumbnail' );
					break;
				case 'name':
					$edit_link = get_edit_post_link( $post->ID );
					$title     = _draft_or_post_title();

					echo '<strong><a class="row-title" href="' . esc_url( $edit_link ) . '">' . esc_html( $title ) . '</a>';

					_post_states( $post );

					echo '</strong>';

					if ( $post->post_parent > 0 ) {
						echo '&nbsp;&nbsp;&larr; <a href="' . esc_url( get_edit_post_link( $post->post_parent ) ) . '">' . esc_html( get_the_title( $post->post_parent ) ) . '</a>';
					}

					// Excerpt view
					if ( isset( $_GET['mode'] ) && 'excerpt' == $_GET['mode'] ) {
						echo esc_html( apply_filters( 'the_excerpt', $post->post_excerpt ) );
					}

					// $this->_render_match_row_actions( $post, $title );

					get_inline_data( $post );

					$fname = get_post_meta( $post->ID, '_wpcm_firstname', true );
					$lname = get_post_meta( $post->ID, '_wpcm_lastname', true );
					if ( is_league_mode() ) {
						$staff_club = get_post_meta( $post->ID, '_wpcm_staff_club', true );
					}

					// $jobs = get_the_terms($post->ID, 'wpcm_jobs');
					// if( $jobs ) {
					// foreach( $jobs as $term ) {
					// $jobs[] = $term->slug;
					// }
					// $job = join( ",", $jobs );
					// } else {
					// $job = '';
					// }

					/* Custom inline data for wpclubmanager. */
					echo '
					<div class="hidden" id="wpclubmanager_inline_' . esc_attr( $post->ID ) . '">
						<div class="fname">' . esc_html( $fname ) . '</div>
						<div class="lname">' . esc_html( $lname ) . '</div>
						' . ( is_league_mode() ? '<div class="staff_club">' . esc_html( $staff_club ) . '</div>' : '' ) . '
					</div>
				';

					break;
				case 'jobs':
					$terms = get_the_terms( $post->ID, 'wpcm_jobs' );
					if ( $terms ) {
						foreach ( $terms as $term ) {
							$jobs[] = $term->name;
						}
						$output = join( ', ', $jobs );
						echo esc_html( $output );
					}
					break;
				case 'email':
					$email = get_post_meta( $post->ID, '_wpcm_staff_email', true );
					echo '<a href="mailto:' . esc_html( $email ) . '">' . esc_html( $email ) . '</a>';
					break;
				case 'phone':
					$phone = get_post_meta( $post->ID, '_wpcm_staff_phone', true );
					echo esc_html( $phone );
					break;
				case 'club':
					$club = get_post_meta( $post->ID, '_wpcm_staff_club', true );
					echo esc_html( get_the_title( $club ) );
					break;
				case 'flag':
					$nationality = get_post_meta( $post->ID, 'wpcm_natl', true );
					echo '<img class="flag" src="' . esc_url( WPCM_URL . 'assets/images/flags/' . $nationality ) . '.png" />';
					break;
				case 'age':
					$dob = get_post_meta( $post->ID, 'wpcm_dob', true );
					echo esc_html( get_age( $dob ) );
					break;
			}
		}

		/**
		 * Ouput custom columns for rosters.
		 *
		 * @param string $column
		 */
		public function render_roster_columns( $column ) {

			global $post;

			switch ( $column ) {
				case 'season':
					$seasons = get_the_terms( $post->ID, 'wpcm_season' );
					if ( is_array( $seasons ) ) {
						$season = $seasons[0]->name;
					} else {
						$season = null;
					}
					echo esc_html( $season );
					break;
				case 'team':
					$teams = get_the_terms( $post->ID, 'wpcm_team' );
					if ( is_array( $teams ) ) {
						$team = $teams[0]->name;
					} else {
						$team = null;
					}
					echo esc_html( $team );
					break;
				case 'players':
					$players = unserialize( get_post_meta( $post->ID, '_wpcm_roster_players', true ) );
					echo ( $players ? count( $players ) : '0' );
					break;
				case 'staff':
					$staff = unserialize( get_post_meta( $post->ID, '_wpcm_roster_staff', true ) );
					echo ( $staff ? count( $staff ) : '0' );
					break;
			}
		}

		/**
		 * Ouput custom columns for rosters.
		 *
		 * @param string $column
		 */
		public function render_table_columns( $column ) {

			global $post;

			switch ( $column ) {
				case 'season':
					$seasons = get_the_terms( $post->ID, 'wpcm_season' );
					if ( is_array( $seasons ) ) {
						$season = $seasons[0]->name;
					} else {
						$season = null;
					}
					echo esc_html( $season );
					break;
				case 'comp':
					$comps = get_the_terms( $post->ID, 'wpcm_comp' );
					if ( is_array( $comps ) ) {
						$comp = $comps[0]->name;
					} else {
						$comp = null;
					}
					echo esc_html( $comp );
					break;
				case 'team':
					$teams = get_the_terms( $post->ID, 'wpcm_team' );
					if ( is_array( $teams ) ) {
						$team = $teams[0]->name;
					} else {
						$team = null;
					}
					echo esc_html( $team );
					break;
				case 'clubs':
					$clubs = unserialize( get_post_meta( $post->ID, '_wpcm_table_clubs', true ) );
					echo count( $clubs );
					break;
			}
		}

		/**
		 * Make columns sortable - https://gist.github.com/906872.
		 *
		 * @param  array $columns
		 * @return array
		 */
		public function match_sortable_columns( $columns ) {
			$custom = array(
				'dates' => 'date',
			);
			return wp_parse_args( $custom, $columns );
		}

		/**
		 * Remove edit from the bulk actions.
		 *
		 * @param array $actions
		 * @return array
		 */
		public function wpcm_match_bulk_actions( $actions ) {

			if ( isset( $actions['edit'] ) ) {
				unset( $actions['edit'] );
			}

			return $actions;
		}

		/**
		 * Set list table primary column for post types.
		 * Support for WordPress 4.3.
		 *
		 * @param  string $default
		 * @param  string $screen_id
		 *
		 * @return string
		 */
		public function list_table_primary_column( $default, $screen_id ) {

			if ( 'edit-wpcm_match' === $screen_id ) {
				return 'name';
			}
			if ( 'edit-wpcm_club' === $screen_id ) {
				return 'name';
			}
			if ( 'edit-wpcm_player' === $screen_id ) {
				return 'name';
			}
			if ( 'edit-wpcm_staff' === $screen_id ) {
				return 'name';
			}

			return $default;
		}

		/**
		 * Set row actions for products and orders.
		 *
		 * @param  array   $actions
		 * @param  WP_Post $post
		 *
		 * @return array
		 */
		public function row_actions( $actions, $post ) {

			if ( null !== $post->post_type && in_array( $post->post_type, array( 'wpcm_match', 'wpcm_club', 'wpcm_player', 'wpcm_staff', 'wpcm_roster', 'wpcm_table', 'wpcm_sponsor' ) ) ) {
				return array_merge( array( 'id' => 'ID: ' . $post->ID ), $actions );
			}

			return $actions;
		}

		/**
		 * Custom quick edit - form.
		 *
		 * @param mixed $column_name
		 * @param mixed $post_type
		 */
		public function quick_edit( $column_name, $post_type ) {

			if ( 'name' != $column_name ) {
				return;
			}

			if ( 'wpcm_match' == $post_type ) {
				$teams   = get_terms( array(
					'taxonomy'   => 'wpcm_team',
					'hide_empty' => false,
				) );
				$seasons = get_terms( array(
					'taxonomy'   => 'wpcm_season',
					'hide_empty' => false,
				) );
				$comps   = get_terms( array(
					'taxonomy'   => 'wpcm_comp',
					'hide_empty' => false,
				) );
				$venues  = get_terms( array(
					'taxonomy'   => 'wpcm_venue',
					'hide_empty' => false,
				) );

				include WPCM()->plugin_path() . '/includes/admin/views/html-quick-edit-match.php';
			} elseif ( 'wpcm_club' == $post_type ) {

				include WPCM()->plugin_path() . '/includes/admin/views/html-quick-edit-club.php';
			} elseif ( 'wpcm_player' == $post_type ) {

				$positions = get_terms( array(
					'taxonomy'   => 'wpcm_position',
					'hide_empty' => false,
				) );
				$clubs     = get_pages( array( 'post_type' => 'wpcm_club' ) );

				include WPCM()->plugin_path() . '/includes/admin/views/html-quick-edit-player.php';
			} elseif ( 'wpcm_staff' == $post_type ) {

				$jobs  = get_terms( array(
					'taxonomy' => 'wpcm_jobs',
					'hide_empty' => false,
				) );
				$clubs = get_pages( array( 'post_type' => 'wpcm_club' ) );

				include WPCM()->plugin_path() . '/includes/admin/views/html-quick-edit-staff.php';
			}
		}

		/**
		 * Quick and bulk edit saving.
		 *
		 * @param int     $post_id
		 * @param WP_Post $post
		 * @return int
		 */
		public function quick_edit_save_post( $post_id, $post ) {

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			// Don't save revisions and autosaves
			if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
				return $post_id;
			}

			$post_types = array( 'wpcm_match', 'wpcm_club', 'wpcm_player', 'wpcm_staff' );
			// Check post type is match
			if ( ! in_array( $post->post_type, $post_types ) ) {
				return $post_id;
			}

			// Check user permission
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}

			// Check nonces
			$nonce = filter_input( INPUT_POST, 'wpclubmanager_quick_edit_nonce', FILTER_UNSAFE_RAW );
			if ( ! $nonce ) {
				return $post_id;
			}
			if ( $nonce && ! wp_verify_nonce( $nonce, 'wpclubmanager_quick_edit_nonce' ) ) {
				return $post_id;
			}

			// Get the post and save
			$post = get_post( $post );

			$edit = filter_input( INPUT_POST, 'wpclubmanager_quick_edit', FILTER_UNSAFE_RAW );
			if ( $edit ) {
				$this->quick_edit_save( $post_id, $post );
			}

			// Clear transient
			do_action( 'delete_plugin_transients' );

			return $post_id;
		}

		/**
		 * Quick edit.
		 *
		 * @param int     $post_id
		 * @param WP_Post $post
		 */
		private function quick_edit_save( $post_id, $post ) {
			global $wpdb;

			if ( 'wpcm_match' === $post->post_type ) {
				// Save fields
				$team = filter_input( INPUT_POST, 'wpcm_team', FILTER_UNSAFE_RAW );
				if ( $team ) {
					$team = wpcm_clean( $team );
					wp_set_object_terms( $post_id, $team, 'wpcm_team' );
				}

				$comp = filter_input( INPUT_POST, 'wpcm_comp', FILTER_UNSAFE_RAW );
				if ( $comp ) {
					$comp = wpcm_clean( $comp );
					wp_set_object_terms( $post_id, $comp, 'wpcm_comp' );
				}

				$season = filter_input( INPUT_POST, 'wpcm_season', FILTER_UNSAFE_RAW );
				if ( $season ) {
					$season = wpcm_clean( $season );
					wp_set_object_terms( $post_id, $season, 'wpcm_season' );
				}

				$venue = filter_input( INPUT_POST, 'wpcm_venue', FILTER_UNSAFE_RAW );
				if ( $venue ) {
					$venue = wpcm_clean( $venue );
					wp_set_object_terms( $post_id, $venue, 'wpcm_venue' );
				}

				$referee = filter_input( INPUT_POST, 'wpcm_referee', FILTER_UNSAFE_RAW );
				if ( $referee ) {
					$referee = wpcm_clean( $referee );
					update_post_meta( $post_id, 'wpcm_referee', $referee );
					$options = get_option( 'wpcm_referee_list', array() );
					if ( ! in_array( $referee, $options ) ) {
						$options[] = $referee;
						update_option( 'wpcm_referee_list', $options );
					}
				}

				$attendance = filter_input( INPUT_POST, 'wpcm_attendance', FILTER_UNSAFE_RAW );
				if ( $attendance ) {
					update_post_meta( $post_id, 'wpcm_attendance', wpcm_clean( $attendance ) );
				}

				$friendly = filter_input( INPUT_POST, 'wpcm_friendly', FILTER_UNSAFE_RAW );
				if ( $friendly ) {
					update_post_meta( $post_id, 'wpcm_friendly', wpcm_clean( $friendly ) );
				} else {
					update_post_meta( $post_id, 'wpcm_friendly', '' );
				}

				$played = filter_input( INPUT_POST, 'wpcm_played', FILTER_UNSAFE_RAW );
				if ( $played ) {
					update_post_meta( $post_id, 'wpcm_played', wpcm_clean( $played ) );
				}

				$goals = filter_input( INPUT_POST, 'wpcm_goals', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
				if ( $goals ) {
					update_post_meta( $post_id, 'wpcm_goals', serialize( $goals ) );
					update_post_meta( $post_id, 'wpcm_home_goals', $goals['total']['home'] );
					update_post_meta( $post_id, 'wpcm_away_goals', $goals['total']['away'] );
				}
			}

			if ( 'wpcm_player' === $post->post_type ) {

				$firstname = filter_input( INPUT_POST, '_wpcm_firstname', FILTER_UNSAFE_RAW );
				if ( $firstname ) {
					update_post_meta( $post_id, '_wpcm_firstname', wpcm_clean( $firstname ) );
				}

				$lastname = filter_input( INPUT_POST, '_wpcm_lastname', FILTER_UNSAFE_RAW );
				if ( $lastname ) {
					update_post_meta( $post_id, '_wpcm_lastname', wpcm_clean( $lastname ) );
				}

				$club = filter_input( INPUT_POST, '_wpcm_player_club', FILTER_UNSAFE_RAW );
				if ( $club ) {
					update_post_meta( $post_id, '_wpcm_player_club', wpcm_clean( $club ) );
				}
			}

			do_action( 'wpclubmanager_quick_edit_save', $post );
		}

		/**
		 * Taxonomy filter dropdowns
		 *
		 * @return void
		 */
		public function request_filter_dropdowns() {

			global $typenow, $wp_query;

			if ( 'wpcm_match' === $typenow ) {
				// comp dropdown
				$selected = filter_input( INPUT_GET, 'wpcm_comp', FILTER_UNSAFE_RAW );
				$selected = $selected ? sanitize_text_field( $selected ) : null;
				$args     = array(
					'show_option_all' => __( 'Show all competitions', 'wp-club-manager' ),
					'taxonomy'        => 'wpcm_comp',
					'name'            => 'wpcm_comp',
					'selected'        => $selected,
				);
				wpcm_dropdown_taxonomies( $args );
				echo PHP_EOL;
				// season dropdown
				$selected = filter_input( INPUT_GET, 'wpcm_season', FILTER_UNSAFE_RAW );
				$selected = $selected ? sanitize_text_field( $selected ) : null;
				$args = array(
					'show_option_all' => __( 'Show all seasons', 'wp-club-manager' ),
					'taxonomy'        => 'wpcm_season',
					'name'            => 'wpcm_season',
					'selected'        => $selected,
				);
				wpcm_dropdown_taxonomies( $args );
				echo PHP_EOL;
				// team dropdown
				if ( is_club_mode() ) {
					$selected = filter_input( INPUT_GET, 'wpcm_team', FILTER_UNSAFE_RAW );
					$selected = $selected ? sanitize_text_field( $selected ) : null;
					$args     = array(
						'show_option_all' => __( 'Show all teams', 'wp-club-manager' ),
						'taxonomy'        => 'wpcm_team',
						'name'            => 'wpcm_team',
						'selected'        => $selected,
					);
					wpcm_dropdown_taxonomies( $args );
					echo PHP_EOL;
				}
			}

			if ( 'wpcm_club' === $typenow ) {
				// comp dropdown
				$selected = filter_input( INPUT_GET, 'wpcm_comp', FILTER_UNSAFE_RAW );
				$selected = $selected ? sanitize_text_field( $selected ) : null;
				$args     = array(
					'show_option_all' => __( 'Show all competitions', 'wp-club-manager' ),
					'taxonomy'        => 'wpcm_comp',
					'name'            => 'wpcm_comp',
					'selected'        => $selected,
				);
				wpcm_dropdown_taxonomies( $args );
				echo PHP_EOL;
				// season dropdown
				$selected = filter_input( INPUT_GET, 'wpcm_season', FILTER_UNSAFE_RAW );
				$selected = $selected ? sanitize_text_field( $selected ) : null;
				$args     = array(
					'show_option_all' => __( 'Show all seasons', 'wp-club-manager' ),
					'taxonomy'        => 'wpcm_season',
					'name'            => 'wpcm_season',
					'selected'        => $selected,
				);
				wpcm_dropdown_taxonomies( $args );
			}

			if ( 'wpcm_player' === $typenow ) {
				// position dropdown
				$selected = filter_input( INPUT_GET, 'wpcm_position', FILTER_UNSAFE_RAW );
				$selected = $selected ? sanitize_text_field( $selected ) : null;
				$args     = array(
					'show_option_all' => __( 'Show all positions', 'wp-club-manager' ),
					'taxonomy'        => 'wpcm_position',
					'name'            => 'wpcm_position',
					'selected'        => $selected,
				);
				wpcm_dropdown_taxonomies( $args );
				echo PHP_EOL;
				// team dropdown
				if ( is_club_mode() ) {
					$selected = filter_input( INPUT_GET, 'wpcm_team', FILTER_UNSAFE_RAW );
					$selected = $selected ? sanitize_text_field( $selected ) : null;
					$args     = array(
						'show_option_all' => __( 'Show all teams', 'wp-club-manager' ),
						'taxonomy'        => 'wpcm_team',
						'name'            => 'wpcm_team',
						'selected'        => $selected,
					);

					wpcm_dropdown_taxonomies( $args );
					echo PHP_EOL;
				}
				// season dropdown
				$selected = filter_input( INPUT_GET, 'wpcm_season', FILTER_UNSAFE_RAW );
				$selected = $selected ? sanitize_text_field( $selected ) : null;
				$args     = array(
					'show_option_all' => __( 'Show all seasons', 'wp-club-manager' ),
					'taxonomy'        => 'wpcm_season',
					'name'            => 'wpcm_season',
					'selected'        => $selected,
				);
				wpcm_dropdown_taxonomies( $args );
			}

			if ( 'wpcm_roster' === $typenow ) {
				// season dropdown
				$selected = filter_input( INPUT_GET, 'wpcm_season', FILTER_UNSAFE_RAW );
				$selected = $selected ? sanitize_text_field( $selected ) : null;
				$args     = array(
					'show_option_all' => __( 'Show all seasons', 'wp-club-manager' ),
					'taxonomy'        => 'wpcm_season',
					'name'            => 'wpcm_season',
					'selected'        => $selected,
				);
				wpcm_dropdown_taxonomies( $args );
				echo PHP_EOL;
				// team dropdown
				$selected = filter_input( INPUT_GET, 'wpcm_team', FILTER_UNSAFE_RAW );
				$selected = $selected ? sanitize_text_field( $selected ) : null;
				$args     = array(
					'show_option_all' => __( 'Show all teams', 'wp-club-manager' ),
					'taxonomy'        => 'wpcm_team',
					'name'            => 'wpcm_team',
					'selected'        => $selected,
				);
				wpcm_dropdown_taxonomies( $args );
				echo PHP_EOL;
			}

			if ( 'wpcm_table' === $typenow ) {
				// comp dropdown
				$comp = filter_input( INPUT_GET, 'wpcm_comp', FILTER_UNSAFE_RAW );
				$selected = $comp ? sanitize_text_field( $comp ) : null;
				$args     = array(
					'show_option_all' => __( 'Show all competitions', 'wp-club-manager' ),
					'taxonomy'        => 'wpcm_comp',
					'name'            => 'wpcm_comp',
					'selected'        => $selected,
				);
				wpcm_dropdown_taxonomies( $args );
				echo PHP_EOL;
				// season dropdown
				$season = filter_input( INPUT_GET, 'wpcm_season', FILTER_UNSAFE_RAW );
				$selected = $season ? sanitize_text_field( $season ) : null;
				$args     = array(
					'show_option_all' => __( 'Show all seasons', 'wp-club-manager' ),
					'taxonomy'        => 'wpcm_season',
					'name'            => 'wpcm_season',
					'selected'        => $selected,
				);
				wpcm_dropdown_taxonomies( $args );
				echo PHP_EOL;
				// team dropdown
				if ( is_club_mode() ) {
					$selected = filter_input( INPUT_GET, 'wpcm_team', FILTER_UNSAFE_RAW );
					$selected = $selected ? sanitize_text_field( $selected ) : null;
					$args     = array(
						'show_option_all' => __( 'Show all teams', 'wp-club-manager' ),
						'taxonomy'        => 'wpcm_team',
						'name'            => 'wpcm_team',
						'selected'        => $selected,
					);
					wpcm_dropdown_taxonomies( $args );
					echo PHP_EOL;
				}
			}
		}

		/**
		 * Print title styles.
		 *
		 * @param WP_Post $post
		 */
		public function title_styles( $post ) {

			global $post;

			$screen = get_current_screen();

			if ( null !== $screen && in_array( $screen->id, array( 'wpcm_match', 'edit-wpcm_match', 'wpcm_player', 'edit-wpcm_player', 'edit-wpcm_staff' ) ) ) {
				if ( null != $post ) {
					if ( 'wpcm_match' === $post->post_type ) {
						?>
					<style type="text/css">#wpclubmanager-match-fixture{margin-top:-20px;}.misc-pub-curtime{display:none;}#titlediv{display:none;}</style>
						<?php
					} elseif ( 'wpcm_player' === $post->post_type && 'publish' !== $post->post_status ) {
						?>
					<style type="text/css">#titlediv{display:none;}#wpclubmanager-player-details{margin-top:-20px;}</style>
						<?php
					} elseif ( 'wpcm_player' === $post->post_type && 'publish' === $post->post_status ) {
						?>
					<style type="text/css">#titlewrap{display:none;}</style>
						<?php
					} elseif ( 'wpcm_staff' === $post->post_type && 'publish' !== $post->post_status ) {
						?>
					<style type="text/css">#titlediv{display:none;}#wpclubmanager-staff-details{margin-top:-20px;}</style>
						<?php
					} elseif ( 'wpcm_staff' === $post->post_type && 'publish' === $post->post_status ) {
						?>
					<style type="text/css">#titlewrap{display:none;}</style>
						<?php
					}
				}
			}
		}

		/**
		 * Change title boxes in admin.
		 *
		 * @param  string $text
		 * @param  object $post
		 * @return string
		 */
		public function enter_title_here( $text, $post ) {

			switch ( $post->post_type ) {
				case 'wpcm_club':
					$text = __( 'Enter club name', 'wp-club-manager' );
					break;
				case 'wpcm_roster':
					$text = __( 'Enter roster name', 'wp-club-manager' );
					break;
				case 'wpcm-table':
					$text = __( 'Enter table name', 'wp-club-manager' );
					break;
				case 'wpcm-sponsor':
					$text = __( 'Enter sponsor name', 'wp-club-manager' );
					break;
			}

			return $text;
		}

		/**
		 * Custom HTML for post thumbnail metabox
		 *
		 * @param string $content
		 *
		 * @return string
		 */
		public function custom_admin_post_thumbnail_html( $content ) {

			global $current_screen;

			if ( 'wpcm_club' == $current_screen->post_type ) {
				$content = str_replace( __( 'Set featured image' ), __( 'Set club badge', 'wp-club-manager' ), $content );
				$content = str_replace( __( 'Remove featured image' ), __( 'Remove club badge', 'wp-club-manager' ), $content );
			} elseif ( 'wpcm_player' == $current_screen->post_type ) {
				$content = str_replace( __( 'Set featured image' ), __( 'Set player image', 'wp-club-manager' ), $content );
				$content = str_replace( __( 'Remove featured image' ), __( 'Remove player image', 'wp-club-manager' ), $content );
			} elseif ( 'wpcm_staff' == $current_screen->post_type ) {
				$content = str_replace( __( 'Set featured image' ), __( 'Set staff image', 'wp-club-manager' ), $content );
				$content = str_replace( __( 'Remove featured image' ), __( 'Remove staff image', 'wp-club-manager' ), $content );
			} elseif ( 'wpcm_sponsor' == $current_screen->post_type ) {
				$content = str_replace( __( 'Set featured image' ), __( 'Set sponsor logo', 'wp-club-manager' ), $content );
				$content = str_replace( __( 'Remove featured image' ), __( 'Remove sponsor logo', 'wp-club-manager' ), $content );
			}

			return $content;
		}

		/**
		 * Change "Featured Image" to "Club Badge" throughout media modals.
		 *
		 * @param  array  $strings Array of strings to translate.
		 * @param  object $post
		 * @return array
		 */
		public function media_view_strings( $strings = array(), $post = null ) {

			if ( is_object( $post ) ) {
				if ( 'wpcm_club' == $post->post_type ) {
					$strings['setFeaturedImageTitle'] = __( 'Set club badge', 'wp-club-manager' );
					$strings['setFeaturedImage']      = __( 'Set club badge', 'wp-club-manager' );
				} elseif ( 'wpcm_player' == $post->post_type ) {
					$strings['setFeaturedImageTitle'] = __( 'Set player image', 'wp-club-manager' );
					$strings['setFeaturedImage']      = __( 'Set player image', 'wp-club-manager' );
				} elseif ( 'wpcm_staff' == $post->post_type ) {
					$strings['setFeaturedImageTitle'] = __( 'Set staff image', 'wp-club-manager' );
					$strings['setFeaturedImage']      = __( 'Set staff image', 'wp-club-manager' );
				} elseif ( 'wpcm_sponsor' == $post->post_type ) {
					$strings['setFeaturedImageTitle'] = __( 'Set sponsor logo', 'wp-club-manager' );
					$strings['setFeaturedImage']      = __( 'Set sponsor logo', 'wp-club-manager' );
				}
			}

			return $strings;
		}

		/**
		 * Replace strings for date of post
		 *
		 * @param string $translated_text
		 * @param string $text
		 * @param string $domain
		 *
		 * @return mixed|string|null
		 */
		public function text_replace( $translated_text, $text, $domain ) {

			global $typenow;

			if ( is_admin() && 'wpcm_player' == $typenow ) {
				if ( 'Scheduled for: <b>%1$s</b>' == $translated_text ) {
					/* translators: 1: joined date */
					$translated_text = __( 'Joins on: <b>%1$s</b>', 'wp-club-manager' );
				} elseif ( 'Published on: <b>%1$s</b>' == $translated_text ) {
					/* translators: 1: joined date */
					$translated_text = __( 'Joined on: <b>%1$s</b>', 'wp-club-manager' );
				} elseif ( 'Publish <b>immediately</b>' == $translated_text ) {
					/* translators: 1: joined date */
					$translated_text = __( 'Joined on: <b>%1$s</b>', 'wp-club-manager' );
				}
			} elseif ( is_admin() && 'wpcm_staff' == $typenow ) {
				if ( 'Scheduled for: <b>%1$s</b>' == $translated_text ) {
					/* translators: 1: joined date */
					$translated_text = __( 'Joins on: <b>%1$s</b>', 'wp-club-manager' );
				} elseif ( 'Published on: <b>%1$s</b>' == $translated_text ) {
					/* translators: 1: joined date */
					$translated_text = __( 'Joined on: <b>%1$s</b>', 'wp-club-manager' );
				} elseif ( 'Publish <b>immediately</b>' == $translated_text ) {
					/* translators: 1: joined date */
					$translated_text = __( 'Joined on: <b>%1$s</b>', 'wp-club-manager' );
				}
			}

			return $translated_text;
		}

		/**
		 * Removes products, orders, and coupons from the list of post types that support "View Mode" switching.
		 * View mode is seen on posts where you can switch between list or excerpt. Our post types don't support
		 * it, so we want to hide the useless UI from the screen options tab.
		 *
		 * @since 2.6
		 * @param  array $post_types Array of post types supporting view mode
		 * @return array             Array of post types supporting view mode, without products, orders, and coupons
		 */
		public function disable_view_mode_options( $post_types ) {
			unset( $post_types['wpcm_match'], $post_types['wpcm_player'], $post_types['wpcm_staff'], $post_types['wpcm_club'], $post_types['wpcm_table'], $post_types['wpcm_roster'] );
			return $post_types;
		}
	}

endif;

new WPCM_Admin_Post_Types();
