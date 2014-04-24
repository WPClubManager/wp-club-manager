<?php
/**
 * Sponsors Widget
 *
 * @author 		ClubPress
 * @category 	Widgets
 * @package 	WPClubManager/Widgets
 * @version 	1.0.0
 * @extends 	WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Sponsors_Widget extends WP_Widget {

	var $wpcm_widget_cssclass;
	var $wpcm_widget_description;
	var $wpcm_widget_idbase;
	var $wpcm_widget_name;

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	function WPCM_Sponsors_Widget() {

		/* Widget variable settings. */
		$this->wpcm_widget_cssclass = 'wpcm-widget widget-sponsors';
		$this->wpcm_widget_description = __( 'Display a sponsors logo.', 'wpclubmanager' );
		$this->wpcm_widget_idbase = 'wpcm-sponsors-widget';
		$this->wpcm_widget_name = __( 'WPCM Sponsors', 'wpclubmanager' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->wpcm_widget_cssclass, 'description' => $this->wpcm_widget_description );

		/* Create the widget. */
		$this->WP_Widget('wpcm_sponsors', $this->wpcm_widget_name, $widget_ops);
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
			
		extract( $args );

		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$id = $instance['id'];
		$direct_link = $instance['direct_link'];

		$link_url = get_post_meta( $id, 'wpcm_link_url', true );

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div class="wpcm-sponsor-widget clearfix">';

		if ( $direct_link ) {

			echo '<a href="'.$link_url.'" target="_blank">';
								
			echo get_the_post_thumbnail( $id );

			echo '</a>';

		} else {

			echo '<a href="'.get_permalink( $id ).'">';
								
			echo get_the_post_thumbnail( $id );

			echo '</a>';

		}
								
		echo '</div>';

		wp_reset_postdata();

		echo $after_widget;

	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['id'] = strip_tags( $new_instance['id'] );
		$instance['direct_link'] = strip_tags( $new_instance['direct_link'] );
		$instance['title'] = strip_tags( $new_instance['title'] );

		foreach( $new_instance as $key => $value ) {

			$instance[$key] = strip_tags( $value );
		}

		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {

		$defaults = array( 
			'id' => null,
			'direct_link' => null,
			'title' => __( 'Sponsors', 'wpclubmanager' )
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<?php $field = 'title'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Title', 'wpclubmanager') ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" /></p>

		<?php $field = 'id'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Choose a sponsor', 'wpclubmanager') ?>:</label>
		<?php
		wpcm_dropdown_posts( array(
			'post_type' => 'wpcm_sponsor',
			'show_option_none' => __( 'None' ),
			'limit' => -1,
			'selected' => $instance[$field],
			'orderby' => 'post_date',
			'order' => 'DESC',
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		) );
		?>

		<?php $field = 'direct_link'; ?>
		<p><label class="selectit" for="<?php echo $this->get_field_id( $field ); ?>">
				<input type="checkbox" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="1"<?php if( $instance[$field] ) echo ' checked' ?> />
							<?php _e( 'Link directly to website?', 'wpclubmanager' ); ?>
			</label></p>
	<?php }
}

register_widget( 'WPCM_Sponsors_Widget' );