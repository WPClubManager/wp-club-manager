<?php
/**
 * WPClubManager Core Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author      ClubPress
 * @category    Core
 * @package     WPClubManager/Functions
 * @version     2.2.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Include core functions
require 'wpcm-conditional-functions.php';
require 'wpcm-preset-functions.php';
require 'wpcm-stats-functions.php';
require 'wpcm-club-functions.php';
require 'wpcm-player-functions.php';
require 'wpcm-match-functions.php';
require 'wpcm-standings-functions.php';
require 'wpcm-user-functions.php';
require 'wpcm-deprecated-functions.php';
require 'wpcm-formatting-functions.php';

/**
 * Get template part (for templates like the loop).
 *
 * @access public
 * @param mixed  $slug
 * @param string $name (default: '')
 * @return void
 */
function wpclubmanager_get_template_part( $slug, $name = '' ) {

	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/wpclubmanager/slug-name.php
	if ( $name ) {
		$template = locate_template( array( "{$slug}-{$name}.php", WPCM()->template_path() . "{$slug}-{$name}.php" ) );
	}

	// Get default slug-name.php
	if ( ! $template && $name && file_exists( WPCM()->plugin_path() . "/templates/{$slug}-{$name}.php" ) ) {
		$template = WPCM()->plugin_path() . "/templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/wpclubmanager/slug.php
	if ( ! $template ) {
		$template = locate_template( array( "{$slug}.php", WPCM()->template_path() . "{$slug}.php" ) );
	}

	// Allow 3rd party plugin filter template file from their plugin
	$template = apply_filters( 'wpclubmanager_get_template_part', $template, $slug, $name );

	if ( $template ) {
		load_template( $template, false );
	}
}


/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param mixed  $template_name
 * @param array  $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function wpclubmanager_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	if ( $args && is_array( $args ) ) {
		extract( $args ); // phpcs:ignore
	}

	$located = wpclubmanager_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, esc_html( sprintf( '<code>%s</code> does not exist.', $located ) ), '1.3' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters( 'wpclubmanager_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'wpclubmanager_before_template_part', $template_name, $template_path, $located, $args );

	include $located;

	do_action( 'wpclubmanager_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Like wpcm_get_template, but returns the HTML instead of outputting.
 *
 * @param string $template_name
 * @param array  $args
 * @param string $template_path
 * @param string $default_path
 *
 * @return false|string
 * @since 1.4.0
 * @see   wpcm_get_template
 */
function wpcm_get_template_html( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	ob_start();
	wpcm_get_template( $template_name, $args, $template_path, $default_path );
	return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *      yourtheme       /   $template_path  /   $template_name
 *      yourtheme       /   $template_name
 *      $default_path   /   $template_name
 *
 * @access public
 * @param mixed  $template_name
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return string
 */
function wpclubmanager_locate_template( $template_name, $template_path = '', $default_path = '' ) {

	if ( ! $template_path ) {
		$template_path = WPCM_TEMPLATE_PATH;
	}
	if ( ! $default_path ) {
		$default_path = WPCM()->plugin_path() . '/templates/';
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template
	if ( ! $template || WPCM_TEMPLATE_DEBUG_MODE ) {
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters( 'wpclubmanager_locate_template', $template, $template_name, $template_path );
}

/**
 * Get an image size.
 *
 * Variable is filtered by wpclubmanager_get_image_size_{image_size}
 *
 * @param string $image_size
 * @return array
 */
function wpcm_get_image_size( $image_size ) {

	if ( is_array( $image_size ) ) {
		$width  = isset( $image_size[0] ) ? $image_size[0] : '300';
		$height = isset( $image_size[1] ) ? $image_size[1] : '300';
		$crop   = isset( $image_size[2] ) ? $image_size[2] : 1;

		$size = array(
			'width'  => $width,
			'height' => $height,
			'crop'   => $crop,
		);

		$image_size = $width . '_' . $height;

	} elseif ( in_array( $image_size, array( 'player_single', 'staff_single', 'player_thumbnail', 'staff_thumbnail', 'club_thumbnail', 'club_thumbnail' ) ) ) {

		$size           = get_option( $image_size . '_image_size', array() );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 1;

	} else {

		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1,
		);
	}

	return apply_filters( 'wpclubmanager_get_image_size_' . $image_size, $size );
}

/**
 * Function to flush rewrite rules
 */
function wpcm_flush_rewrite_rules() {

	$post_types = new WPCM_Post_Types();
	$post_types->register_taxonomies();
	$post_types->register_post_types();
	flush_rewrite_rules();
}

/**
 * Save WPCM nonce
 */
function wpcm_nonce() {

	wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );
}

/**
 * Get information about available image sizes
 *
 * @param string $size
 *
 * @return array|false|mixed
 */
function wpcm_get_image_sizes( $size = '' ) {

	global $_wp_additional_image_sizes;

	$sizes                        = array();
	$get_intermediate_image_sizes = get_intermediate_image_sizes();

	// Create the full array with sizes and crop info
	foreach ( $get_intermediate_image_sizes as $_size ) {
		if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
			$sizes[ $_size ]['width']  = get_option( $_size . '_size_w' );
			$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
			$sizes[ $_size ]['crop']   = (bool) get_option( $_size . '_crop' );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}
	}

	// Get only 1 size if found
	if ( $size ) {
		if ( isset( $sizes[ $size ] ) ) {
			return $sizes[ $size ];
		} else {
			return false;
		}
	}
	return $sizes;
}

/**
 * Get the placeholder image URL for player, staff and club badges
 *
 * @access public
 * @return string
 */
function wpcm_placeholder_img_src() {

	return apply_filters( 'wpclubmanager_placeholder_img_src', WPCM()->plugin_url() . '/assets/images/placeholder.png' );
}

/**
 * Get the placeholder image
 *
 * @access public
 *
 * @param string $size
 *
 * @return string
 */
function wpcm_placeholder_img( $size = 'player_thumbnail' ) {

	$dimensions = wpcm_get_image_size( $size );

	return apply_filters( 'wpclubmanager_placeholder_img', '<img src="' . wpcm_placeholder_img_src() . '" alt="Placeholder" width="' . esc_attr( $dimensions['width'] ) . '" class="wpclubmanager-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />' );
}

/**
 * Get the placeholder image URL for player, staff and club badges
 *
 * @access public
 * @return string
 */
function wpcm_crest_placeholder_img_src() {

	return apply_filters( 'wpclubmanager_crest_placeholder_img_src', WPCM()->plugin_url() . '/assets/images/crest-placeholder.png' );
}

/**
 * Get the crest placeholder image
 *
 * @access public
 *
 * @param string $size
 *
 * @return string
 */
function wpcm_crest_placeholder_img( $size = 'crest-small' ) {

	$dimensions = wpcm_get_image_sizes( $size );

	return apply_filters( 'wpclubmanager_crest_placeholder_img', '<img src="' . wpcm_crest_placeholder_img_src() . '" alt="Placeholder" width="' . esc_attr( $dimensions['width'] ) . '" class="wpclubmanager-crest-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />' );
}

/**
 * Returns get_terms() ordered by term_meta.
 *
 * @access public
 * @param int    $post
 * @param string $taxonomy
 * @return mixed
 */
function wpcm_get_ordered_post_terms( $post, $taxonomy ) {

	$terms = wp_get_object_terms( $post, $taxonomy );
	if ( $terms ) {
		$term_ids = array();
		foreach ( $terms as $term ) {
			$term_ids[] = $term->term_id;
		}
		if ( ! empty( $term_ids ) ) {

			return get_terms( array(
				'taxonomy'     => $taxonomy,
				'include'      => $term_ids,
				'meta_key'     => 'tax_position',
				'meta_compare' => 'NUMERIC',
				'orderby'      => 'meta_value_num',
			) );

		} else {

			return wp_get_object_terms( $post, $taxonomy, array(
				'meta_key'     => 'tax_position',
				'meta_compare' => 'NUMERIC',
				'orderby'      => 'meta_value_num',
				'order'        => 'DESC',
			) );

		}
	}
}

/**
 * Get default club option.
 *
 * @access public
 * @return mixed
 */
function get_default_club() {

	$default_club = get_option( 'wpcm_default_club' );
	$club         = false;
	if ( ! empty( $default_club ) ) {

		$club = get_option( 'wpcm_default_club' );
	}

	return $club;
}

/**
 * Get match format option.
 *
 * @access public
 * @return mixed
 */
function get_match_title_format() {

	$format = get_option( 'wpcm_match_title_format' );

	return $format;
}

/**
 * WP Club Manager Core Supported Themes
 *
 * @since 2.1.7
 * @return array
 */
function wpcm_get_core_supported_themes() {

	return array( 'twentytwenty', 'twentynineteen', 'twentyeighteen', 'twentyseventeen', 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );
}

/**
 * Get team display names
 *
 * @access public
 * @param string $post
 * @return mixed
 */
if ( ! function_exists( 'wpcm_get_team_name' ) ) {
	/**
	 * @param WP_Post $post
	 * @param int     $id
	 *
	 * @return mixed|string
	 */
	function wpcm_get_team_name( $post, $id ) {

		$club = get_default_club();

		if ( $post == $club ) {

			$teams = wp_get_object_terms( $id, 'wpcm_team' );

			if ( ! empty( $teams ) && is_array( $teams ) ) {

				foreach ( $teams as $team ) {

					$team       = reset( $teams );
					$t_id       = $team->term_id;
					$team_meta  = get_option( "taxonomy_term_$t_id" );
					$team_label = isset( $team_meta['wpcm_team_label'] ) ? $team_meta['wpcm_team_label'] : false;

					if ( $team_label ) {
						$team_name = $team_label;
					} else {
						$team_name = get_the_title( $post );
					}
				}
			} else {

				$team_name = get_the_title( $post );

			}
		} else {

			$team_name = get_the_title( $post );

		}

		return $team_name;
	}
}

/**
 * Generate a rand hash.
 *
 * @since  1.4.0
 * @return string
 */
function wpcm_rand_hash() {

	if ( function_exists( 'openssl_random_pseudo_bytes' ) ) {
		return bin2hex( openssl_random_pseudo_bytes( 20 ) );
	} else {
		return sha1( wp_rand() );
	}
}

/**
 * Returns whether teams exist.
 *
 * @since  2.0.2
 * @return boolean
 */
function has_teams() {

	$teams = false;
	if ( taxonomy_exists( 'wpcm_team' ) ) {
		$terms = get_terms( array(
			'taxonomy'   => 'wpcm_team',
			'hide_empty' => false,
		) );
		if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
			$teams = true;
		}
	}

	return $teams;
}

/**
 * Get array of teams.
 *
 * @param int|WP_Post $post
 *
 * @return array
 * @since  2.0.0
 */
function get_the_teams( $post ) {

	$teams = get_the_terms( $post, 'wpcm_team' );
	if ( is_array( $teams ) ) {
		foreach ( $teams as $team ) {
			$teams[] = $team->term_id;
		}
	} else {
		$teams = array();
	}

	return $teams;
}

/**
 * Get array of seasons.
 *
 * @param int|WP_Post $post
 *
 * @return array
 * @since  2.0.0
 */
function get_the_seasons( $post ) {

	$seasons = get_the_terms( $post, 'wpcm_season' );
	if ( is_array( $seasons ) ) {
		foreach ( $seasons as $season ) {
			$seasons[] = $season->term_id;
		}
	} else {
		$seasons = array();
	}

	return $seasons;
}

/**
 * Return current seaason.
 *
 * @since  2.2.0
 * @return array
 */
function get_current_season() {

	$seasons         = get_terms( array(
		'taxonomy'     => 'wpcm_season',
		'meta_key'     => 'tax_position',
		'meta_compare' => 'NUMERIC',
		'orderby'      => 'meta_value_num',
		'hide_empty'   => false,
	) );
	$season          = $seasons[0];
	$current['id']   = $season->term_id;
	$current['name'] = $season->name;
	$current['slug'] = $season->slug;

	return $current;
}

/**
 * Sort biggest score.
 *
 * @param array $a
 * @param array $b
 *
 * @return int
 * @since  2.0.0
 */
function sort_biggest_score( $a, $b ) {

	if ( $a['gd'] == $b['gd'] ) {
		if ( $a['f'] == $b['f'] ) {
			return 0;
		} else {
			return ( $a['f'] < $b['f'] ) ? -1 : 1;
		}
	}
	return ( $a['gd'] < $b['gd'] ) ? -1 : 1;
}

/**
 * Rewrite hierachical club URLs.
 *
 * @since  2.0.0
 */
function wpcm_club_rewrites() {
	$permalink      = get_option( 'wpclubmanager_club_slug' );
	$club_permalink = empty( $permalink ) ? _x( 'club', 'slug', 'wp-club-manager' ) : $permalink;
	add_rewrite_rule( $club_permalink . '\/(.*)', 'index.php?post_type=wpcm_club&name=$matches[1]', 'top' );
}
add_action( 'init', 'wpcm_club_rewrites' );

/**
 * Fix club permalinks.
 *
 * @param string  $post_link
 * @param WP_Post $post
 * @param bool    $leavename
 *
 * @return string
 * @since  2.0.0
 */
function wpcm_club_permalinks( $post_link, $post, $leavename ) {
	if ( isset( $post->post_type ) && 'wpcm_club' == $post->post_type ) {

		$permalink      = get_option( 'wpclubmanager_club_slug' );
		$club_permalink = empty( $permalink ) ? _x( 'club', 'slug', 'wp-club-manager' ) : $permalink;

		$post_link = home_url( $club_permalink . '/' . $post->post_name );
	}

	return $post_link;
}
add_filter( 'post_type_link', 'wpcm_club_permalinks', 10, 3 );

/**
 * Prevent slug duplicates in Clubs.
 *
 * @param string $slug
 * @param int    $post_ID
 * @param string $post_status
 * @param string $post_type
 * @param string $post_parent
 * @param string $original_slug
 *
 * @return string
 * @since  2.0.0
 */
function wpcm_prevent_slug_duplicates( $slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug ) {
	$check_post_types = array(
		'post',
		'page',
		'wpcm_club',
	);

	if ( ! in_array( $post_type, $check_post_types ) ) {
		return $slug;
	}

	if ( 'wpcm_club' == $post_type ) {
		// Saving a wpcm_club post, check for duplicates in POST or PAGE post types
		$post_match = get_page_by_path( $slug, 'OBJECT', 'post' );
		$page_match = get_page_by_path( $slug, 'OBJECT', 'page' );

		if ( $post_match || $page_match ) {
			$slug .= '-duplicate';
		}
	} else {
		// Saving a POST or PAGE, check for duplicates in wpcm_club post type
		$wpcm_club_match = get_page_by_path( $slug, 'OBJECT', 'wpcm_club' );

		if ( $wpcm_club_match ) {
			$slug .= '-duplicate';
		}
	}

	return $slug;
}
add_filter( 'wp_unique_post_slug', 'wpcm_prevent_slug_duplicates', 10, 6 );
