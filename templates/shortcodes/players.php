<?php
/**
 * Players
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="wpcm-players-shortcode">

	<?php echo ( $title && ! $type == 'widget' ? '<h3>' . $title . '</h3>' : '' ); ?>

	<table>
		<thead>
			<tr>

				<?php foreach( $stats as $stat ) {
					if ( $stat !== 'subs' ) { ?>

						<th class="<?php echo $stat; ?>"><?php echo $stats_labels[$stat]; ?></th>

					<?php }
				} ?>

			</tr>
		</thead>
		<tbody>

		<?php $count = 0;
		foreach( $player_details as $player_detail ) {
			$count++;
			if ( $limit > 0 && $count > $limit ) {
				break;
			} ?>

			<tr>

			<?php foreach( $stats as $stat ) {
				if ( $stat !== 'subs' ) { ?>

					<td class="<?php echo $stat; ?>">

						<?php echo wpcm_get_player_stat( $player_detail, $stat ); ?>

					</td>

				<?php }
			} ?>

			</tr>

		<?php } ?>

		</tbody>
	</table>
	<?php if ( isset( $linkpage ) ) { ?>
		<a href="<?php echo get_page_link( $linkpage ); ?>" class="wpcm-view-link">
			<?php echo $linktext; ?>
		</a>
	<?php } ?>
</div>