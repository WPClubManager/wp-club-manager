<?php
/**
 * Single Player - Meta
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post; ?>

<table>
	<tbody>

		<?php
		if ( get_option( 'wpcm_player_profile_show_number' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Squad No.', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo esc_html( get_post_meta( $post->ID, 'wpcm_number', true ) ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_dob' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Date of Birth', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $post->ID, 'wpcm_dob', true ) ) ) ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_age' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Age', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo esc_html( get_age( get_post_meta( $post->ID, 'wpcm_dob', true ) ) ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_height' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Height', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo esc_html( get_post_meta( $post->ID, 'wpcm_height', true ) ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_weight' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Weight', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo esc_html( get_post_meta( $post->ID, 'wpcm_weight', true ) ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_season' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Season', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo wp_kses_post( wpcm_get_player_seasons( $post->ID ) ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_team' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Team', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo wp_kses_post( wpcm_get_player_teams( $post->ID ) ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_position' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Position', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo wp_kses_post( wpcm_get_player_positions( $post->ID ) ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_hometown' ) == 'yes' || get_option( 'wpcm_player_profile_show_nationality' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Birthplace', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo ( get_option( 'wpcm_player_profile_show_hometown' ) == 'yes' ? esc_html( get_post_meta( $post->ID, 'wpcm_hometown', true ) ) : '' ); ?> <?php echo ( get_option( 'wpcm_player_profile_show_nationality' ) == 'yes' ? '<img class="flag" src="' . esc_url( WPCM_URL ) . 'assets/images/flags/' . esc_url( get_post_meta( $post->ID, 'wpcm_natl', true ) ) . '.png" />' : '' ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_joined' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Joined', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo esc_html( date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ) ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_exp' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Experience', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?>
				</td>
			</tr>
			<?php
		}

		if ( get_option( 'wpcm_player_profile_show_prevclubs' ) == 'yes' ) {
			?>
			<tr>
				<th>
					<?php esc_html_e( 'Previous Clubs', 'wp-club-manager' ); ?>
				</th>
				<td>
					<?php echo ( get_post_meta( $post->ID, 'wpcm_prevclubs', true ) ? esc_html( get_post_meta( $post->ID, 'wpcm_prevclubs', true ) ) : esc_html__( 'None', 'wp-club-manager' ) ); ?>
				</td>
			</tr>
		<?php } ?>

	</tbody>
</table>
