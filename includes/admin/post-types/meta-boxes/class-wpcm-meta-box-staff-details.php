<?php
/**
 * Staff Details
 *
 * Displays the staff details box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Staff_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		global $post, $wp_locale;

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$job_id = null;
		$jobs = get_the_terms( $post->ID, 'wpcm_jobs' );
		$job_ids = array();
		if ( $jobs ):
			foreach ( $jobs as $job ):
				$job_ids[] = $job->term_id;
			endforeach;
		endif;

		$dob = get_post_meta( $post->ID, 'wpcm_dob', true );

		if ( empty( $dob ) ) {
			$dob = '1988-01-01';
		}

		$dob_day = substr( $dob, 8, 2 );
		$dob_month = substr( $dob, 5, 2 );
		$dob_year = substr( $dob, 0, 4 );

		$natl = get_post_meta( $post->ID, 'wpcm_natl', true );
		$time_adj = current_time( 'timestamp' ); ?>
		
		<p>
			<label><?php _e( 'Job Title', 'wp-club-manager' ); ?></label>
			<?php
				$args = array(
					'taxonomy' => 'wpcm_jobs',
					'name' => 'tax_input[wpcm_jobs][]',
					'selected' => $job_ids,
					'values' => 'term_id',
					'placeholder' => sprintf( __( 'Choose %s', 'wp-club-manager' ), __( 'jobs', 'wp-club-manager' ) ),
					'class' => '',
					'attribute' => 'multiple',
					'chosen' => true,
				);
				wpcm_dropdown_taxonomies( $args );
			?>
		</p>
		<?php
		wpclubmanager_wp_text_input( array( 'id' => '_wpcm_staff_email', 'label' => __( 'Email Address', 'wp-club-manager' ), 'class' => 'regular-text' ) );

		wpclubmanager_wp_text_input( array( 'id' => '_wpcm_staff_phone', 'label' => __( 'Contact Number', 'wp-club-manager' ), 'class' => 'regular-text' ) );
		?>
		<p>
			<label><?php _e( 'Date of Birth', 'wp-club-manager' ); ?></label>
			<select name="wpcm_dob_day" id="wpcm_dob_day" class="chosen_select_dob">
				<?php for ( $i = 1; $i < 32; $i = $i +1 ): ?>
					<option value="<?php echo zeroise($i, 2); ?>"<?php echo ($i == $dob_day ? ' selected="selected"' : ''); ?>>
						<?php echo zeroise($i, 2); ?>
					</option>
				<?php endfor; ?>
			</select>
			<select name="wpcm_dob_month" id="wpcm_dob_month" class="chosen_select_dob">
				<?php for ( $i = 1; $i < 13; $i = $i +1 ): ?>
					<option value="<?php echo zeroise($i, 2); ?>"<?php echo ($i == $dob_month ? ' selected="selected"' : ''); ?>>
						<?php echo zeroise($i, 2); ?>-<?php echo $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ); ?>
					</option>
				<?php endfor; ?>
			</select>
			<input type="text" name="wpcm_dob_year" id="wpcm_dob_year" value="<?php echo $dob_year; ?>" size="4" maxlength="4" autocomplete="off" />
		</p><?php
		
		wpclubmanager_wp_country_select( array( 'id' => 'wpcm_natl', 'label' => __( 'Nationality', 'wp-club-manager' ) ) );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if( isset( $_POST['_wpcm_staff_email'] ) ) {
			update_post_meta( $post_id, '_wpcm_staff_email', $_POST['_wpcm_staff_email'] );
		}
		if( isset( $_POST['_wpcm_staff_phone'] ) ) {
			update_post_meta( $post_id, '_wpcm_staff_phone', $_POST['_wpcm_staff_phone'] );
		}
		if( isset( $_POST['wpcm_natl'] ) ) {
			update_post_meta( $post_id, 'wpcm_natl', $_POST['wpcm_natl'] );
		}
		$dob_year = substr( zeroise( (int) $_POST['wpcm_dob_year'], 4 ), 0, 4 );
		$dob_month = substr( zeroise( (int) $_POST['wpcm_dob_month'], 2 ), 0, 2 );
		$dob_day = substr( zeroise( (int) $_POST['wpcm_dob_day'], 2 ), 0, 2 );
		update_post_meta( $post_id, 'wpcm_dob', $dob_year . '-' . $dob_month. '-' . $dob_day );
	}
}