<?php
/**
 * Load frontend assets.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.0.0
 */

class WPCM_Frontend_Scripts {

	/**
	 * Constructor
	 */
	public function __construct () {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'wp_print_scripts', array( $this, 'check_jquery' ), 25 );
	}

	/**
	 * Get styles for the frontend
	 * @return array
	 */
	public static function get_styles() {

		return apply_filters( 'wpclubmanager_enqueue_styles', array(
			'wpclubmanager-general' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', WPCM()->plugin_url() ) . '/assets/css/wpclubmanager.css',
				'deps'    => '',
				'version' => WPCM_VERSION,
				'media'   => 'all'
			),
		) );
	}

	/**
	 * Loads the scripts for the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 * @return void
	 */
	public function load_scripts() {

		global $post, $wp;

		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$assets_path          = str_replace( array( 'http:', 'https:' ), '', WPCM()->plugin_url() ) . '/assets/';
		$frontend_script_path = $assets_path . 'js/frontend/';

		wp_register_script( 'google-maps-api', '//maps.google.com/maps/api/js?sensor=false' );

		// Global frontend scripts
		wp_enqueue_script( 'wpclubmanager', $frontend_script_path . 'wpclubmanager' . $suffix . '.js', array( 'jquery' ), WPCM_VERSION, true );

		// CSS Styles
		$enqueue_styles = $this->get_styles();

		if ( $enqueue_styles )
			foreach ( $enqueue_styles as $handle => $args )
				wp_enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
		
	}

	/**
	 * WC requires jQuery 1.7 since it uses functions like .on() for events.
	 * If, by the time wp_print_scrips is called, jQuery is outdated (i.e not
	 * using the version in core) we need to deregister it and register the
	 * core version of the file.
	 *
	 * @access public
	 * @return void
	 */
	public function check_jquery() {

		global $wp_scripts;

		// Enforce minimum version of jQuery
		if ( ! empty( $wp_scripts->registered['jquery']->ver ) && ! empty( $wp_scripts->registered['jquery']->src ) && 0 >= version_compare( $wp_scripts->registered['jquery']->ver, '1.7' ) ) {
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', '/wp-includes/js/jquery/jquery.js', array(), '1.7' );
			wp_enqueue_script( 'jquery' );
		}
	}
}

new WPCM_Frontend_Scripts();