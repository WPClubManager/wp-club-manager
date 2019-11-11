<?php
/**
 * The template for displaying product content in the single-staff.php template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-single-staff.php
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="wpcm-player-info wpcm-row">

	    <div class="wpcm-profile-image">
			
			<?php echo wpcm_get_player_thumbnail( $post->ID, 'staff_single' ); ?>

		</div>

		<div class="wpcm-profile-meta">

			<h1 class="entry-title"><?php the_title(); ?></h1>

			<table>
							
				<tbody>

					<?php

					if ( get_option( 'wpcm_staff_profile_show_dob' ) == 'yes') { ?>

						<tr>
							<th>
								<?php _e( 'Birthday', 'wp-club-manager' ); ?>
							</th>
							<td>
								<?php echo date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $post->ID, 'wpcm_dob', true ) ) ); ?>
							</td>
						</tr>
					<?php }

					if ( get_option( 'wpcm_staff_profile_show_age' ) == 'yes') { ?>

						<tr>
							<th>
								<?php _e( 'Age', 'wp-club-manager' ); ?>
							</th>
							<td>
								<?php echo get_age( get_post_meta( $post->ID, 'wpcm_dob', true ) ); ?>
							</td>
						</tr>
					<?php }

					if ( get_option( 'wpcm_staff_profile_show_season' ) == 'yes') {

						$seasons = get_the_terms( $post->ID, 'wpcm_season' );
								
						if ( is_array( $seasons ) ) {

							$player_seasons = array();

							foreach ( $seasons as $value ) {

								$player_seasons[] = $value->name;
							} ?>

							<tr>
								<th>
									<?php _e( 'Season', 'wp-club-manager' ); ?>
								</th>
								<td>
									<?php echo implode( ', ', $player_seasons ); ?>
								</td>
							</tr>
						<?php
						}
					}

					if ( get_option( 'wpcm_staff_profile_show_team' ) == 'yes') {

						$teams = get_the_terms( $post->ID, 'wpcm_team' );

						if ( is_array( $teams ) ) {
									
							$player_teams = array();

							foreach ( $teams as $team ) {
								
								$player_teams[] = $team->name;
							} ?>

							<tr>
								<th>
									<?php _e( 'Team', 'wp-club-manager' ); ?>
								</th>
								<td>
									<?php echo implode( ', ', $player_teams ); ?>
								</td>
							</tr>
						<?php
						}
					}

					if ( get_option( 'wpcm_staff_profile_show_jobs' ) == 'yes') {

						$jobs = get_the_terms( $post->ID, 'wpcm_jobs' );

						if ( is_array( $jobs ) ) {

							$player_jobs = array();

							foreach ( $jobs as $job ) {
								
								$player_jobs[] = $job->name;
							} ?>

							<tr>
								<th>
									<?php _e( 'Job', 'wp-club-manager' ); ?>
								</th>
								<td>
									<?php echo implode( ', ', $player_jobs ); ?>
								</td>
							</tr>
						<?php
						}
					}

					if ( get_option( 'wpcm_show_staff_email' ) == 'yes') {

						$email = get_post_meta( $post->ID, '_wpcm_staff_email', true ); ?>

						<tr>
							<th>
								<?php _e( 'Email', 'wp-club-manager' ); ?>
							</th>
							<td>
								<a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
							</td>
						</tr>
					<?php
					}

					if ( get_option( 'wpcm_show_staff_phone' ) == 'yes') {

						$phone = get_post_meta( $post->ID, '_wpcm_staff_phone', true ); ?>

						<tr>
							<th>
								<?php _e( 'Phone', 'wp-club-manager' ); ?>
							</th>
							<td>
								<?php echo $phone; ?>
							</td>
						</tr>
					<?php
					}

					if ( get_option( 'wpcm_staff_profile_show_hometown' ) == 'yes' || get_option( 'wpcm_staff_profile_show_nationality' ) == 'yes') { ?>
						<tr>
							<th>
								<?php _e( 'Birthplace', 'wp-club-manager' ); ?>
							</th>
							<td>
								<?php echo ( get_option( 'wpcm_staff_profile_show_hometown' ) == 'yes' ? get_post_meta( $post->ID, 'wpcm_hometown', true ) : '' ); ?> <?php echo ( get_option( 'wpcm_staff_profile_show_nationality' ) == 'yes' ? '<img class="flag" src="' . WPCM_URL . 'assets/images/flags/' . get_post_meta( $post->ID, 'wpcm_natl', true ) . '.png" />' : '' ); ?>
							</td>
						</tr>
					<?php }

					if ( get_option( 'wpcm_staff_profile_show_joined' ) == 'yes') { ?>

						<tr>
							<th>
								<?php _e( 'Joined', 'wp-club-manager' ); ?>
							</th>
							<td>
								<?php echo date_i18n( get_option( 'date_format' ), strtotime( $post->post_date ) ); ?>
							</td>
						</tr>
					<?php
					} ?>

				</tbody>
						
			</table>

		</div>

	</div>

	<div class="wpcm-profile-bio wpcm-row">

		<?php
		if ( get_the_content() ) { ?>

			<div class="wpcm-entry-content">

				<?php the_content(); ?>

			</div>

		<?php } ?>

	</div>


	<?php do_action( 'wpclubmanager_after_single_staff_bio' ); ?>

</article>