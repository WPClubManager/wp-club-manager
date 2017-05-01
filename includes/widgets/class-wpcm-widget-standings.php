<?php
/**
 * Standings Widget
 *
 * @author 		ClubPress
 * @category 	Widgets
 * @package 	WPClubManager/Widgets
 * @version 	1.4.5
 * @extends 	WPCM_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Standings_Widget extends WPCM_Widget {

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass 	= 'wpcm-widget widget-standings';
		$this->widget_description 	= __( 'Display your clubs league standings.', 'wp-club-manager' );
		$this->widget_idbase 		= 'wpcm-standings-widget';
		$this->widget_name 		= __( 'WPCM Standings', 'wp-club-manager' );
		$this->settings     			= array(
			'title'  => array(
				'type'  		=> 'text',
				'std'   		=> __( 'Standings', 'wp-club-manager' ),
				'label' 		=> __( 'Title', 'wp-club-manager' )
			),
			'limit' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 7,
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
			'orderby' => array(
				'type'  => 'orderby_standings_columns',
				'std'   => 'pts',
				'label' => __( 'Order by', 'wp-club-manager' ),
			),
			'order' => array(
				'type'  => 'select',
				'std'   => 'DESC',
				'label' => _x( 'Order', 'Sorting order', 'wp-club-manager' ),
				'options' => array(
					'ASC'  => __( 'Lowest to highest', 'wp-club-manager' ),
					'DESC' => __( 'Highest to lowest', 'wp-club-manager' ),
				)
			),
			'thumb' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Show club badge', 'wp-club-manager' )
			),
			'linkclub' => array(
				'type'  => 'checkbox',
				'std'   => 0,
				'label' => __( 'Link to club pages', 'wp-club-manager' )
			),
			'display_columns' => array(
				'type'  => 'section_heading',
				'label' => __( 'Display Columns', 'wp-club-manager' ),
				'std'   => '',
			),
			'stats'  => array(
				'type'  => 'standings_columns',
				'std'   => 'p,w,pts',
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
			'excludes' => array(
				'type'  => 'text',
				'label' => __( 'Exclude clubs', 'wp-club-manager' ),
				'std'	=> '',
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

		echo do_shortcode('[wpcm_standings' . $options_string . ' type="widget"]');

		$this->widget_end( $args );
	}
}