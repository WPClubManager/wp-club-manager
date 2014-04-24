<?php
/**
 * Players Widget
 *
 * @author 		ClubPress
 * @category 	Widgets
 * @package 	WPClubManager/Widgets
 * @version 	1.0.0
 * @extends 	WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Players_Widget extends WP_Widget {

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
	function WPCM_Players_Widget() {

		/* Widget variable settings. */
		$this->wpcm_widget_cssclass = 'wpcm-widget widget-players';
		$this->wpcm_widget_description = __( 'Display a table of players details.', 'wpclubmanager' );
		$this->wpcm_widget_idbase = 'wpcm-players-widget';
		$this->wpcm_widget_name = __( 'WPCM Players', 'wpclubmanager' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->wpcm_widget_cssclass, 'description' => $this->wpcm_widget_description );

		/* Create the widget. */
		$this->WP_Widget('wpcm_players', $this->wpcm_widget_name, $widget_ops);
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
			
			if ( $value != -1 )
				$options_string .= ' ' . $key . '="' . $value . '"';
		}

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div class="clearfix">';

		echo do_shortcode('[wpcm_players' . $options_string . ']');

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
		
		foreach( $new_instance as $key => $value ) {
			
			if ( is_array( $value ) )
				$value = implode(',', $value);
			
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
			'limit' => 10,
			'season' => null,
			'club' => get_option( 'wpcm_default_club' ),
			'team' => null,
			'position' => null,
			'orderby' => 'number',
			'order' => 'ASC',
			'show_flag' => get_option( 'wpcm_player_list_show_flag' ),
			'show_position' => get_option( 'wpcm_player_list_show_position' ),
			'show_age' => get_option( 'wpcm_player_list_show_age' ),
			'show_dob' => get_option( 'wpcm_player_list_show_dob' ),
			'show_name' => get_option( 'wpcm_player_gallery_show_name' ),
			'show_number' => get_option( 'wpcm_player_gallery_show_number' ),
			'linktext' => __( 'View all players', 'wpclubmanager' ),
			'linkpage' => null,
			'stats' => 'flag,number,name,position,age',
			'title' => __( 'Players', 'wpclubmanager' )
		);

		$wpcm_player_stats_labels = array(
			'goals' => get_option( 'wpcm_player_goals_label'),
			'assists' => get_option( 'wpcm_player_assists_label'),
			'yellowcards' => get_option( 'wpcm_player_yellowcards_label'),
			'redcards' => get_option( 'wpcm_player_redcards_label'),
			'rating' => get_option( 'wpcm_player_rating_label'),
			'mvp' => get_option( 'wpcm_player_mvp_label')
		);

		$player_stats_labels = array_merge( array( 'appearances' => __( 'Appearances', 'wpclubmanager' ) ), $wpcm_player_stats_labels );
		$stats_labels = array_merge(
			array(
				'flag' => __( 'Flag', 'wpclubmanager' ),
				'number' => __( 'Number', 'wpclubmanager' ),
				'name' => __( 'Name', 'wpclubmanager' ),
				'position' => __( 'Position', 'wpclubmanager' ),
				'age' => __( 'Age', 'wpclubmanager' ),
				'team' => __( 'Team', 'wpclubmanager' ),
				'season' => __( 'Season', 'wpclubmanager' ),
				'dob' => __( 'Date of Birth', 'wpclubmanager' ),
				'hometown' => __( 'Hometown', 'wpclubmanager' ),
				'joined' => __( 'Joined', 'wpclubmanager' )
			),
			$player_stats_labels
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
		$stats = explode( ',', $instance['stats'] );
		?>
		
		<?php $field = 'title'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Title', 'wpclubmanager') ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" /></p>
		
		<?php $field = 'limit'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Limit', 'wpclubmanager') ?>:</label>
		<input id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" size="3" /></p>
		
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
		
		<?php $field = 'team'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Team', 'wpclubmanager') ?>:</label>
		<?php
		wp_dropdown_categories(array(
			'show_option_none' => __( 'All' ),
			'hide_empty' => 0,
			'orderby' => 'title',
			'taxonomy' => 'wpcm_team',
			'selected' => $instance[$field],
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		));
		?></p>
		
		<?php $field = 'position'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Position', 'wpclubmanager') ?>:</label>
		<?php
		wp_dropdown_categories(array(
			'show_option_none' => __( 'All' ),
			'hide_empty' => 0,
			'orderby' => 'title',
			'taxonomy' => 'wpcm_position',
			'selected' => $instance[$field],
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		));
		?></p>
				
		<?php $field = 'orderby'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Order by', 'wpclubmanager') ?>:</label>
		<select id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>">
			<option id="number" value="number"<?php if ( $instance[$field] == 'number' ) echo ' selected'; ?>><?php _e( 'Number', 'wpclubmanager' ); ?></option>
			<option id="menu_order" value="menu_order"<?php if ( $instance[$field] == 'menu_order' ) echo ' selected'; ?>><?php _e( 'Page order' ); ?></option>
			<?php foreach ( $player_stats_labels as $key => $val ) { ?>

				<option id="<?php echo $key; ?>" value="<?php echo $key; ?>"<?php if ( $instance[$field] == $key ) echo ' selected'; ?>><?php echo $val; ?></option>

			<?php } ?>
		</select></p>
			
		<?php $field = 'order'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Order', 'wpclubmanager') ?>:</label>
		<?php
		$wpcm_order_options = array(
			'ASC' => __( 'Lowest to highest', 'wpclubmanager' ),
			'DESC' => __( 'Highest to lowest', 'wpclubmanager' )
		);
		?>
		<select id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>">
			<?php foreach ( $wpcm_order_options as $key => $val ) { ?>

				<option id="<?php echo $key; ?>" value="<?php echo $key; ?>"<?php if ( $instance[$field] == $key ) echo ' selected'; ?>><?php echo $val; ?></option>

			<?php } ?>
		</select></p>
			
		<?php $field = 'stats'; ?>
		<p><label><?php _e( 'Statistics', 'wpclubmanager' ); ?></label>
		<table>
			<tr>
				<?php $count = 0;
				
				foreach ( $stats_labels as $key => $value ) {
					
					$count++;
					if ( $count > 2 ) {
						$count = 1;
						echo '</tr><tr>';
					}
				?>
				<td>
					<label class="selectit" for="<?php echo $this->get_field_id( $field ); ?>-<?php echo $key; ?>">
						<input type="checkbox" id="<?php echo $this->get_field_id( $field ); ?>-<?php echo $key; ?>" name="<?php echo $this->get_field_name( $field ); ?>[]" value="<?php echo $key; ?>" <?php checked( in_array( $key, $stats ) ); ?> />
						<?php echo $value; ?>
					</label>
				</td>
			<?php } ?>
			</tr>
		</table></p>
			
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

		<?php
	}
}

register_widget( 'WPCM_Players_Widget' );