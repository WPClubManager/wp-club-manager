<?php
/**
 * Club Staff
 *
 * Display staff for clubs in League Mode only.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.0.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Club_Staff
 */
class WPCM_Meta_Box_Club_Staff {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		$args = array(
			'post_type'      => 'wpcm_staff',
			'posts_per_page' => -1,
			'meta_query'     => array(
				array(
					'key'   => '_wpcm_staff_club',
					'value' => $post->ID,
				),
			),
		);

		$players = get_posts( $args );
		?>

		<div id="wpcm-club-player-stats">
			<table>
				<?php
				if ( null == $players ) {
					?>
					<thead>
						<tr>
							<th><?php esc_html_e( 'Name', 'wp-club-manager' ); ?></th>
							<th></th>
						</tr>
					</thead>
					<?php
				}
				?>
				<tbody>

				<?php
				foreach ( $players as $player ) {
					?>

					<tr data-club="<?php echo esc_attr( $player->ID ); ?>">

						<td class="club">
							<?php echo esc_html( $player->post_title ); ?>
						</td>
						<td class="roster-actions">
							<a class="" href="<?php echo esc_url( get_edit_post_link( $player->ID ) ); ?>"><?php esc_html_e( 'Manage', 'wp-club-manager' ); ?></a>
						</td>

					</tr>
				<?php } ?>

				</tbody>
			</table>
		</div>

		<?php
	}
}
