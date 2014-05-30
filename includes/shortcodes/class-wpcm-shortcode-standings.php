<?php
/**
 * Standing Shortcode
 *
 * @author 		Clubpress
 * @category 	Shortcodes
 * @package 	WPClubManager/Shortcodes
 * @version     1.1.1
 */
class WPCM_Shortcode_Standings {

	/**
	 * Output the standings shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {

		if ( ! isset( $atts['id'] ) && isset( $atts[0] ) && is_numeric( $atts[0] ) )
			$atts['id'] = $atts[0];

		wpclubmanager_get_template( 'shortcodes/standings.php', $atts );
	}
}