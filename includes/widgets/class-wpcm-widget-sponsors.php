<?php
/**
 * Sponsors Widget
 *
 * @author 		ClubPress
 * @category 	Widgets
 * @package 	WPClubManager/Widgets
 * @version 	2.0.0
 * @extends 	WPCM_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Sponsors_Widget extends WPCM_Widget {

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

		/* Widget variable settings. */
		$this->widget_cssclass 		= 'wpcm-widget widget-sponsors';
		$this->widget_description 	= __( 'Display a sponsors logo.', 'wp-club-manager' );
		$this->widget_idbase 		= 'wpcm-sponsors-widget';
		$this->widget_name 			= __( 'WPCM Sponsors', 'wp-club-manager' );
		$this->settings           	= array(
			'title'  => array(
				'type'  		=> 'text',
				'std'   		=> __( 'Sponsors', 'wp-club-manager' ),
				'label' 		=> __( 'Title', 'wp-club-manager' )
			),
			'id' => array(
				'type'  		=> 'posts_select',
				'post_type'   	=> 'wpcm_sponsor',
				'show_option_none'   => false,
				'label' 		=> __( 'Choose a sponsor', 'wp-club-manager' ),
				'orderby' 		=> 'post_date',
				'order' 		=> 'DESC',
				'limit' 		=> -1,
				'std'   		=> null
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
			
		$this->widget_start( $args, $instance );

		$link_url = get_post_meta( $instance['id'], 'wpcm_link_url', true );
		$link_new_window = get_post_meta( $instance['id'], 'wpcm_link_nw', true );
		$nw = ( $link_new_window ) ? ' target="_blank"' : ''; ?>

		<a href="<?php echo $link_url; ?>"<?php echo $nw; ?>>					
			<?php echo get_the_post_thumbnail( $instance['id'] ); ?>
		</a>

		<?php $this->widget_end( $args );
	}
}