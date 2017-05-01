<?php
/**
 * Players Shortcode
 *
 * @author 		Clubpress
 * @category 	Shortcodes
 * @package 	WPClubManager/Shortcodes
 * @version     1.4.7
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Shortcode_Players {

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

		extract(shortcode_atts(array(
		), $atts));

		$limit 		= ( isset( $atts['limit'] ) ? $atts['limit'] : -1 );
		$season 	= ( isset( $atts['season'] ) ? $atts['season'] : NULL );
		$team 		= ( isset( $atts['team'] ) ? $atts['team'] : NULL );
		$position 	= ( isset( $atts['position'] ) ? $atts['position'] : NULL);
		$orderby 	= ( isset( $atts['orderby'] ) ? $atts['orderby'] : 'number' );
		$order 		= ( isset( $atts['order'] ) ? $atts['order'] : 'ASC' );
		$linktext 	= ( isset( $atts['linktext'] ) ? $atts['linktext'] : __( 'View all players', 'wp-club-manager' ) );
		$linkpage 	= ( isset( $atts['linkpage'] ) ? $atts['linkpage'] : null );
		$stats 		= ( isset( $atts['stats'] ) ? $atts['stats'] : 'flag,number,name,position,age,height,weight' );
		$title 		= ( isset( $atts['title'] ) ? $atts['title'] : __( 'Players', 'wp-club-manager' ) );
		$type 		= ( isset( $atts['type'] ) ? $atts['type'] : '' );

		$disable_cache = get_option( 'wpcm_disable_cache' );
		if( $disable_cache === 'no' && $type !== 'widget' ) {
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $atts, 'players' );
			$output = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if( $output === false ) {

			$player_stats_labels = wpcm_get_player_stats_labels();

			$stats_labels = array_merge( wpcm_player_header_labels(), $player_stats_labels );

			if ( $limit == 0 ) {
				$limit = -1;
			}

			$stats = explode( ',', $stats );

			foreach( $stats as $key => $value ) {
				$stats[$key] = strtolower( trim( $value ) );
				if ( !array_key_exists( $stats[$key], $stats_labels ) )
					unset( $stats[$key] );
			}

			$numposts = $limit;

			if ( array_intersect_key( array_flip( $stats ), $player_stats_labels ) ){
				$numposts = -1;
			}
			$orderby = strtolower( $orderby );	
			$order = strtoupper( $order );

			$args = array(
				'post_type' => 'wpcm_player',
				'tax_query' => array(),
				'numposts' => $numposts,
				'posts_per_page' => $numposts,
				'orderby' => 'meta_value_num',
				'meta_key' => 'wpcm_number',
				'order' => $order,
				'suppress_filters' => 0
			);


			if ( $orderby == 'name' ) {
			    $args['orderby'] = 'name';
			}
			if ( $orderby == 'menu_order' ) {
			    $args['orderby'] = 'menu_order';
			}

			if ( $season ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_season',
					'terms' => $season,
					'field' => 'term_id'
				);
			}

			if ( $team ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_team',
					'terms' => $team,
					'field' => 'term_id'
				);
			}

			if ( $position ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_position',
					'terms' => $position,
					'field' => 'term_id'
				);
			}

			$players = get_posts( $args );

			$player_details = array();

			$count = 0;	
			if ( sizeof( $players ) > 0 ) {

				foreach( $players as $player ) {

					$player_details[$player->ID] = array();
					$count++;
					if ( array_intersect_key( array_flip( $stats ), $player_stats_labels ) ) {
						$player_stats = get_wpcm_player_stats( $player->ID );
					}
					foreach( $stats as $stat ) {

						$player_details[$player->ID][$stat] = '';

						if ( array_key_exists( $stat, $player_stats_labels ) )  {
							if ( $team ) {
								if ( $season ) {
									$player_details[$player->ID][$stat] = $player_stats[$team][$season]['total'][$stat];
								} else {
									$player_details[$player->ID][$stat] = $player_stats[$team][0]['total'][$stat];
								}
							} else {
								if ( $season ) {
									$player_details[$player->ID][$stat] = $player_stats[0][$season]['total'][$stat];
								} else {
									$player_details[$player->ID][$stat] = $player_stats[0][0]['total'][$stat];
								}
							}
						} else {
							switch ( $stat ) {
								case 'thumb':
									$player_details[$player->ID][$stat] = '<a href="' . get_permalink( $player->ID ) . '">' . wpcm_get_player_thumbnail( $player->ID, 'player_thumbnail' ) . '</a>';
								break;
								case 'flag':
									$player_details[$player->ID][$stat] = '<img class="flag" src="' . WPCM_URL . 'assets/images/flags/' . get_post_meta( $player->ID, 'wpcm_natl', true ) . '.png" />';
								break;
								case 'number':
									$player_details[$player->ID][$stat] = get_post_meta( $player->ID, 'wpcm_number', true );
								break;
								case 'name':
									$player_details[$player->ID][$stat] = '<a href="' . get_permalink( $player->ID ) . '">' . $player->post_title . '</a>';
								break;
								case 'position':
									$player_details[$player->ID][$stat] = wpcm_get_player_positions( $player->ID );
								break;
								case 'team':
									$player_details[$player->ID][$stat] = wpcm_get_player_teams( $player->ID );
								break;
								case 'season':
									$player_details[$player->ID][$stat] = wpcm_get_player_seasons( $player->ID );
								break;
								case 'age':
									$player_details[$player->ID][$stat] = get_age( get_post_meta( $player->ID, 'wpcm_dob', true ) );
								break;
								case 'dob':
									$player_details[$player->ID][$stat] = date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $player->ID, 'wpcm_dob', true ) ) );
								break;
								case 'height':
									$player_details[$player->ID][$stat] = get_post_meta( $player->ID, 'wpcm_height', true );
								break;
								case 'weight':
									$player_details[$player->ID][$stat] = get_post_meta( $player->ID, 'wpcm_weight', true );
								break;
								case 'hometown':
									$player_details[$player->ID][$stat] = '<img class="flag" src="'. WPCM_URL .'assets/images/flags/' . $natl . '.png" /> ' . get_post_meta( $player->ID, 'wpcm_hometown', true );
								break;
								case 'joined':
									$player_details[$player->ID][$stat] = date_i18n( get_option( 'date_format' ), strtotime( $player->post_date ) );
								break;
								case 'subs':
									$player_details[$player->ID][$stat] = get_player_subs_total( $player->ID, $season, $team );
								break;
							}
						}
					}
				}
				if ( array_key_exists( $orderby, $player_stats_labels ) ) {
					$player_details = subval_sort( $player_details, $orderby );
					if ( $order == 'DESC' ) {
						$player_details = array_reverse( $player_details );
					}
				}
				ob_start();
				wpclubmanager_get_template( 'shortcodes/players.php', array(
					'type' 			 => $type,
					'title' 		 => $title,
					'stats' 		 => $stats,
					'player_details' => $player_details,
					'count' 		 => $count,
					'limit' 		 => $limit,
					'stats_labels' 	 => $stats_labels,
					'linkpage' 		 => $linkpage,
					'linktext'  	 => $linktext
					) );
				$output = ob_get_clean();

				wp_reset_postdata();
				if( $disable_cache === 'no' && $type !== 'widget' ) {
					set_transient( $transient_name, $output, 4*WEEK_IN_SECONDS );
					do_action('update_plugin_transient_keys', $transient_name);
				}
			}
		}

		echo $output;
	}
}