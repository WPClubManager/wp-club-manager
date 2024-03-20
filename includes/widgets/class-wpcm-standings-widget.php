<?php
/**
 * Standings Widget
 *
 * @author      ClubPress
 * @category    Widgets
 * @package     WPClubManager/Widgets
 * @version     2.1.0
 * @extends     WPCM_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Standings_Widget
 */
class WPCM_Standings_Widget extends WPCM_Widget {

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass    = 'wpcm-widget widget-standings';
		$this->widget_description = __( 'Display your clubs league tables.', 'wp-club-manager' );
		$this->widget_idbase      = 'wpcm-standings-widget';
		$this->widget_name        = __( 'WPCM League Tables', 'wp-club-manager' );
		$this->settings           = array(
			'title'           => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Widget Title', 'wp-club-manager' ),
			),
			'id'              => array(
				'type'             => 'posts_select',
				'post_type'        => 'wpcm_table',
				'show_option_none' => false,
				'orderby'          => 'pts',
				'order'            => 'DESC',
				'limit'            => -1,
				'label'            => __( 'Choose League Table', 'wp-club-manager' ),
				'std'              => null,
			),
			'limit'           => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 7,
				'label' => __( 'Limit', 'wp-club-manager' ),
			),
			'focus'           => array(
				'type'  => 'focus_select',
				'std'   => '',
				'label' => __( 'Focus', 'wp-club-manager' ),
			),
			'abbr'            => array(
				'type'  => 'checkbox',
				'std'   => '',
				'label' => __( 'Use club abbreviations', 'wp-club-manager' ),
			),
			'thumb'           => array(
				'type'  => 'checkbox',
				'std'   => '',
				'label' => __( 'Show club badge', 'wp-club-manager' ),
			),
			'notes'           => array(
				'type'  => 'checkbox',
				'std'   => '',
				'label' => __( 'Display Notes', 'wp-club-manager' ),
			),
			'linkclub'        => array(
				'type'  => 'checkbox',
				'std'   => '',
				'label' => __( 'Link to club pages', 'wp-club-manager' ),
			),
			'display_columns' => array(
				'type'  => 'section_heading',
				'label' => __( 'Display Columns', 'wp-club-manager' ),
				'std'   => '',
			),
			'columns'         => array(
				'type' => 'standings_columns',
				'std'  => 'p,w,pts',
			),
			'link_options'    => array(
				'type'  => 'section_heading',
				'label' => __( 'Link Options', 'wp-club-manager' ),
				'std'   => '',
			),
			'linktext'        => array(
				'type'  => 'text',
				'std'   => __( 'View all standings', 'wp-club-manager' ),
				'label' => __( 'Link text', 'wp-club-manager' ),
			),
			'linkpage'        => array(
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
	public function widget( $args, $instance ) {

		$options_string = '';
		foreach ( $instance as $key => $value ) {
			if ( 'linkclub' === $key ) {
				$key = 'link_club';
			}
			if ( -1 != $value ) {
				$options_string .= ' ' . $key . '="' . $value . '"';
			}
		}

		$this->widget_start( $args, $instance );

		echo do_shortcode( '[league_table' . $options_string . ' type="widget"]' );

		$this->widget_end( $args );
	}
}
