<?php
/**
 * Birthdays Widget
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     1.5.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly ?>

<ul class="wpcm-birthdays-widget">

	<?php
	foreach ( $posts as $this_post_id => $value ) {

		$dob  = get_post_meta( $this_post_id, 'wpcm_dob', true );
		$date = gmdate( 'F j', strtotime( $dob ) );
		$age  = '';
		if ( $show_age ) {
			$age = get_age( $dob );
			if ( gmdate( 'm-d' ) !== $value ) {
				$age = get_age( $dob ) + 1;
			}
		}

		// Today
		if ( gmdate( 'm-d' ) == $value ) {
			?>

			<li class="birthday-today">
				<h3 class="happy-birthday"><?php esc_html_e( 'Happy Birthday!', 'wp-club-manager' ); ?></h3>
				<div class="birthday-post">
					<a href="<?php echo esc_url( get_the_permalink( $this_post_id ) ); ?>" class="birthday-image">
						<?php echo wp_kses_post( wpcm_get_player_thumbnail( $post, 'player_thumbnail' ) ); ?>
					</a>
					<div class="birthday-meta">
						<h4><?php echo esc_html( get_the_title( $this_post_id ) ); ?> <span><?php echo esc_html( $age ); ?></span></h4>
						<p class="birthdate"><?php esc_html_e( 'Today', 'wp-club-manager' ); ?></p>
					</div>
				</div>
			</li>

			<?php
			// Upcoming
		} else {
			?>

			<li>
				<div class="birthday-post">
					<a href="<?php echo esc_url( get_the_permalink( $this_post_id ) ); ?>" class="birthday-image">
						<?php echo wp_kses_post( wpcm_get_player_thumbnail( $post, 'player_thumbnail' ) ); ?>
					</a>
					<div class="birthday-meta">
						<h4><?php echo esc_html( get_the_title( $this_post_id ) ); ?> <span><?php echo esc_html( $age ); ?></span></h4>
						<p class="birthdate"><?php echo esc_html( $date ); ?></p>
					</div>
				</div>
			</li>

			<?php
		}
	}
	?>

</ul>
