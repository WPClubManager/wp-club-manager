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
} // Exit if accessed directly ?>

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
