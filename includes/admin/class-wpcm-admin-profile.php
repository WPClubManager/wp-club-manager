<?php
/**
 * Add extra profile fields for users in admin
 *
 * @author   Clubpress
 * @category Admin
 * @package  WPClubManager/Admin
 * @version  1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Profile' ) ) :

/**
 * WPCM_Admin_Profile Class.
 */
class WPCM_Admin_Profile {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'show_user_profile', array( $this, 'add_user_meta_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'add_user_meta_fields' ) );

		add_action( 'personal_options_update', array( $this, 'save_user_meta_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_user_meta_fields' ) );
	}

	/**
	 * Get Address Fields for the edit user pages.
	 *
	 * @return array Fields to display which are filtered through wpclubmanager_user_meta_fields before being returned
	 */
	public function get_user_meta_fields() {
		$show_fields = apply_filters('wpclubmanager_user_meta_fields', array(
			'contact' => array(
				'title' => __( 'Player Details', 'wp-club-manager' ),
				'fields' => array(
					'players_phone' => array(
						'label'       => __( 'Telephone', 'wp-club-manager' ),
						'description' => ''
					),
					'twitter_username' => array(
						'label'       => __( 'Twitter Username', 'wp-club-manager' ),
						'description' => ''
					),
				)
			)
		) );
		return $show_fields;
	}

	/**
	 * Show Address Fields on edit user pages.
	 *
	 * @param WP_User $user
	 */
	public function add_user_meta_fields( $user ) {
		if ( ! current_user_can( 'manage_wpclubmanager' ) ) {
			return;
		}

		$show_fields = $this->get_user_meta_fields();

		foreach ( $show_fields as $fieldset ) :
			?>
			<h3><?php echo $fieldset['title']; ?></h3>
			<table class="form-table">
				<?php
				foreach ( $fieldset['fields'] as $key => $field ) :
					?>
					<tr>
						<th><label for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field['label'] ); ?></label></th>
						<td>
							<?php if ( ! empty( $field['type'] ) && 'select' == $field['type'] ) : ?>
								<select name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" class="<?php echo ( ! empty( $field['class'] ) ? $field['class'] : '' ); ?>" style="width: 25em;">
									<?php
										$selected = esc_attr( get_user_meta( $user->ID, $key, true ) );
										foreach ( $field['options'] as $option_key => $option_value ) : ?>
										<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $selected, $option_key, true ); ?>><?php echo esc_attr( $option_value ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php else : ?>
							<input type="text" name="<?php echo esc_attr( $key ); ?>" id="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( get_user_meta( $user->ID, $key, true ) ); ?>" class="<?php echo ( ! empty( $field['class'] ) ? $field['class'] : 'regular-text' ); ?>" />
							<?php endif; ?>
							<br/>
							<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
						</td>
					</tr>
					<?php
				endforeach;
				?>
			</table>
			<?php
		endforeach;
	}

	/**
	 * Save Address Fields on edit user pages.
	 *
	 * @param int $user_id User ID of the user being saved
	 */
	public function save_user_meta_fields( $user_id ) {
		$save_fields = $this->get_user_meta_fields();

		foreach ( $save_fields as $fieldset ) {

			foreach ( $fieldset['fields'] as $key => $field ) {

				if ( isset( $_POST[ $key ] ) ) {
					update_user_meta( $user_id, $key, wpcm_clean( $_POST[ $key ] ) );
				}
			}
		}
	}
}

endif;

return new WPCM_Admin_Profile();