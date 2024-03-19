<?php
/**
 * Formatting
 *
 * Functions for formatting data.
 *
 * @author      ClubPress
 * @category    Core
 * @package     WPClubManager/Functions
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Sanitize taxonomy names. Slug format (no spaces, lowercase).
 *
 * Doesn't use sanitize_title as this destroys utf chars.
 *
 * @access public
 * @param mixed $taxonomy
 * @return string
 */
// function wpcm_sanitize_taxonomy_name( $taxonomy ) {

// $filtered = strtolower( remove_accents( stripslashes( strip_tags( $taxonomy ) ) ) );
// $filtered = preg_replace( '/&.+?;/', '', $filtered ); // Kill entities
// $filtered = str_replace( array( '.', '\'', '"' ), '', $filtered ); // Kill quotes and full stops.
// $filtered = str_replace( array( ' ', '_' ), '-', $filtered ); // Replace spaces and underscores.

// return apply_filters( 'sanitize_taxonomy_name', $filtered, $taxonomy );
// }

/**
 * Clean variables
 *
 * @access public
 * @param string $var
 * @return string
 */
function wpcm_clean( $var ) {

	return sanitize_text_field( $var );
}

/**
 * Merge two arrays
 *
 * @access public
 * @param array $a1
 * @param array $a2
 * @return array
 */
// function wpcm_array_overlay( $a1, $a2 ) {

// foreach( $a1 as $k => $v ) {
// if ( ! array_key_exists( $k, $a2 ) ) {
// continue;
// }
// if ( is_array( $v ) && is_array( $a2[ $k ] ) ) {
// $a1[ $k ] = wpcm_array_overlay( $v, $a2[ $k ] );
// } else {
// $a1[ $k ] = $a2[ $k ];
// }
// }
// return $a1;
// }

/**
 * Month Num To Name
 *
 * Takes a month number and returns the name three letter name of it.
 *
 * @since 1.0
 *
 * @param integer $n
 * @return string Short month name
 */
// function month_num_to_name( $n ) {
// $timestamp = mktime( 0, 0, 0, $n, 1, 2005 );

// return date_i18n( "M", $timestamp );
// }

/**
 * Subvalue sorting.
 *
 * @access public
 *
 * @param array  $a
 * @param string $subkey
 *
 * @return array
 */
function subval_sort( $a, $subkey ) {

	foreach ( $a as $k => $v ) {

		$b[ $k ] = strtolower( $v[ $subkey ] ?? '' );
	}

	if ( null != $b ) {

		asort( $b );

		foreach ( $b as $key => $val ) {

			$c[] = $a[ $key ];
		}

		return $c;
	}

	return array();
}

/**
 * @param array $arr
 * @param mixed $key
 * @param mixed $default
 *
 * @return mixed|null
 */
function wpcm_array_value( $arr = array(), $key = 0, $default = null ) {
	return ( isset( $arr[ $key ] ) ? $arr[ $key ] : $default );
}

// function wpcm_array_combine( $keys = array(), $values = array() ) {
// $output = array();
// foreach ( $keys as $key ):
// if ( is_array( $values ) && array_key_exists( $key, $values ) )
// $output[ $key ] = $values[ $key ];
// else
// $output[ $key ] = array();
// endforeach;
// return $output;
// }

/**
 * Array values to integer.
 *
 * @access public
 * @param string &$value
 * @param string $key
 * @return void
 */
if ( ! function_exists( 'wpcm_array_values_to_int' ) ) {
	/**
	 * @param mixed  $value
	 * @param string $key
	 *
	 * @return void
	 */
	function wpcm_array_values_to_int( &$value, $key ) {

		$value = (int) $value;
	}
}

/**
 * Filter checked arrays.
 *
 * @access public
 * @param string $value
 * @return mixed
 */
if ( ! function_exists( 'wpcm_array_filter_checked' ) ) {
	/**
	 * @param array $value
	 *
	 * @return bool
	 */
	function wpcm_array_filter_checked( $value ) {

		return ( array_key_exists( 'checked', $value ) );
	}
}

/**
 * WP Club Manager Date Format - Allows to change date format for everything WP Club Manager
 *
 * @access public
 * @return string
 */
// function wpcm_date_format() {

// return apply_filters( 'wpclubmanager_date_format', get_option( 'date_format' ) );
// }

/**
 * WP Club Manager Time Format - Allows to change time format for everything WP Club Manager
 *
 * @access public
 * @return string
 */
// function wpcm_time_format() {

// return apply_filters( 'wpclubmanager_time_format', get_option( 'time_format' ) );
// }

/**
 * let_to_num function.
 *
 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
 *
 * @param mixed $size
 *
 * @return int
 */
function wpcm_let_to_num( $size ) {
	$l   = substr( $size, -1 );
	$ret = substr( $size, 0, -1 );
	switch ( strtoupper( $l ) ) {
		case 'P':
			$ret *= 1024;
			break;
		case 'T':
			$ret *= 1024;
			break;
		case 'G':
			$ret *= 1024;
			break;
		case 'M':
			$ret *= 1024;
			break;
		case 'K':
			$ret *= 1024;
			break;
	}
	return $ret;
}

/**
 * Dropdown posts function.
 *
 * @access public
 *
 * @param array $args
 *
 * @return void
 */
function wpcm_dropdown_posts( $args = array() ) {

	$defaults = array(
		'show_option_none' => false,
		'numberposts'      => -1,
		'posts_per_page'   => -1,
		'orderby'          => 'title',
		'order'            => 'ASC',
		'name'             => null,
		'id'               => null,
		'selected'         => null,
		'class'            => null,
	);

	$args = array_merge( $defaults, $args );

	if ( ! $args['id'] ) {

		$args['id'] = $args['name'];
	}
		echo '<select name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['id'] ) . '" class="postform ' . esc_attr( $args['class'] ) . ' chosen_select">';
		unset( $args['name'] );
	if ( $args['show_option_none'] ) {

		echo '<option value=""' . ( '' === $args['selected'] ? ' selected' : '' ) . '>' . esc_html( $args['show_option_none'] ) . '</option>';
	}

	$posts = get_posts( $args );

	foreach ( $posts as $post ) {

		$name = get_the_title( $post->ID );

		if ( isset( $args['post_type'] ) && 'wpcm_match' === $args['post_type'] ) {

			$timestamp   = strtotime( $post->post_date );
			$date_format = get_option( 'date_format' );
			$name        = date_i18n( $date_format, $timestamp ) . ' - ' . $name;
		}

		echo '<option class="level-0" value="' . esc_attr( $post->ID ) . '"' . ( $post->ID == $args['selected'] ? ' selected' : '' ) . '>' . esc_html( $name ) . '</option>';
	}

	echo '</select>';
}

/**
 * Dropdown taxonomies function.
 *
 * @access public
 *
 * @param array $args
 *
 * @return bool
 */
function wpcm_dropdown_taxonomies( $args = array() ) {

	$defaults = array(
		'show_option_all'  => false,
		'show_option_none' => false,
		'taxonomy'         => null,
		'name'             => null,
		'id'               => null,
		'selected'         => null,
		'hide_empty'       => false,
		'meta_key'         => 'tax_position',
		'meta_compare'     => 'NUMERIC',
		'orderby'          => 'meta_value_num',
		'values'           => 'slug',
		'class'            => null,
		'attribute'        => null,
		'placeholder'      => null,
		'chosen'           => false,
	);

	$args = array_merge( $defaults, $args );

	if ( ! $args['taxonomy'] ) {
		return false;
	}

	$get_terms_args = $args;
	unset( $get_terms_args['name'] );

	$terms = get_terms( $get_terms_args );
	$name  = ( $args['name'] ) ? $args['name'] : $args['taxonomy'];
	$id    = ( $args['id'] ) ? $args['id'] : $name;

	unset( $args['name'] );
	unset( $args['id'] );

	$class = $args['class'];
	unset( $args['class'] );

	$attribute = $args['attribute'];
	unset( $args['attribute'] );

	$placeholder = $args['placeholder'];
	unset( $args['placeholder'] );

	$selected = $args['selected'];
	unset( $args['selected'] );

	$chosen = $args['chosen'];
	unset( $args['chosen'] );

	sprintf( '<input type="hidden" name="tax_input[%s][]" value="0">', esc_attr( $args['taxonomy'] ) );

	if ( $terms ) :

		printf( '<select name="%s" class="postform %s" %s>', esc_attr( $name ), esc_attr( $class . ( $chosen ? ' chosen_select' : '' ) ), ( null !== $placeholder ? 'data-placeholder="' . esc_html( $placeholder ) . '" ' : '' ) . esc_html( $attribute ) );

		if ( strpos( $attribute, 'multiple' ) === false ) :

			if ( $args['show_option_all'] ) :

				printf( '<option value="0">%s</option>', esc_html( $args['show_option_all'] ) );

			endif;

			if ( $args['show_option_none'] ) :

				printf( '<option value="-1">%s</option>', esc_html( $args['show_option_none'] ) );

			endif;

		endif;

		foreach ( $terms as $term ) :
			if ( 'term_id' === $args['values'] ) :
				$this_value = $term->term_id;
			else :
				$this_value = $term->slug;
			endif;
			if ( strpos( $attribute, 'multiple' ) !== false ) :
				$selected_attribute = in_array( $this_value, $selected ) ? 'selected' : '';
			else :
				$selected_attribute = selected( $this_value, $selected, false );
			endif;
			echo sprintf( '<option value="%s" %s>%s</option>', esc_attr( $this_value ), $selected_attribute, esc_html( $term->name ) ); // phpcs:ignore
		endforeach;
		print( '</select>' );
		return true;
	else :
		return false;

	endif;
}

/**
 * Match player subs dropdown.
 *
 * @access public
 *
 * @param string $name
 * @param array  $arr
 * @param string $selected
 * @param string $atts
 *
 * @return mixed $output
 */
function wpcm_form_dropdown( $name, $arr = array(), $selected = null, $atts = null ) {

	$output = '<select name="' . esc_attr( $name ) . '" class="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '"';

	if ( $atts ) :

		foreach ( $atts as $key => $value ) :

			$output .= ' ' . $key . '="' . esc_attr( $value ) . '"';
		endforeach;
	endif;

	$output .= '>';

	foreach ( $arr as $key => $value ) {

		$output .= '<option' . ( $selected == $key ? ' selected' : '' ) . ' value="' . esc_html( $key ) . '">' . esc_html( $value ) . '</option>';
	}

	$output .= '</select>';

	return $output;
}

/**
 * Calculate age from birth date.
 *
 * @access public
 *
 * @param string $p_str_date
 *
 * @return mixed
 */
function get_age( $p_str_date ) {

	list($y, $m, $d) = explode( '-', $p_str_date );

	return( gmdate( 'md' ) < $m . $d ? gmdate( 'Y' ) - $y - 1 : gmdate( 'Y' ) - $y );
}

/**
 * Calculate age from birth date.
 *
 * @access public
 *
 * @param mixed $a
 * @param mixed $b
 *
 * @return mixed
 */
function compare_dates( $a, $b ) {

	if ( $a == $b ) {
		return 0;
	}

	return ( strtotime( $a ) < strtotime( $b ) ) ? -1 : 1;
}

/**
 * Calculate division.
 *
 * @access public
 *
 * @param mixed $a
 * @param mixed $b
 *
 * @return mixed
 */
function wpcm_divide( $a, $b ) {
	if ( 0 != $b ) {
		$result = $a / $b;
	} else {
		$result = 0;
	}
	return $result;
}
