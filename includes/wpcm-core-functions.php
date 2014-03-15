<?php
/**
 * WPClubManager Core Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Include core functions
include( 'wpcm-conditional-functions.php' );
include( 'wpcm-club-functions.php');
include( 'wpcm-player-functions.php');
include( 'wpcm-match-functions.php');
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
		$template = locate_template( array ( "{$slug}-{$name}.php", "{WPCM()->template_path}{$slug}-{$name}.php" ) );

	// Get default slug-name.php
	if ( !$template && $name && file_exists( WPCM()->plugin_path() . "/templates/{$slug}-{$name}.php" ) )
		$template = WPCM()->plugin_path() . "/templates/{$slug}-{$name}.php";

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/wpclubmanager/slug.php
	if ( !$template )
		$template = locate_template( array ( "{$slug}.php", "{WPCM()->template_path}{$slug}.php" ) );

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

	do_action( 'wpclubmanager_before_template_part', $template_name, $template_path, $located, $args );

	include( $located );

	do_action( 'wpclubmanager_after_template_part', $template_name, $template_path, $located, $args );
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
	if ( ! $template )
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
	
	if ( in_array( $image_size, array( 'player_single', 'staff_single','player_thumbnail', 'staff_thumbnail' ) ) ) {
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
 * Queue some JavaScript code to be output in the footer.
 *
 * @param string $code
 */
function wpcm_enqueue_js( $code ) {
	global $wpcm_queued_js;

	if ( empty( $wpcm_queued_js ) ) {
		$wpcm_queued_js = '';
	}

	$wpcm_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function wpcm_print_js() {
	
	global $wpcm_queued_js;

	if ( ! empty( $wpcm_queued_js ) ) {

		echo "<!-- WPClubManager JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

		// Sanitize
		$wpcm_queued_js = wp_check_invalid_utf8( $wpcm_queued_js );
		$wpcm_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $wpcm_queued_js );
		$wpcm_queued_js = str_replace( "\r", '', $wpcm_queued_js );

		echo $wpcm_queued_js . "});\n</script>\n";

		unset( $wpcm_queued_js );
	}
}

/**
 * Subvalue sorting.
 *
 * @access public
 * @param array
 * @param string $subkey
 * @return array
 */
if (!function_exists('subval_sort')) {
	function subval_sort($a,$subkey) {

		foreach($a as $k=>$v) {

			$b[$k] = strtolower($v[$subkey]);
		}

		if ($b != null) {

			asort($b);

			foreach($b as $key=>$val) {

				$c[] = $a[$key];
			}

			return $c;
		}

		return array();
	}
}

/**
 * Get the term slug from ID.
 *
 * @access public
 * @param string $term
 * @param string $taxonomy
 * @return mixed $slug
 */
if (!function_exists('wpcm_get_term_slug')) {
	function wpcm_get_term_slug( $term, $taxonomy ) {

		$slug = null;

		if ( is_numeric( $term ) && $term > 0 ) {

			$term_object = get_term( $term, $taxonomy );

			if ( $term_object )

				$slug = $term_object->slug;
		}

		return $slug;
	}
}

/**
 * Dropdown posts function.
 *
 * @access public
 * @param array
 * @return void
 */
if (!function_exists('wpcm_dropdown_posts')) {
	function wpcm_dropdown_posts( $args = array() ) {

		$defaults = array(
			'show_option_none' => false,
			'numberposts' => -1,
			'posts_per_page' => -1,
			'orderby' => 'title',
			'order' => 'ASC',
			'name' => null,
			'id' => null,
			'selected' => null
		);

		$args = array_merge( $defaults, $args );

		if ( ! $args['id'] )

			$args['id'] = $args['name'];
			echo '<select name="' . $args['name'] . '" id="' . $args['id'] . '" class="postform chosen_select">';
			unset( $args['name'] );
		if ( $args['show_option_none'])
			
			echo '<option value="-1"' . ( '-1' == $args['selected'] ? ' selected' : '' ) . '>' . $args['show_option_none'] . '</option>';

		$posts = get_posts( $args );

		foreach ( $posts as $post ) {

			$name = get_the_title( $post->ID );

			if ( isset( $args['post_type'] ) && $args['post_type'] == 'wpcm_match' ) {

				$timestamp = strtotime( $post->post_date );
				$date_format = get_option( 'date_format' );
				$name = date_i18n( $date_format, $timestamp ) . ' - ' . $name; 
			}

			echo '<option class="level-0" value="' . $post->ID . '"' . ( $post->ID == $args['selected'] ? ' selected' : '' ) . '>' . $name . '</option>';
		}

		echo '</select>';
	}
}

/**
 * Dropdown taxonomies function.
 *
 * @access public
 * @param array
 * @return void
 */
if (!function_exists('wpcm_dropdown_taxonomies')) {
	function wpcm_dropdown_taxonomies( $args = array() ) {

		$defaults = array(
			'show_option_all' => false,
			'show_option_none' => false,
			'taxonomy' => null,
			'name' => null,
			'selected' => null
		);

		$args = array_merge( $defaults, $args ); 
		$terms = get_terms( $args['taxonomy'] );
		$name = ( $args['name'] ) ? $args['name'] : $args['taxonomy'];

		if ( $terms ) {

			printf( '<select name="%s" class="postform">', $name );

			if ( $args['show_option_all'] ) {

				printf( '<option value="0">%s</option>', $args['show_option_all'] );
			}

			if ( $args['show_option_none'] ) {

				printf( '<option value="-1">%s</option>', $args['show_option_none'] );
			}

			foreach ( $terms as $term ) {

				printf( '<option value="%s" %s>%s</option>', $term->slug, selected( true, $args['selected'] == $term->slug ), $term->name );
			}

			print( '</select>' );
		}
	}
}

/**
 * Match player subs dropdown.
 *
 * @access public
 * @param string $name
 * @param array
 * @param string $selected
 * @param string $atts
 * @return mixed $output
 */
if (!function_exists('form_dropdown')) {
	function form_dropdown($name, $arr = array(), $selected = null, $atts = null) {

		$output = '<select name="'.$name.'" class="'.$name.'" id="'.$name.'"';

		if ($atts):

			foreach ($atts as $key => $value):

				$output .= ' '.$key.'="'.$value.'"';
			endforeach;
		endif;

		$output .= '>';

		foreach($arr as $key => $value) {

			$output .= '<option'.($selected == $key ? ' selected' : '').' value="'.$key.'">'.$value.'</option>';
		}

		$output .= '</select>';

		return $output;
	}
}

/**
 * Calculate age from birth date.
 *
 * @access public
 * @param string $p_strDate
 * @return mixed
 */
if (!function_exists('get_age')) {
	function get_age( $p_strDate ) {

    	list($Y,$m,$d)    = explode("-",$p_strDate);

    	return( date("md") < $m.$d ? date("Y")-$Y-1 : date("Y")-$Y );
	}
}

/**
 * Match player subs dropdown.
 *
 * @access public
 * @param array
 * @param string $type ('manual')
 * @param string $index ('goals')
 * @return int
 */
if (!function_exists('get_wpcm_stats_value')) {
	function get_wpcm_stats_value( $stats = array(), $type = 'manual', $index = 'goals' ) {

		if ( is_array( $stats ) ) {

			if ( array_key_exists( $type, $stats ) ) {

				if ( array_key_exists( $index, $stats[$type] ) ) {

					return (float)$stats[$type][$index];
				}
			}
		}

		return 0;
	}
}

/**
 * Get the value of the stats.
 *
 * @access public
 * @param string $stats
 * @param string $type
 * @param string $index
 * @return void
 */
if (!function_exists('wpcm_stats_value')) {
	function wpcm_stats_value( $stats, $type, $index ) {

		echo get_wpcm_stats_value( $stats, $type, $index );
	}
}

/**
 * Get match player stats.
 *
 * @access public
 * @param string $post_id
 * @return mixed $players
 */
if (!function_exists('get_wpcm_match_player_stats')) {
	function get_wpcm_match_player_stats( $post_id = null ) {

		if ( !$post_id ) global $post_id;

		$players = unserialize( get_post_meta( $post_id, 'wpcm_players', true ) );
		$output = array();

		if( is_array( $players ) ):

			foreach( $players as $id => $stats ):

				if ( $stats['checked'] )

					$output[$key] = $stats;
			endforeach;
		endif;

		return $players;
	}
}

/**
 * Array values to integer.
 *
 * @access public
 * @param string &$value
 * @param string $key
 * @return void
 */
if (!function_exists('wpcm_array_values_to_int')) {
	function wpcm_array_values_to_int( &$value, $key ) {

		$value = (int)$value;
	}
}

/**
 * Filter checked arrays.
 *
 * @access public
 * @param string $value
 * @return mixed
 */
if (!function_exists('wpcm_array_filter_checked')) {
	function wpcm_array_filter_checked( $value) {
		
		return ( array_key_exists( 'checked', $value ) );
	}
}