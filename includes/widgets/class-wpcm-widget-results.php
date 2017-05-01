<?php
/**
 * Results Widget
 *
 * @author 		ClubPress
 * @category 	Widgets
 * @package 	WPClubManager/Widgets
 * @version 	1.4.8
 * @extends 	WPCM_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Results_Widget extends WPCM_Widget {

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass    = 'wpcm-widget widget-results';
		$this->widget_description = __( 'Display most recent results.', 'wp-club-manager' );
		$this->widget_id          = 'wpcm_results';
		$this->widget_name        = __( 'WPCM Results', 'wp-club-manager' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Results', 'wp-club-manager' ),
				'label' => __( 'Title', 'wp-club-manager' )
			),
			'limit' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 3,
				'label' => __( 'Limit', 'wp-club-manager' )
			),
			'comp' => array(
				'type'  => 'tax_select',
				'taxonomy'   => 'wpcm_comp',
				'std'   => 'All',
				'label' => __( 'Competition', 'wp-club-manager' ),
			),
			'season' => array(
				'type'  => 'tax_select',
				'taxonomy'   => 'wpcm_season',
				'std'   => 'All',
				'label' => __( 'Season', 'wp-club-manager' ),
			),
			'team' => array(
				'type'  => 'tax_select',
				'taxonomy'   => 'wpcm_team',
				'std'   => 'All',
				'label' => __( 'Team', 'wp-club-manager' ),
			),
			'venue' => array(
				'type'  => 'select',
				'std'   => 'all',
				'label' => __( 'Venue', 'wp-club-manager' ),
				'options' => array(
					'all'  => __( 'All', 'wp-club-manager' ),
					'home' => __( 'Home', 'wp-club-manager' ),
					'away' => __( 'Away', 'wp-club-manager' ),
				)
			),
			'display_options' => array(
				'type'  => 'section_heading',
				'label' => __( 'Display Options', 'wp-club-manager' ),
				'std'   => '',
			),
			'show_date' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Date', 'wp-club-manager' )
			),
			'show_time' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Kick Off', 'wp-club-manager' )
			),
			'show_score' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Score', 'wp-club-manager' )
			),
			'show_comp' => array(
				'type'  => 'checkbox',
				'std'   => 1,
				'label' => __( 'Competition', 'wp-club-manager' )
			),
			'show_team' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Team', 'wp-club-manager' )
			),
			'link_options' => array(
				'type'  => 'section_heading',
				'label' => __( 'Link Options', 'wp-club-manager' ),
				'std'   => '',
			),
			'linktext'  => array(
				'type'  => 'text',
				'std'   => __( 'View all standings', 'wp-club-manager' ),
				'label' => __( 'Link text', 'wp-club-manager' )
			),
			'linkpage' => array(
				'type'  => 'pages_select',
				'label' => __( 'Link page', 'wp-club-manager' ),
				'std'   => 'None',
			),
			
		);
		parent::__construct();
	}

	/**
	 * Query the results and return them.
	 * @param  array $args
	 * @param  array $instance
	 * @return WP_Query
	 */
	public function get_results( $args, $instance ) {

		$limit = absint( $instance['limit'] );
		$comp = isset( $instance['comp'] ) ? $instance['comp'] : null;
		$season = isset( $instance['season'] ) ? $instance['season'] : null;
		$team = isset( $instance['team'] ) ? $instance['team'] : null;
		$club = get_option( 'wpcm_default_club' );
		$venue = isset( $instance['venue'] ) ? $instance['venue'] : 'all';		
    	if ( $limit == 0 ) $limit = -1;
		if ( $comp <= 0 ) $comp = null;
		if ( $season <= 0 ) $season = null;
		if ( $team <= 0 ) $team = null;

		$query_args = array(
			'numberposts' => $limit,
			'order' => 'DESC',
			'orderby' => 'post_date',
			'post_type' => 'wpcm_match',
			'meta_query' => array(
				array(
					'key' => 'wpcm_played',
					'value' => true
				)
			),
			'posts_per_page' => $limit,
		);

		if( isset( $venue ) && $venue == 'home' ) {
			$query_args['meta_query'] = array(
				array(
					'key' => 'wpcm_home_club',
					'value' => $club,
				),
			);
		} elseif( isset( $venue ) && $venue == 'away' ) {
			$query_args['meta_query'] = array(
				array(
					'key' => 'wpcm_away_club',
					'value' => $club,
				),
			);
		} else {
			$query_args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key' => 'wpcm_home_club',
					'value' => $club,
				),
				array(
					'key' => 'wpcm_away_club',
					'value' => $club,
				)
			);
		}

		$query_args['meta_query'] = array(
			array(
				'key' => 'wpcm_played',
				'value' => true,
			),
		);

		if ( isset( $comp ) )
			$query_args['tax_query'][] = array(
				'taxonomy' => 'wpcm_comp',
				'terms' => $comp,
				'field' => 'term_id',
			);

		if ( isset( $season ) )
			$query_args['tax_query'][] = array(
				'taxonomy' => 'wpcm_season',
				'terms' => $season,
				'field' => 'term_id',
			);

		if ( isset( $team ) )
			$query_args['tax_query'][] = array(
				'taxonomy' => 'wpcm_team',
				'terms' => $team,
				'field' => 'term_id',
			);

		return new WP_Query( apply_filters( 'wpclubmanager_results_widget_query_args', $query_args ) );
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

		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		$this->widget_start( $args, $instance );

		if ( ( $results = $this->get_results( $args, $instance ) ) && $results->have_posts() ) {

			echo apply_filters( 'wpclubmanager_before_widget_results', '<ul class="wpcm-matches-widget">' );
		
			while ( $results->have_posts() ) : $results->the_post();

				$post = get_the_ID();
				$sides = wpcm_get_match_clubs( $post );
				$badges = wpcm_get_match_badges( $post, 'crest-medium' );
				$comp = wpcm_get_match_comp( $post );
				$team = wpcm_get_match_team( $post );
				$score = wpcm_get_match_result( $post );
				$played = get_post_meta( $post, 'wpcm_played', true );
				$show_date = ! empty( $instance['show_date'] );
		    	$show_time = ! empty( $instance['show_time'] );
		    	$show_score = ! empty( $instance['show_score'] );
		    	$show_comp = ! empty( $instance['show_comp'] );
		    	$show_team = ! empty( $instance['show_team'] );

				wpclubmanager_get_template( 'content-widget-results.php', array( 'team' => $team, 'comp' => $comp, 'sides' => $sides, 'badges' => $badges, 'score' => $score, 'show_date' => $show_date, 'show_time' => $show_time, 'show_comp' => $show_comp, 'show_team' => $show_team, 'show_score' => $show_score, 'played' => $played ) );

			endwhile;

			echo apply_filters( 'wpclubmanager_after_widget_results', '</ul>' );

		} else {

			echo '<p class="inner">'.__('No more matches scheduled.', 'wp-club-manager').'</p>';
		}

		wp_reset_postdata();
		
		$linktext = $instance['linktext'];
		$linkpage = $instance['linkpage'];
		if($linkpage <= 0) $linkpage = null;
		
		if ( isset( $linkpage ) )
			echo '<a href="' . get_page_link( $linkpage ) . '" class="wpcm-view-link">' . $linktext . '</a>';

		$this->widget_end( $args );

		echo $this->cache_widget( $args, ob_get_clean() );
	}
}