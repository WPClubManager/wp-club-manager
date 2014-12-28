<?php
/**
 * Standings Widget
 *
 * @author 		ClubPress
 * @category 	Widgets
 * @package 	WPClubManager/Widgets
 * @version 	1.1.0
 * @extends 	WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Standings_Widget extends WP_Widget {

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
	function WPCM_Standings_Widget() {

		/* Widget variable settings. */
		$this->wpcm_widget_cssclass = 'wpcm-widget widget-standings';
		$this->wpcm_widget_description = __( 'Display your clubs league standings.', 'wpclubmanager' );
		$this->wpcm_widget_idbase = 'wpcm-standings-widget';
		$this->wpcm_widget_name = __( 'WPCM Standings', 'wpclubmanager' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->wpcm_widget_cssclass, 'description' => $this->wpcm_widget_description );

		/* Create the widget. */
		$this->WP_Widget('wpcm_standings', $this->wpcm_widget_name, $widget_ops);
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
		$options_string = '';
		
		foreach( $instance as $key => $value ) {

			$options_string .= ' ' . $key . '="' . $value . '"';
		}
		
		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div class="clearfix">';

		echo do_shortcode('[wpcm_standings' . $options_string . ' type="widget"]');

		echo '</div>';

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

		$instance['linkclub'] = strip_tags( $new_instance['linkclub'] );
		$instance['thumb'] = strip_tags( $new_instance['thumb'] );
		
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
			'limit' => 7,
			'comp' => null,
			'season' => null,
			'orderby' => 'pts',
			'order' => 'DESC',
			'linktext' => __( 'View all standings', 'wpclubmanager' ),
			'linkpage' => null,
			'stats' => 'p,w,d,l,otw,otl,pct,f,a,gd,b,pts',
			'title' => __( 'Standings', 'wpclubmanager' ),
			'linkclub' => null,
			'thumb' => null,
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<?php $field = 'title'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Title', 'wpclubmanager') ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" /></p>
		
		<?php $field = 'limit'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Limit', 'wpclubmanager') ?>:</label>
		<input id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" size="3" /></p>
		
		<?php $field = 'comp'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Competition', 'wpclubmanager') ?>:</label>
		<?php
		wp_dropdown_categories(array(
			'show_option_none' => __( 'All' ),
			'hide_empty' => 0,
			'orderby' => 'title',
			'taxonomy' => 'wpcm_comp',
			'selected' => $instance[$field],
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		));
		?></p>
		
		<?php $field = 'season'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Season', 'wpclubmanager') ?>:</label>
		<?php
		wp_dropdown_categories(array(
			'show_option_none' => __( 'All' ),
			'hide_empty' => 0,
			'orderby' => 'title',
			'taxonomy' => 'wpcm_season',
			'selected' => $instance[$field],
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		));
		?></p>
		
		<?php $field = 'orderby'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Order by', 'wpclubmanager') ?>:</label>
		<input id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" size="3" /></p>
		
		<?php $field = 'order'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Order', 'wpclubmanager') ?>:</label>
		<input id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" size="3" /></p>
		
		<?php $field = 'linktext'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Link text', 'wpclubmanager') ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" /></p>
		
		<?php $field = 'linkpage'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Link page', 'wpclubmanager') ?>:</label>
		<?php
		wp_dropdown_pages( array(
			'show_option_none' => __( 'None' ),
			'selected' => $instance[$field],
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		) );
		?></p>

		<?php $field = 'thumb'; ?>
		<p><label class="selectit" for="<?php echo $this->get_field_id( $field ); ?>">
				<input type="checkbox" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="1"<?php if( $instance[$field] ) echo ' checked' ?> />
							<?php _e( 'Show Club Badge', 'wpclubmanager' ); ?>
			</label></p>

		<?php $field = 'linkclub'; ?>
		<p><label class="selectit" for="<?php echo $this->get_field_id( $field ); ?>">
				<input type="checkbox" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="1"<?php if( $instance[$field] ) echo ' checked' ?> />
							<?php _e( 'Link to Clubs', 'wpclubmanager' ); ?>
			</label></p>
		
		<?php $field = 'stats'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Statistics', 'wpclubmanager') ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" /></p>

		<?php
	}
}

register_widget( 'WPCM_Standings_Widget' );