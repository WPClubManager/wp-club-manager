<?php
/**
 * Single Match - Box Scores
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$sport     = get_option( 'wpcm_sport' );
$sep       = get_option( 'wpcm_match_goals_delimiter' );
$intgoals  = unserialize( get_post_meta( $post->ID, 'wpcm_goals', true ) );
$played    = get_post_meta( $post->ID, 'wpcm_played', true );
$home_club = get_post_meta( $post->ID, 'wpcm_home_club', true );
$away_club = get_post_meta( $post->ID, 'wpcm_away_club', true );
$overtime  = get_post_meta( $post->ID, 'wpcm_overtime', true );
$shootout  = get_post_meta( $post->ID, 'wpcm_shootout', true );

$sports = array( 'volleyball', 'basketball', 'football', 'footy', 'hockey', 'floorball' );
if ( in_array( $sport, $sports ) ) {

	if ( $played ) { ?>

		<table class="wpcm-ss-table wpcm-box-scores">
			<thead>
				<tr>
					<th></th>
					<th><?php esc_html_e( '1st', 'wp-club-manager' ); ?></th>
					<th><?php esc_html_e( '2nd', 'wp-club-manager' ); ?></th>
					<th><?php esc_html_e( '3rd', 'wp-club-manager' ); ?></th>
					<?php
					$sports = array( 'volleyball', 'basketball', 'football', 'footy' );
					if ( in_array( $sport, $sports ) ) {
						?>
						<th><?php esc_html_e( '4th', 'wp-club-manager' ); ?></th>
						<?php
					}
					if ( 'volleyball' === $sport ) {
						?>
						<th><?php esc_html_e( '5th', 'wp-club-manager' ); ?></th>
						<?php
					}
					if ( in_array( $sport, array( 'hockey', 'floorball' ) ) ) {
						if ( '1' === $overtime ) {
							?>
							<th><?php echo esc_html_x( 'OT', 'Overtime', 'wp-club-manager' ); ?></th>
							<?php
						}
						if ( '1' === $shootout ) {
							?>
							<th><?php echo esc_html_x( 'SO', 'Shootout', 'wp-club-manager' ); ?></th>
							<?php
						}
					}
					?>
					<th><?php echo esc_html_x( 'T', 'Total', 'wp-club-manager' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo esc_html( get_the_title( $home_club ) ); ?></td>
					<?php
					if ( isset( $intgoals['q1'] ) ) {
						?>
						<td><?php echo esc_html( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ? __( 'x', 'wp-club-manager' ) : $intgoals['q1']['home'] ); ?></td>
						<?php
					}
					if ( isset( $intgoals['q2'] ) ) {
						?>
						<td><?php echo esc_html( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ? __( 'x', 'wp-club-manager' ) : $intgoals['q2']['home'] ); ?></td>
						<?php
					}
					if ( isset( $intgoals['q3'] ) ) {
						?>
						<td><?php echo esc_html( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ? __( 'x', 'wp-club-manager' ) : $intgoals['q3']['home'] ); ?></td>
						<?php
					}
					if ( in_array( $sport, $sports ) ) {
						if ( isset( $intgoals['q4'] ) ) {
							?>
							<td><?php echo esc_html( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ? __( 'x', 'wp-club-manager' ) : $intgoals['q4']['home'] ); ?></td>
							<?php
						}
					}
					if ( 'volleyball' === $sport ) {
						if ( isset( $intgoals['q5'] ) ) {
							?>
							<td><?php echo esc_html( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ? __( 'x', 'wp-club-manager' ) : $intgoals['q5']['home'] ); ?></td>
							<?php
						}
					}
					if ( in_array( $sport, array( 'hockey', 'floorball' ) ) ) {
						if ( '1' === $overtime ) {
							if ( $intgoals['total']['home'] > $intgoals['total']['away'] ) {
								?>
								<td><?php esc_html_e( '1', 'wp-club-manager' ); ?></td>
							<?php } else { ?>
								<td><?php esc_html_e( '0', 'wp-club-manager' ); ?></td>
								<?php
							}
						}
						if ( '1' === $shootout ) {
							if ( $intgoals['total']['home'] > $intgoals['total']['away'] ) {
								?>
								<td><?php esc_html_e( '1', 'wp-club-manager' ); ?></td>
							<?php } else { ?>
								<td><?php esc_html_e( '0', 'wp-club-manager' ); ?></td>
								<?php
							}
						}
					}
					?>
					<td><?php echo esc_html( $intgoals['total']['home'] ); ?></td>
				</tr>
				<tr>
					<td><?php echo esc_html( get_the_title( $away_club ) ); ?></td>
					<?php
					if ( isset( $intgoals['q1'] ) ) {
						?>
						<td><?php echo esc_html( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ? __( 'x', 'wp-club-manager' ) : $intgoals['q1']['away'] ); ?></td>
						<?php
					}
					if ( isset( $intgoals['q2'] ) ) {
						?>
						<td><?php echo esc_html( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ? __( 'x', 'wp-club-manager' ) : $intgoals['q2']['away'] ); ?></td>
						<?php
					}
					if ( isset( $intgoals['q3'] ) ) {
						?>
						<td><?php echo esc_html( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ? __( 'x', 'wp-club-manager' ) : $intgoals['q3']['away'] ); ?></td>
						<?php
					}
					if ( in_array( $sport, $sports ) ) {
						if ( isset( $intgoals['q4'] ) ) {
							?>
							<td><?php echo esc_html( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ? __( 'x', 'wp-club-manager' ) : $intgoals['q4']['away'] ); ?></td>
							<?php
						}
					}
					if ( 'volleyball' === $sport ) {
						if ( isset( $intgoals['q5'] ) ) {
							?>
							<td><?php echo esc_html( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ? __( 'x', 'wp-club-manager' ) : $intgoals['q5']['away'] ); ?></td>
							<?php
						}
					}
					if ( in_array( $sport, array( 'hockey', 'floorball' ) ) ) {
						if ( '1' === $overtime ) {
							if ( $intgoals['total']['away'] > $intgoals['total']['home'] ) {
								?>
								<td>1</td>
							<?php } else { ?>
								<td>0</td>
								<?php
							}
						}
						if ( '1' === $shootout ) {
							if ( $intgoals['total']['away'] > $intgoals['total']['home'] ) {
								?>
								<td>1</td>
							<?php } else { ?>
								<td>0</td>
								<?php
							}
						}
					}
					?>
					<td><?php echo esc_html( $intgoals['total']['away'] ); ?></td>
				</tr>
			</tbody>
		</table>
		<?php
	}
} elseif ( $played ) {
	if ( isset( $intgoals['q1'] ) ) {
		?>
			<div class="wpcm-ss-halftime wpcm-box-scores">
			<?php
			if ( 'yes' === get_option( 'wpcm_hide_scores' ) && ! is_user_logged_in() ) {
				echo esc_html_x( 'HT:', 'Half time', 'wp-club-manager' );
				?>
				<?php esc_html_e( 'x', 'wp-club-manager' ); ?> <?php echo esc_html( $sep ); ?> <?php
					esc_html_e( 'x', 'wp-club-manager' );
			} else {
				echo esc_html_x( 'HT:', 'Half time', 'wp-club-manager' );
				?>
				<?php echo esc_html( $intgoals['q1']['home'] ); ?> <?php echo esc_html( $sep ); ?> <?php
					echo esc_html( $intgoals['q1']['away'] );
			}
			?>
			</div>
			<?php
	}
}
