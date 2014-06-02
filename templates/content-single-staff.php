<?php
/**
 * The template for displaying product content in the single-staff.php template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-single-staff.php
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.1.2
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="wpcm-player-info wpcm-row">

	    <div class="wpcm-profile-image">
			
			<?php if ( has_post_thumbnail() ) {
					
				echo the_post_thumbnail( 'player_single' );
				
			} else {
							
				echo apply_filters( 'wpclubmanager_single_staff_image', sprintf( '<img src="%s" alt="Placeholder" />', wpcm_placeholder_img_src() ), $post->ID );
						
			} ?>

		</div>

		<div class="wpcm-profile-meta">

			<h1 class="entry-title"><?php the_title(); ?></h1>

			<?php //$season = '0'; ?>

			<table>
							
				<tbody>

					<?php

					if ( get_option( 'wpcm_player_profile_show_dob' ) == 'yes') { ?>

						<tr>
							<th>
								<?php _e( 'Birthday', 'wpclubmanager' ); ?>
							</th>
							<td>
								<?php echo date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $post->ID, 'wpcm_dob', true ) ) ); ?>
							</td>
						</tr>
					<?php }

					if ( get_option( 'wpcm_player_profile_show_age' ) == 'yes') { ?>

						<tr>
							<th>
								<?php _e( 'Age', 'wpclubmanager' ); ?>
							</th>
							<td>
								<?php echo get_age( get_post_meta( $post->ID, 'wpcm_dob', true ) ); ?>
							</td>
						</tr>
					<?php }

					if ( get_option( 'wpcm_player_profile_show_season' ) == 'yes') {

						$seasons = get_the_terms( $post->ID, 'wpcm_season' );
								
						if ( is_array( $seasons ) ) {

							$player_seasons = array();

							foreach ( $seasons as $value ) {

								$player_seasons[] = $value->name;
							} ?>

							<tr>
								<th>
									<?php _e( 'Season', 'wpclubmanager' ); ?>
								</th>
								<td>
									<?php echo implode( ', ', $player_seasons ); ?>
								</td>
							</tr>
						<?php
						}
					}

					if ( get_option( 'wpcm_player_profile_show_team' ) == 'yes') {

						$teams = get_the_terms( $post->ID, 'wpcm_team' );

						if ( is_array( $teams ) ) {
									
							$player_teams = array();

							foreach ( $teams as $team ) {
								
								$player_teams[] = $team->name;
							} ?>

							<tr>
								<th>
									<?php _e( 'Team', 'wpclubmanager' ); ?>
								</th>
								<td>
									<?php echo implode( ', ', $player_teams ); ?>
								</td>
							</tr>
						<?php
						}
					}

					if ( get_option( 'wpcm_player_profile_show_jobs' ) == 'yes') {

						$jobs = get_the_terms( $post->ID, 'wpcm_job' );

						if ( is_array( $jobs ) ) {

							$player_jobs = array();

							foreach ( $jobs as $job ) {
								
								$player_jobs[] = $job->name;
							} ?>

							<tr>
								<th>
									<?php _e( 'Job', 'wpclubmanager' ); ?>
								</th>
								<td>
									<?php echo implode( ', ', $player_jobs ); ?>
								</td>
							</tr>
						<?php
						}
					}

					if ( get_option( 'wpcm_player_profile_show_hometown' ) == 'yes') {

						$natl = get_post_meta( $post->ID, 'wpcm_natl', true ); ?>

						<tr>
							<th>
								<?php _e( 'Nationality', 'wpclubmanager' ); ?>
							</th>
							<td>
								<img class="flag" src="<?php echo WPCM_URL; ?>assets/images/flags/<?php echo $natl; ?>.png" />
							</td>
						</tr>
					<?php
					}

					if ( get_option( 'wpcm_player_profile_show_joined' ) == 'yes') { ?>

						<tr>
							<th>
								<?php _e( 'Joined', 'wpclubmanager' ); ?>
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