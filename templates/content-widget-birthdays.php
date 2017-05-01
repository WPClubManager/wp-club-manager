<?php
/**
 * Birthdays Widget
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     1.5.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<ul class="wpcm-birthdays-widget">

	<?php
	foreach ( $posts as $post => $value ) {

		$dob = get_post_meta( $post, 'wpcm_dob', true );
		$date = date( 'F j', strtotime( $dob ) );
		$age = '';
		if( $show_age ) {
			$age = get_age( $dob );
			if( date( 'm-d' ) !== $value ) {
				$age = get_age( $dob ) + 1;
			}
		}

		// Today
		if( date( 'm-d' ) == $value ) { ?>

			<li class="birthday-today">
				<h3 class="happy-birthday"><?php _e( 'Happy Birthday!', 'wp-club-manager' ); ?></h3>
				<div class="birthday-post">
					<a href="<?php echo get_the_permalink( $post ); ?>" class="birthday-image">
						<?php echo wpcm_get_player_thumbnail( $post, 'player_thumbnail' ); ?>
					</a>
					<div class="birthday-meta">
						<h4><?php echo get_the_title( $post ); ?> <span><?php echo $age; ?></span></h4>
						<p class="birthdate"><?php _e( 'Today', 'wp-club-manager' ); ?></p>
					</div>
				</div>
			</li>

		<?php
		// Upcoming
		} else { ?>

			<li>
				<div class="birthday-post">
					<a href="<?php echo get_the_permalink( $post ); ?>" class="birthday-image">
						<?php echo wpcm_get_player_thumbnail( $post, 'player_thumbnail' ); ?>
					</a>
					<div class="birthday-meta">
						<h4><?php echo get_the_title( $post ); ?> <span><?php echo $age; ?></span></h4>
						<p class="birthdate"><?php echo $date; ?></p>
					</div>
				</div>
			</li>

		<?php
		}
	} ?>
			
</ul>