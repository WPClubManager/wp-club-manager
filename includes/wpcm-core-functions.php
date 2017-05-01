<?php
/**
 * WPClubManager Core Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.4.6
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Include core functions
include( 'wpcm-conditional-functions.php' );
include( 'wpcm-preset-functions.php' );
include( 'wpcm-stats-functions.php');
include( 'wpcm-club-functions.php');
include( 'wpcm-player-functions.php');
include( 'wpcm-match-functions.php');
include( 'wpcm-standings-functions.php');
include( 'wpcm-user-functions.php' );
include( 'wpcm-deprecated-functions.php');
include( 'wpcm-formatting-functions.php' );

/**
 * Get template part (for templates like the loop).
 *
 * @access public
 * @param mixed $slug
 * @param string $name (default: '')
 * @return void
 */
function wpclubmanager_get_template_part( $slug, $name = '' ) {

	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/wpclubmanager/slug-name.php
	if ( $name )
		$template = locate_template( array ( "{$slug}-{$name}.php", WPCM()->template_path() . "{$slug}-{$name}.php" ) );

	// Get default slug-name.php
	if ( !$template && $name && file_exists( WPCM()->plugin_path() . "/templates/{$slug}-{$name}.php" ) )
		$template = WPCM()->plugin_path() . "/templates/{$slug}-{$name}.php";

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/wpclubmanager/slug.php
	if ( !$template )
		$template = locate_template( array ( "{$slug}.php", WPCM()->template_path() . "{$slug}.php" ) );

	// Allow 3rd party plugin filter template file from their plugin
	$template = apply_filters( 'wpclubmanager_get_template_part', $template, $slug, $name );

	if ( $template )
		load_template( $template, false );
}


/**
 * Get other templates (e.g. product attributes) passing attributes and including the file.
 *
 * @access public
 * @param mixed $template_name
 * @param array $args (default: array())
 * @param string $template_path (default: '')
 * @param string $default_path (default: '')
 * @return void
 */
function wpclubmanager_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {

	if ( $args && is_array($args) )
		extract( $args );

	$located = wpclubmanager_locate_template( $template_name, $template_path, $default_path );

	if ( ! file_exists( $located ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $located ), '1.3' );
		return;
	}

	// Allow 3rd party plugin filter template file from their plugin
	$located = apply_filters( 'wpclubmanager_get_template', $located, $template_name, $args, $template_path, $default_path );

	do_action( 'wpclubmanager_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'wpclubmanager_after_template_part', $template_name, $template_path, $located, $args );
}

/**
 * Like wpcm_get_template, but returns the HTML instead of outputting.
 * @see wpcm_get_template
 * @since 1.4.0
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
 *		yourtheme		/	$template_path	/	$template_name
 *		yourtheme		/	$template_name
 *		$default_path	/	$template_name
 *
 * @access public
 * @param mixed $template_name
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
			$template_name
		)
	);

	// Get default template
	if ( ! $template || WPCM_TEMPLATE_DEBUG_MODE )
		$template = $default_path . $template_name;

	// Return what we found
	return apply_filters('wpclubmanager_locate_template', $template, $template_name, $template_path);
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
			'crop'   => $crop
		);

		$image_size = $width . '_' . $height;

	} elseif ( in_array( $image_size, array( 'player_single', 'staff_single','player_thumbnail', 'staff_thumbnail' ) ) ) {

		$size           = get_option( $image_size . '_image_size', array() );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 1;

	} else {
		
		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1
		);
	}

	return apply_filters( 'wpclubmanager_get_image_size_' . $image_size, $size );
}

/**
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function wpclubmanager_enqueue_js( $code ) {
	global $wpclubmanager_queued_js;

	if ( empty( $wpclubmanager_queued_js ) ) {
		$wpclubmanager_queued_js = '';
	}

	$wpclubmanager_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function wpclubmanager_print_js() {
	global $wpclubmanager_queued_js;

	if ( ! empty( $wpclubmanager_queued_js ) ) {

		echo "<!-- WP Club Manager JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

		// Sanitize
		$wpclubmanager_queued_js = wp_check_invalid_utf8( $wpclubmanager_queued_js );
		$wpclubmanager_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $wpclubmanager_queued_js );
		$wpclubmanager_queued_js = str_replace( "\r", '', $wpclubmanager_queued_js );

		echo $wpclubmanager_queued_js . "});\n</script>\n";

		unset( $wpclubmanager_queued_js );
	}
}

/**
 * Enables template debug mode
 */
function wpcm_template_debug_mode() {
	if ( ! defined( 'WPCM_TEMPLATE_DEBUG_MODE' ) ) {
		$status_options = get_option( 'wpclubmanager_status_options', array() );
		if ( ! empty( $status_options['template_debug_mode'] ) && current_user_can( 'manage_options' ) ) {
			define( 'WPCM_TEMPLATE_DEBUG_MODE', true );
		} else {
			define( 'WPCM_TEMPLATE_DEBUG_MODE', false );
		}
	}
}
add_action( 'after_setup_theme', 'wpcm_template_debug_mode', 20 );


/**
 * Get information about available image sizes
 */
function wpcm_get_image_sizes( $size = '' ) {
 
    global $_wp_additional_image_sizes;
 
    $sizes = array();
    $get_intermediate_image_sizes = get_intermediate_image_sizes();
 
    // Create the full array with sizes and crop info
    foreach( $get_intermediate_image_sizes as $_size ) {
        if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
            $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
            $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
            $sizes[ $_size ]['crop'] = (bool) get_option( $_size . '_crop' );
        } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
            $sizes[ $_size ] = array( 
                'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                'crop' =>  $_wp_additional_image_sizes[ $_size ]['crop']
            );
        }
    }
 
    // Get only 1 size if found
    if ( $size ) {
        if( isset( $sizes[ $size ] ) ) {
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
 * @return string
 */
function wpcm_placeholder_img( $size = 'player_thumbnail' ) {
	$dimensions = wpcm_get_image_size( $size );

	return apply_filters('wpclubmanager_placeholder_img', '<img src="' . wpcm_placeholder_img_src() . '" alt="Placeholder" width="' . esc_attr( $dimensions['width'] ) . '" class="wpclubmanager-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />' );
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
 * @return string
 */
function wpcm_crest_placeholder_img( $size = 'crest-small' ) {
	$dimensions = wpcm_get_image_sizes( $size );
	
	return apply_filters('wpclubmanager_crest_placeholder_img', '<img src="' . wpcm_crest_placeholder_img_src() . '" alt="Placeholder" width="' . esc_attr( $dimensions['width'] ) . '" class="wpclubmanager-crest-placeholder wp-post-image" height="' . esc_attr( $dimensions['height'] ) . '" />' );
}

function wpcm_flush_rewrite_rules() {
    // Flush rewrite rules
    $post_types = new WPCM_Post_Types();
    $post_types->register_taxonomies();
    $post_types->register_post_types();
    flush_rewrite_rules();
}

function wpcm_nonce() {
	wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );
}

/**
 * Get the term slug from ID.
 *
 * @access public
 * @param string $term
 * @param string $taxonomy
 * @return mixed $slug
 */
function wpcm_get_term_slug( $term, $taxonomy ) {

	$slug = null;

	if ( is_numeric( $term ) && $term > 0 ) {

		$term_object = get_term( $term, $taxonomy );

		if ( $term_object )

			$slug = $term_object->slug;
	}

	return $slug;
}

/**
 * Returns get_terms() ordered by term_meta.
 *
 * @access public
 * @param int $post
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
	    if( !empty( $term_ids ) ) {

	    	return get_terms( array( 'taxonomy' => $taxonomy, 'include' => $term_ids ) );

	    } else {

	    	return wp_get_object_terms( $post, $taxonomy );
	    	
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
	$club = get_option( 'wpcm_default_club' );

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
 * @since 1.3
 * @return array
 */
function wpcm_get_core_supported_themes() {
	return array( 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );
}

/**
 * Get team display names
 *
 * @access public
 * @param string $post
 * @return mixed
 */
if (!function_exists('wpcm_get_team_name')) {
	function wpcm_get_team_name( $post, $id ) {

		$club = get_default_club();

		if( $post == $club ) {

			$teams = wp_get_object_terms( $id, 'wpcm_team' );

			if ( ! empty( $teams ) && is_array( $teams ) ) {

				foreach ( $teams as $team ) {

					$team = reset($teams);
					$t_id = $team->term_id;
					$team_meta = get_option( "taxonomy_term_$t_id" );
					$team_label = $team_meta['wpcm_team_label'];

					if ( $team_label ) {
						$team_name =  $team_label;
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
 * Decode address
 *
 * @access public
 * @param string $address
 * @return mixed $coordinates
 */
function wpcm_decode_address( $address ) {

    $address_hash = md5( $address );
    $coordinates = get_transient( $address_hash );
    $api_key = get_option( 'wpcm_google_map_api');
	
	if ( false === $coordinates ) {
		$args = array( 
			'address' => urlencode( $address ),
			'api' => urlencode( $api_key )
		);
		$url = add_query_arg( $args, 'https://maps.googleapis.com/maps/api/geocode/json' );

     	$response = wp_remote_get( $url );
		
     	if ( is_wp_error( $response ) )
     		return;

		if ( $response['response']['code'] == 200 ) {
	     	$data = wp_remote_retrieve_body( $response );
			
	     	if ( is_wp_error( $data ) )
	     		return;
			
			$data = json_decode( $data );

			if ( $data->status === 'OK' ) {
			  	$coordinates = $data->results[0]->geometry->location;

			  	$cache_value['lat'] = $coordinates->lat;
			  	$cache_value['lng'] = $coordinates->lng;

			  	// cache coordinates for 1 month
			  	set_transient( $address_hash, $cache_value, 3600*24*30 );
				$coordinates = $cache_value;

			} elseif ( $data->status === 'ZERO_RESULTS' ) {
			  	return __( 'No location found for the entered address.', 'wp-club-manager' );
			} elseif( $data->status === 'INVALID_REQUEST' ) {
			   	return __( 'Invalid request. Address is missing', 'wp-club-manager' );
			} else {
				return __( 'Something went wrong while retrieving your map.', 'wp-club-manager' );
			}
		} else {
		 	return __( 'Unable to contact Google API service.', 'wp-club-manager' );
		}
		
	}
	
	return $coordinates;
}

/**
 * Add row actions for clubs
 *
 * @since 1.4.5
 * @param  array $actions
 * @param  WP_Post $post
 * @return array
 */
function  wpcm_club_row_actions( $actions, $post ) {
	if ( 'wpcm_club' === $post->post_type ) {
		return array_merge( array( 'id' => 'ID: ' . $post->ID ), $actions );
	}

	return $actions;
}
add_filter( 'post_row_actions', 'wpcm_club_row_actions', 2, 100 );