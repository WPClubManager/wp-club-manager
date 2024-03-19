<?php
/**
 * Staff List Shortcode
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
 * WPCM_Shortcode_Staff_List
 */
class WPCM_Shortcode_Staff_List {

	/**
	 * Output the standings shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		extract( shortcode_atts( array(), $atts ) ); // phpcs:ignore

		$id          = ( isset( $atts['id'] ) ? $atts['id'] : null );
		$limit       = ( isset( $atts['limit'] ) ? $atts['limit'] : -1 );
		$job         = ( isset( $atts['job'] ) ? $atts['job'] : null );
		$orderby     = ( isset( $atts['orderby'] ) ? $atts['orderby'] : 'name' );
		$order       = ( isset( $atts['order'] ) ? $atts['order'] : 'ASC' );
		$linktext    = ( isset( $atts['linktext'] ) ? $atts['linktext'] : __( 'View all staff', 'wp-club-manager' ) );
		$linkpage    = ( isset( $atts['linkpage'] ) ? $atts['linkpage'] : null );
		$columns     = ( isset( $atts['columns'] ) ? $atts['columns'] : 'flag,name,job,age' );
		$title       = ( isset( $atts['title'] ) ? $atts['title'] : __( 'Staff', 'wp-club-manager' ) );
		$name_format = ( isset( $atts['name_format'] ) ? $atts['name_format'] : 'full' );
		$type        = ( isset( $atts['type'] ) ? $atts['type'] : '' );

		if ( '' === $limit ) {
			$limit = -1;
		}
		if ( '' === $job ) {
			$job = null;
		}
		if ( '' === $orderby ) {
			$orderby = 'number';
		}
		if ( '' === $order ) {
			$order = 'ASC';
		}
		if ( '' === $columns ) {
			$columns = 'flag,name,job,age';
		}
		if ( '' === $name_format ) {
			$name_format = 'full';
		}
		if ( '' === $linkpage ) {
			$linkpage = null;
		}

		$disable_cache = get_option( 'wpcm_disable_cache' );
		if ( 'no' === $disable_cache ) {
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $atts, 'staff_list' );
			$output         = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if ( false === $output ) {

			$selected_staff = (array) unserialize( get_post_meta( $id, '_wpcm_roster_staff', true ) );

			$stats_labels = wpcm_staff_labels();

			if ( 0 === (int) $limit ) {
				$limit = - 1;
			}

			$stats = explode( ',', $columns );

			foreach ( $stats as $key => $value ) {
				$stats[ $key ] = strtolower( trim( $value ) );
				if ( ! array_key_exists( $stats[ $key ], $stats_labels ) ) {
					unset( $stats[ $key ] );
				}
			}
			if ( array_intersect_key( array_flip( $stats ), $stats_labels ) ) {
				$limit = -1;
			}

			$orderby = strtolower( $orderby );
			$order   = strtoupper( $order );

			$args = array(
				'post_type'        => 'wpcm_staff',
				'tax_query'        => array(), // phpcs:ignore
				'posts_per_page'   => $limit,
				'order'            => $order,
				'orderby'          => $orderby,
				'suppress_filters' => 0,
				'post__in'         => $selected_staff,
			);
			if ( $job ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_jobs',
					'terms'    => $job,
					'field'    => 'term_id',
				);
			}

			$employees = get_posts( $args );

			$staff_details = array();

			$count = 0;

			if ( count( $employees ) > 0 ) {
				foreach ( $employees as $employee ) {
					++$count;
					foreach ( $stats as $stat ) {

						switch ( $stat ) {
							case 'thumb':
								$staff_details[ $employee->ID ][ $stat ] = '<a href="' . get_permalink( $employee->ID ) . '">' . wpcm_get_player_thumbnail( $employee->ID, 'staff_thumbnail' ) . '</a>';
								break;
							case 'flag':
								$staff_details[ $employee->ID ][ $stat ] = '<img class="flag" src="' . WPCM_URL . 'assets/images/flags/' . get_post_meta( $employee->ID, 'wpcm_natl', true ) . '.png" />';
								break;
							case 'name':
								$staff_details[ $employee->ID ][ $stat ] = '<a href="' . get_permalink( $employee->ID ) . '">' . get_player_title( $employee->ID, $name_format ) . '</a>';
								break;
							case 'job':
								$staff_details[ $employee->ID ][ $stat ] = wpcm_get_staff_jobs( $employee->ID );
								break;
							case 'email':
								$staff_details[ $employee->ID ][ $stat ] = '<a href="mailto:' . get_post_meta( $employee->ID, '_wpcm_staff_email', true ) . '">' . get_post_meta( $employee->ID, '_wpcm_staff_email', true ) . '</a>';
								break;
							case 'phone':
								$staff_details[ $employee->ID ][ $stat ] = get_post_meta( $employee->ID, '_wpcm_staff_phone', true );
								break;
							case 'team':
								$staff_details[ $employee->ID ][ $stat ] = wpcm_get_player_teams( $employee->ID );
								break;
							case 'season':
								$staff_details[ $employee->ID ][ $stat ] = wpcm_get_player_seasons( $employee->ID );
								break;
							case 'age':
								$staff_details[ $employee->ID ][ $stat ] = get_age( get_post_meta( $employee->ID, 'wpcm_dob', true ) );
								break;
							case 'joined':
								$staff_details[ $employee->ID ][ $stat ] = date_i18n( get_option( 'date_format' ), strtotime( $employee->post_date ) );
								break;
						}
					}
				}

				if ( array_key_exists( $orderby, $atts ) ) {
					$staff_details = subval_sort( $staff_details, $orderby );
					if ( 'DESC' === $order ) {
						$staff_details = array_reverse( $staff_details );
					}
				}

				if ( count( $employees ) > 0 ) {

					ob_start();
					wpclubmanager_get_template( 'shortcodes/staff.php', array(
						'title'         => $title,
						'limit'         => $limit,
						'count'         => $count,
						'stats'         => $stats,
						'staff_details' => $staff_details,
						'stats_labels'  => $stats_labels,
						'linkpage'      => $linkpage,
						'linktext'      => $linktext,
					) );
					$output = ob_get_clean();

					wp_reset_postdata();
					if ( 'no' === $disable_cache ) {
						set_transient( $transient_name, $output, 4 * WEEK_IN_SECONDS );
						do_action( 'update_plugin_transient_keys', $transient_name );
					}
				}
			}
		}

		echo $output; // phpcs:ignore
	}
}
