<?php
/**
 * Birthdays Widget
 *
 * @author      ClubPress
 * @category    Widgets
 * @package     WPClubManager/Widgets
 * @version     2.1.4
 * @extends     WPCM_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Birthdays_Widget
 */
class WPCM_Birthdays_Widget extends WPCM_Widget {

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass    = 'wpcm-widget widget-birthdays';
		$this->widget_description = __( 'Display upcoming player and staff birthdays.', 'wp-club-manager' );
		$this->widget_id          = 'wpcm_birthdays';
		$this->widget_name        = __( 'WPCM Birthdays', 'wp-club-manager' );
		$this->settings           = array(
			'title'         => array(
				'type'  => 'text',
				'std'   => __( 'Upcoming Birthdays', 'wp-club-manager' ),
				'label' => __( 'Title', 'wp-club-manager' ),
			),
			'id'            => array(
				'type'             => 'posts_select',
				'post_type'        => 'wpcm_roster',
				'show_option_none' => false,
				'std'              => null,
				'orderby'          => 'post_id',
				'order'            => 'ASC',
				'limit'            => -1,
				'label'            => __( 'Choose Roster', 'wp-club-manager' ),
			),
			'include_staff' => array(
				'type'  => 'checkbox',
				'std'   => '1',
				'label' => __( 'Include Staff', 'wp-club-manager' ),
			),
			'date'          => array(
				'type'    => 'select',
				'std'     => '+4 weeks',
				'label'   => __( 'Date Range', 'wp-club-manager' ),
				'options' => array(
					'today'    => __( 'Today', 'wp-club-manager' ),
					'+1 week'  => __( '1 Week', 'wp-club-manager' ),
					'+2 weeks' => __( '2 Weeks', 'wp-club-manager' ),
					'+4 weeks' => __( '4 Weeks', 'wp-club-manager' ),
					'+6 weeks' => __( '6 Weeks', 'wp-club-manager' ),
				),
			),
			'show_age'      => array(
				'type'  => 'checkbox',
				'std'   => '1',
				'label' => __( 'Show Age', 'wp-club-manager' ),
			),

		);
		parent::__construct();
	}

	/**
	 * Query the players and return them.
	 *
	 * @param  array $args
	 * @param  array $instance
	 * @return WP_Query
	 */
	public function get_birthdays( $args, $instance ) {

		$id            = isset( $instance['id'] ) ? $instance['id'] : null;
		$include_staff = isset( $instance['include_staff'] ) ? $instance['include_staff'] : 1;
		$date          = isset( $instance['date'] ) ? $instance['date'] : '+4 weeks';

		if ( $include_staff ) {
			$post_types       = array( 'wpcm_player', 'wpcm_staff' );
			$selected_players = (array) unserialize( get_post_meta( $id, '_wpcm_roster_players', true ) );
			$selected_staff   = (array) unserialize( get_post_meta( $id, '_wpcm_roster_staff', true ) );
			$selected         = array_merge( $selected_players, $selected_staff );
		} else {
			$post_types = 'wpcm_player';
			$selected   = (array) unserialize( get_post_meta( $id, '_wpcm_roster_players', true ) );
		}

		$query_args = array(
			'numberposts'      => -1,
			'posts_per_page'   => -1,
			'order'            => 'ASC',
			'post_type'        => $post_types,
			'suppress_filters' => 0,
			'post__in'         => $selected,
		);

		$players = get_posts( $query_args );

		foreach ( $players as $player ) :

			$dob = get_post_meta( $player->ID, 'wpcm_dob', true );
			if ( $dob ) {
				list( $y, $m, $d ) = explode( '-', $dob );
				$month_day            = gmdate( 'Y-' . $m . '-' . $d );
				$posts[ $player->ID ] = $month_day;
			}

		endforeach;

		uasort( $posts, 'compare_dates' );

		$new_posts = array();

		foreach ( $posts as $post => $value ) {

			list( $y, $m, $d ) = explode( '-', $value );
			$month_day         = gmdate( $m . '-' . $d );
			$timespan          = gmdate( 'm-d', strtotime( $date ) );
			if ( $month_day <= $timespan && $month_day >= gmdate( 'm-d' ) ) {

				$new_posts[ $post ] = $month_day;
			}
		}

		if ( ! empty( $new_posts ) ) {
			return $new_posts;
		}
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	public function widget( $args, $instance ) {

		$show_age = isset( $instance['show_age'] ) ? $instance['show_age'] : 1;

		$posts = $this->get_birthdays( $args, $instance );

		if ( empty( $posts ) ) {
			return;
		}

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		$this->widget_start( $args, $instance );

		wpclubmanager_get_template( 'content-widget-birthdays.php', array(
			'posts'    => $posts,
			'show_age' => $show_age,
		) );

		wp_reset_postdata();

		$this->widget_end( $args );

		echo wp_kses_post( $this->cache_widget( $args, ob_get_clean() ) );
	}
}
