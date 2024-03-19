<?php
/**
 * Staff Gallery Shortcode
 *
 * @author      Clubpress
 * @category    Shortcodes
 * @package     WPClubManager/Shortcodes
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Shortcode_Staff_Gallery
 */
class WPCM_Shortcode_Staff_Gallery {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		return WPCM_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the standings shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		extract( shortcode_atts( array(), $atts ) ); // phpcs:ignore

		$id          = ( isset( $atts['id'] ) ? $atts['id'] : null );
		$title       = ( isset( $atts['title'] ) ? $atts['title'] : __( 'Staff Gallery', 'wp-club-manager' ) );
		$limit       = ( isset( $atts['limit'] ) ? $atts['limit'] : -1 );
		$jobs        = ( isset( $atts['jobs'] ) ? $atts['jobs'] : null );
		$orderby     = ( isset( $atts['orderby'] ) ? $atts['orderby'] : 'name' );
		$order       = ( isset( $atts['order'] ) ? $atts['order'] : 'ASC' );
		$columns     = ( isset( $atts['columns'] ) ? $atts['columns'] : '3' );
		$linktext    = ( isset( $atts['linktext'] ) ? $atts['linktext'] : __( 'View all staff', 'wp-club-manager' ) );
		$linkpage    = ( isset( $atts['linkpage'] ) ? $atts['linkpage'] : null );
		$name_format = ( isset( $atts['name_format'] ) ? $atts['name_format'] : 'full' );
		$type        = ( isset( $atts['type'] ) ? $atts['type'] : '' );

		if ( '' === $limit ) {
			$limit = -1;
		}
		if ( '' === $position ) {
			$position = null;
		}
		if ( '' === $orderby ) {
			$orderby = 'number';
		}
		if ( '' === $order ) {
			$order = 'ASC';
		}
		if ( '' === $columns ) {
			$columns = '3';
		}
		if ( '' === $name_format ) {
			$name_format = 'full';
		}
		if ( '' === $linkpage ) {
			$linkpage = null;
		}

		$disable_cache = get_option( 'wpcm_disable_cache' );
		if ( 'no' === $disable_cache && 'widget' !== $type ) {
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $atts, 'staff_gallery' );
			$output         = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if ( false === $output ) {

			$selected_staff = (array) unserialize( get_post_meta( $id, '_wpcm_roster_staff', true ) );
			$seasons        = get_the_terms( $id, 'wpcm_season' );
			$season         = $seasons[0]->term_id;
			$teams          = get_the_terms( $id, 'wpcm_team' );
			$team           = $teams[0]->term_id;

			$orderby = strtolower( $orderby );
			$order   = strtoupper( $order );

			$query_args = array(
				'post_type'      => 'wpcm_staff',
				'tax_query'      => array(), // phpcs:ignore
				'numposts'       => $limit,
				'posts_per_page' => -1,
				'orderby'        => $orderby,
				'order'          => $order,
				'post__in'       => $selected_staff,
			);

			if ( $jobs ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'wpcm_jobs',
					'terms'    => $jobs,
					'field'    => 'term_id',
				);
			}

			$employee = get_posts( $query_args );

			if ( $employee ) {

				$employee_details = array();

				foreach ( $employee as $employee ) {

					$employee_details[ $employee->ID ] = array();

					if ( has_post_thumbnail( $employee->ID ) ) {
						$thumb = get_the_post_thumbnail( $employee->ID, 'player-medium' );
					} else {
						$thumb = wpcm_placeholder_img( 'full' );
					}

					$employee_details[ $employee->ID ]['image'] = '<a href="' . get_permalink( $employee->ID ) . '">' . $thumb . '</a>';

					$employee_details[ $employee->ID ]['title'] = '<a href="' . get_permalink( $employee->ID ) . '">' . get_player_title( $employee->ID, $name_format ) . '</a>';

				}

				ob_start();

				wpclubmanager_get_template( 'shortcodes/staff-gallery.php', array(
					'type'             => $type,
					'title'            => $title,
					'employee_details' => $employee_details,
					'limit'            => $limit,
					'linkpage'         => $linkpage,
					'linktext'         => $linktext,
					'columns'          => $columns,
				) );

				$output = ob_get_clean();
				wp_reset_postdata();

				if ( 'no' === $disable_cache ) {
					set_transient( $transient_name, $output, 4 * WEEK_IN_SECONDS );
					do_action( 'update_plugin_transient_keys', $transient_name );
				}
			}
		}

		echo $output; // phpcs:ignore
	}
}
