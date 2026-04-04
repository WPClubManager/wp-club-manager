<?php
/**
 * Matche Opponents Shortcode
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     2.2.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// Initialize variables that may be set via extract() in the calling context.
$away_badge = isset( $away_badge ) ? $away_badge : '';
$club       = isset( $club ) ? $club : '';
$home_badge = isset( $home_badge ) ? $home_badge : '';
$linktext   = isset( $linktext ) ? $linktext : '';
$matches    = isset( $matches ) ? $matches : array();
$show_abbr  = isset( $show_abbr ) ? $show_abbr : false;
$show_comp  = isset( $show_comp ) ? $show_comp : false;
$show_team  = isset( $show_team ) ? $show_team : false;
$show_thumb = isset( $show_thumb ) ? $show_thumb : false;
$title      = isset( $title ) ? $title : '';
?>

<div class="wpcm-fixtures-shortcode">

	<?php echo( $title ? '<h3>' . esc_html( $title ) . '</h3>' : '' ); ?>

	<table>
		<tbody>

			<?php
			foreach ( $matches as $match ) {
				$home_club = get_post_meta( $match->ID, 'wpcm_home_club', true );
				$played    = get_post_meta( $match->ID, 'wpcm_played', true );
				$timestamp = strtotime( $match->post_date );
				$venue     = wpcm_get_match_venue( $match->ID );
				$team      = wpcm_get_match_team( $match->ID );
				$comp      = wpcm_get_match_comp( $match->ID );
				$result    = wpcm_get_match_result( $match->ID );
				if ( $show_abbr ) {
					$opponent = wpcm_get_match_opponents( $match->ID, true );
				} else {
					$opponent = wpcm_get_match_opponents( $match->ID, false );
				}
				$class = wpcm_get_match_outcome( $match->ID );

				// Display Badge
				if ( '1' === $show_thumb ) {
					$badges     = wpcm_get_match_badges( $match->ID, 'crest-small' );
					$home_badge = '<td class="club-thumb">' . $badges[0] . '</td>';
					$away_badge = '<td class="club-thumb">' . $badges[1] . '</td>';
				}
				?>


			<tr data-url="<?php echo esc_url( get_post_permalink( $match->ID, false, true ) ); ?>">
				<td class="wpcm-date">
					<a class="wpcm-matches-href" href="<?php echo esc_url( get_post_permalink( $match->ID, false, true ) ); ?>">
						<?php echo esc_html( date_i18n( apply_filters( 'wpclubmanager_match_date_format', 'D d M' ), $timestamp ) ); ?>, <?php echo esc_html( date_i18n( apply_filters( 'wpclubmanager_match_time_format', get_option( 'time_format' ) ), $timestamp ) ); ?>
					</a>
				</td>
				<td class="venue"><?php echo esc_html( is_array( $venue ) ? $venue['status'] : '' ); ?></td>
				<?php
				if ( '1' === $show_thumb ) {
					echo wp_kses_post( $club === $home_club ? $away_badge : $home_badge );
				}
				?>
				<td class="opponent"><?php echo esc_html( $opponent ); ?></td>
				<?php if ( '1' === $show_team ) { ?>
				<td class="team"><?php echo esc_html( $team[0] ); ?></td>
				<?php } ?>
				<?php if ( '1' === $show_comp ) { ?>
				<td class="competition"><?php echo esc_html( $comp[1] ); ?></td>
				<?php } ?>
				<td class="result <?php echo esc_attr( $class ); ?>"><?php echo esc_html( $result[0] ); ?> <?php echo( $played ? '<span class="' . esc_attr( $class ) . '"></span>' : '' ); ?></td>
			</tr>

				<?php
			}
			?>


		</tbody>
	</table>
	<?php if ( isset( $linkpage ) ) { ?>
	<a href="<?php echo esc_url( get_page_link( $linkpage ) ); ?>" class="wpcm-view-link">
		<?php echo esc_html( $linktext ); ?>
	</a>
	<?php } ?>
</div>
