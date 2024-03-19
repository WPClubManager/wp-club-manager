<?php
/**
 * Staff Details
 *
 * Displays the staff details box.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Staff_Details
 */
class WPCM_Meta_Box_Staff_Details {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$jobs    = get_the_terms( $post->ID, 'wpcm_jobs' );
		$job_ids = array();
		if ( $jobs ) :
			foreach ( $jobs as $job ) :
				$job_ids[] = $job->term_id;
			endforeach;
		endif;

		$club = get_post_meta( $post->ID, '_wpcm_staff_club', true );
		$dob  = ( get_post_meta( $post->ID, 'wpcm_dob', true ) ) ? get_post_meta( $post->ID, 'wpcm_dob', true ) : '1990-01-01';

		wpclubmanager_wp_text_input( array(
			'id'    => '_wpcm_firstname',
			'label' => __( 'First Name', 'wp-club-manager' ),
			'class' => 'regular-text',
		) );

		wpclubmanager_wp_text_input( array(
			'id'    => '_wpcm_lastname',
			'label' => __( 'Last Name', 'wp-club-manager' ),
			'class' => 'regular-text',
		) ); ?>

		<?php
		if ( is_league_mode() ) {
			?>
			<p>
				<label><?php esc_html_e( 'Current Club', 'wp-club-manager' ); ?></label>
				<?php
				wpcm_dropdown_posts( array(
					'id'               => '_wpcm_staff_club',
					'name'             => '_wpcm_staff_club',
					'post_type'        => 'wpcm_club',
					'limit'            => -1,
					'show_option_none' => __( 'Choose club', 'wp-club-manager' ),
					'class'            => 'chosen_select',
					'selected'         => $club,
				));
				?>
			</p>
			<?php
		}

		// if ( get_option( 'wpcm_staff_profile_show_jobs' ) == 'yes') {
		?>
					<p>
				<label><?php esc_html_e( 'Job Title', 'wp-club-manager' ); ?></label>
				<?php
					$args = array(
						'taxonomy'    => 'wpcm_jobs',
						'name'        => 'tax_input[wpcm_jobs][]',
						'selected'    => $job_ids,
						'values'      => 'term_id',
						'placeholder' => __( 'Choose jobs', 'wp-club-manager' ),
						'class'       => '',
						'attribute'   => 'multiple',
						'chosen'      => true,
					);
					wpcm_dropdown_taxonomies( $args );
					?>
			</p>
		<?php
		// }

		// if ( get_option( 'wpcm_show_staff_email' ) == 'yes') {
			wpclubmanager_wp_text_input( array(
				'id'    => '_wpcm_staff_email',
				'label' => __( 'Email Address', 'wp-club-manager' ),
				'class' => 'regular-text',
			) );
		// }

		// if ( get_option( 'wpcm_show_staff_phone' ) == 'yes') {
			wpclubmanager_wp_text_input( array(
				'id'    => '_wpcm_staff_phone',
				'label' => __( 'Contact Number', 'wp-club-manager' ),
				'class' => 'regular-text',
			) );
		// }

		// if ( get_option( 'wpcm_staff_profile_show_dob' ) == 'yes') {
			wpclubmanager_wp_text_input( array(
				'id'                => 'wpcm_dob',
				'label'             => __( 'Date of Birth', 'wp-club-manager' ),
				'placeholder'       => _x( 'YYYY-MM-DD', 'placeholder', 'wp-club-manager' ),
				'description'       => '',
				'value'             => $dob,
				'class'             => 'birth-date-picker',
				'custom_attributes' => array( 'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ),
			) );
		// }

		// if ( get_option( 'wpcm_staff_profile_show_hometown' ) == 'yes') {
			wpclubmanager_wp_text_input( array(
				'id'    => '_wpcm_staff_hometown',
				'label' => __( 'Birthplace', 'wp-club-manager' ),
				'class' => 'regular-text',
			) );
		// }

		// if ( get_option( 'wpcm_staff_profile_show_nationality' ) == 'yes') {
			wpclubmanager_wp_country_select( array(
				'id'    => 'wpcm_natl',
				'label' => __( 'Nationality', 'wp-club-manager' ),
			) );
		// }

		do_action( 'wpclubmanager_admin_staff_details', $post->ID );
	}

	/**
	 * Save meta box data
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {
		if ( ! check_admin_referer( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' ) ) {
			return;
		}

		$club = filter_input( INPUT_POST, '_wpcm_staff_club', FILTER_UNSAFE_RAW );
		if ( $club ) {
			update_post_meta( $post_id, '_wpcm_staff_club', sanitize_text_field( $club ) );
		}

		$dob = filter_input( INPUT_POST, 'wpcm_dob', FILTER_UNSAFE_RAW );
		if ( $dob ) {
			update_post_meta( $post_id, 'wpcm_dob', sanitize_text_field( $dob ) );
		}

		$firstname = filter_input( INPUT_POST, '_wpcm_firstname', FILTER_UNSAFE_RAW );
		if ( $firstname ) {
			update_post_meta( $post_id, '_wpcm_firstname', sanitize_text_field( $firstname ) );
		}

		$lastname = filter_input( INPUT_POST, '_wpcm_lastname', FILTER_UNSAFE_RAW );
		if ( $lastname ) {
			update_post_meta( $post_id, '_wpcm_lastname', sanitize_text_field( $lastname ) );
		}

		$email = filter_input( INPUT_POST, '_wpcm_staff_email', FILTER_UNSAFE_RAW );
		if ( $email ) {
			update_post_meta( $post_id, '_wpcm_staff_email', sanitize_text_field( $email ) );
		}

		$phone = filter_input( INPUT_POST, '_wpcm_staff_phone', FILTER_UNSAFE_RAW );
		if ( $phone ) {
			update_post_meta( $post_id, '_wpcm_staff_phone', sanitize_text_field( $phone ) );
		}

		$hometown = filter_input( INPUT_POST, '_wpcm_staff_hometown', FILTER_UNSAFE_RAW );
		if ( $hometown ) {
			update_post_meta( $post_id, '_wpcm_staff_hometown', sanitize_text_field( $hometown ) );
		}

		$natl = filter_input( INPUT_POST, 'wpcm_natl', FILTER_UNSAFE_RAW );
		if ( $natl ) {
			update_post_meta( $post_id, 'wpcm_natl', sanitize_text_field( $natl ) );
		}

		do_action( 'wpclubmanager_after_admin_staff_save', $post_id );

		do_action( 'delete_plugin_transients' );
	}
}
