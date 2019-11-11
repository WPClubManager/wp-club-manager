<?php
/**
 * Staff Shortcode
 *
 * @author 		Clubpress
 * @category 	Shortcodes
 * @package 	WPClubManager/Shortcodes
 * @version     1.4.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Shortcode_Staff {

	/**
	 * Output the standings shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		extract(shortcode_atts(array(
		), $atts));

		$limit 		= ( isset( $atts['limit'] ) ? $atts['limit'] : -1 );
		$title 		= ( isset( $atts['title'] ) ? $atts['title'] : __( 'Staff', 'wp-club-manager' ));
		$team 		= ( isset( $atts['team'] ) ? $atts['team'] : null );
		$season 	= ( isset( $atts['season'] ) ? $atts['season'] : null );
		$order 		= ( isset( $atts['order'] ) ? $atts['order'] : 'ASC' );
		$orderby 	= ( isset( $atts['orderby'] ) ? $atts['orderby'] : 'name' );
		$jobs 		= ( isset( $atts['jobs'] ) ? $atts['jobs'] : null );
		$stats 		= ( isset( $atts['stats'] ) ? $atts['stats'] : 'flag,number,name,job,age' );
		$linktext 	= ( isset( $atts['linktext'] ) ? $atts['linktext'] : __( 'View all staff', 'wp-club-manager' ));
		$linkpage 	= ( isset( $atts['linkpage'] ) ? $atts['linkpage'] : null );

		$disable_cache = get_option( 'wpcm_disable_cache' );
		if( $disable_cache === 'no') {
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $atts, 'staff' );
			$output = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if( $output === false ) {

			$stats_labels = wpcm_staff_labels();

			if ( $limit == 0 )
				$limit = -1;
			if ( $team <= 0 )
				$team = null;

			$stats = explode( ',', $stats );

			foreach( $stats as $key => $value ) {
				$stats[$key] = strtolower( trim( $value ) );
				if ( !array_key_exists( $stats[$key], $stats_labels ) )
					unset( $stats[$key] );
			}

			$orderby = strtolower( $orderby );	
			$order = strtoupper( $order );
			$output = '';

			$query_args = array(
				'post_type' => 'wpcm_staff',
				'tax_query' => array(),
				'numposts' => $limit,
				'posts_per_page' => $limit,
				'orderby' => $orderby,
				'order' => $order
			);

			if ( $season ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'wpcm_season',
					'terms' => $season,
					'field' => 'term_id'
				);
			}

			if ( $team ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'wpcm_team',
					'terms' => $team,
					'field' => 'term_id'
				);
			}

			if ( $jobs ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'wpcm_jobs',
					'terms' => $jobs,
					'field' => 'term_id'
				);
			}

			$employees = get_posts( $query_args );

			$count = 0;
			foreach( $employees as $employee ) {
				$count++;
				foreach( $stats as $stat ) {

					switch ( $stat ) {
						case 'thumb':
							$staff_details[$employee->ID][$stat] = '<a href="' . get_permalink( $employee->ID ) . '">' . wpcm_get_player_thumbnail( $employee->ID, 'staff_thumbnail' ) . '</a>';
							break;
						case 'flag':
							$staff_details[$employee->ID][$stat] = '<img class="flag" src="' . WPCM_URL . 'assets/images/flags/' . get_post_meta( $employee->ID, 'wpcm_natl', true ) . '.png" />';
							break;
						case 'name':
							$staff_details[$employee->ID][$stat] = '<a href="' . get_permalink( $employee->ID ) . '">' . $employee->post_title . '</a>';
							break;
						case 'job':
							$staff_details[$employee->ID][$stat] = wpcm_get_staff_jobs( $employee->ID );
							break;
						case 'email':
							$staff_details[$employee->ID][$stat] = '<a href="mailto:' . get_post_meta( $employee->ID, '_wpcm_staff_email', true ) . '">' . get_post_meta( $employee->ID, '_wpcm_staff_email', true ) . '</a>';
							break;
						case 'phone':
							$staff_details[$employee->ID][$stat] = get_post_meta( $employee->ID, '_wpcm_staff_phone', true );
							break;
						case 'team':
							$staff_details[$employee->ID][$stat] = wpcm_get_player_teams( $employee->ID );
							break;
						case 'season':
							$staff_details[$employee->ID][$stat] = wpcm_get_player_seasons( $employee->ID );
							break;
						case 'age':
							$staff_details[$employee->ID][$stat] = get_age( get_post_meta( $employee->ID, 'wpcm_dob', true ) );
							break;
						case 'joined':
							$staff_details[$employee->ID][$stat] = date_i18n( get_option( 'date_format' ), strtotime( $employee->post_date ) );
							break;
					}
				}
			}

			if ( array_key_exists( $orderby, $atts ) ) {
				$staff_details = subval_sort( $staff_details, $orderby );
				if ( $order == 'DESC' )
					$staff_details = array_reverse( $staff_details );
			}

			$count = 0;	
			if ( sizeof( $employees ) > 0 ) {

				ob_start();
				wpclubmanager_get_template( 'shortcodes/staff.php', array(
					'title' 		=> $title,
					'limit' 		=> $limit,
					'count' 		=> $count,
					'stats' 		=> $stats,
					'staff_details' => $staff_details,
					'stats_labels'  => $stats_labels,
					'linkpage' 		=> $linkpage,
					'linktext' 		=> $linktext
				) );
				$output = ob_get_clean();

				if ( isset( $linkpage ) ) { ?>
					<a href="<?php echo get_page_link( $linkpage ); ?>" class="wpcm-view-link">
						<?php echo $linktext; ?>
						</a>
				<?php }

				wp_reset_postdata();
				if( $disable_cache === 'no') {
					set_transient( $transient_name, $output, 4*WEEK_IN_SECONDS );
					do_action('update_plugin_transient_keys', $transient_name);
				}
			}
		}

		echo $output;
	}
}