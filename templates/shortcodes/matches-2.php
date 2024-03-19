<?php
/**
 * Matches - New matches shortcode layout
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly ?>

<div class="wpcm-fixtures-shortcode">

	<?php echo ( $title ? '<h3>' . esc_html( $title ) . '</h3>' : '' ); ?>

	<ul class="wpcm-matches-list">

	<?php
	foreach ( $matches as $match ) {

		$played      = get_post_meta( $match->ID, 'wpcm_played', true );
		$timestamp   = strtotime( $match->post_date );
		$time_format = get_option( 'time_format' );
		$class       = wpcm_get_match_outcome( $match->ID );
		$comp        = wpcm_get_match_comp( $match->ID );
		$sides       = wpcm_get_match_clubs( $match->ID );
		$side1       = $sides[0];
		$side2       = $sides[1];
		$result      = wpcm_get_match_result( $match->ID );
		?>

		<li class="wpcm-matches-list-item <?php echo esc_attr( $class ); ?>">
			<a href="<?php echo esc_url( get_post_permalink( $match->ID, false, true ) ); ?>" class="wpcm-matches-list-link">
				<span class="wpcm-matches-list-col wpcm-matches-list-date">
					<?php echo esc_html( date_i18n( 'D d M', $timestamp ) ); ?>
				</span>
				<span class="wpcm-matches-list-col wpcm-matches-list-club1">
					<?php echo esc_html( $side1 ); ?>
				</span>
				<span class="wpcm-matches-list-col wpcm-matches-list-status">
					<span class="wpcm-matches-list-<?php echo ( $played ? 'result' : 'time' ); ?> <?php echo esc_attr( $class ); ?>">
						<?php echo esc_html( $played ? $result[0] : date_i18n( $time_format, $timestamp ) ); ?>
					</span>
				</span>
				<span class="wpcm-matches-list-col wpcm-matches-list-club2">
					<?php echo esc_html( $side2 ); ?>
				</span>
				<span class="wpcm-matches-list-col wpcm-matches-list-info">
					<?php echo esc_html( $comp[1] ); ?>
				</span>
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
