<?php
/**
 * Player Details
 *
 * Displays the player details box.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.1.3
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPCM_Meta_Box_Player_Details
 */
class WPCM_Meta_Box_Player_Details {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$positions    = get_the_terms( $post->ID, 'wpcm_position' );
		$position_ids = array();
		if ( $positions ) :
			foreach ( $positions as $position ) :
				$position_ids[] = $position->term_id;
			endforeach;
		endif;

		$club = get_post_meta( $post->ID, '_wpcm_player_club', true );
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
		) );

		if ( is_league_mode() ) { ?>
			<p>
				<label><?php esc_html_e( 'Current Club', 'wp-club-manager' ); ?></label>
				<?php
				wpcm_dropdown_posts( array(
					'id'               => '_wpcm_player_club',
					'name'             => '_wpcm_player_club',
					'post_type'        => 'wpcm_club',
					'limit'            => -1,
					'show_option_none' => __( 'None', 'wp-club-manager' ),
					'class'            => 'chosen_select',
					'selected'         => $club,
				));
				?>
			</p>
			<?php
		}

		// if ( get_option( 'wpcm_player_profile_show_number' ) == 'yes') {
			wpclubmanager_wp_text_input( array(
				'id'    => 'wpcm_number',
				'label' => __( 'Squad Number', 'wp-club-manager' ),
				'class' => 'measure-text',
			) );
		// }

		// if ( get_option( 'wpcm_player_profile_show_position' ) == 'yes') {
		?>
			<p>
				<label><?php esc_html_e( 'Position', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'    => 'wpcm_position',
					'name'        => 'tax_input[wpcm_position][]',
					'selected'    => $position_ids,
					'values'      => 'term_id',
					'placeholder' => __( 'Choose positions', 'wp-club-manager' ),
					'class'       => 'regular-text',
					'attribute'   => 'multiple',
					'chosen'      => true,
				);
				wpcm_dropdown_taxonomies( $args );
				?>
			</p>
		<?php
		// }
		?>

		<?php
		// if ( get_option( 'wpcm_player_profile_show_dob' ) == 'yes') {
			wpclubmanager_wp_text_input( array(
				'id'                => 'wpcm_dob',
				'label'             => __( 'Date of Birth', 'wp-club-manager' ),
				'placeholder'       => _x( 'YYYY-MM-DD', 'placeholder', 'wp-club-manager' ),
				'description'       => '',
				'value'             => $dob,
				'class'             => 'wpcm-birth-date-picker',
				'custom_attributes' => array( 'pattern' => '[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])' ),
			) );
		// }

		// if ( get_option( 'wpcm_player_profile_show_height' ) == 'yes') {
			wpclubmanager_wp_text_input( array(
				'id'    => 'wpcm_height',
				'label' => __( 'Height', 'wp-club-manager' ),
				'class' => 'measure-text',
			) );
		// }

		// if ( get_option( 'wpcm_player_profile_show_weight' ) == 'yes') {
			wpclubmanager_wp_text_input( array(
				'id'    => 'wpcm_weight',
				'label' => __( 'Weight', 'wp-club-manager' ),
				'class' => 'measure-text',
			) );
		// }

		// if ( get_option( 'wpcm_player_profile_show_hometown' ) == 'yes') {
			wpclubmanager_wp_text_input( array(
				'id'    => 'wpcm_hometown',
				'label' => __( 'Birthplace', 'wp-club-manager' ),
				'class' => 'regular-text',
			) );
		// }

		// if ( get_option( 'wpcm_player_profile_show_nationality' ) == 'yes') {
			wpclubmanager_wp_country_select( array(
				'id'    => 'wpcm_natl',
				'label' => __( 'Nationality', 'wp-club-manager' ),
			) );
		// }

		// if ( get_option( 'wpcm_player_profile_show_prevclubs' ) == 'yes') {
			wpclubmanager_wp_textarea_input( array(
				'id'    => 'wpcm_prevclubs',
				'label' => __( 'Previous Clubs', 'wp-club-manager' ),
				'class' => 'regular-text',
			) );
		// }

		do_action( 'wpclubmanager_admin_player_details', $post->ID );
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

		self::update_meta( $post_id, '_wpcm_player_club' );
		self::update_meta( $post_id, 'wpcm_dob' );
		self::update_meta( $post_id, '_wpcm_firstname' );
		self::update_meta( $post_id, '_wpcm_lastname' );
		self::update_meta( $post_id, 'wpcm_number' );
		self::update_meta( $post_id, 'wpcm_height' );
		self::update_meta( $post_id, 'wpcm_weight' );
		self::update_meta( $post_id, 'wpcm_natl' );
		self::update_meta( $post_id, 'wpcm_hometown' );
		self::update_meta( $post_id, 'wpcm_prevclubs' );

		do_action( 'wpclubmanager_after_admin_player_save', $post_id );

		do_action( 'delete_plugin_transients' );
	}

	/**
	 * Update post meta
	 *
	 * @param int    $post_id
	 * @param string $key
	 *
	 * @return void
	 */
	protected static function update_meta( $post_id, $key ) {
		$value = filter_input( INPUT_POST, $key, FILTER_UNSAFE_RAW );
		if ( $value ) {
			update_post_meta( $post_id, $key, sanitize_text_field( $value ) );
		}
	}
}
