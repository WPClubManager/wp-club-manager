<?php
/**
 * Player Gallery Shortcode
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
 * WPCM_Shortcode_Player_Gallery
 */
class WPCM_Shortcode_Player_Gallery {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts
	 *
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
		$title       = ( isset( $atts['title'] ) ? $atts['title'] : __( 'Players Gallery', 'wp-club-manager' ) );
		$limit       = ( isset( $atts['limit'] ) ? $atts['limit'] : -1 );
		$position    = ( isset( $atts['position'] ) ? $atts['position'] : null );
		$orderby     = ( isset( $atts['orderby'] ) ? $atts['orderby'] : 'number' );
		$order       = ( isset( $atts['order'] ) ? $atts['order'] : 'ASC' );
		$columns     = ( isset( $atts['columns'] ) ? $atts['columns'] : '3' );
		$linktext    = ( isset( $atts['linktext'] ) ? $atts['linktext'] : __( 'View all players', 'wp-club-manager' ) );
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
			$transient_name = WPCM_Cache_Helper::create_plugin_transient_name( $atts, 'player_gallery' );
			$output         = get_transient( $transient_name );
		} else {
			$output = false;
		}

		if ( false === $output ) {

			$selected_players = (array) unserialize( get_post_meta( $id, '_wpcm_roster_players', true ) );
			$seasons          = get_the_terms( $id, 'wpcm_season' );
			$season           = $seasons[0]->term_id;
			$teams            = get_the_terms( $id, 'wpcm_team' );
			$team             = $teams[0]->term_id;

			$player_stats_labels = wpcm_get_player_stats_labels();

			$orderby = strtolower( $orderby );
			$order   = strtoupper( $order );

			$query_args = array(
				'post_type'      => 'wpcm_player',
				'tax_query'      => array(), // phpcs:ignore
				'numposts'       => $limit,
				'posts_per_page' => -1,
				'orderby'        => 'meta_value_num',
				'meta_key'       => 'wpcm_number',
				'order'          => $order,
				'post__in'       => $selected_players,
			);

			if ( $position ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'wpcm_position',
					'terms'    => $position,
					'field'    => 'term_id',
				);
			}

			$players = get_posts( $query_args );

			if ( $players ) {

				$player_details = array();

				foreach ( $players as $player ) {

					$player_details[ $player->ID ] = array();

					$player_details[ $player->ID ]['id'] = $player->ID;

					$player_stats = get_wpcm_player_stats( $player->ID );

					$thumb        = wpcm_get_player_thumbnail( $player->ID, 'player_full' );
					$url          = get_permalink( $player->ID );
					$player_title = get_player_title( $player->ID, $name_format );

					$player_details[ $player->ID ]['image'] = apply_filters( 'wpclubmanager_player_gallery_image', '<a href="' . esc_url( $url ) . '">' . $thumb . '</a>', $url, $thumb );

					$player_details[ $player->ID ]['title'] = apply_filters( 'wpclubmanager_player_gallery_title', '<a href="' . esc_url( $url ) . '">' . wp_kses_post( $player_title ) . '</a>', $url, $player_title );

					if ( array_key_exists( $orderby, $player_stats_labels ) ) {
						if ( $team ) {
							if ( $season ) {
								$player_details[ $player->ID ][ $orderby ] = $player_stats[ $team ][ $season ]['total'][ $orderby ];
							} else {
								$player_details[ $player->ID ][ $orderby ] = $player_stats[ $team ][0]['total'][ $orderby ];
							}
						} elseif ( $season ) {
								$player_details[ $player->ID ][ $orderby ] = $player_stats[0][ $season ]['total'][ $orderby ];
						} else {
							$player_details[ $player->ID ][ $orderby ] = $player_stats[0][0]['total'][ $orderby ];
						}
					}
				}

				if ( array_key_exists( $orderby, $player_stats_labels ) ) {

					$player_details = subval_sort( $player_details, $orderby );

					if ( is_array( $player_details ) ) {

						if ( 'DESC' === $order ) {
							$player_details = array_reverse( $player_details );
						}
					}
				}

				ob_start();

				wpclubmanager_get_template( 'shortcodes/players-gallery.php', array(
					'type'           => $type,
					'title'          => $title,
					'orderby'        => $orderby,
					'player_details' => $player_details,
					'limit'          => $limit,
					'name_format'    => $name_format,
					'linkpage'       => $linkpage,
					'linktext'       => $linktext,
					'columns'        => $columns,
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
