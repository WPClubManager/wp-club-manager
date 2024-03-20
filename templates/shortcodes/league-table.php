<?php
/**
 * Standings
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     2.1.0
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
				<?php
				foreach ( $columns as $column ) {
					?>

					<th class="<?php echo esc_attr( $column ); ?>"><?php echo wp_kses_post( $stats_labels[ $column ] ); ?></th>

				<?php } ?>

			</tr>
		</thead>
		<tbody>

		<?php
		foreach ( $clubs as $club ) {

			$club_stats = $club->wpcm_stats;
			?>

			<tr class="<?php echo ( $default_club == $club->ID ? 'highlighted ' : '' ); ?>">

				<td class="pos">
					<?php echo esc_html( $club->place ); ?>
				</td>

				<td class="club">
					<?php
					echo wp_kses_post( $club->thumb );
					if ( $default_club == $club->ID ) {
						if ( 1 == $abbr ) {
							$club_abbr = get_club_abbreviation( $club->ID );
							echo esc_html( $club_abbr );
						} elseif ( $team_label ) {
								echo esc_html( $team_label );
						} else {
							echo esc_html( $club->post_title );
						}
					} elseif ( 1 == $abbr ) {
							echo ( 1 == $link_club ? '<a href="' . esc_url( get_the_permalink( $club->ID ) ) . '">' : '' );
							$club_abbr = get_club_abbreviation( $club->ID );
							echo esc_html( $club_abbr );
							echo ( 1 == $link_club ? '</a>' : '' );
					} else {
						echo ( 1 == $link_club ? '<a href="' . esc_url( get_the_permalink( $club->ID ) ) . '">' : '' );
						echo esc_html( $club->post_title );
						echo ( 1 == $link_club ? '</a>' : '' );
					}
					?>
				</td>

				<?php foreach ( $columns as $column ) { ?>

					<td class="<?php echo esc_attr( $column ); ?>"><?php echo esc_html( $club_stats[ $column ] ); ?></td>

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
