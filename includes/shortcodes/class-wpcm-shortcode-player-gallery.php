<?php
/**
 * Player Gallery Shortcode
 *
 * @author 		Clubpress
 * @category 	Shortcodes
 * @package 	WPClubManager/Shortcodes
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Shortcode_Player_Gallery {

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

		$id 		= ( isset( $atts['id'] ) ? $atts['id'] : null );
		$title 		= ( isset( $atts['title'] ) ? $atts['title'] : __( 'Players Gallery', 'wp-club-manager' ) );
		$limit 		= ( isset( $atts['limit'] ) ? $atts['limit'] : -1 );
		$position 	= ( isset( $atts['position'] ) ? $atts['position'] : NULL);
		$orderby 	= ( isset( $atts['orderby'] ) ? $atts['orderby'] : 'number' );
		$order 		= ( isset( $atts['order'] ) ? $atts['order'] : 'ASC' );
		$columns 	= ( isset( $atts['columns'] ) ? $atts['columns'] : '3' );
		$linktext 	= ( isset( $atts['linktext'] ) ? $atts['linktext'] : __( 'View all players', 'wp-club-manager' ) );
		$linkpage 	= ( isset( $atts['linkpage'] ) ? $atts['linkpage'] : null );
		$name_format = ( isset( $atts['name_format'] ) ? $atts['name_format'] : 'full' );
		$type 		= ( isset( $atts['type'] ) ? $atts['type'] : '' );

		if( $limit == '' )
			$limit = -1;
		if( $position == '' )
			$position = null;
		if( $orderby == '' )
			$orderby = 'number';
		if( $order == '' )
			$order = 'ASC';
		if( $columns == '' )
			$columns = '3';
		if( $name_format == '' )
			$name_format = 'full';
		if( $linkpage == '' )
			$linkpage = null;

		$disable_cache = get_option( 'wpcm_disable_cache' );
		if( $disable_cache === 'no' && $type !== 'widget' ) {
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $atts, 'player_gallery' );
			$output = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if( $output === false ) {

			$selected_players = (array)unserialize( get_post_meta( $id, '_wpcm_roster_players', true ) );
			$seasons = get_the_terms( $id, 'wpcm_season' );
			$season = $seasons[0]->term_id;
			$teams = get_the_terms( $id, 'wpcm_team' );
			$team = $teams[0]->term_id;

			$player_stats_labels = wpcm_get_player_stats_labels();
			
			$orderby = strtolower( $orderby );
			$order = strtoupper( $order );
			
			$query_args = array(
				'post_type' => 'wpcm_player',
				'tax_query' => array(),
				'numposts' => $limit,
				'posts_per_page' => -1,
				'orderby' => 'meta_value_num',
				'meta_key' => 'wpcm_number',
				'order' => $order,
				'post__in' => $selected_players
			);

			if ( $position ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'wpcm_position',
					'terms' => $position,
					'field' => 'term_id'
				);
			}

			$players = get_posts( $query_args );	
			
			if( $players ) {

				$player_details = array();

				foreach( $players as $player ) {

					$player_details[$player->ID] = array();

					$player_stats = get_wpcm_player_stats( $player->ID );

					if ( has_post_thumbnail( $player->ID ) ) {
						$thumb = get_the_post_thumbnail( $player->ID, 'player-medium' );
					} else {
						$thumb = '<img src="' . get_template_directory_uri() . '/includes/images/player-medium.jpg"/>';
					}

					$player_details[$player->ID]['image'] = '<a href="' . get_permalink( $player->ID ) . '">' . $thumb . '</a>';

					$player_details[$player->ID]['title'] = '<a href="' . get_permalink( $player->ID ) . '">' . $player->post_title . '</a>';

					if ( array_key_exists( $orderby, $player_stats_labels ) )  {
						if ( $team ) {
							if ( $season ) {
								$player_details[$player->ID][$orderby] = $player_stats[$team][$season]['total'][$orderby];
							} else {
								$player_details[$player->ID][$orderby] = $player_stats[$team][0]['total'][$orderby];
							}
						} else {
							if ( $season ) {
								$player_details[$player->ID][$orderby] = $player_stats[0][$season]['total'][$orderby];
							} else {
								$player_details[$player->ID][$orderby] = $player_stats[0][0]['total'][$orderby];
							}
						}
					}

				}

				if ( array_key_exists( $orderby, $player_stats_labels ) ) {

					$player_details = subval_sort( $player_details, $orderby );

					if( is_array( $player_details ) ) {
						
						if ( $order == 'DESC' ) {
							$player_details = array_reverse( $player_details );
						}
					}
				}

				ob_start();
				
				wpclubmanager_get_template( 'shortcodes/players-gallery.php', array(
					'type' 			 => $type,
					'title' 		 => $title,
					'orderby'		 => $orderby,
					'player_details' => $player_details,
					'limit' 		 => $limit,
					'linkpage' 		 => $linkpage,
					'linktext'  	 => $linktext,
					'columns'		 => $columns
					) );
				
				$output = ob_get_clean();
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