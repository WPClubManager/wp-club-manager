<?php
/**
 * WPClubManager Admin Settings Class.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Settings' ) ) :

class WPCM_Admin_Settings {

	private static $settings = array();
	private static $errors   = array();
	private static $messages = array();

	/**
	 * Include the settings page classes
	 */
	public static function get_settings_pages() {

		if ( empty( self::$settings ) ) {
			$settings = array();

			include_once( 'settings/class-wpcm-settings-page.php' );

			$settings[] = include( 'settings/class-wpcm-settings-general.php' );
			$settings[] = include( 'settings/class-wpcm-settings-clubs.php' );
			$settings[] = include( 'settings/class-wpcm-settings-players.php' );
			$settings[] = include( 'settings/class-wpcm-settings-staff.php' );
			$settings[] = include( 'settings/class-wpcm-settings-matches.php' );
			$settings[] = include( 'settings/class-wpcm-settings-standings.php' );
			if( in_array( 'wpcm-player-appearances/wpcm-player-appearances.php', apply_filters('active_plugins', get_option('active_plugins'))) || in_array( 'wpcm-players-gallery/wpcm-player-gallery.php', apply_filters('active_plugins', get_option('active_plugins'))) || in_array( 'wpcm-sponsors-pro/wpcm-sponsors-pro.php', apply_filters('active_plugins', get_option('active_plugins'))) ) {
				$settings[] = include( 'settings/class-wpcm-settings-licenses.php' );
			}

			self::$settings = apply_filters( 'wpclubmanager_get_settings_pages', $settings );
		}
		return self::$settings;
	}

	/**
	 * Save the settings
	 */
	public static function save() {
		global $current_section, $current_tab;

		if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpclubmanager-settings' ) )
	    		die( __( 'Action failed. Please refresh the page and retry.', 'wp-club-manager' ) );

	    // Trigger actions
	   	do_action( 'wpclubmanager_settings_save_' . $current_tab );
	    do_action( 'wpclubmanager_update_options_' . $current_tab );
	    do_action( 'wpclubmanager_update_options' );

    	self::add_message( __( 'Your settings have been saved.', 'wp-club-manager' ) );

		do_action( 'wpclubmanager_settings_saved' );
	}

	/**
	 * Add a message
	 * @param string $text
	 */
	public static function add_message( $text ) {
		self::$messages[] = $text;
	}

	/**
	 * Add an error
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$errors[] = $text;
	}

	/**
	 * Output messages + errors
	 */
	public static function show_messages() {
		if ( sizeof( self::$errors ) > 0 ) {
			foreach ( self::$errors as $error )
				echo '<div id="message" class="error fade"><p><strong>' . esc_html( $error ) . '</strong></p></div>';
		} elseif ( sizeof( self::$messages ) > 0 ) {
			foreach ( self::$messages as $message )
				echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
		}
	}

	/**
	 * Settings page.
	 *
	 * Handles the display of the main wpclubmanager settings page in admin.
	 *
	 * @access public
	 * @return void
	 */
	public static function output() {
		
	    global $current_section, $current_tab;

	    do_action( 'wpclubmanager_settings_start' );

	    wp_enqueue_script( 'wpclubmanager_settings', WPCM()->plugin_url() . '/assets/js/admin/settings.js', array( 'jquery', 'jquery-ui-sortable', 'iris' ), WPCM()->version, true );

		wp_localize_script( 'wpclubmanager_settings', 'wpclubmanager_settings_params', array(
			'i18n_nav_warning' => __( 'The changes you made will be lost if you navigate away from this page.', 'wp-club-manager' ),
			'wpcm_nonce' => wp_create_nonce('wpcm-nonce')
		) );

		// Include settings pages
		self::get_settings_pages();

		// Get current tab
		$current_tab     = empty( $_GET['tab'] ) ? 'general' : sanitize_title( $_GET['tab'] );

	    // Save settings if data has been posted
	    if ( ! empty( $_POST ) )
	    	self::save();

	    // Add any posted messages
	    if ( ! empty( $_GET['wpcm_error'] ) )
	    	self::add_error( stripslashes( $_GET['wpcm_error'] ) );

	     if ( ! empty( $_GET['wpcm_message'] ) )
	    	self::add_message( stripslashes( $_GET['wpcm_message'] ) );

	    self::show_messages();

	    // Get tabs for the settings page
	    $tabs = apply_filters( 'wpclubmanager_settings_tabs_array', array() );

	    include 'views/html-admin-settings.php';
	}

	/**
	 * Get a setting from the settings API.
	 *
	 * @param mixed $option
	 * @return string
	 */
	public static function get_option( $option_name, $default = '' ) {
		// Array value
		if ( strstr( $option_name, '[' ) ) {

			parse_str( $option_name, $option_array );

			// Option name is first key
			$option_name = current( array_keys( $option_array ) );

			// Get value
			$option_values = get_option( $option_name, '' );

			$key = key( $option_array[ $option_name ] );

			if ( isset( $option_values[ $key ] ) )
				$option_value = $option_values[ $key ];
			else
				$option_value = null;

		// Single value
		} else {
			$option_value = get_option( $option_name, null );
		}

		if ( is_array( $option_value ) )
			$option_value = array_map( 'stripslashes', $option_value );
		elseif ( ! is_null( $option_value ) )
			$option_value = stripslashes( $option_value );

		return $option_value === null ? $default : $option_value;
	}

	/**
	 * Output admin fields.
	 *
	 * Loops though the wpclubmanager options array and outputs each field.
	 *
	 * @access public
	 * @param array $options Opens array to output
	 */
	public static function output_fields( $options ) {
	    foreach ( $options as $value ) {
	    	if ( ! isset( $value['type'] ) ) continue;
	    	if ( ! isset( $value['id'] ) ) $value['id'] = '';
	    	if ( ! isset( $value['title'] ) ) $value['title'] = isset( $value['name'] ) ? $value['name'] : '';
	    	if ( ! isset( $value['class'] ) ) $value['class'] = '';
	    	if ( ! isset( $value['css'] ) ) $value['css'] = '';
	    	if ( ! isset( $value['default'] ) ) $value['default'] = '';
	    	if ( ! isset( $value['desc'] ) ) $value['desc'] = '';
	    	if ( ! isset( $value['desc_tip'] ) ) $value['desc_tip'] = false;
	    	if ( ! isset( $value['options'] ) ) $value['options'] = '';

	    	// Custom attribute handling
			$custom_attributes = array();

			if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) )
				foreach ( $value['custom_attributes'] as $attribute => $attribute_value )
					$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';

			// Description handling
			if ( ! empty( $value['desc'] ) ) {
				$description = $value['desc'];
			} else {
				$description = '';
			}

			if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ) ) ) {
				$description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
			} elseif ( $description && in_array( $value['type'], array( 'checkbox' ) ) ) {
				$description =  wp_kses_post( $description );
			} elseif ( $description ) {
				$description = '<span class="description">' . wp_kses_post( $description ) . '</span>';
			}

			// Switch based on type
	        switch( $value['type'] ) {

	        	// Section Titles
	            case 'title':
	            	if ( ! empty( $value['title'] ) ) {
	            		echo '<div class="stuffbox"><h3>' . esc_html( $value['title'] ) . '</h3><div class="inside">';
	            	}
	            	if ( ! empty( $value['desc'] ) ) {
	            		echo wpautop( wptexturize( wp_kses_post( $value['desc'] ) ) );
	            	}
	            	echo '<table class="form-table">'. "\n\n";
	            	if ( ! empty( $value['id'] ) ) {
	            		do_action( 'wpclubmanager_settings_' . sanitize_title( $value['id'] ) );
	            	}
	            break;

	            // Section Ends
	            case 'sectionend':
	            	if ( ! empty( $value['id'] ) ) {
	            		do_action( 'wpclubmanager_settings_' . sanitize_title( $value['id'] ) . '_end' );
	            	}
	            	echo '</table></div></div>';
	            	if ( ! empty( $value['id'] ) ) {
	            		do_action( 'wpclubmanager_settings_' . sanitize_title( $value['id'] ) . '_after' );
	            	}
	            break;

	            // Standard text inputs and subtypes like 'number'
	            case 'text':
	            case 'email':
	            case 'number':
	            case 'color' :
	            case 'password' :

	            	$type 			= $value['type'];
	            	$class 			= '';
	            	$option_value 	= self::get_option( $value['id'], $value['default'] ); ?>

	            	<tr>
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
						
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
	                    	<input
	                    		name="<?php echo esc_attr( $value['id'] ); ?>"
	                    		id="<?php echo esc_attr( $value['id'] ); ?>"
	                    		type="<?php echo esc_attr( $type ); ?>"
	                    		style="<?php echo esc_attr( $value['css'] ); ?>"
	                    		value="<?php echo esc_attr( $option_value ); ?>"
	                    		class="<?php echo esc_attr( $value['class'] ); ?>"
	                    		<?php echo implode( ' ', $custom_attributes ); ?>
	                    		/> <?php echo $description; ?>
	                    </td>
	                </tr><?php
	            break;

	            // Textarea
	            case 'textarea':

	            	$option_value 	= self::get_option( $value['id'], $value['default'] );

	            	?><tr>
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
						
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
	                    	<?php echo $description; ?>

	                        <textarea
	                        	name="<?php echo esc_attr( $value['id'] ); ?>"
	                        	id="<?php echo esc_attr( $value['id'] ); ?>"
	                        	style="<?php echo esc_attr( $value['css'] ); ?>"
	                        	class="<?php echo esc_attr( $value['class'] ); ?>"
	                        	<?php echo implode( ' ', $custom_attributes ); ?>
	                        	><?php echo esc_textarea( $option_value );  ?></textarea>
	                    </td>
	                </tr><?php
	            break;

	            // Select boxes
	            case 'select' :
	            case 'multiselect' :

	            	$option_value 	= self::get_option( $value['id'], $value['default'] );

	            	?><tr>
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
	                    	<select
	                    		name="<?php echo esc_attr( $value['id'] ); ?><?php if ( $value['type'] == 'multiselect' ) echo '[]'; ?>"
	                    		id="<?php echo esc_attr( $value['id'] ); ?>"
	                    		style="<?php echo esc_attr( $value['css'] ); ?>"
	                    		class="<?php echo esc_attr( $value['class'] ); ?>"
	                    		<?php echo implode( ' ', $custom_attributes ); ?>
	                    		<?php if ( $value['type'] == 'multiselect' ) echo 'multiple="multiple"'; ?>
	                    		>
		                    	<?php
			                        foreach ( $value['options'] as $key => $val ) {
			                        	?>
			                        	<option value="<?php echo esc_attr( $key ); ?>" <?php

				                        	if ( is_array( $option_value ) )
				                        		selected( in_array( $key, $option_value ), true );
				                        	else
				                        		selected( $option_value, $key );

			                        	?>><?php echo $val ?></option>
			                        	<?php
			                        }
			                    ?>
	                       </select> <?php echo $description; ?>
	                    </td>
	                </tr><?php
	            break;

	            // Radio inputs
	            case 'radio' :

	            	$option_value 	= self::get_option( $value['id'], $value['default'] );

	            	?><tr>
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>						
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
	                    	<fieldset>
	                    		<?php echo $description; ?>
	                    		<ul>
	                    		<?php
	                    			foreach ( $value['options'] as $key => $val ) {
			                        	?>
			                        	<li>
			                        		<label><input
				                        		name="<?php echo esc_attr( $value['id'] ); ?>"
				                        		value="<?php echo $key; ?>"
				                        		type="radio"
					                    		style="<?php echo esc_attr( $value['css'] ); ?>"
					                    		class="<?php echo esc_attr( $value['class'] ); ?>"
					                    		<?php echo implode( ' ', $custom_attributes ); ?>
					                    		<?php checked( $key, $option_value ); ?>
				                        		/> <?php echo $val ?></label>
			                        	</li>
			                        	<?php
			                        }
	                    		?>
	                    		</ul>
	                    	</fieldset>
	                    </td>
	                </tr>
	                <?php
	            break;

	            // Checkbox input
	            case 'checkbox' :

					$option_value    = self::get_option( $value['id'], $value['default'] );
					$visbility_class = array();

	            	if ( ! isset( $value['hide_if_checked'] ) ) {
	            		$value['hide_if_checked'] = false;
	            	}
	            	if ( ! isset( $value['show_if_checked'] ) ) {
	            		$value['show_if_checked'] = false;
	            	}
	            	if ( $value['hide_if_checked'] == 'yes' || $value['show_if_checked'] == 'yes' ) {
	            		$visbility_class[] = 'hidden_option';
	            	}
	            	if ( $value['hide_if_checked'] == 'option' ) {
	            		$visbility_class[] = 'hide_options_if_checked';
	            	}
	            	if ( $value['show_if_checked'] == 'option' ) {
	            		$visbility_class[] = 'show_options_if_checked';
	            	}

	            	if ( ! isset( $value['checkboxgroup'] ) || 'start' == $value['checkboxgroup'] ) {
	            		?>
		            		<tr class="<?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
								<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?></th>
								<td class="forminp forminp-checkbox">
									<fieldset>
						<?php
	            	} else {
	            		?>
		            		<fieldset class="<?php echo esc_attr( implode( ' ', $visbility_class ) ); ?>">
	            		<?php
	            	}

	            	if ( ! empty( $value['title'] ) ) {
	            		?>
	            			<legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ) ?></span></legend>
	            		<?php
	            	}

	            	?>
						<label for="<?php echo $value['id'] ?>">
							<input
								name="<?php echo esc_attr( $value['id'] ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								type="checkbox"
								value="1"
								<?php checked( $option_value, 'yes'); ?>
								<?php echo implode( ' ', $custom_attributes ); ?>
							/> <?php echo $description ?>
						</label>
					<?php

					if ( ! isset( $value['checkboxgroup'] ) || 'end' == $value['checkboxgroup'] ) {
									?>
									</fieldset>
								</td>
							</tr>
						<?php
					} else {
						?>
							</fieldset>
						<?php
					}
	            break;

	            // Image width settings
	            case 'image_width' :

	            	$width 	= self::get_option( $value['id'] . '[width]', $value['default']['width'] );
	            	$height = self::get_option( $value['id'] . '[height]', $value['default']['height'] );
	            	$crop 	= checked( 1, self::get_option( $value['id'] . '[crop]', $value['default']['crop'] ), false );

	            	?><tr>
						<th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?></th>
	                    <td class="forminp image_width_settings">

	                    	<input name="<?php echo esc_attr( $value['id'] ); ?>[width]" id="<?php echo esc_attr( $value['id'] ); ?>-width" type="text" size="3" value="<?php echo $width; ?>" /> &times; <input name="<?php echo esc_attr( $value['id'] ); ?>[height]" id="<?php echo esc_attr( $value['id'] ); ?>-height" type="text" size="3" value="<?php echo $height; ?>" />px

	                    	<label><input name="<?php echo esc_attr( $value['id'] ); ?>[crop]" id="<?php echo esc_attr( $value['id'] ); ?>-crop" type="checkbox" <?php echo $crop; ?> /> <?php _e( 'Hard Crop?', 'wp-club-manager' ); ?></label>
	                    	</td>
	                </tr><?php
	            break;

	            // Single page selects
	            case 'default_club' :

	            	$args = array(  'name' => $value['id'],
									'id' => $value['id'],
									'post_type' => 'wpcm_club',
									'limit' => -1,
									'show_option_none' => __( 'None' ),
									'class'				=> $value['class'],
									'echo' 				=> false,
									'selected' => absint( self::get_option( $value['id'] ) )
									);

	            	if( isset( $value['args'] ) )
	            		$args = wp_parse_args( $value['args'], $args );

	            	?><tr>
	                    <th scope="row" class="titledesc"><label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ) ?></label></th>
	                    <td class="forminp">
				        	<?php wpcm_dropdown_posts( $args ); ?> <?php echo $description; ?>
				        </td>
	               	</tr><?php
	            break;



	            // Single page selects
	            case 'single_select_page' :

	            	$args = array( 'name'				=> $value['id'],
	            				   'id'					=> $value['id'],
	            				   'sort_column' 		=> 'menu_order',
	            				   'sort_order'			=> 'ASC',
	            				   'show_option_none' 	=> ' ',
	            				   'class'				=> $value['class'],
	            				   'echo' 				=> false,
	            				   'selected'			=> absint( self::get_option( $value['id'] ) )
	            				   );

	            	if( isset( $value['args'] ) )
	            		$args = wp_parse_args( $value['args'], $args );

	            	?><tr class="single_select_page">
	                    <th scope="row" class="titledesc"><?php echo esc_html( $value['title'] ) ?></th>
	                    <td class="forminp">
				        	<?php echo str_replace(' id=', " data-placeholder='" . __( 'Select a page&hellip;', 'wp-club-manager' ) .  "' style='" . $value['css'] . "' class='" . $value['class'] . "' id=", wp_dropdown_pages( $args ) ); ?> <?php echo $description; ?>
				        </td>
	               	</tr><?php
	            break;

	            // Single country selects
	            case 'single_select_country' :
					$country_setting = (string) self::get_option( $value['id'] );
					$countries       = WPCM()->countries->countries;

	            	if ( strstr( $country_setting, ':' ) ) {
						$country_setting = explode( ':', $country_setting );
						$country         = current( $country_setting );
	            	} else {
						$country = $country_setting;
	            	}
	            	?><tr>
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
						</th>
	                    <td class="forminp"><select name="<?php echo esc_attr( $value['id'] ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>" data-placeholder="<?php _e( 'Choose a country&hellip;', 'wp-club-manager' ); ?>" title="Country" class="chosen_select">
				        	<?php WPCM()->countries->country_dropdown_options( $country ); ?>
				        </select> <?php echo $description; ?>
	               		</td>
	               	</tr><?php
	            break;

	            case 'license_key':

	    			$option_value = self::get_option( $value['id'], $value['default'] ); ?>

					<div class="wpcm-license-keys">
						<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
	                    <input name="<?php echo esc_attr( $value['id'] ); ?>" id="<?php echo esc_attr( $value['id'] ); ?>" value="<?php echo esc_attr( $option_value ); ?>"
	                    		class="regular-text" />
                    	<?php
                    	if ( 'valid' == get_option( $value['options']['is_valid_license_option'] ) ) { ?>
							<input type="submit" class="button-secondary" name="<?php echo esc_attr( $value['id'] ); ?>_deactivate" value="<?php _e( 'Deactivate License', 'wp-club-manager' ); ?>"/>
						<?php } ?>
	                </div><?php

	                wp_nonce_field( $value['id'] . '-nonce', $value['id'] . '-nonce' );

				break;
				
				case 'standings_columns':
					
					$option_value 	= self::get_option( $value['id'], $value['default'] );
					if( !$option_value ) {
						$sport = get_option('wpcm_sport');
						$data = wpcm_get_sport_presets();
						$cols = $data[$sport]['standings_columns'];
						foreach( $cols as $col => $val ) {
								$columns[] = $col;
						}
						$option_value = implode( $columns, ',' );
					} else {
						$columns = explode( $option_value, ',' );
					}
					$stats = wpcm_get_preset_labels( 'standings' );
					$stats_names = wpcm_get_preset_labels( 'standings', 'name' ); ?>
					
					<tr>
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
							<div class="wpcm-multiple-select-wrap">
								<select name="wpcm_table_stats_display[]" id="<?php echo esc_attr( $value['id'] ); ?>" class="wpcm-chosen-multiple" multiple>
									<?php
										foreach ( $stats as $key => $val ) {
											?>
											<option value="<?php echo esc_attr( $key ); ?>" <?php if ( in_array( $key, $columns ) ) echo 'selected'; ?>><?php echo $val ?></option>
											<?php
										}
									?>
								</select>
							</div>
							<input id="input-order" type="hidden" value="<?php echo $option_value; ?>" name="<?php echo esc_attr( $value['id'] ); ?>" />
	                    </td>
	                </tr>
					<?php

				break;

				case 'cache_button': ?>

					<tr>
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>							
						</th>
	                    <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
							<button name="wpcm-submit" id="wpcm_submit" class="button secondary"/><?php _e('Clear plugin cache', 'wp-club-manager'); ?></button>
							<img src="<?php echo admin_url('/images/wpspin_light.gif'); ?>" class="waiting" id="wpcm_loading" style="display:none;"/>
						</td>
	                </tr>
				<?php
				break;

	            // Default: run an action
	            default:
	            	do_action( 'wpclubmanager_admin_field_' . $value['type'], $value );
	            break;
	    	}
		}
	}

	/**
	 * Save admin fields.
	 *
	 * Loops though the wpclubmanager options array and outputs each field.
	 *
	 * @access public
	 * @param array $options Opens array to output
	 * @return bool
	 */
	public static function save_fields( $options ) {
	    if ( empty( $_POST ) )
	    	return false;

	    // Options to update will be stored here
	    $update_options = array();

	    // Loop options and get values to save
	    foreach ( $options as $value ) {

	    	if ( ! isset( $value['id'] ) )
	    		continue;

	    	$type = isset( $value['type'] ) ? sanitize_title( $value['type'] ) : '';

	    	// Get the option name
	    	$option_value = null;

	    	switch ( $type ) {

		    	// Standard types
		    	case "checkbox" :

		    		if ( isset( $_POST[ $value['id'] ] ) ) {
		    			$option_value = 'yes';
		            } else {
		            	$option_value = 'no';
		            }

		    	break;

		    	case "textarea" :

			    	if ( isset( $_POST[$value['id']] ) ) {
			    		$option_value = wp_kses_post( trim( stripslashes( $_POST[ $value['id'] ] ) ) );
		            } else {
		                $option_value = '';
		            }

		    	break;

		    	case "text" :
		    	case 'email':
	            case 'number':
		    	case "select" :
		    	case "color" :
	            case 'password' :
	            case 'default_club' :
		    	case "single_select_page" :
		    	case "single_select_country" :
		    	case 'radio' :
		    	case 'license_key' :

		    		if ( isset( $_POST[$value['id']] ) ) {
			        	$option_value = wpcm_clean( stripslashes( $_POST[ $value['id'] ] ) );
			        } else {
			            $option_value = '';
			        }

		    	break;

		    	// Special types
				case "multiselect" :
				case "standings_columns" :
				
					$option_value = wpcm_clean( stripslashes( $_POST[ $value['id'] ] ) );

				break;

		    	case "image_width" :

			    	if ( isset( $_POST[$value['id'] ]['width'] ) ) {

		              	$update_options[ $value['id'] ]['width']  = wpcm_clean( stripslashes( $_POST[ $value['id'] ]['width'] ) );
		              	$update_options[ $value['id'] ]['height'] = wpcm_clean( stripslashes( $_POST[ $value['id'] ]['height'] ) );

						if ( isset( $_POST[ $value['id'] ]['crop'] ) )
							$update_options[ $value['id'] ]['crop'] = 1;
						else
							$update_options[ $value['id'] ]['crop'] = 0;

		            } else {
		            	$update_options[ $value['id'] ]['width'] 	= $value['default']['width'];
		            	$update_options[ $value['id'] ]['height'] 	= $value['default']['height'];
		            	$update_options[ $value['id'] ]['crop'] 	= $value['default']['crop'];
		            }

		    	break;

		    	// Custom handling
		    	default :

		    		do_action( 'wpclubmanager_update_option_' . $type, $value );

		    	break;

	    	}

	    	if ( ! is_null( $option_value ) ) {
		    	// Check if option is an array
				if ( strstr( $value['id'], '[' ) ) {

					parse_str( $value['id'], $option_array );

		    		// Option name is first key
		    		$option_name = current( array_keys( $option_array ) );

		    		// Get old option value
		    		if ( ! isset( $update_options[ $option_name ] ) )
		    			 $update_options[ $option_name ] = get_option( $option_name, array() );

		    		if ( ! is_array( $update_options[ $option_name ] ) )
		    			$update_options[ $option_name ] = array();

		    		// Set keys and value
		    		$key = key( $option_array[ $option_name ] );

		    		$update_options[ $option_name ][ $key ] = $option_value;

				// Single value
				} else {
					$update_options[ $value['id'] ] = $option_value;
				}
			}

	    	// Custom handling
	    	do_action( 'wpclubmanager_update_option', $value );
	    }

	    // Now save the options
	    foreach( $update_options as $name => $value )
	    	update_option( $name, $value );

	    return true;
	}

	/**
	 * Configure sport
	 *
	 * @access public
	 * @return void
	 */
	public static function configure_sport( $sport ) {
		// Get array of taxonomies to insert
		$term_groups = wpcm_array_value( $sport, 'terms', array() );

		foreach( $term_groups as $taxonomy => $terms ):
			// Find empty terms and destroy
			$allterms = get_terms( $taxonomy, 'hide_empty=0' );

			foreach( $allterms as $term ):
				if ( $term->count == 0 )
					wp_delete_term( $term->term_id, $taxonomy );
			endforeach;

			// Insert terms
			foreach( $terms as $term ):
				wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
			endforeach;
		endforeach;

		// Get array of taxonomies to insert
		$stats_labels = wpcm_array_value( $sport, 'stats_labels' );

		foreach( $stats_labels as $key => $value ):
			update_option( 'wpcm_show_stats_' . $key, 'yes' );
		endforeach;

    	update_option( 'wpcm_primary_result', 0 );
	}
}

endif;