<?php
/**
 * Match Fixture
 *
 * Displays the match fixture box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Match_Fixture {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		$post_id = $post->ID;
		$home_club = get_post_meta( $post_id, 'wpcm_home_club', true );
		$away_club = get_post_meta( $post_id, 'wpcm_away_club', true );

		$club_buttons_ajax_nonce = wp_create_nonce( 'wpcm_club_buttons_ajax_nonce' ); ?>

		<table id="fixtures-table">
			<thead>
				<tr>
					<th><?php _e( 'Home', 'wpclubmanager' ); ?></th>
					<th><?php _e( 'Away', 'wpclubmanager' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>
						<a class="thickbox wpcm-club-big-button" id="wpcm_home_club_button" data-club="<?php echo $home_club; ?>" href="<?php echo admin_url('admin-ajax.php?action=wpcm_club_buttons&width=664&side=home&eid=wpcm_home_club&nonce=' . $club_buttons_ajax_nonce ); ?>" title="<?php printf( __( 'Select %s', 'wpclubmanager' ), __( 'Club', 'wpclubmanager' ) ); ?>">
							<?php if ( $home_club ) { ?>
								<?php echo get_the_post_thumbnail( $home_club, 'crest-large', array( 'title' => sprintf( __( 'Select %s', 'wpclubmanager' ), __( 'Club', 'wpclubmanager' ) ) ) ); ?>
								<span class="ellipsis"><?php echo get_the_title( $home_club ); ?></span>
							<?php } else { ?>
								<span class="button action"><?php printf( __( 'Select %s', 'wpclubmanager' ), __( 'Club', 'wpclubmanager' ) ); ?></span>
							<?php } ?>
						</a>
						<input type="hidden" name="wpcm_home_club" id="wpcm_home_club" value="<?php echo $home_club ?>" />
					</td>
					<td class="away-club">
						<a class="thickbox wpcm-club-big-button" id="wpcm_away_club_button" data-club="<?php echo $away_club; ?>" href="<?php echo admin_url('admin-ajax.php?action=wpcm_club_buttons&width=664&side=away&eid=wpcm_away_club&nonce=' . $club_buttons_ajax_nonce ); ?>" title="<?php printf( __( 'Select %s', 'wpclubmanager' ), __( 'Club', 'wpclubmanager' ) ); ?>">
							<?php if ( $away_club ) { ?>
								<?php echo get_the_post_thumbnail( $away_club, 'crest-large', array( 'title' => sprintf( __( 'Select %s', 'wpclubmanager' ), __( 'Club', 'wpclubmanager' ) ) ) ); ?>
								<span class="ellipsis"><?php echo get_the_title( $away_club ); ?></span>
							<?php } else { ?>
								<span class="button action"><?php printf( __( 'Select %s', 'wpclubmanager' ), __( 'Club', 'wpclubmanager' ) ); ?></span>
							<?php } ?>
						</a>
						<input type="hidden" name="wpcm_away_club" id="wpcm_away_club" value="<?php echo $away_club ?>" />
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="post_title" value="" />
	<?php }

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		$home_club = $_POST['wpcm_home_club'];
		$away_club = $_POST['wpcm_away_club'];

		$comp = $_POST['wpcm_comp'];
		$season = $_POST['wpcm_season'];

		update_post_meta( $post_id, 'wpcm_home_club', $home_club );
		update_post_meta( $post_id, 'wpcm_away_club', $away_club );

		// add comps and sesaons to clubs
		wp_set_post_terms( $home_club, $comp, 'wpcm_comp', true );
		wp_set_post_terms( $home_club, $season, 'wpcm_season', true );
		wp_set_post_terms( $away_club, $comp, 'wpcm_comp', true );
		wp_set_post_terms( $away_club, $season, 'wpcm_season', true );
	}
}