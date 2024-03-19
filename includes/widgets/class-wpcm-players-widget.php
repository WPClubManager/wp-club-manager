<?php
/**
 * Players Widget
 *
 * @author      ClubPress
 * @category    Widgets
 * @package     WPClubManager/Widgets
 * @version     2.0.6
 * @extends     WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Players_Widget
 */
class WPCM_Players_Widget extends WPCM_Widget {

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass    = 'wpcm-widget widget-players';
		$this->widget_description = __( 'Display a table of players details.', 'wp-club-manager' );
		$this->widget_idbase      = 'wpcm-players-widget';
		$this->widget_name        = __( 'WPCM Players List', 'wp-club-manager' );
		$this->settings           = array(
			'title'           => array(
				'type'  => 'text',
				'std'   => __( 'Players', 'wp-club-manager' ),
				'label' => __( 'Widget Title', 'wp-club-manager' ),
			),
			'id'              => array(
				'type'             => 'posts_select',
				'post_type'        => 'wpcm_roster',
				'show_option_none' => false,
				'std'              => null,
				'orderby'          => 'post_id',
				'order'            => 'ASC',
				'limit'            => -1,
				'label'            => __( 'Choose Roster', 'wp-club-manager' ),
			),
			'limit'           => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 3,
				'label' => __( 'Limit', 'wp-club-manager' ),
			),
			'position'        => array(
				'type'     => 'tax_select',
				'taxonomy' => 'wpcm_position',
				'std'      => 'All',
				'label'    => __( 'Position', 'wp-club-manager' ),
			),
			'orderby'         => array(
				'type'    => 'orderby_players_stats',
				'std'     => 'number',
				'label'   => __( 'Order by', 'wp-club-manager' ),
				'options' => array(
					'number'     => __( 'Number', 'wp-club-manager' ),
					'menu_order' => __( 'Page Order', 'wp-club-manager' ),
				),
			),
			'order'           => array(
				'type'    => 'select',
				'std'     => 'ASC',
				'label'   => _x( 'Order', 'Sorting order', 'wp-club-manager' ),
				'options' => array(
					'ASC'  => __( 'Lowest to highest', 'wp-club-manager' ),
					'DESC' => __( 'Highest to lowest', 'wp-club-manager' ),
				),
			),
			'display_options' => array(
				'type'  => 'section_heading',
				'label' => __( 'Display Columns', 'wp-club-manager' ),
				'std'   => -1,
			),
			'columns'         => array(
				'type' => 'player_stats',
				'std'  => 'number,name,thumb,position',
			),
			'name_format'     => array(
				'type'    => 'select',
				'std'     => 'full',
				'label'   => __( 'Name Format', 'wp-club-manager' ),
				'options' => array(
					'full'    => __( 'First Last', 'wp-club-manager' ),
					'last'    => __( 'Last', 'wp-club-manager' ),
					'initial' => __( 'F. Last', 'wp-club-manager' ),
				),
			),
			'link_options'    => array(
				'type'  => 'section_heading',
				'label' => __( 'Link Options', 'wp-club-manager' ),
				'std'   => -1,
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
			if ( -1 != $value ) {
				$options_string .= ' ' . $key . '="' . $value . '"';
			}
		}

		$this->widget_start( $args, $instance );

		echo do_shortcode( '[player_list' . $options_string . ' type="widget"]' );

		$this->widget_end( $args );
	}
}
