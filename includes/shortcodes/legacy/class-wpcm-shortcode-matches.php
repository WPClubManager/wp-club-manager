<?php
/**
 * Matches Shortcode
 *
 * @author 		Clubpress
 * @category 	Shortcodes
 * @package 	WPClubManager/Shortcodes
 * @version     1.4.5
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Shortcode_Matches {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts
	 * @return string
	 */
	// public static function get( $atts ) {
	// 	return WPCM_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	// }

	/**
	 * Output the standings shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {
		
		extract(shortcode_atts(array(
		), $atts));

		$type 		= ( isset( $atts['type'] ) ? $atts['type'] : '1' );
		$format 	= ( isset( $atts['format'] ) ? $atts['format'] : '1' );
		$limit 		= ( isset( $atts['limit'] ) ? $atts['limit'] : -1 );
		$title 		= ( isset( $atts['title'] ) ? $atts['title'] : __( 'Fixtures & Results', 'wp-club-manager' ));
		$comp 		= ( isset( $atts['comp'] ) ? $atts['comp'] : null );
		$season 	= ( isset( $atts['season'] ) ? $atts['season'] : null );
		$team 		= ( isset( $atts['team'] ) ? $atts['team'] : null );
		$month 		= ( isset( $atts['month'] ) ? $atts['month'] : null );
		$venue 		= ( isset( $atts['venue'] ) ? $atts['venue'] : 'all' );
		$thumb 		= ( isset( $atts['thumb'] ) ? $atts['thumb'] : null );
		$show_team 	= ( isset( $atts['show_team'] ) ? $atts['show_team'] : null );
		$show_comp 	= ( isset( $atts['show_comp'] ) ? $atts['show_comp'] : null );
		$link_club  = ( isset( $atts['link_club'] ) ? $atts['link_club'] : null );
		$linktext 	= ( isset( $atts['linktext'] ) ? $atts['linktext'] : __( 'View all results', 'wp-club-manager' ));
		$linkpage 	= ( isset( $atts['linkpage'] ) ? $atts['linkpage'] : null );

		if( $limit == '' )
			$limit = -1;
		if( $comp == -1 )
			$comp = null;
		if( $season == -1 )
			$season = null;
		if( $team == -1 )
			$team = null;
		if( $month == -1 )
			$month = null;

		$disable_cache = get_option( 'wpcm_disable_cache' );
		if( $disable_cache === 'no') {
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $atts, 'matches' );
			$output = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if( $output === false ) {

			$club = get_default_club();
			if( $format == '1' ){
				$format = array('publish','future');
				$order = 'ASC';
			}elseif( $format == '2' ){
				$format = 'future';
				$order = 'ASC';
			}elseif( $format == '3' ){
				$format = 'publish';
				$order = 'DESC';
			}

			// get matches
			$query_args = array(
				'tax_query' => array(),
				'numberposts' => $limit,
				'order' => $order,
				'orderby' => 'post_date',
				'post_type' => 'wpcm_match',
				'post_status' => $format,
				'posts_per_page' => $limit
			);

			if ( $format == '2' ) {
				$query_args['meta_query'] = array(
					array(
						'key' => 'wpcm_played',
						'value' => false
					)
				);
			}

			if( isset( $venue ) && $venue == 'home' ) {
				$query_args['meta_query'] = array(
					array(
						'key' => 'wpcm_home_club',
						'value' => $club,
					),
				);
			} elseif( isset( $venue ) && $venue == 'away' ) {
				$query_args['meta_query'] = array(
					array(
						'key' => 'wpcm_away_club',
						'value' => $club,
					),
				);
			} else {
				$query_args['meta_query'] = array(
					'relation' => 'OR',
					array(
						'key' => 'wpcm_home_club',
						'value' => $club,
					),
					array(
						'key' => 'wpcm_away_club',
						'value' => $club,
					)
				);
			}

			if ( isset( $comp ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'wpcm_comp',
					'terms' => $comp,
					'field' => 'term_id'
				);
			}
			if ( isset( $season ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'wpcm_season',
					'terms' => $season,
					'field' => 'term_id'
				);
			}
			if ( isset( $team ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'wpcm_team',
					'terms' => $team,
					'field' => 'term_id'
				);
			}
			// if ( isset( $venue ) ) {
			// 	$query_args['tax_query'][] = array(
			// 		'taxonomy' => 'wpcm_venue',
			// 		'terms' => $venue,
			// 		'field' => 'term_id'
			// 	);
			// }
			if ( isset( $month ) ) {
				$query_args['date_query'] = array(
					'month' => $month
				);
			}

			$matches = get_posts( $query_args );

			if ( $matches ) {
				if( $type == '2' ) {
					ob_start();
					wpclubmanager_get_template( 'shortcodes/matches-2.php', array(
						'title' 	=> $title, 
						'matches' 	=> $matches,
						'linkpage' 	=> $linkpage,
						'linktext'  => $linktext
					) );
					$output = ob_get_clean();

				} else {
					ob_start();
					wpclubmanager_get_template( 'shortcodes/matches.php', array(
						'title' 	=> $title,
						'club' 		=> $club, 
						'link_club' => $link_club,
						'thumb' 	=> $thumb,
						'show_team' => $show_team,
						'show_comp' => $show_comp,
						'matches' 	=> $matches,
						'linkpage' 	=> $linkpage,
						'linktext'  => $linktext
					) );
					$output = ob_get_clean();
				}

				wp_reset_postdata();
				if( $disable_cache === 'no' ) {
					set_transient( $transient_name, $output, 4*WEEK_IN_SECONDS );
					do_action('update_plugin_transient_keys', $transient_name);
				}
			} else { ?>
				
				<p><?php _e('No matches yet.', 'wp-club-manager'); ?></p>
			<?php
			}
		}

		echo $output;
	}
}