<?php
/**
 * Match Calendar - Monthly calendar grid view
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @var int $month */
/** @var int $year */
/** @var array $matches_by_day */

$site_tz       = wp_timezone();
$month_dt      = new DateTimeImmutable( sprintf( '%04d-%02d-01', $year, $month ), $site_tz );
$month_name    = wp_date( 'F', $month_dt->getTimestamp(), $site_tz );
$days_in_month = (int) $month_dt->format( 't' );
// 1 = Monday … 7 = Sunday (ISO-8601).
$first_weekday = (int) $month_dt->format( 'N' );

$day_labels = array(
	__( 'Mon', 'wp-club-manager' ),
	__( 'Tue', 'wp-club-manager' ),
	__( 'Wed', 'wp-club-manager' ),
	__( 'Thu', 'wp-club-manager' ),
	__( 'Fri', 'wp-club-manager' ),
	__( 'Sat', 'wp-club-manager' ),
	__( 'Sun', 'wp-club-manager' ),
);

// Navigation: previous / next month.
$prev_month = $month - 1;
$prev_year  = $year;
if ( $prev_month < 1 ) {
	$prev_month = 12;
	--$prev_year;
}
$next_month = $month + 1;
$next_year  = $year;
if ( $next_month > 12 ) {
	$next_month = 1;
	++$next_year;
}
?>

<div class="wpcm-calendar">

	<div class="wpcm-calendar-header">
		<?php
		$prev_url = esc_url( add_query_arg(
			array(
				'wpcm_cal_month' => $prev_month,
				'wpcm_cal_year'  => $prev_year,
			)
		) );
		?>
		<a class="wpcm-calendar-nav wpcm-calendar-prev" href="<?php echo $prev_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" aria-label="<?php esc_attr_e( 'Previous month', 'wp-club-manager' ); ?>">
			&laquo;
		</a>
		<span class="wpcm-calendar-title">
			<?php echo esc_html( $month_name . ' ' . $year ); ?>
		</span>
		<?php
		$next_url = esc_url( add_query_arg(
			array(
				'wpcm_cal_month' => $next_month,
				'wpcm_cal_year'  => $next_year,
			)
		) );
		?>
		<a class="wpcm-calendar-nav wpcm-calendar-next" href="<?php echo $next_url; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>" aria-label="<?php esc_attr_e( 'Next month', 'wp-club-manager' ); ?>">
			&raquo;
		</a>
	</div>

	<table class="wpcm-calendar-table">
		<thead>
			<tr>
				<?php foreach ( $day_labels as $label ) : ?>
					<th scope="col"><?php echo esc_html( $label ); ?></th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php
			$current_day = 1;
			$cell        = 1;

			// Calculate total cells needed (complete weeks).
			$total_cells = $first_weekday - 1 + $days_in_month;
			$total_rows  = (int) ceil( $total_cells / 7 );

			for ( $row = 0; $row < $total_rows; $row++ ) :
				?>
				<tr>
					<?php
					for ( $col = 0; $col < 7; $col++ ) :
						if ( $cell < $first_weekday || $current_day > $days_in_month ) :
							?>
							<td class="wpcm-calendar-empty"></td>
							<?php
						else :
							$has_matches = isset( $matches_by_day[ $current_day ] );
							$css_class   = 'wpcm-calendar-day';
							if ( $has_matches ) {
								$css_class .= ' wpcm-calendar-has-match';
							}
							?>
							<td class="<?php echo esc_attr( $css_class ); ?>">
								<span class="wpcm-calendar-day-number"><?php echo esc_html( (string) $current_day ); ?></span>
								<?php
								if ( $has_matches ) :
									foreach ( $matches_by_day[ $current_day ] as $match ) :
										$played    = get_post_meta( $match->ID, 'wpcm_played', true );
										$timestamp = strtotime( $match->post_date );
										$sides     = wpcm_get_match_clubs( $match->ID, true );
										$result    = wpcm_get_match_result( $match->ID );
										?>
										<a href="<?php echo esc_url( get_post_permalink( $match->ID, false, true ) ); ?>" class="wpcm-calendar-match">
											<span class="wpcm-calendar-match-teams">
												<?php echo esc_html( $sides[0] . ' v ' . $sides[1] ); ?>
											</span>
											<span class="wpcm-calendar-match-info">
												<?php
												if ( $played ) {
													echo esc_html( $result[0] );
												} else {
													echo esc_html( date_i18n( apply_filters( 'wpclubmanager_match_time_format', get_option( 'time_format' ) ), $timestamp ) );
												}
												?>
											</span>
										</a>
										<?php
									endforeach;
								endif;
								?>
							</td>
							<?php
							++$current_day;
						endif;
						++$cell;
					endfor;
					?>
				</tr>
			<?php endfor; ?>
		</tbody>
	</table>

</div>
