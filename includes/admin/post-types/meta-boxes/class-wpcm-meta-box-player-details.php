<?php
/**
 * Player Details
 *
 * Displays the player details box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Player_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		global $post, $wp_locale;

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );
		
		$number = get_post_meta( $post->ID, 'wpcm_number', true );
		$position_id = null;
		$positions = get_the_terms( $post->ID, 'wpcm_position' );
		$position_ids = array();
		if ( $positions ):
			foreach ( $positions as $position ):
				$position_ids[] = $position->term_id;
			endforeach;
		endif;

		$dob = get_post_meta( $post->ID, 'wpcm_dob', true );

		if ( empty( $dob ) ) $dob = '1988-01-01';
		$dob_day = substr( $dob, 8, 2 );
		$dob_month = substr( $dob, 5, 2 );
		$dob_year = substr( $dob, 0, 4 );

		$height = get_post_meta( $post->ID, 'wpcm_height', true );
		$weight = get_post_meta( $post->ID, 'wpcm_weight', true );

		$natl = get_post_meta( $post->ID, 'wpcm_natl', true );
		$hometown = get_post_meta( $post->ID, 'wpcm_hometown', true );
		$prevclubs = get_post_meta( $post->ID, 'wpcm_prevclubs', true );
		$time_adj = current_time( 'timestamp' );
		
		wpclubmanager_wp_text_input( array( 'id' => 'wpcm_number', 'label' => __( 'Squad Number', 'wp-club-manager' ), 'class' => 'small-text' ) ); ?>
		
		<p>
			<label><?php _e( 'Position', 'wp-club-manager' ); ?></label>
			<?php
			$args = array(
				'taxonomy' => 'wpcm_position',
				'name' => 'tax_input[wpcm_position][]',
				'selected' => $position_ids,
				'values' => 'term_id',
				'placeholder' => sprintf( __( 'Choose %s', 'wp-club-manager' ), __( 'positions', 'wp-club-manager' ) ),
				'class' => '',
				'attribute' => 'multiple',
				'chosen' => true,
			);
			wpcm_dropdown_taxonomies( $args );
			?>
		</p>

		<p>
			<label><?php _e( 'Date of Birth', 'wp-club-manager' ); ?></label>
			<select name="wpcm_dob_day" id="wpcm_dob_day" class="chosen_select_dob">
				<?php for ( $a = 1; $a < 32; $a = $a +1 ): ?>
					<option value="<?php echo zeroise($a, 2); ?>"<?php echo ($a == $dob_day ? ' selected="selected"' : ''); ?>>
						<?php echo zeroise($a, 2); ?>
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

		wpclubmanager_wp_text_input( array( 'id' => 'wpcm_height', 'label' => __( 'Height', 'wp-club-manager' ), 'class' => 'measure-text' ) );

		wpclubmanager_wp_text_input( array( 'id' => 'wpcm_weight', 'label' => __( 'Weight', 'wp-club-manager' ), 'class' => 'measure-text' ) );

		wpclubmanager_wp_text_input( array( 'id' => 'wpcm_hometown', 'label' => __( 'Birthplace', 'wp-club-manager' ), 'class' => 'regular-text' ) );

		wpclubmanager_wp_country_select( array( 'id' => 'wpcm_natl', 'label' => __( 'Nationality', 'wp-club-manager' ) ) );

		wpclubmanager_wp_textarea_input( array( 'id' => 'wpcm_prevclubs', 'label' => __( 'Previous Clubs', 'wp-club-manager'), 'class' => 'regular-text' ) );

	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		$dob_year = substr( zeroise( (int) $_POST['wpcm_dob_year'], 4 ), 0, 4 );
		$dob_month = substr( zeroise( (int) $_POST['wpcm_dob_month'], 2 ), 0, 2 );
		$dob_day = substr( zeroise( (int) $_POST['wpcm_dob_day'], 2 ), 0, 2 );
		$formatted_date = date( 'Y-m-d', strtotime( $dob_year . '-' . $dob_month. '-' . $dob_day ) );

		update_post_meta( $post_id, 'wpcm_dob', $formatted_date );
		
		if( isset( $_POST['wpcm_number'] ) ) {
			update_post_meta( $post_id, 'wpcm_number', $_POST['wpcm_number'] );
		}
		if( isset( $_POST['wpcm_height'] ) ) {
			update_post_meta( $post_id, 'wpcm_height', $_POST['wpcm_height'] );
		}
		if( isset( $_POST['wpcm_weight'] ) ) {
			update_post_meta( $post_id, 'wpcm_weight', $_POST['wpcm_weight'] );
		}
		if( isset( $_POST['wpcm_natl'] ) ) {
			update_post_meta( $post_id, 'wpcm_natl', $_POST['wpcm_natl'] );
		}
		if( isset( $_POST['wpcm_hometown'] ) ) {
			update_post_meta( $post_id, 'wpcm_hometown', $_POST['wpcm_hometown'] );
		}
		if( isset( $_POST['wpcm_prevclubs'] ) ) {
			update_post_meta( $post_id, 'wpcm_prevclubs', $_POST['wpcm_prevclubs'] );
		}

		do_action( 'delete_plugin_transients' );

		do_action('wpclubmanager_after_admin_player_save', $post_id );
	}
}