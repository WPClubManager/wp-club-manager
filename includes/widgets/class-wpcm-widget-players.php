<?php
/**
 * Players Widget
 *
 * @author 		ClubPress
 * @category 	Widgets
 * @package 	WPClubManager/Widgets
 * @version 	1.4.0
 * @extends 	WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Players_Widget extends WPCM_Widget {	

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass 	  = 'wpcm-widget widget-players';
		$this->widget_description = __( 'Display a table of players details.', 'wp-club-manager' );
		$this->widget_idbase 	  = 'wpcm-players-widget';
		$this->widget_name 		  = __( 'WPCM Players', 'wp-club-manager' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Players', 'wp-club-manager' ),
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
			'position' => array(
				'type'  => 'tax_select',
				'taxonomy'   => 'wpcm_position',
				'std'   => 'All',
				'label' => __( 'Position', 'wp-club-manager' ),
			),
			'orderby' => array(
				'type'  => 'orderby_players_stats',
				'std'   => 'number',
				'label' => __( 'Order by', 'wp-club-manager' ),
				'options' => array(
					'number'   => __( 'Number', 'wp-club-manager' ),
					'menu_order'  => __( 'Page Order', 'wp-club-manager' ),
				)
			),
			'order' => array(
				'type'  => 'select',
				'std'   => 'ASC',
				'label' => _x( 'Order', 'Sorting order', 'wp-club-manager' ),
				'options' => array(
					'ASC'  => __( 'Lowest to highest', 'wp-club-manager' ),
					'DESC' => __( 'Highest to lowest', 'wp-club-manager' ),
				)
			),
			'display_options' => array(
				'type'  => 'section_heading',
				'label' => __( 'Display Options', 'wp-club-manager' ),
				'std'   => '',
			),
			'stats' => array(
				'type'  => 'player_stats',
				'std'   => 'flag,number,name,position,age',
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
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		$options_string = '';
		foreach( $instance as $key => $value ) {	
			if ( $value != -1 ) $options_string .= ' ' . $key . '="' . $value . '"';
		}

		$this->widget_start( $args, $instance );

		echo do_shortcode('[wpcm_players' . $options_string . ' type="widget"]');

		$this->widget_end( $args );
	}
}