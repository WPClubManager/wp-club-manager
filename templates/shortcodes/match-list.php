<?php
/**
 * Matches - League matches shortcode layout
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     2.2.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly ?>

<div class="wpcm-fixtures-shortcode">

	<?php echo ( $title ? '<h3>' . esc_html( $title ) . '</h3>' : '' ); ?>

	<ul class="wpcm-matches-list">

	<?php
	foreach ( $matches as $match ) {

		$played    = get_post_meta( $match->ID, 'wpcm_played', true );
		$timestamp = strtotime( $match->post_date );
		$comp      = wpcm_get_match_comp( $match->ID );
		if ( $show_abbr ) {
			$sides = wpcm_get_match_clubs( $match->ID, true );
		} else {
			$sides = wpcm_get_match_clubs( $match->ID, false );
		}
		$side1  = $sides[0];
		$side2  = $sides[1];
		$result = wpcm_get_match_result( $match->ID );

		// Display Badge
		$home_badge = '';
		$away_badge = '';
		if ( '1' === $show_thumb ) {
			$badges     = wpcm_get_match_badges( $match->ID, 'crest-small' );
			$home_badge = '<span class="club-thumb">' . $badges[0] . '</span>';
			$away_badge = '<span class="club-thumb">' . $badges[1] . '</span>';
		}
		?>

		<li class="wpcm-matches-list-item">
			<a href="<?php echo esc_url( get_post_permalink( $match->ID, false, true ) ); ?>" class="wpcm-matches-list-link">
				<span class="wpcm-matches-list-col wpcm-matches-list-date">
					<?php echo esc_html( date_i18n( apply_filters( 'wpclubmanager_match_date_format', 'D d M' ), $timestamp ) ); ?>
				</span>
				<span class="wpcm-matches-list-col wpcm-matches-list-club1">
					<?php echo esc_html( $side1 ); ?>
				</span>
				<?php echo wp_kses_post( $home_badge ); ?>
				<span class="wpcm-matches-list-col wpcm-matches-list-status">
					<span class="wpcm-matches-list-<?php echo ( $played ? 'result' : 'time' ); ?>">
						<?php echo esc_html( $played ? $result[0] : date_i18n( apply_filters( 'wpclubmanager_match_time_format', get_option( 'time_format' ) ), $timestamp ) ); ?>
					</span>
				</span>
				<?php echo wp_kses_post( $away_badge ); ?>
				<span class="wpcm-matches-list-col wpcm-matches-list-club2">
					<?php echo esc_html( $side2 ); ?>
				</span>
				<?php if ( 1 === $show_comp ) { ?>
					<span class="wpcm-matches-list-col wpcm-matches-list-info">
						<?php echo esc_html( $comp[1] ); ?>
					</span>
					<?php
				}
				?>
			</a>
		</li>

	<?php } ?>

	</ul>

	<?php if ( isset( $linkpage ) ) { ?>
		<a href="<?php echo esc_url( get_page_link( $linkpage ) ); ?>" class="wpcm-view-link">
			<?php echo esc_html( $linktext ); ?>
		</a>
	<?php } ?>

</div>
