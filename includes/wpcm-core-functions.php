<?php
/**
 * WPClubManager Core Functions
 *
 * Functions available on both the front-end and admin.
 *
 * @author 		ClubPress
 * @category 	Core
 * @package 	WPClubManager/Functions
 * @version     1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Include core functions
include( 'wpcm-conditional-functions.php' );
include( 'wpcm-club-functions.php');
include( 'wpcm-player-functions.php');
include( 'wpcm-match-functions.php');
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
 * Get the crest placeholder image
 *
 * @access public
 * @return string
 */
function wpcm_crest_placeholder_img( $size = 'crest-small' ) {
	return apply_filters('wpclubmanager_crest_placeholder_img', '<img src="' . wpcm_placeholder_img_src() . '" alt="Placeholder" width="25" class="wpclubmanager-crest-placeholder wp-post-image" height="25" />' );
}

if ( !function_exists( 'wpcm_flush_rewrite_rules' ) ) {
	function wpcm_flush_rewrite_rules() {
	    // Flush rewrite rules
	    $post_types = new WPCM_Post_Types();
	    $post_types->register_taxonomies();
	    $post_types->register_post_types();
	    flush_rewrite_rules();
	}
}

if ( !function_exists( 'wpcm_nonce' ) ) {
	function wpcm_nonce() {
		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );
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

if ( !function_exists( 'wpcm_array_value' ) ) {
	function wpcm_array_value( $arr = array(), $key = 0, $default = null ) {
		return ( isset( $arr[ $key ] ) ? $arr[ $key ] : $default );
	}
}

if ( !function_exists( 'wpcm_array_combine' ) ) {
	function wpcm_array_combine( $keys = array(), $values = array() ) {
		$output = array();
		foreach ( $keys as $key ):
			if ( is_array( $values ) && array_key_exists( $key, $values ) )
				$output[ $key ] = $values[ $key ];
			else
				$output[ $key ] = array();
		endforeach;
		return $output;
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
			
			echo '<option value=""' . ( '' == $args['selected'] ? ' selected' : '' ) . '>' . $args['show_option_none'] . '</option>';

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

/**
 * Sports presets. Code adapted from SportsPress
 *
 * Get an array of sport options and settings.
 * @return array
 */
function wpcm_get_sport_presets() {
	return apply_filters( 'wpcm_sports', array(
		'baseball' => array(
			'name' => __( 'Baseball', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => '',
						'slug' => '',
					),
				),
			),
			'stats_labels' => array(
				'ab' => '<a title="' . __('At Bats', 'wpclubmanager'). '">' . __('AB', 'wpclubmanager') . '</a>',
				'h' => '<a title="' . __('Hits', 'wpclubmanager'). '">' . __('H', 'wpclubmanager') . '</a>',
				'r' => '<a title="' . __('Runs', 'wpclubmanager'). '">' . __('R', 'wpclubmanager') . '</a>',
				'er' => '<a title="' . __('Earned Runs', 'wpclubmanager'). '">' . __('ER', 'wpclubmanager') . '</a>',
				'hr' => '<a title="' . __('Home Runs', 'wpclubmanager'). '">' . __('HR', 'wpclubmanager') . '</a>',
				'2b' => '<a title="' . __('Doubles', 'wpclubmanager'). '">' . __('2B', 'wpclubmanager') . '</a>',
				'3b' => '<a title="' . __('Triples', 'wpclubmanager'). '">' . __('3B', 'wpclubmanager') . '</a>',
				'rbi' => '<a title="' . __('Runs Batted In', 'wpclubmanager'). '">' . __('RBI', 'wpclubmanager') . '</a>',
				'bb' => '<a title="' . __('Bases on Bulk', 'wpclubmanager'). '">' . __('BB', 'wpclubmanager') . '</a>',
				'so' => '<a title="' . __('Strike Outs', 'wpclubmanager'). '">' . __('SO', 'wpclubmanager') . '</a>',
				'sb' => '<a title="' . __('Stolen Bases', 'wpclubmanager'). '">' . __('SB', 'wpclubmanager') . '</a>',
				'cs' => '<a title="' . __('Caught Stealing', 'wpclubmanager'). '">' . __('CS', 'wpclubmanager') . '</a>',
				'tc' => '<a title="' . __('Total Chances', 'wpclubmanager'). '">' . __('TC', 'wpclubmanager') . '</a>',
				'po' => '<a title="' . __('Putouts', 'wpclubmanager'). '">' . __('PO', 'wpclubmanager') . '</a>',
				'a' => '<a title="' . __('Assists', 'wpclubmanager'). '">' . __('A', 'wpclubmanager') . '</a>',
				'e' => '<a title="' . __('Errors', 'wpclubmanager'). '">' . __('E', 'wpclubmanager') . '</a>',
				'dp' => '<a title="' . __('Double Plays', 'wpclubmanager'). '">' . __('DP', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'basketball' => array(
			'name' => __( 'Basketball', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Point Guard',
						'slug' => 'pointguard',
					),
					array(
						'name' => 'Shooting Guard',
						'slug' => 'shootingguard',
					),
					array(
						'name' => 'Small Forward',
						'slug' => 'smallforward',
					),
					array(
						'name' => 'Power Forward',
						'slug' => 'powerforward',
					),
					array(
						'name' => 'Center',
						'slug' => 'center',
					),
				),
			),
			'stats_labels' => array(
				'min' => '<a title="' . __('Minutes', 'wpclubmanager'). '">' . __('MIN', 'wpclubmanager') . '</a>',
				'fgm' => '<a title="' . __('Field Goals Made', 'wpclubmanager'). '">' . __('FGM', 'wpclubmanager') . '</a>',
				'fga' => '<a title="' . __('Field Goals Attempted', 'wpclubmanager'). '">' . __('FGA', 'wpclubmanager') . '</a>',
				'3pm' => '<a title="' . __('3 Points Made', 'wpclubmanager'). '">' . __('3PM', 'wpclubmanager') . '</a>',
				'3pa' => '<a title="' . __('3 Ponits Attempted', 'wpclubmanager'). '">' . __('3PA', 'wpclubmanager') . '</a>',
				'ftm' => '<a title="' . __('Free Throws Made', 'wpclubmanager'). '">' . __('FTM', 'wpclubmanager') . '</a>',
				'fta' => '<a title="' . __('Free Throws Attempted', 'wpclubmanager'). '">' . __('FTA', 'wpclubmanager') . '</a>',
				'or' => '<a title="' . __('Offensive Rebounds', 'wpclubmanager'). '">' . __('OR', 'wpclubmanager') . '</a>',
				'dr' => '<a title="' . __('Defensive Rebounds', 'wpclubmanager'). '">' . __('DR', 'wpclubmanager') . '</a>',
				'reb' => '<a title="' . __('Rebounds', 'wpclubmanager'). '">' . __('REB', 'wpclubmanager') . '</a>',
				'ast' => '<a title="' . __('Assists', 'wpclubmanager'). '">' . __('AST', 'wpclubmanager') . '</a>',
				'blk' => '<a title="' . __('Blocks', 'wpclubmanager'). '">' . __('BLK', 'wpclubmanager') . '</a>',
				'stl' => '<a title="' . __('Steals', 'wpclubmanager'). '">' . __('STL', 'wpclubmanager') . '</a>',
				'pf' => '<a title="' . __('Personal Fouls', 'wpclubmanager'). '">' . __('PF', 'wpclubmanager') . '</a>',
				'to' => '<a title="' . __('Turnovers', 'wpclubmanager'). '">' . __('TO', 'wpclubmanager') . '</a>',
				'pts' => '<a title="' . __('Points', 'wpclubmanager'). '">' . __('PTS', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'floorball' => array(
			'name' => __( 'Floorball', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Goalkeeper',
						'slug' => 'goalkeeper',
					),
					array(
						'name' => 'Defender',
						'slug' => 'defender',
					),
					array(
						'name' => 'Forward',
						'slug' => 'forward',
					),
				),
			),
			'stats_labels' => array(
				'g' => '<a title="' . __('Goals', 'wpclubmanager'). '">' . __('G', 'wpclubmanager') . '</a>',
				'a' => '<a title="' . __('Assists', 'wpclubmanager'). '">' . __('A', 'wpclubmanager') . '</a>',
				'plusminus' => '<a title="' . __('Plus/Minus Rating', 'wpclubmanager'). '">' . __('+/-', 'wpclubmanager') . '</a>',
				'sog' => '<a title="' . __('Shots on Goal', 'wpclubmanager'). '">' . __('SOG', 'wpclubmanager') . '</a>',
				'pim' => '<a title="' . __('Penalty Minutes', 'wpclubmanager'). '">' . __('PIM', 'wpclubmanager') . '</a>',
				'redcards' => '<a title="' . __('Red Cards', 'wpclubmanager'). '">' . __('RC', 'wpclubmanager') . '</a>',
				'sav' => '<a title="' . __('Saves', 'wpclubmanager'). '">' . __('SAV', 'wpclubmanager') . '</a>',
				'ga' => '<a title="' . __('Goals Against', 'wpclubmanager'). '">' . __('GA', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'football' => array(
			'name' => __( 'American Football', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Quarterback',
						'slug' => 'quarterback',
					),
					array(
						'name' => 'Running Back',
						'slug' => 'runningback',
					),
					array(
						'name' => 'Wide Receiver',
						'slug' => 'widereceiver',
					),
					array(
						'name' => 'Tight End',
						'slug' => 'tightend',
					),
					array(
						'name' => 'Defensive Lineman',
						'slug' => 'defensivelineman',
					),
					array(
						'name' => 'Linebacker',
						'slug' => 'linebacker',
					),
					array(
						'name' => 'Defensive Back',
						'slug' => 'defensiveback',
					),
					array(
						'name' => 'Kickoff Kicker',
						'slug' => 'kickoffkicker',
					),
					array(
						'name' => 'Kick Returner',
						'slug' => 'kickreturner',
					),
					array(
						'name' => 'Punter',
						'slug' => 'punter',
					),
					array(
						'name' => 'Punt Returner',
						'slug' => 'puntreturner',
					),
					array(
						'name' => 'Field Goal Kicker',
						'slug' => 'fieldgoalkicker',
					),
				),
			),
			'stats_labels' => array(
				'pa_cmp' => '<a title="' . __('Pass Completions', 'wpclubmanager'). '">' . __('CMP', 'wpclubmanager') . '</a>',
				'pa_yds' => '<a title="' . __('Passing Yards', 'wpclubmanager'). '">' . __('YDS', 'wpclubmanager') . '</a>',
				'sc_pass' => '<a title="' . __('Passing Touchdowns', 'wpclubmanager'). '">' . __('PASS', 'wpclubmanager') . '</a>',
				'pa_int' => '<a title="' . __('Passing Interceptions', 'wpclubmanager'). '">' . __('INT', 'wpclubmanager') . '</a>',
				'ru_yds' => '<a title="' . __('Rushing Yards', 'wpclubmanager'). '">' . __('YDS', 'wpclubmanager') . '</a>',
				'sc_rush' => '<a title="' . __('Rushing Touchdowns', 'wpclubmanager'). '">' . __('RUSH', 'wpclubmanager') . '</a>',
				're_rec' => '<a title="' . __('Receptions', 'wpclubmanager'). '">' . __('REC', 'wpclubmanager') . '</a>',
				're_yds' => '<a title="' . __('Receiving Yards', 'wpclubmanager'). '">' . __('YDS', 'wpclubmanager') . '</a>',
				'sc_rec' => '<a title="' . __('Receiving Touchdowns', 'wpclubmanager'). '">' . __('REC', 'wpclubmanager') . '</a>',
				'de_total' => '<a title="' . __('Total Tackles', 'wpclubmanager'). '">' . __('TOTAL', 'wpclubmanager') . '</a>',
				'de_sack' => '<a title="' . __('Sacks', 'wpclubmanager'). '">' . __('SACK', 'wpclubmanager') . '</a>',
				'de_ff' => '<a title="' . __('Fumbles', 'wpclubmanager'). '">' . __('FF', 'wpclubmanager') . '</a>',
				'de_int' => '<a title="' . __('Interceptions', 'wpclubmanager'). '">' . __('INT', 'wpclubmanager') . '</a>',
				'de_kb' => '<a title="' . __('Blocked Kicks', 'wpclubmanager'). '">' . __('KB', 'wpclubmanager') . '</a>',
				'sc_td' => '<a title="' . __('Touchdowns', 'wpclubmanager'). '">' . __('TD', 'wpclubmanager') . '</a>',
				'sc_2pt' => '<a title="' . __('2 Point Conversions', 'wpclubmanager'). '">' . __('2PT', 'wpclubmanager') . '</a>',
				'sc_fg' => '<a title="' . __('Field Goals', 'wpclubmanager'). '">' . __('FG', 'wpclubmanager') . '</a>',
				'sc_pat' => '<a title="' . __('Extra Points', 'wpclubmanager'). '">' . __('PAT', 'wpclubmanager') . '</a>',
				'sc_pts' => '<a title="' . __('Total Points', 'wpclubmanager'). '">' . __('PTS', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'footy' => array(
			'name' => __( 'Australian Rules Football', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Full Back',
						'slug' => 'full-back',
					),
					array(
						'name' => 'Back Pocket',
						'slug' => 'back-pocket',
					),
					array(
						'name' => 'Centre Half-Back',
						'slug' => 'centre-half-back',
					),
					array(
						'name' => 'Half-Back Flank',
						'slug' => 'half-back-flank',
					),
					array(
						'name' => 'Centre Half-Forward',
						'slug' => 'centre-half-forward',
					),
					array(
						'name' => 'Half-Forward Flank',
						'slug' => 'half-forward-flank',
					),
					array(
						'name' => 'Full Forward',
						'slug' => 'full-forward',
					),
					array(
						'name' => 'Forward Pocket',
						'slug' => 'forward-pocket',
					),
					array(
						'name' => 'Follower',
						'slug' => 'follower',
					),
					array(
						'name' => 'Inside Midfield',
						'slug' => 'inside-midfield',
					),
					array(
						'name' => 'Outside Midfield',
						'slug' => 'outside-midfield',
					),
				),
			),
			'stats_labels' => array(
				'k' => '<a title="' . __('Kicks', 'wpclubmanager'). '">' . __('K', 'wpclubmanager') . '</a>',
				'hb' => '<a title="' . __('Handballs', 'wpclubmanager'). '">' . __('HB', 'wpclubmanager') . '</a>',
				'd' => '<a title="' . __('Disposals', 'wpclubmanager'). '">' . __('D', 'wpclubmanager') . '</a>',
				'cp' => '<a title="' . __('Contested Possesion', 'wpclubmanager'). '">' . __('CP', 'wpclubmanager') . '</a>',
				'm' => '<a title="' . __('Marks', 'wpclubmanager'). '">' . __('M', 'wpclubmanager') . '</a>',
				'cm' => '<a title="' . __('Contested Marks', 'wpclubmanager'). '">' . __('CM', 'wpclubmanager') . '</a>',
				'ff' => '<a title="' . __('Frees For', 'wpclubmanager'). '">' . __('FF', 'wpclubmanager') . '</a>',
				'fa' => '<a title="' . __('Frees Against', 'wpclubmanager'). '">' . __('FA', 'wpclubmanager') . '</a>',
				'clg' => '<a title="' . __('Clangers', 'wpclubmanager'). '">' . __('C', 'wpclubmanager') . '</a>',
				'tkl' => '<a title="' . __('Tackles', 'wpclubmanager'). '">' . __('T', 'wpclubmanager') . '</a>',
				'i50' => '<a title="' . __('Inside 50s', 'wpclubmanager'). '">' . __('I50', 'wpclubmanager') . '</a>',
				'r50' => '<a title="' . __('Rebound 50s', 'wpclubmanager'). '">' . __('R50', 'wpclubmanager') . '</a>',
				'1pct' => '<a title="' . __('One-Percenters', 'wpclubmanager'). '">' . __('1PCT', 'wpclubmanager') . '</a>',
				'ho' => '<a title="' . __('Hit-Outs', 'wpclubmanager'). '">' . __('HO', 'wpclubmanager') . '</a>',
				'g' => '<a title="' . __('Goals', 'wpclubmanager'). '">' . __('G', 'wpclubmanager') . '</a>',
				'b' => '<a title="' . __('Behinds', 'wpclubmanager'). '">' . __('B', 'wpclubmanager') . '</a>',
				'yellowcards' => '<a title="' . __('Yellow Cards', 'wpclubmanager'). '">' . __('YC', 'wpclubmanager') . '</a>',
				'redcards' => '<a title="' . __('Red Cards', 'wpclubmanager'). '">' . __('RC', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'gaelic' => array(
			'name' => __( 'Gaelic Football / Hurling', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Goalkeeper',
						'slug' => 'goalkeeper',
					),
					array(
						'name' => 'Defender',
						'slug' => 'defender',
					),
					array(
						'name' => 'Midfielder',
						'slug' => 'midfielder',
					),
					array(
						'name' => 'Forward',
						'slug' => 'forward',
					),
				),
			),
			'stats_labels' => array(
				'g' => '<a title="' . __('Goals', 'wpclubmanager'). '">' . __('G', 'wpclubmanager') . '</a>',
				'pts' => '<a title="' . __('Points', 'wpclubmanager'). '">' . __('P', 'wpclubmanager') . '</a>',
				'gff' => '<a title="' . __('Goals from Frees', 'wpclubmanager'). '">' . __('GFF', 'wpclubmanager') . '</a>',
				'sog' => '<a title="' . __('Points from Frees', 'wpclubmanager'). '">' . __('PFF', 'wpclubmanager') . '</a>',
				'yellowcards' => '<a title="' . __('Yellow Cards', 'wpclubmanager'). '">' . __('YC', 'wpclubmanager') . '</a>',
				'blackcards' => '<a title="' . __('Black Cards', 'wpclubmanager'). '">' . __('BC', 'wpclubmanager') . '</a>',
				'redcards' => '<a title="' . __('Red Cards', 'wpclubmanager'). '">' . __('RC', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'handball' => array(
			'name' => __( 'Handball', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Goalkeeper',
						'slug' => 'goalkeeper',
					),
					array(
						'name' => 'Left Wing',
						'slug' => 'left-wing',
					),
					array(
						'name' => 'Left Back',
						'slug' => 'left-back',
					),
					array(
						'name' => 'Center',
						'slug' => 'center',
					),
					array(
						'name' => 'Right Wing',
						'slug' => 'right-wing',
					),
					array(
						'name' => 'Right Back',
						'slug' => 'right-back',
					),
					array(
						'name' => 'Pivot',
						'slug' => 'pivot',
					),
				),
			),
			'stats_labels' => array(
				'goals' => '<a title="' . __('Goals', 'wpclubmanager'). '">' . __('GLS', 'wpclubmanager') . '</a>',
				'2min' => '<a title="' . __('2 Minute Suspension', 'wpclubmanager'). '">' . __('2MIN', 'wpclubmanager') . '</a>',
				'yellowcards' => '<a title="' . __('Yellow Cards', 'wpclubmanager'). '">' . __('YC', 'wpclubmanager') . '</a>',
				'redcards' => '<a title="' . __('Red Cards', 'wpclubmanager'). '">' . __('RC', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'hockey_field' => array(
			'name' => __( 'Field Hockey', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Goalie',
						'slug' => 'goalie',
					),
					array(
						'name' => 'Defence',
						'slug' => 'defence',
					),
					array(
						'name' => 'Midfield',
						'slug' => 'midfield',
					),
					array(
						'name' => 'Forward',
						'slug' => 'forward',
					),
				),
			),
			'stats_labels' => array(
				'gls' => '<a title="' . __('Goals', 'wpclubmanager'). '">' . __('G', 'wpclubmanager') . '</a>',
				'ass' => '<a title="' . __('Assists', 'wpclubmanager'). '">' . __('A', 'wpclubmanager') . '</a>',
				'sho' => '<a title="' . __('Shots', 'wpclubmanager'). '">' . __('SH', 'wpclubmanager') . '</a>',
				'sog' => '<a title="' . __('Shots on Goal', 'wpclubmanager'). '">' . __('SOG', 'wpclubmanager') . '</a>',
				'sav' => '<a title="' . __('Saves', 'wpclubmanager'). '">' . __('SAV', 'wpclubmanager') . '</a>',
				'greencards' => '<a title="' . __('Green Cards', 'wpclubmanager'). '">' . __('GC', 'wpclubmanager') . '</a>',
				'yellowcards' => '<a title="' . __('Yellow Cards', 'wpclubmanager'). '">' . __('YC', 'wpclubmanager') . '</a>',
				'redcards' => '<a title="' . __('Red Cards', 'wpclubmanager'). '">' . __('RC', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'hockey' => array(
			'name' => __( 'Ice Hockey', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Goalie',
						'slug' => 'goalie',
					),
					array(
						'name' => 'Defense',
						'slug' => 'defense',
					),
					array(
						'name' => 'Center',
						'slug' => 'center',
					),
					array(
						'name' => 'Right Wing',
						'slug' => 'right-wing',
					),
					array(
						'name' => 'Left Wing',
						'slug' => 'left-wing',
					),
				),
			),
			'stats_labels' => array(
				'g' => '<a title="' . __('Goals', 'wpclubmanager'). '">' . __('G', 'wpclubmanager') . '</a>',
				'a' => '<a title="' . __('Assists', 'wpclubmanager'). '">' . __('A', 'wpclubmanager') . '</a>',
				'plusminus' => '<a title="' . __('Plus/Minus Rating', 'wpclubmanager'). '">' . __('+/-', 'wpclubmanager') . '</a>',
				'sog' => '<a title="' . __('Shots on Goal', 'wpclubmanager'). '">' . __('SOG', 'wpclubmanager') . '</a>',
				'ms' => '<a title="' . __('Missed Shots', 'wpclubmanager'). '">' . __('MS', 'wpclubmanager') . '</a>',
				'bs' => '<a title="' . __('Blocked Shots', 'wpclubmanager'). '">' . __('BS', 'wpclubmanager') . '</a>',
				'pim' => '<a title="' . __('Penalty Minutes', 'wpclubmanager'). '">' . __('PIM', 'wpclubmanager') . '</a>',
				'ht' => '<a title="' . __('Hits', 'wpclubmanager'). '">' . __('HT', 'wpclubmanager') . '</a>',
				'fw' => '<a title="' . __('Faceoffs Won', 'wpclubmanager'). '">' . __('FW', 'wpclubmanager') . '</a>',
				'fl' => '<a title="' . __('Faceoffs Lost', 'wpclubmanager'). '">' . __('FL', 'wpclubmanager') . '</a>',
				'sav' => '<a title="' . __('Saves', 'wpclubmanager'). '">' . __('SAV', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'netball' => array(
			'name' => __( 'Netball', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Goal Shooter',
						'slug' => 'goal-shooter',
					),
					array(
						'name' => 'Goal Attack',
						'slug' => 'goal-attack',
					),
					array(
						'name' => 'Wing Attack',
						'slug' => 'wing-attack',
					),
					array(
						'name' => 'Centre',
						'slug' => 'centre',
					),
					array(
						'name' => 'Wing Defence',
						'slug' => 'wing-defence',
					),
					array(
						'name' => 'Goal Defence',
						'slug' => 'goal-defence',
					),
					array(
						'name' => 'Goal Keeper',
						'slug' => 'goal-keeper',
					),
				),
			),
			'stats_labels' => array(
				'g' => '<a title="' . __('Goals', 'wpclubmanager'). '">' . __('GLS', 'wpclubmanager') . '</a>',
				'gatt' => '<a title="' . __('Goal Attempts', 'wpclubmanager'). '">' . __('ATT', 'wpclubmanager') . '</a>',
				'gass' => '<a title="' . __('Goal Assists', 'wpclubmanager'). '">' . __('AST', 'wpclubmanager') . '</a>',
				'rbs' => '<a title="' . __('Rebounds', 'wpclubmanager'). '">' . __('REB', 'wpclubmanager') . '</a>',
				'cpr' => '<a title="' . __('Centre Pass Receives', 'wpclubmanager'). '">' . __('CPR', 'wpclubmanager') . '</a>',
				'int' => '<a title="' . __('Interceptions', 'wpclubmanager'). '">' . __('INT', 'wpclubmanager') . '</a>',
				'def' => '<a title="' . __('Deflections', 'wpclubmanager'). '">' . __('DEF', 'wpclubmanager') . '</a>',
				'pen' => '<a title="' . __('Penaties', 'wpclubmanager'). '">' . __('PEN', 'wpclubmanager') . '</a>',
				'to' => '<a title="' . __('Turnovers', 'wpclubmanager'). '">' . __('TO', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'rugby' => array(
			'name' => __( 'Rugby', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Scrum Half',
						'slug' => 'scrum-half',
					),
					array(
						'name' => 'Fly Half',
						'slug' => 'fly-half',
					),
					array(
						'name' => 'Centre',
						'slug' => 'centre',
					),
					array(
						'name' => 'Winger',
						'slug' => 'winger',
					),
					array(
						'name' => 'Full Back',
						'slug' => 'full-back',
					),
					array(
						'name' => 'Prop',
						'slug' => 'prop',
					),
					array(
						'name' => 'Hooker',
						'slug' => 'hooker',
					),
					array(
						'name' => 'Lock',
						'slug' => 'lock',
					),
					array(
						'name' => 'Flanker',
						'slug' => 'flanker',
					),
					array(
						'name' => 'No. 8',
						'slug' => 'no-8',
					),
				),
			),
			'stats_labels' => array(
				't' => '<a title="' . __('Tries', 'wpclubmanager'). '">' . __('TRI', 'wpclubmanager') . '</a>',
				'c' => '<a title="' . __('Conversions', 'wpclubmanager'). '">' . __('CON', 'wpclubmanager') . '</a>',
				'p' => '<a title="' . __('Penalties', 'wpclubmanager'). '">' . __('PEN', 'wpclubmanager') . '</a>',
				'dg' => '<a title="' . __('Drop Goals', 'wpclubmanager'). '">' . __('DG', 'wpclubmanager') . '</a>',
				'yellowcards' => '<a title="' . __('Yellow Cards', 'wpclubmanager'). '">' . __('YC', 'wpclubmanager') . '</a>',
				'redcards' => '<a title="' . __('Red Cards', 'wpclubmanager'). '">' . __('RC', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager') . '</a>',
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'soccer' => array(
			'name' => __( 'Football (Soccer)', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Goalkeeper',
						'slug' => 'goalkeeper',
					),
					array(
						'name' => 'Defender',
						'slug' => 'defender',
					),
					array(
						'name' => 'Midfielder',
						'slug' => 'midfielder',
					),
					array(
						'name' => 'Forward',
						'slug' => 'forward',
					),
				),
			),
			'stats_labels' => array(
				'goals' => '<a title="' . __('Goals', 'wpclubmanager'). '">' . __('GLS', 'wpclubmanager') . '</a>',
				'assists' => '<a title="' . __('Assists', 'wpclubmanager'). '">' . __('AST', 'wpclubmanager') . '</a>',
				'penalties' => '<a title="' . __('Penalty Goals', 'wpclubmanager'). '">' . __('PENS', 'wpclubmanager') . '</a>',
				'og' => '<a title="' . __('Own Goals', 'wpclubmanager'). '">' . __('OG', 'wpclubmanager') . '</a>',
				'cs' => '<a title="' . __('Clean Sheets', 'wpclubmanager'). '">' . __('CS', 'wpclubmanager') . '</a>',
				'yellowcards' => '<a title="' . __('Yellow Cards', 'wpclubmanager'). '">' . __('YC', 'wpclubmanager') . '</a>',
				'redcards' => '<a title="' . __('Red Cards', 'wpclubmanager'). '">' . __('RC', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
		'volleyball' => array(
			'name' => __( 'Volleyball', 'wpclubmanager' ),
			'terms' => array(
				// Positions
				'wpcm_position' => array(
					array(
						'name' => 'Outside Hitter',
						'slug' => 'outside-hitter',
					),
					array(
						'name' => 'Middle Blocker',
						'slug' => 'middle-blocker',
					),
					array(
						'name' => 'Setter',
						'slug' => 'setter',
					),
					array(
						'name' => 'Opposite',
						'slug' => 'opposite',
					),
					array(
						'name' => 'Defensive Specialist',
						'slug' => 'defensive-specialist',
					),
					array(
						'name' => 'Libero',
						'slug' => 'libero',
					),
				),
			),
			'stats_labels' => array(
				'ace' => '<a title="' . __('Aces', 'wpclubmanager'). '">' . __('ACE', 'wpclubmanager') . '</a>',
				'kill' => '<a title="' . __('Kills', 'wpclubmanager'). '">' . __('KILL', 'wpclubmanager') . '</a>',
				'blk' => '<a title="' . __('Blocks', 'wpclubmanager'). '">' . __('BLK', 'wpclubmanager') . '</a>',
				'bass' => '<a title="' . __('Block Assists', 'wpclubmanager'). '">' . __('BA', 'wpclubmanager') . '</a>',
				'sass' => '<a title="' . __('Setting Assists', 'wpclubmanager'). '">' . __('SA', 'wpclubmanager') . '</a>',
				'dig' => '<a title="' . __('Digs', 'wpclubmanager'). '">' . __('DIG', 'wpclubmanager') . '</a>',
				'rating' => '<a title="' . __('Rating', 'wpclubmanager'). '">' . __('RAT', 'wpclubmanager'),
				'mvp' => '<a title="' . __('Player of Match', 'wpclubmanager'). '">' . __('POM', 'wpclubmanager') . '</a>',
			),
		),
	));
}

function wpcm_get_sport_options() {
	$sports = wpcm_get_sport_presets();
	$options = array();
	foreach ( $sports as $slug => $data ):
		$options[ $slug ] = $data['name'];
	endforeach;
	return $options;
}

function wpcm_get_sports_stats_labels() {
	$sport = get_option('wpcm_sport');

	$data = wpcm_get_sport_presets();

	$wpcm_player_stats_labels = $data[$sport]['stats_labels'];

	return $wpcm_player_stats_labels;
}

function wpcm_decode_address( $address ) {

    $address_hash = md5( $address );

    $coordinates = get_transient( $address_hash );
	
	if ( false === $coordinates ) {
		$args = array( 'address' => urlencode( $address ) );
		$url = add_query_arg( $args, 'http://maps.googleapis.com/maps/api/geocode/json' );
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
			  	return __( 'No location found for the entered address.', 'wp-gmaps' );
			} elseif( $data->status === 'INVALID_REQUEST' ) {
			   	return __( 'Invalid request. Address is missing', 'wp-gmaps' );
			} else {
				return __( 'Something went wrong while retrieving your map.', 'wp-gmaps' );
			}
		} else {
		 	return __( 'Unable to contact Google API service.', 'wp-gmaps' );
		}
		
	}
	
	return $coordinates;
}