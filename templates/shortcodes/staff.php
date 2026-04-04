<?php
/**
 * Staff shortcode template
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// Initialize variables that may be set via extract() in the calling context.
$limit         = isset( $limit ) ? $limit : 0;
$linktext      = isset( $linktext ) ? $linktext : '';
$staff_details = isset( $staff_details ) ? $staff_details : array();
$stats         = isset( $stats ) ? $stats : array();
$stats_labels  = isset( $stats_labels ) ? $stats_labels : array();
$title         = isset( $title ) ? $title : '';
?>

<div class="wpcm-players-shortcode">

	<?php echo ( $title ? '<h3 class="wpcm-sc-title">' . esc_html( $title ) . '</h3>' : '' ); ?>

	<table>
		<thead>
			<tr>

			<?php foreach ( $stats as $stat ) { ?>

				<th class="<?php echo esc_attr( $stat ); ?>">
					<?php echo wp_kses_post( $stats_labels[ $stat ] ); ?>
				</th>

			<?php } ?>

			</tr>
		</thead>
		<tbody>

		<?php
		$count = 0;
		foreach ( $staff_details as $staff_detail ) {
			++$count;
			if ( $limit > 0 && $count > $limit ) {
				break;
			}
			?>

			<tr>

			<?php foreach ( $stats as $stat ) { ?>

				<td class="<?php echo esc_attr( $stat ); ?>">
					<?php echo wp_kses_post( $staff_detail[ $stat ] ); ?>
				</td>

			<?php } ?>

			</tr>

		<?php } ?>

		</tbody>
	</table>
	<?php if ( isset( $linkpage ) ) { ?>
		<a href="<?php echo esc_url( get_page_link( $linkpage ) ); ?>" class="wpcm-view-link">
			<?php echo esc_html( $linktext ); ?>
		</a>
	<?php } ?>
</div>
