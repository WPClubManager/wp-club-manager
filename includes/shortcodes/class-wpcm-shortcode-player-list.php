<?php
/**
 * Player List Shortcode
 *
 * @author      Clubpress
 * @category    Shortcodes
 * @package     WPClubManager/Shortcodes
 * @version     2.2.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_Shortcode_Player_List
 */
class WPCM_Shortcode_Player_List {

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

		$id          = ( isset( $atts['id'] ) ? $atts['id'] : '' );
		$limit       = ( isset( $atts['limit'] ) ? $atts['limit'] : '' );
		$position    = ( isset( $atts['position'] ) ? $atts['position'] : null );
		$orderby     = ( isset( $atts['orderby'] ) ? $atts['orderby'] : 'number' );
		$order       = ( isset( $atts['order'] ) ? $atts['order'] : 'ASC' );
		$linktext    = ( isset( $atts['linktext'] ) ? $atts['linktext'] : __( 'View all players', 'wp-club-manager' ) );
		$linkpage    = ( isset( $atts['linkpage'] ) ? $atts['linkpage'] : null );
		$stats       = ( isset( $atts['columns'] ) ? $atts['columns'] : 'flag,number,name,position,age,height,weight' );
		$title       = ( isset( $atts['title'] ) ? $atts['title'] : __( 'Players', 'wp-club-manager' ) );
		$name_format = ( isset( $atts['name_format'] ) ? $atts['name_format'] : 'full' );
		$type        = ( isset( $atts['type'] ) ? $atts['type'] : '' );

		if ( '' === $position ) {
			$position = null;
		}
		if ( '' === $orderby ) {
			$orderby = 'number';
		}
		if ( '' === $order ) {
			$order = 'ASC';
		}
		if ( '' === $stats ) {
			$stats = 'flag,number,name,position,age,height,weight';
		}
		if ( '' === $name_format ) {
			$name_format = 'full';
		}
		if ( '' === $linkpage ) {
			$linkpage = null;
		}

		$disable_cache = get_option( 'wpcm_disable_cache' );
		if ( 'no' === $disable_cache && 'widget' !== $type ) {
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $atts, 'player_list' );
			$output         = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if ( false === $output ) {

			if ( is_club_mode() ) {
				$selected_players = (array) unserialize( get_post_meta( $id, '_wpcm_roster_players', true ) );
				$seasons          = get_the_terms( $id, 'wpcm_season' );
				$season           = $seasons[0]->term_id;
				$teams            = get_the_terms( $id, 'wpcm_team' );
				$team             = $teams[0]->term_id;
			} else {
				$team   = $id;
				$season = -1;
			}

			$player_stats_labels = wpcm_get_player_stats_labels();

			$stats_labels = array_merge( wpcm_player_header_labels(), $player_stats_labels );

			$stats = explode( ',', $stats );

			foreach ( $stats as $key => $value ) {
				$stats[ $key ] = strtolower( trim( $value ) );
				if ( ! array_key_exists( $stats[ $key ], $stats_labels ) ) {
					unset( $stats[ $key ] );
				}
			}

			$orderby = strtolower( $orderby );
			$order   = strtoupper( $order );

			if ( is_club_mode() ) {

				$args = array(
					'post_type'      => 'wpcm_player',
					'tax_query'      => array(), // phpcs:ignore
					'posts_per_page' => -1,
					'order'          => $order,
					'post__in'       => $selected_players,
				);
				if ( 'number' === $orderby ) {
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = 'wpcm_number';
				}
				if ( 'name' === $orderby ) {
					$args['orderby'] = 'name';
				}
				if ( 'menu_order' === $orderby ) {
					$args['orderby'] = 'menu_order';
				}
				if ( $position ) {
					$args['tax_query'][] = array(
						'taxonomy' => 'wpcm_position',
						'terms'    => $position,
						'field'    => 'term_id',
					);
				}
			} else {

				$args = array(
					'post_type'      => 'wpcm_player',
					'tax_query'      => array(), // phpcs:ignore
					'posts_per_page' => $limit,
					'order'          => $order,
					'meta_query'     => array(), // phpcs:ignore
				);

				if ( '' !== $id ) {
					$args['meta_query'][] = array(
						'key'   => '_wpcm_player_club',
						'value' => $id,
					);
				}
				if ( 'number' === $orderby ) {
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = 'wpcm_number';
				}
				if ( 'name' === $orderby ) {
					$args['orderby'] = 'name';
				}
				if ( 'menu_order' === $orderby ) {
					$args['orderby'] = 'menu_order';
				}

				if ( $position ) {
					$args['tax_query'][] = array(
						'taxonomy' => 'wpcm_position',
						'terms'    => $position,
						'field'    => 'term_id',
					);
				}
			}

			$players = get_posts( $args );

			$player_details = array();

			$count = count( $players );

			if ( '' === $limit ) {
				$limit = $count;
			}

			if ( $count > 0 ) {

				foreach ( $players as $player ) {

					$player_details[ $player->ID ] = array();

					$player_details[ $player->ID ]['id'] = $player->ID;

					if ( array_intersect_key( array_flip( $stats ), $player_stats_labels ) ) {
						$player_stats = get_wpcm_player_stats( $player->ID );
					}
					foreach ( $stats as $stat ) {

						$player_details[ $player->ID ][ $stat ] = '';

						if ( array_key_exists( $stat, $player_stats_labels ) ) {
							if ( $team ) {
								if ( $season ) {
									$player_details[ $player->ID ][ $stat ] = isset( $player_stats[ $team ][ $season ]['total'][ $stat ] ) ? $player_stats[ $team ][ $season ]['total'][ $stat ] : '0';
								} else {
									$player_details[ $player->ID ][ $stat ] = isset( $player_stats[ $team ][0]['total'][ $stat ] ) ? $player_stats[ $team ][0]['total'][ $stat ] : '0';
								}
							} elseif ( $season ) {
									$player_details[ $player->ID ][ $stat ] = isset( $player_stats[0][ $season ]['total'][ $stat ] ) ? $player_stats[0][ $season ]['total'][ $stat ] : '0';
							} else {
								$player_details[ $player->ID ][ $stat ] = isset( $player_stats[0][0]['total'][ $stat ] ) ? $player_stats[0][0]['total'][ $stat ] : '0';
							}
						} else {
							switch ( $stat ) {
								case 'thumb':
									$player_details[ $player->ID ][ $stat ] = '<a href="' . get_permalink( $player->ID ) . '">' . wpcm_get_player_thumbnail( $player->ID, 'player_thumbnail' ) . '</a>';
									break;
								case 'flag':
									$player_details[ $player->ID ][ $stat ] = '<img class="flag" src="' . WPCM_URL . 'assets/images/flags/' . get_post_meta( $player->ID, 'wpcm_natl', true ) . '.png" />';
									break;
								case 'number':
									$player_details[ $player->ID ][ $stat ] = get_post_meta( $player->ID, 'wpcm_number', true );
									break;
								case 'name':
									$player_details[ $player->ID ][ $stat ] = '<a href="' . get_permalink( $player->ID ) . '">' . get_player_title( $player->ID, $name_format ) . '</a>';
									break;
								case 'position':
									$player_details[ $player->ID ][ $stat ] = wpcm_get_player_positions( $player->ID );
									break;
								case 'team':
									$player_details[ $player->ID ][ $stat ] = wpcm_get_player_teams( $player->ID );
									break;
								case 'season':
									$player_details[ $player->ID ][ $stat ] = wpcm_get_player_seasons( $player->ID );
									break;
								case 'age':
									$player_details[ $player->ID ][ $stat ] = get_age( get_post_meta( $player->ID, 'wpcm_dob', true ) );
									break;
								case 'dob':
									$player_details[ $player->ID ][ $stat ] = date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $player->ID, 'wpcm_dob', true ) ) );
									break;
								case 'height':
									$player_details[ $player->ID ][ $stat ] = get_post_meta( $player->ID, 'wpcm_height', true );
									break;
								case 'weight':
									$player_details[ $player->ID ][ $stat ] = get_post_meta( $player->ID, 'wpcm_weight', true );
									break;
								case 'hometown':
									$player_details[ $player->ID ][ $stat ] = '<img class="flag" src="' . WPCM_URL . 'assets/images/flags/' . $natl . '.png" /> ' . get_post_meta( $player->ID, 'wpcm_hometown', true );
									break;
								case 'joined':
									$player_details[ $player->ID ][ $stat ] = date_i18n( get_option( 'date_format' ), strtotime( $player->post_date ) );
									break;
								case 'subs':
									$player_details[ $player->ID ][ $stat ] = get_player_subs_total( $player->ID, $season, $team );
									break;
							}
						}
					}
				}
				if ( array_key_exists( $orderby, $player_stats_labels ) ) {
					$player_details = subval_sort( $player_details, $orderby );
					if ( 'DESC' === $order ) {
						$player_details = array_reverse( $player_details );
					}
				}

				$player_details = array_slice( $player_details, 0, $limit );

				ob_start();
				wpclubmanager_get_template( 'shortcodes/players.php', array(
					'type'           => $type,
					'title'          => $title,
					'stats'          => $stats,
					'player_details' => $player_details,
					'count'          => $count,
					'limit'          => $limit,
					'stats_labels'   => $stats_labels,
					'name_format'    => $name_format,
					'linkpage'       => $linkpage,
					'linktext'       => $linktext,
				) );
				$output = ob_get_clean();

				wp_reset_postdata();
				if ( 'no' === $disable_cache && 'widget' !== $type ) {
					set_transient( $transient_name, $output, 4 * WEEK_IN_SECONDS );
					do_action( 'update_plugin_transient_keys', $transient_name );
				}
			}
		}

		echo $output; // phpcs:ignore
	}
}
