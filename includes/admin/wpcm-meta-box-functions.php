<?php
/**
* WPClubManager Meta Box Functions
*
* @author      ClubPress
* @category    Core
* @package     WPClubManager/Admin/Functions
* @version     1.4.0
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Output a text input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wpclubmanager_wp_text_input( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';

	echo '<p class="' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" /> ';

	if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WPCM()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

	}
	echo '</p>';
}

/**
 * Output a hidden input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wpclubmanager_wp_hidden_input( $field ) {
	global $thepostid, $post;

	$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['value'] = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['class'] = isset( $field['class'] ) ? $field['class'] : '';

	echo '<input type="hidden" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) .  '" /> ';
}

/**
 * Output a textarea input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wpclubmanager_wp_textarea_input( $field ) {
	global $thepostid, $post;

	$thepostid 				= empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder'] 	= isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value'] 		= isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['rows'] 	= isset( $field['rows'] ) ? $field['rows'] : '4';
	$field['cols'] 	= isset( $field['cols'] ) ? $field['cols'] : '40';

	echo '<p class="' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><textarea class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" rows="' . esc_attr( $field['rows'] ) . '" cols="' . esc_attr( $field['cols'] ) . '">' . esc_textarea( $field['value'] ) . '</textarea> ';

	if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WPCM()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

	}
	echo '</p>';
}

/**
 * Output a checkbox input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wpclubmanager_wp_checkbox( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class']         = isset( $field['class'] ) ? $field['class'] : 'checkbox';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['cbvalue']       = isset( $field['cbvalue'] ) ? $field['cbvalue'] : 'yes';
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];

	echo '<p class="' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><input type="checkbox" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['cbvalue'] ) . '" ' . checked( $field['value'], $field['cbvalue'], false ) . ' /> ';

	if ( ! empty( $field['description'] ) ) echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';

	echo '</p>';
}

/**
 * Output a select input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wpclubmanager_wp_select( $field ) {
	global $thepostid, $post;

	$thepostid 				= empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'chosen_select';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value'] 		= isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );

	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '" style="width:150px;display:inline-block">' . wp_kses_post( $field['label'] ) . '</label><select id="' . esc_attr( $field['id'] ) . '" name="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '">';

	foreach ( $field['options'] as $key => $value ) {

		echo '<option value="' . esc_attr( $value ) . '" ' . selected( esc_attr( $field['value'] ), esc_attr( $value ), false ) . '>' . esc_html( $value ) . '</option>';

	}

	echo '</select> ';

	if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WPCM()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

	}
	echo '</p>';
}

/**
 * Output a radio input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wpclubmanager_wp_radio( $field ) {
	global $thepostid, $post;

	$thepostid 				= empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'select short';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value'] 		= isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];

	echo '<fieldset class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><legend>' . wp_kses_post( $field['label'] ) . '</legend><ul class="wc-radios">';

    foreach ( $field['options'] as $key => $value ) {

		echo '<li><label><input
        		name="' . esc_attr( $field['name'] ) . '"
        		value="' . esc_attr( $key ) . '"
        		type="radio"
        		class="' . esc_attr( $field['class'] ) . '"
        		' . checked( esc_attr( $field['value'] ), esc_attr( $key ), false ) . '
        		/> ' . esc_html( $value ) . '</label>
    	</li>';
	}
    echo '</ul>';

    if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WPCM()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

	}

    echo '</fieldset>';
}

/**
 * Output a color picker.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wpclubmanager_wp_color_input( $field ) {
	global $thepostid, $post;

	$thepostid              = empty( $thepostid ) ? $post->ID : $thepostid;
	$field['placeholder']   = isset( $field['placeholder'] ) ? $field['placeholder'] : '';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value']         = isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	$field['name']          = isset( $field['name'] ) ? $field['name'] : $field['id'];
	$field['type']          = isset( $field['type'] ) ? $field['type'] : 'text';

	echo '<p class="' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label><span class="colorpickpreview" style="background: ' . esc_attr( $field['value'] ) . ';"></span><input type="' . esc_attr( $field['type'] ) . '" class="colorpick" name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" /></p>';
}

/**
 * Output a radio input box.
 *
 * @access public
 * @param array $field
 * @return void
 */
function wpclubmanager_wp_country_select( $field ) {

	global $thepostid, $post;

	$country_setting = get_post_meta( $post->ID, 'wpcm_natl', true);

	if( $country_setting ) {
		$country = $country_setting;
	} else {
		$country = get_option('wpcm_default_country');
	}

	$countries = WPCM()->countries->countries;

	$thepostid 				= empty( $thepostid ) ? $post->ID : $thepostid;
	$field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'chosen_select';
	$field['wrapper_class'] = isset( $field['wrapper_class'] ) ? $field['wrapper_class'] : '';
	$field['value'] 		= isset( $field['value'] ) ? $field['value'] : get_post_meta( $thepostid, $field['id'], true );
	
	echo '<p class="form-field ' . esc_attr( $field['id'] ) . '_field ' . esc_attr( $field['wrapper_class'] ) . '"><label for="' . esc_attr( $field['id'] ) . '">' . wp_kses_post( $field['label'] ) . '</label>';

	echo '<select name="' . esc_attr( $field['id'] ) . '" data-placeholder="' . __( 'Choose a country&hellip;', 'wp-club-manager' ) . '" title="Country" class="' . esc_attr( $field['class'] ) . '">';

	WPCM()->countries->country_dropdown_options( $country = $country );

	echo '</select>';
				        
	if ( ! empty( $field['description'] ) ) {

		if ( isset( $field['desc_tip'] ) && false !== $field['desc_tip'] ) {
			echo '<img class="help_tip" data-tip="' . esc_attr( $field['description'] ) . '" src="' . esc_url( WPCM()->plugin_url() ) . '/assets/images/help.png" height="16" width="16" />';
		} else {
			echo '<span class="description">' . wp_kses_post( $field['description'] ) . '</span>';
		}

	}

	echo '</p>';
}