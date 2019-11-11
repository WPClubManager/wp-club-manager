<?php
/**
 * Staff Details
 *
 * Displays the staff details box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     2.1.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Staff_Details {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$jobs = get_the_terms( $post->ID, 'wpcm_jobs' );
		$job_ids = array();
		if ( $jobs ):
			foreach ( $jobs as $job ):
				$job_ids[] = $job->term_id;
			endforeach;
		endif;

		$club = get_post_meta( $post->ID, '_wpcm_staff_club', true );
		$dob = ( get_post_meta( $post->ID, 'wpcm_dob', true ) ) ? get_post_meta( $post->ID, 'wpcm_dob', true ) : '1990-01-01';
		
		wpclubmanager_wp_text_input( array( 
			'id' => '_wpcm_firstname',
			'label' => __( 'First Name', 'wp-club-manager' ),
			'class' => 'regular-text'
		) );

		wpclubmanager_wp_text_input( array( 
			'id' => '_wpcm_lastname',
			'label' => __( 'Last Name', 'wp-club-manager' ),
			'class' => 'regular-text'
		) ); ?>

		<?php
		if( is_league_mode() ) { ?>
			<p>
				<label><?php _e( 'Current Club', 'wp-club-manager' ); ?></label>
				<?php
				wpcm_dropdown_posts( array(
					'id'				=> '_wpcm_staff_club',
					'name' 				=> '_wpcm_staff_club',
					'post_type' 		=> 'wpcm_club',
					'limit' 			=> -1,
					'show_option_none'	=> __( 'Choose club', 'wp-club-manager' ),
					'class'				=> 'chosen_select',
					'selected'			=> $club
				));
				?>
			</p>
		<?php 
		}
		
		//if ( get_option( 'wpcm_staff_profile_show_jobs' ) == 'yes') { ?>		
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
		//}

		//if ( get_option( 'wpcm_show_staff_email' ) == 'yes') {
			wpclubmanager_wp_text_input( array( 'id' => '_wpcm_staff_email', 'label' => __( 'Email Address', 'wp-club-manager' ), 'class' => 'regular-text' ) );
		//}
		
		//if ( get_option( 'wpcm_show_staff_phone' ) == 'yes') {
			wpclubmanager_wp_text_input( array( 'id' => '_wpcm_staff_phone', 'label' => __( 'Contact Number', 'wp-club-manager' ), 'class' => 'regular-text' ) );
		//}

		//if ( get_option( 'wpcm_staff_profile_show_dob' ) == 'yes') {
			wpclubmanager_wp_text_input( array( 'id' => 'wpcm_dob', 'label' => __( 'Date of Birth', 'wp-club-manager' ), 'placeholder' => _x( 'YYYY-MM-DD', 'placeholder', 'wp-club-manager' ), 'description' => '', 'value' => $dob,'class' => 'birth-date-picker', 'custom_attributes' => array( 'pattern' => "[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" ) ) );
		//}

		//if ( get_option( 'wpcm_staff_profile_show_hometown' ) == 'yes') {
			wpclubmanager_wp_text_input( array( 'id' => '_wpcm_staff_hometown', 'label' => __( 'Birthplace', 'wp-club-manager' ), 'class' => 'regular-text' ) );
		//}
		
		//if ( get_option( 'wpcm_staff_profile_show_nationality' ) == 'yes') {
			wpclubmanager_wp_country_select( array( 'id' => 'wpcm_natl', 'label' => __( 'Nationality', 'wp-club-manager' ) ) );
		//}

		do_action('wpclubmanager_admin_staff_details', $post->ID );
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if( isset( $_POST['_wpcm_staff_club'] ) ) {
			update_post_meta( $post_id, '_wpcm_staff_club', $_POST['_wpcm_staff_club'] );
		}
		if( isset( $_POST['wpcm_dob'] ) ) {
			update_post_meta( $post_id, 'wpcm_dob', $_POST['wpcm_dob'] );
		}
		if( isset( $_POST['_wpcm_firstname'] ) ) {
			update_post_meta( $post_id, '_wpcm_firstname', $_POST['_wpcm_firstname'] );
		}
		if( isset( $_POST['_wpcm_lastname'] ) ) {
			update_post_meta( $post_id, '_wpcm_lastname', $_POST['_wpcm_lastname'] );
		}
		if( isset( $_POST['_wpcm_staff_email'] ) ) {
			update_post_meta( $post_id, '_wpcm_staff_email', $_POST['_wpcm_staff_email'] );
		}
		if( isset( $_POST['_wpcm_staff_phone'] ) ) {
			update_post_meta( $post_id, '_wpcm_staff_phone', $_POST['_wpcm_staff_phone'] );
		}
		if( isset( $_POST['_wpcm_staff_hometown'] ) ) {
			update_post_meta( $post_id, '_wpcm_staff_hometown', $_POST['_wpcm_staff_hometown'] );
		}
		if( isset( $_POST['wpcm_natl'] ) ) {
			update_post_meta( $post_id, 'wpcm_natl', $_POST['wpcm_natl'] );
		}

		do_action('wpclubmanager_after_admin_staff_save', $post_id );

		do_action( 'delete_plugin_transients' );
	}
}