<?php
/**
 * Abstract Widget Class
 *
 * @author 		Clubpress
 * @category 	Widgets
 * @package 	WPClubManager/Abstracts
 * @version 	1.4.0
 * @extends 	WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

abstract class WPCM_Widget extends WP_Widget {

	public $widget_cssclass;
	public $widget_description;
	public $widget_id;
	public $widget_name;
	public $settings;

	/**
	 * Constructor
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'   => $this->widget_cssclass,
			'description' => $this->widget_description
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * get_cached_widget function.
	 */
	function get_cached_widget( $args ) {
		$cache = wp_cache_get( apply_filters( 'wpclubmanager_cached_widget_id', $this->widget_id ), 'widget' );

		if ( ! is_array( $cache ) )
			$cache = array();

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget
	 */
	public function cache_widget( $args, $content ) {
		wp_cache_set( apply_filters( 'wpclubmanager_cached_widget_id', $this->widget_id ), array( $args['widget_id'] => $content ), 'widget' );

		return $content;
	}

	/**
	 * Flush the cache
	 * @return [type]
	 */
	public function flush_widget_cache() {
		wp_cache_delete( apply_filters( 'wpclubmanager_cached_widget_id', $this->widget_id ), 'widget' );
	}

	/**
	 * Output the html at the start of a widget
	 *
	 * @param  array $args
	 * @return string
	 */
	public function widget_start( $args, $instance ) {
		echo $args['before_widget'];

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
	}

	/**
	 * Output the html at the end of a widget
	 *
	 * @param  array $args
	 * @return string
	 */
	public function widget_end( $args ) {
		echo $args['after_widget'];
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

		if ( ! $this->settings )
			return $instance;

		// Loop settings and get values to save.
		foreach ( $this->settings as $key => $setting ) {
			if ( ! isset( $setting['type'] ) ) {
				continue;
			}

			// Format the value based on settings type.
			switch ( $setting['type'] ) {
				case 'number' :
					$instance[ $key ] = absint( $new_instance[ $key ] );

					if ( isset( $setting['min'] ) && '' !== $setting['min'] ) {
						$instance[ $key ] = max( $instance[ $key ], $setting['min'] );
					}

					if ( isset( $setting['max'] ) && '' !== $setting['max'] ) {
						$instance[ $key ] = min( $instance[ $key ], $setting['max'] );
					}
				break;
				case 'textarea' :
					$instance[ $key ] = wp_kses( trim( wp_unslash( $new_instance[ $key ] ) ), wp_kses_allowed_html( 'post' ) );
				break;
				case 'checkbox' :
					$instance[ $key ] = is_null( $new_instance[ $key ] ) ? 0 : 1;
				break;
				case 'player_stats' :
				case 'standings_columns' :
					if ( is_array( $new_instance[ $key ] ) ) $new_instance[ $key ] = implode(',', $new_instance[ $key ]);
					$instance[ $key ] = strip_tags($new_instance[ $key ]);
				break;
				default:
					$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
				break;
			}

			/**
			 * Sanitize the value of a setting.
			 */
			$instance[ $key ] = apply_filters( 'wpclubmanager_widget_settings_sanitize_option', $instance[ $key ], $new_instance, $key, $setting );
		}

		$this->flush_widget_cache();

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

		if ( ! $this->settings )
			return;

		foreach ( $this->settings as $key => $setting ) {

			$value   = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {
				case "text" :
					?>
					<p class="wpcm-widget-admin">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case "number" :
					?>
					<p class="wpcm-widget-admin">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
					</p>
					<?php
				break;
				case "select" :
					?>
					<div class="wpcm-widget-admin">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<?php
				break;

				case "tax_select" :
					?>
					 <div class="wpcm-widget-admin"><label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<?php
						$args = array(
							'show_option_none' 	=> __( 'All', 'wp-club-manager' ),
							'hide_empty' 		=> 0,
							'orderby' 			=> 'title',
							'taxonomy' 			=> $setting['taxonomy'],
							'selected' 			=> $value,
							'name' 				=> $this->get_field_name( $key ),
							'id' 				=> $this->get_field_id( $key )
						);
						wp_dropdown_categories( $args ); ?>
					</div>
					<?php
				break;

				case "pages_select" :
					?>
					 <div class="wpcm-widget-admin"><label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<?php
						$args = array(
							'show_option_none' 	=> __( 'None', 'wp-club-manager' ),
							'selected' 			=> $value,
							'name' 				=> $this->get_field_name( $key ),
							'id' 				=> $this->get_field_id( $key )
						);
						wp_dropdown_pages( $args ); ?>
					</div>
					<?php
				break;

				case "posts_select" :
					?>
					 <div class="wpcm-widget-admin"><label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<?php
						$args = array(
							'show_option_none' 	=> __( 'None', 'wp-club-manager' ),
							'selected' 			=> $value,
							'name' 				=> $this->get_field_name( $key ),
							'id' 				=> $this->get_field_id( $key ),
							'post_type'        	=> $setting['post_type'],
							'orderby' 			=> $setting['orderby'],
							'order' 			=> $setting['order'],
							'limit' 			=> $setting['limit']
						);
						wpcm_dropdown_posts( $args );  ?>
					</div>
					<?php
				break;

				case "checkbox" :
					?>
					 <p class="wpcm-widget-admin">
						<input id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
					</p>
					<?php
				break;

				case 'orderby_players_stats' :
					$player_stats_labels = wpcm_get_player_stats_names(); ?>
					 <div class="wpcm-widget-admin">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php _e('Order by', 'wp-club-manager') ?></label>
						<select id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
							<?php foreach ( $player_stats_labels as $option_key => $option_value ) : ?>

								<option id="<?php echo $key; ?>" value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo strip_tags( $option_value ); ?></option>

							<?php endforeach; ?>
						</select>
					</div>
					<?php
				break;

				case 'player_stats' :
					$stats_labels = wpcm_get_player_all_names();
					$stats = explode( ',', $value ); ?>
					 <div class="wpcm-widget-admin">
						<table>
							<tr>
								<?php $count = 0;
								foreach ( $stats_labels as $option_key => $option_value ) {
									$count++;
									if ( $count > 2 ) {
										$count = 1;
										echo '</tr><tr>';
									} ?>
									<td>
										<label class="selectit" for="<?php echo $this->get_field_id( $key ); ?>-<?php echo $option_key; ?>">
										<input type="checkbox" id="<?php echo $this->get_field_id( $key ); ?>-<?php echo $option_key; ?>" name="<?php echo $this->get_field_name( $key ); ?>[]" value="<?php echo $option_key; ?>" <?php checked( in_array( $option_key, $stats ) ); ?> />
											<?php echo strip_tags($option_value); ?>
										</label>
									</td>
								<?php } ?>
							</tr>
						</table>
					</div>
				<?php
				break;

				case 'orderby_standings_columns' :
					$standings_columns_labels = wpcm_get_preset_labels( 'standings', 'name' ); ?>
					 <div class="wpcm-widget-admin">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php _e('Order by', 'wp-club-manager') ?></label>
						<select id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $standings_columns_labels as $option_key => $option_value ) : ?>

								<option id="<?php echo $key; ?>" value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo strip_tags( $option_value ); ?></option>

							<?php endforeach; ?>
						</select>
					</div>
					<?php
				break;

				case 'standings_columns' :
					$column_labels = wpcm_get_preset_labels( 'standings', 'label' );
					$columns = explode( ',', $value ); ?>
					 <div class="wpcm-widget-admin">
						<table>
							<tr>
								<?php $count = 0;
								foreach ( $column_labels as $option_key => $option_value ) {
									$count++;
									if ( $count > 4 ) {
										$count = 1;
										echo '</tr><tr>';
									} ?>
									<td>
										<label class="selectit" for="<?php echo $this->get_field_id( $key ); ?>-<?php echo $option_key; ?>">
										<input type="checkbox" id="<?php echo $this->get_field_id( $key ); ?>-<?php echo $option_key; ?>" name="<?php echo $this->get_field_name( $key ); ?>[]" value="<?php echo $option_key; ?>" <?php checked( in_array( $option_key, $columns ) ); ?> />
											<?php echo strip_tags($option_value); ?>
										</label>
									</td>
								<?php } ?>
							</tr>
						</table>
					</div>
				<?php
				break;

				case "section_heading" :
					?>
					<h4><?php echo $setting['label']; ?></h4>
					<?php
				break;

				default :
					do_action( 'wpclubmanager_widget_field_' . $setting['type'], $key, $value, $setting, $instance );
				break;
			}
		}
	}
}