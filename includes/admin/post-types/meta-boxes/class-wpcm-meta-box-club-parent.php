<?php
/**
 * Parent Club Meta Box
 *
 * Choose a parent club.
 *
 * @author      ClubPress
 * @category    Admin
 * @package     WPClubManager/Admin/Meta Boxes
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Meta_Box_Club_Parent
 */
class WPCM_Meta_Box_Club_Parent {

	/**
	 * Output the metabox
	 *
	 * @param WP_Post $post
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$child_array = get_pages( array(
			'post_type'     => 'wpcm_club',
			'post_per_page' => -1,
		) );
		$children    = get_page_children( $post->ID, $child_array );
		$count       = count( $children );

		if ( $post->post_parent ) {
			$club = $post->post_parent;
		} else {
			$club = null;
		} ?>

		<p>
			<label><?php esc_html_e( 'Parent Club', 'wp-club-manager' ); ?></label>
			<?php
			wpcm_dropdown_posts(array(
				'name'             => 'parent_id',
				'id'               => 'parent_id',
				'post_type'        => 'wpcm_club',
				'limit'            => -1,
				'show_option_none' => __( 'None', 'wp-club-manager' ),
				'class'            => 'chosen_select',
				'order'            => 'ASC',
				'orderby'          => 'name',
				'selected'         => $club,
				'echo'             => false,
			));
			if ( $club ) {
				?>
				<span class="edit"><a href="<?php echo esc_url( get_edit_post_link( $club ) ); ?>"><?php esc_html_e( 'Edit club', 'wp-club-manager' ); ?></span></a>
				<?php
			}
			?>
		</p>

		<?php
		if ( $children ) {
			?>
			<span class="label">
				<?php echo esc_html( _n( 'Child Club', 'Child Clubs', $count, 'wp-club-manager' ) ); ?>
			</span>
			<ul>
				<?php
				foreach ( $children as $child ) {
					?>
					<li><a href="<?php echo esc_url( get_edit_post_link( $child->ID ) ); ?>"><?php echo esc_html( $child->post_title ); ?></a></li>
					<?php
				}
				?>
			</ul>
			<?php
		}
	}

	/**
	 * Save meta box data
	 *
	 * @param int     $post_id
	 * @param WP_Post $post
	 */
	public static function save( $post_id, $post ) {
		if ( ! check_admin_referer( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' ) ) {
			return;
		}

		$parent = filter_input( INPUT_POST, 'parent_id', FILTER_VALIDATE_INT );
		if ( isset( $parent ) ) {
			update_post_meta( $post_id, '_wpcm_club_parent', $parent );
		}
	}
}
