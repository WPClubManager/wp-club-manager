<?php
/**
 * Standings
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly ?>

<div class="wpcm-standings-shortcode wpcm-standings">

	<?php echo ( $title && 'widget' !== $type ? '<h3>' . esc_html( $title ) . '</h3>' : '' ); ?>

	<table>
		<thead>
			<tr>
				<th></th>
				<th></th>
				<?php foreach ( $stats as $stat ) { ?>

					<th class="<?php echo esc_attr( $stat ); ?>"><?php echo esc_html( $stats_labels[ $stat ] ); ?></th>

				<?php } ?>

			</tr>
		</thead>
		<tbody>

		<?php
		$rownum = 0;
		foreach ( $clubs as $club ) {
			++$rownum;
			$club_stats = $club->wpcm_stats;
			?>

			<tr class="<?php echo ( $club->ID === (int) $center ? 'highlighted ' : '' ) . ( 0 === $rownum % 2 ? 'even' : 'odd' ) . ( $rownum === $limit ? ' last' : '' ); ?>">

				<td class="pos">
					<?php echo esc_html( $club->place ); ?>
				</td>

				<td class="club">
					<?php
					echo wp_kses_post( $club->thumb );
					echo ( 1 === $link_club ? '<a href="' . esc_url( get_the_permalink( $club->ID ) ) . '">' : '' );
					echo esc_html( $club->post_title );
					echo ( 1 === $link_club ? '</a>' : '' );
					?>
				</td>

				<?php foreach ( $stats as $stat ) { ?>

					<td class="<?php echo esc_attr( $stat ); ?>"><?php echo esc_html( $club_stats[ $stat ] ); ?></td>

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
