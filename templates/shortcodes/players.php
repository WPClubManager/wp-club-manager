<?php
/**
 * Players
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// Initialize variables that may be set via extract() in the calling context.
$limit          = isset( $limit ) ? $limit : 0;
$linktext       = isset( $linktext ) ? $linktext : '';
$player_details = isset( $player_details ) ? $player_details : array();
$stats          = isset( $stats ) ? $stats : array();
$stats_labels   = isset( $stats_labels ) ? $stats_labels : array();
$title          = isset( $title ) ? $title : '';
$type           = isset( $type ) ? $type : '';
?>

<div class="wpcm-players-shortcode">

	<?php echo ( $title && 'widget' !== $type ? '<h3>' . wp_kses_post( $title ) . '</h3>' : '' ); ?>

	<table>
		<thead>
			<tr>

				<?php
				foreach ( $stats as $stat ) {
					if ( 'subs' !== $stat ) {
						?>

						<th class="<?php echo esc_attr( $stat ); ?>"><?php echo wp_kses_post( $stats_labels[ $stat ] ); ?></th>

						<?php
					}
				}
				?>

			</tr>
		</thead>
		<tbody>

		<?php
		$count = 0;
		foreach ( $player_details as $player_detail ) {
			++$count;
			if ( $limit > 0 && $count > $limit ) {
				break;
			}
			?>

			<tr>

			<?php
			foreach ( $stats as $stat ) {
				if ( 'subs' !== $stat ) {
					?>

					<td class="<?php echo esc_attr( $stat ); ?>">

						<?php echo wp_kses_post( wpcm_get_player_stat( $player_detail, $stat ) ); ?>

					</td>

					<?php
				}
			}
			?>

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
