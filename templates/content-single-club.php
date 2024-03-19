<?php
/**
 * The template for displaying product content in the single-club.php template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-single-club.php
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     2.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$details          = get_club_details( $post );
$primary_color_bg = ( $details['primary_color'] ) ? ' style="background-color:' . $details['primary_color'] . ';color:#fff;text-shadow: 0 0 3px #000;"' : '';

do_action( 'wpclubmanager_before_single_club' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="wpcm-club-details wpcm-row">

		<h2 class="entry-title">
			<span>
				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'crest-medium' );
				} else {
					apply_filters( 'wpclubmanager_club_image', sprintf( '<img src="%s" alt="Placeholder" />', wpcm_placeholder_img_src() ), $post->ID );
				}
				?>
			</span>
			<?php the_title(); ?>
		</h2>

		<table>
			<tbody>
				<?php if ( $details['formed'] ) { ?>
					<tr>
						<th><?php esc_html_e( 'Formed', 'wp-club-manager' ); ?></th>
						<td><?php echo esc_html( $details['formed'] ); ?></td>
					</tr>
				<?php } ?>
				<tr>
					<th><?php esc_html_e( 'Ground', 'wp-club-manager' ); ?></th>
					<td><?php echo esc_html( $details['venue']['name'] ); ?></td>
				</tr>

				<?php
				if ( $details['venue']['capacity'] ) {
					?>
					<tr class="capacity">
						<th><?php esc_html_e( 'Capacity', 'wp-club-manager' ); ?></th>
						<td><?php echo esc_html( $details['venue']['capacity'] ); ?></td>
					</tr>
					<?php
				}

				if ( $details['venue']['address'] ) {
					?>
					<tr class="address">
						<th><?php esc_html_e( 'Address', 'wp-club-manager' ); ?></th>
						<td><?php echo esc_html( nl2br( $details['venue']['address'] ) ); ?></td>
					</tr>
					<?php
				}

				if ( $details['venue']['description'] ) {
					?>
					<tr class="description">
						<th><?php esc_html_e( 'Ground Info', 'wp-club-manager' ); ?></th>
						<td><?php echo esc_html( $details['venue']['description'] ); ?></td>
					</tr>
					<?php
				}
				?>
				<?php if ( $details['honours'] ) { ?>
					<tr>
						<th><?php esc_html_e( 'Honours', 'wp-club-manager' ); ?></th>
						<td><?php echo esc_html( nl2br( $details['honours'] ) ); ?></td>
					</tr>
				<?php } ?>
				<?php if ( $details['website'] ) { ?>
					<tr>
						<th></th>
						<td><a href="<?php echo esc_url( $details['website'] ); ?>" target="_blank"><?php esc_html_e( 'Visit website', 'wp-club-manager' ); ?></a></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<?php do_action( 'wpclubmanager_after_single_club_details' ); ?>

	</div>

	<?php if ( get_the_content() ) : ?>

		<div class="wpcm-entry-content">

			<?php the_content(); ?>

		</div>

	<?php endif; ?>

	<?php if ( $details['venue']['address'] && 'yes' === get_option( 'wpcm_club_settings_venue' ) ) { ?>

		<div class="wpcm-club-map">

			<?php echo do_shortcode( '[map_venue id="' . $details['venue']['id'] . '" width="100%" height="260"]' ); ?>

		</div>

		<?php
	}

	if ( is_club_mode() ) {

		if ( 'yes' === get_option( 'wpcm_club_settings_h2h' ) || 'yes' === get_option( 'wpcm_club_settings_matches' ) ) {

			$matches = wpcm_head_to_heads( $post->ID );
			?>

			<h3><?php /* translators: 1: plugin name(s). */ echo esc_html( sprintf( __( 'Matches against %s', 'wp-club-manager' ), $post->post_title ) ); ?></h3>

			<?php
		}

		if ( 'yes' === get_option( 'wpcm_club_settings_h2h' ) ) {

			$outcome = wpcm_head_to_head_count( $matches );
			?>

			<ul class="wpcm-h2h-list">
				<li class="wpcm-h2h-list-p"<?php echo esc_attr( $primary_color_bg ); ?>>
					<span class="wpcm-h2h-list-count"><?php echo esc_html( $outcome['total'] ); ?></span> <span class="wpcm-h2h-list-desc"><?php __( 'games', 'wp-club-manager' ); ?></span>
				</li>
				<li class="wpcm-h2h-list-w"<?php echo esc_attr( $primary_color_bg ); ?>>
					<span class="wpcm-h2h-list-count"><?php echo esc_html( $outcome['wins'] ); ?></span> <span class="wpcm-h2h-list-desc"><?php __( 'wins', 'wp-club-manager' ); ?></span>
				</li>
				<li class="wpcm-h2h-list-d"<?php echo esc_attr( $primary_color_bg ); ?>>
					<span class="wpcm-h2h-list-count"><?php echo esc_html( $outcome['draws'] ); ?></span> <span class="wpcm-h2h-list-desc"><?php __( 'draws', 'wp-club-manager' ); ?></span>
				</li>
				<li class="wpcm-h2h-list-l"<?php echo esc_attr( $primary_color_bg ); ?>>
					<span class="wpcm-h2h-list-count"><?php echo esc_html( $outcome['losses'] ); ?></span> <span class="wpcm-h2h-list-desc"><?php __( 'losses', 'wp-club-manager' ); ?></span>
				</li>
			</ul>

			<?php
		}

		if ( 'yes' === get_option( 'wpcm_club_settings_matches' ) ) {
			?>

			<ul class="wpcm-matches-list">

				<?php
				foreach ( $matches as $match ) {

					$played      = get_post_meta( $match->ID, 'wpcm_played', true );
					$timestamp   = strtotime( $match->post_date );
					$time_format = get_option( 'time_format' );
					$class       = wpcm_get_match_outcome( $match->ID );
					$comp        = wpcm_get_match_comp( $match->ID );
					$sides       = wpcm_get_match_clubs( $match->ID );
					$result      = wpcm_get_match_result( $match->ID );
					?>

					<li class="wpcm-matches-list-item <?php echo esc_attr( $class ); ?>">
						<a href="<?php echo esc_url( get_post_permalink( $match->ID, false, true ) ); ?>" class="wpcm-matches-list-link">
							<span class="wpcm-matches-list-col wpcm-matches-list-date">
								<?php echo esc_html( date_i18n( apply_filters( 'wpclubmanager_match_date_format', 'D d M' ), $timestamp ) ); ?>
							</span>
							<span class="wpcm-matches-list-col wpcm-matches-list-club1">
								<?php echo esc_html( $sides[0] ); ?>
							</span>
							<span class="wpcm-matches-list-col wpcm-matches-list-status">
								<span class="wpcm-matches-list-<?php echo ( $played ? 'result' : 'time' ); ?> <?php echo esc_attr( $class ); ?>">
									<?php echo esc_html( ( $played ? $result[0] : date_i18n( apply_filters( 'wpclubmanager_match_time_format', get_option( 'time_format' ) ), $timestamp ) ) ); ?>
								</span>
							</span>
							<span class="wpcm-matches-list-col wpcm-matches-list-club2">
								<?php echo esc_html( $sides[1] ); ?>
							</span>
							<span class="wpcm-matches-list-col wpcm-matches-list-info">
								<?php echo esc_html( $comp[1] ); ?>
							</span>
						</a>
					</li>

					<?php
				}
				?>

			</ul>

			<?php
		}
	}

	if ( is_league_mode() ) {
		?>

		<h3><?php esc_html_e( 'Matches', 'wp-club-manager' ); ?></h3>

		<ul class="wpcm-matches-list">

			<?php
			$matches = club_matches_list( $post->ID );

			foreach ( $matches as $match ) {

				$played      = get_post_meta( $match->ID, 'wpcm_played', true );
				$timestamp   = strtotime( $match->post_date );
				$time_format = get_option( 'time_format' );
				$class       = wpcm_get_match_outcome( $match->ID );
				$comp        = wpcm_get_match_comp( $match->ID );
				$sides       = wpcm_get_match_clubs( $match->ID );
				$result      = wpcm_get_match_result( $match->ID );
				?>

				<li class="wpcm-matches-list-item <?php echo esc_attr( $class ); ?>">
					<a href="<?php echo esc_url( get_post_permalink( $match->ID, false, true ) ); ?>" class="wpcm-matches-list-link">
						<span class="wpcm-matches-list-col wpcm-matches-list-date">
							<?php echo esc_html( date_i18n( 'D d M', $timestamp ) ); ?>
						</span>
						<span class="wpcm-matches-list-col wpcm-matches-list-club1">
							<?php echo esc_html( $sides[0] ); ?>
						</span>
						<span class="wpcm-matches-list-col wpcm-matches-list-status">
							<span class="wpcm-matches-list-<?php echo ( $played ? 'result' : 'time' ); ?> <?php echo esc_attr( $class ); ?>">
								<?php echo esc_html( ( $played ? $result[0] : date_i18n( $time_format, $timestamp ) ) ); ?>
							</span>
						</span>
						<span class="wpcm-matches-list-col wpcm-matches-list-club2">
							<?php echo esc_html( $sides[1] ); ?>
						</span>
						<span class="wpcm-matches-list-col wpcm-matches-list-info">
							<?php echo esc_html( $comp[1] ); ?>
						</span>
					</a>
				</li>

				<?php
			}
			?>

		</ul>

		<?php
	}

	do_action( 'wpclubmanager_after_single_club_content' );
	?>

</article>

<?php do_action( 'wpclubmanager_after_single_club' ); ?>
