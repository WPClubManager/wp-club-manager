<?php
/**
 * Results Widget
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     2.2.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;
$timestamp = strtotime( $post->post_date ); ?>

<li class="fixture">
	<div class="fixture-meta">
		<?php if ( $show_team ) { ?>
			<div class="team">
				<span><?php echo esc_html( $team[0] ); ?></span>
			</div>
			<?php
		}
		if ( $show_comp ) {
			?>
			<div class="competition">
				<span><?php echo esc_html( $comp[0] ); ?>&nbsp;<?php echo esc_html( $comp[2] ); ?></span>
			</div>
		<?php } ?>
	</div>
	<a href="<?php echo esc_url( get_permalink() ); ?>">
		<div class="clubs">
			<h4 class="home-clubs">
				<div class="home-logo"><?php echo wp_kses_post( $badges[0] ); ?></div>
				<?php echo esc_html( $sides[0] ); ?>
				<div class="score"><?php echo ( $played && $show_score ? esc_html( $score[1] ) : '' ); ?></div>
			</h4>
			<h4 class="away-clubs">
				<div class="away-logo"><?php echo wp_kses_post( $badges[1] ); ?></div>
				<?php echo esc_html( $sides[1] ); ?>
				<div class="score"><?php echo ( $played && $show_score ? esc_html( $score[2] ) : '' ); ?></div>
			</h4>
		</div>
	</a>
	<div class="wpcm-date">
		<div class="kickoff">
			<?php
			echo ( $show_date ? esc_html( date_i18n( apply_filters( 'wpclubmanager_match_date_format', get_option( 'date_format' ) ), $timestamp ) ) : '' );
			echo ( $show_date && $show_time ? ' - ' : '' );
			echo ( $show_time ? esc_html( date_i18n( apply_filters( 'wpclubmanager_match_time_format', get_option( 'time_format' ) ), $timestamp ) ) : '' );
			?>
		</div>
	</div>
</li>
