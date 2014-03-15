<?php
/**
 * Staff Details
 *
 * Displays the staff details box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Staff_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		global $post, $wp_locale;

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		//$selected_club = get_post_meta( $post->ID, 'wpcm_club', true );
		$jobs_id = null;
		$jobs = get_the_terms( $post->ID, 'wpcm_jobs' );

		if ( !empty( $jobs ) ) {
			$jobs_id = $jobs[0]->term_id;
		}

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
			<label><?php _e( 'Job Title', 'wpclubmanager' ); ?></label>
			<?php
				wp_dropdown_categories( array(
					'show_option_none' => __( 'None' ),
					'orderby' => 'title',
					'hide_empty' => false,
					'taxonomy' => 'wpcm_jobs',
					'selected' => $jobs_id,
					'name' => 'wpcm_jobs',
					'class' => 'chosen_select',
				) );
			?>
		</p>
		<p>
			<label><?php _e( 'Date of Birth', 'wpclubmanager' ); ?></label>
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
		
		wpclubmanager_wp_country_select( array( 'id' => 'wpcm_natl', 'label' => __( 'Nationality', 'wpclubmanager' ) ) );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		$dob_year = substr( zeroise( (int) $_POST['wpcm_dob_year'], 4 ), 0, 4 );
		$dob_month = substr( zeroise( (int) $_POST['wpcm_dob_month'], 2 ), 0, 2 );
		$dob_day = substr( zeroise( (int) $_POST['wpcm_dob_day'], 2 ), 0, 2 );
		
		//update_post_meta( $post->ID, 'wpcm_club', $_POST['wpcm_club'] );
		wp_set_post_terms( $post_id, $_POST['wpcm_jobs'], 'wpcm_jobs' );	
		update_post_meta( $post_id, 'wpcm_dob', $dob_year . '-' . $dob_month. '-' . $dob_day );
		update_post_meta( $post_id, 'wpcm_natl', $_POST['wpcm_natl'] );
	}
}