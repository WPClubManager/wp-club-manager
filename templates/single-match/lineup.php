<?php
/**
 * Single Match - Lineup
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$played                   = get_post_meta( $post->ID, 'wpcm_played', true );
$players                  = unserialize( get_post_meta( $post->ID, 'wpcm_players', true ) );
$wpcm_player_stats_labels = wpcm_get_preset_labels();
$subs_not_used            = get_post_meta( $post->ID, '_wpcm_match_subs_not_used', true );

if ( $played && $players ) {

	if ( array_key_exists( 'lineup', $players ) && is_array( $players['lineup'] ) ) { ?>

		<div class="wpcm-match-stats-start">

			<h3><?php echo esc_html_e( 'Lineup', 'wp-club-manager' ); ?></h3>

			<table class="wpcm-lineup-table">
				<thead>
					<tr>

						<?php if ( 'yes' === get_option( 'wpcm_lineup_show_shirt_numbers' ) ) { ?>

							<th class="shirt-number"></th>

						<?php } ?>

						<th class="name"><?php esc_html_e( 'Name', 'wp-club-manager' ); ?></th>

						<?php
						foreach ( $wpcm_player_stats_labels as $key => $val ) {
							if ( ! in_array( $key, wpcm_exclude_keys() ) && 'yes' === get_option( 'wpcm_show_stats_' . $key ) && 'yes' === get_option( 'wpcm_match_show_stats_' . $key ) ) {
								?>

								<th class="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $val ); ?></th>

								<?php
							}
						}
						if ( 'yes' === get_option( 'wpcm_show_stats_greencards' ) && 'yes' === get_option( 'wpcm_match_show_stats_greencards' ) || 'yes' === get_option( 'wpcm_show_stats_yellowcards' ) && 'yes' === get_option( 'wpcm_match_show_stats_yellowcards' ) || 'yes' === get_option( 'wpcm_show_stats_blackcards' ) && 'yes' === get_option( 'wpcm_match_show_stats_blackcards' ) || 'yes' === get_option( 'wpcm_show_stats_redcards' ) && 'yes' === get_option( 'wpcm_match_show_stats_redcards' ) ) {
							?>

								<th class="notes"><?php esc_html_e( 'Cards', 'wp-club-manager' ); ?></th>

						<?php } ?>

					</tr>
				</thead>
				<tbody>

					<?php
					$count = 0;
					foreach ( $players['lineup'] as $key => $value ) {
						++$count;

						wpclubmanager_get_template( 'single-match/lineup-row.php', array(
							'key'   => $key,
							'value' => $value,
							'count' => $count,
						) );
					}
					?>

				</tbody>
			</table>
		</div>

		<?php
	}
	if ( array_key_exists( 'subs', $players ) && is_array( $players['subs'] ) || is_array( $subs_not_used ) ) {
		?>

		<div class="wpcm-match-stats-subs">

			<h3><?php echo esc_html_e( 'Subs', 'wp-club-manager' ); ?></h3>

			<table class="wpcm-subs-table">
				<thead>
					<tr>

						<?php if ( 'yes' === get_option( 'wpcm_lineup_show_shirt_numbers' ) ) { ?>

							<th class="shirt-number"></th>

						<?php } ?>

						<th class="name"><?php esc_html_e( 'Name', 'wp-club-manager' ); ?></th>

						<?php
						foreach ( $wpcm_player_stats_labels as $key => $val ) {
							if ( ! in_array( $key, wpcm_exclude_keys() ) && 'yes' === get_option( 'wpcm_show_stats_' . $key ) && 'yes' === get_option( 'wpcm_match_show_stats_' . $key ) ) {
								?>

								<th class="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $val ); ?></th>

								<?php
							}
						}
						if ( 'yes' === get_option( 'wpcm_show_stats_greencards' ) && 'yes' === get_option( 'wpcm_match_show_stats_greencards' ) || 'yes' === get_option( 'wpcm_show_stats_yellowcards' ) && 'yes' === get_option( 'wpcm_match_show_stats_yellowcards' ) || 'yes' === get_option( 'wpcm_show_stats_blackcards' ) && 'yes' === get_option( 'wpcm_match_show_stats_blackcards' ) || 'yes' === get_option( 'wpcm_show_stats_redcards' ) && 'yes' === get_option( 'wpcm_match_show_stats_redcards' ) ) {
							?>

								<th class="notes"><?php esc_html_e( 'Cards', 'wp-club-manager' ); ?></th>

						<?php } ?>

					</tr>
				</thead>
				<tbody>

					<?php
					foreach ( $players['subs'] as $key => $value ) {
						++$count;

						wpclubmanager_get_template( 'single-match/lineup-row.php', array(
							'key'   => $key,
							'value' => $value,
							'count' => $count,
						) );
					}

					if ( is_array( $subs_not_used ) ) {

						foreach ( $subs_not_used as $key => $value ) {
							++$count;

							wpclubmanager_get_template( 'single-match/lineup-row.php', array(
								'key'   => $key,
								'value' => array(),
								'count' => $count,
							) );
						}
					}
					?>

				</tbody>
			</table>
		</div>

		<?php
	}
}
