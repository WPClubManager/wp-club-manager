<?php
/**
 * Plugin Name: WP Club Manager
 * Version: 1.5.7
 * Plugin URI: https://wpclubmanager.com
 * Description: A plugin to help you run a sports club website easily and quickly.
 * Author: Clubpress
 * Author URI: https://wpclubmanager.com
 * Requires at least: 4.2
 * Tested up to: 4.7
 * 
 * Text Domain: wp-club-manager
 * Domain Path: /languages/
 *
 * @package   WPClubManager
 * @category  Core
 * @author    Clubpress <info@wpclubmanager.com>
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPClubManager' ) ) :

/**
 * Main WPClubManager Class
 *
 * @class WPClubManager
 */
final class WPClubManager {

	/**
	 * @var string
	 */
	public $version = '1.5.7';

	/**
	 * @var WPClubManager The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * @var WPCM_Countries $countries
	 */
	public $countries = null;

	/**
	 * @var WPCM_Sports $sports
	 */
	public $sports = null;

	/**
	 * Main WPClubManager Instance
	 *
	 * Ensures only one instance of WPClubManager is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WPCM()
	 * @return WPClubManager - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-club-manager' ), '1.1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'wp-club-manager' ), '1.1.0' );
	}

	/**
	 * WPClubManager Constructor.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return WPClubManager
	 */
	public function __construct() {

		$this->constants();
		$this->includes();
		$this->load_hooks();

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		add_action( 'after_setup_theme', array( $this, 'compatibility' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( 'WPCM_Shortcodes', 'init' ) );
		// if( is_admin() && is_plugins_page() ) {
  //           add_filter( 'admin_footer', array( $this, 'wpcm_add_deactivation_feedback_modal' ) );
  //       }

		do_action( 'wpclubmanager_loaded' );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wpcm-settings' ) . '" title="' . esc_attr( __( 'View WP Club Manager Settings', 'wp-club-manager' ) ) . '">' . __( 'Settings', 'wp-club-manager' ) . '</a>',
			'docs'    => '<a href="' . esc_url( apply_filters( 'wpclubmanager_docs_url', 'http://docs.wpclubmanager.com', 'wp-club-manager' ) ) . '" title="' . esc_attr( __( 'View WP Club Manager Documentation', 'wp-club-manager' ) ) . '">' . __( 'Docs', 'wp-club-manager' ) . '</a>',
	 		'support' => '<a href="' . esc_url( apply_filters( 'wpclubmanager_support_url', 'http://wpclubmanager.com/support/' ) ) . '" title="' . esc_attr( __( 'Support', 'wp-club-manager' ) ) . '">' . __( 'Support', 'wp-club-manager' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Defines constants used by the plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @return void
	 */
	private function constants() {

		define( 'WPCM_PLUGIN_FILE', __FILE__ );
		define( 'WPCM_VERSION', $this->version );

		if ( ! defined( 'WPCM_TEMPLATE_PATH' ) ) {
			define( 'WPCM_TEMPLATE_PATH', $this->template_path() );
		}
		if ( !defined( 'WPCM_URL' ) ) {
			define( 'WPCM_URL', plugin_dir_url( __FILE__ ) );
		}
		if ( !defined( 'WPCM_PATH' ) ) {
			define( 'WPCM_PATH', plugin_dir_path( __FILE__ ) );
		}
		if ( !defined( 'WPCM_PLUGIN_BASENAME' ) ) {
			define( 'WPCM_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		}
		if ( !defined( 'WPCM_BASENAME' ) ) {
			define( 'WPCM_BASENAME', plugin_basename( __FILE__ ) );
		}
	}

	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) );
		}
	}

	/**
	 * Loads the initial files needed by the plugin.
	 *
	 * @since  1.4.0
	 * @access private
	 * @return void
	 */
	private function includes() {

		include_once( 'includes/class-wpcm-autoloader.php' );
		include_once( 'includes/wpcm-core-functions.php' );
		include_once( 'includes/wpcm-widget-functions.php' );
		include_once( 'includes/class-wpcm-install.php' );
		include_once( 'includes/class-wpcm-cache-helper.php' );
		include_once( 'includes/class-wpcm-taxonomy-order.php' );

		if ( $this->is_request( 'admin' ) ) {
			include_once( 'includes/admin/class-wpcm-admin.php' );
			include_once( 'includes/admin/class-wpcm-admin-feedback.php' );
		}

		if ( $this->is_request( 'ajax' ) ) {
			$this->ajax_includes();
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}

		include_once( 'includes/class-wpcm-post-types.php' );
		include_once( 'includes/class-wpcm-countries.php' );
		include_once( 'includes/class-wpcm-license-handler.php' );
	}

	/**
	 * Include required ajax files.
	 *
	 * @access public
	 * @return void
	 */
	public function ajax_includes() {

		include_once( 'includes/class-wpcm-ajax.php' );
	}


	/**
	 * Include required frontend files.
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_includes() {

		include_once( 'includes/wpcm-template-hooks.php' );
		include_once( 'includes/class-wpcm-template-loader.php' );
		include_once( 'includes/class-wpcm-frontend-scripts.php' );
		include_once( 'includes/class-wpcm-shortcodes.php' );
	}


	/**
	 * Function used to Init WPCM Template Functions - This makes them pluggable by plugins and themes.
	 *
	 * @access public
	 * @return void
	 */
	public function include_template_functions() {

		include_once( 'includes/wpcm-template-functions.php' );
	}

	public static function load_hooks() {
	 if( is_admin() && is_plugins_page() ) {
	     add_filter( 'admin_footer', 'wpcm_add_deactivation_feedback_modal' );
	 }
	}

	/**
	 * Init WooCommerce when WordPress Initialises.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {

		//Before init action
		do_action( 'before_wpcm_init' );

		// Set up localisation
		$this->load_plugin_textdomain();

		// Load class instances
		$this->countries = new WPCM_Countries();
		$this->sports = new WPCM_Sports();

		// Init action
		do_action( 'wpcm_init' );
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  1.0.3
	 * @access public
	 * @return void
	 */
	public function load_plugin_textdomain() {

		/*
		 * Due to the introduction of language packs through translate.wordpress.org, loading our textdomain is complex
		 *
		 * In v1.3.5, our textdomain changed from "wpclubmanager" to "wp-club-manager"
		 *
		 * To support existing translation files from before the change, we must look for translation files in several places and under several names.
		 *
		 * - wp-content/languages/plugins/wp-club-manager (introduced with language packs)
		 * - wp-content/languages/wpclubmanager/
		 * - wp-content/plugins/wp-club-manager/languages/
		 *
		 * In wp-content/languages/wpclubmanager/ we must look for "wp-club-manager-{lang}_{country}.mo"
		 * In wp-content/languages/wpclubmanager/ we must look for "wpclubmanager-{lang}_{country}.mo" as that was the old file naming convention
		 * In wp-content/languages/plugins/wp-club-manager/ we only need to look for "wp-club-manager-{lang}_{country}.mo" as that is the new structure
		 * In wp-content/plugins/wp-club-manager/languages/, we must look for both naming conventions. This is done by filtering "load_textdomain_mofile"
		 *
		 */

		add_filter( 'load_textdomain_mofile', array( $this, 'load_old_textdomain' ), 10, 2 );

		// Set filter for plugin's languages directory
		$wpcm_lang_dir  = dirname( plugin_basename( WPCM_PLUGIN_FILE ) ) . '/languages/';
		$wpcm_lang_dir  = apply_filters( 'wpclubmanager_languages_directory', $wpcm_lang_dir );

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale',  get_locale(), 'wp-club-manager' );
		$mofile        = sprintf( '%1$s-%2$s.mo', 'wp-club-manager', $locale );

		// Look for wp-content/languages/wpclubmanager/wp-club-manager-{lang}_{country}.mo
		$mofile_global1 = WP_LANG_DIR . '/wpclubmanager/wp-club-manager-' . $locale . '.mo';

		// Look for wp-content/languages/wpclubmanager/wpclubmanager-{lang}_{country}.mo
		$mofile_global2 = WP_LANG_DIR . '/wpclubmanager/wpclubmanager-' . $locale . '.mo';

		// Look in wp-content/languages/plugins/wp-club-manager
		$mofile_global3 = WP_LANG_DIR . '/plugins/wp-club-manager/' . $mofile;

		if ( file_exists( $mofile_global1 ) ) {

			load_textdomain( 'wp-club-manager', $mofile_global1 );

		} elseif ( file_exists( $mofile_global2 ) ) {

			load_textdomain( 'wp-club-manager', $mofile_global2 );

		} elseif ( file_exists( $mofile_global3 ) ) {

			load_textdomain( 'wp-club-manager', $mofile_global3 );

		} else {

			// Load the default language files
			load_plugin_textdomain( 'wp-club-manager', false, $wpcm_lang_dir );
		}
	}

	/**
	 * Load a .mo file for the old textdomain if one exists
	 *
	 * h/t: https://github.com/10up/grunt-wp-plugin/issues/21#issuecomment-62003284
	 */
	function load_old_textdomain( $mofile, $textdomain ) {

		if ( $textdomain === 'wp-club-manager' && ! file_exists( $mofile ) ) {
			$mofile = dirname( $mofile ) . DIRECTORY_SEPARATOR . str_replace( $textdomain, 'wpclubmanager', basename( $mofile ) );
		}

		return $mofile;
	}

	/**
	 * Add Compatibility for various bits.
	 *
	 * @access public
	 * @return void
	 */
	public function compatibility() {

		// Post thumbnail support
		if ( ! current_theme_supports( 'post-thumbnails', 'wpcm_player' ) ) {
			add_theme_support( 'post-thumbnails' );
			remove_post_type_support( 'post', 'thumbnail' );
			remove_post_type_support( 'page', 'thumbnail' );
		} else {
			add_post_type_support( 'wpcm_player', 'thumbnail' );
		}

		// Add image sizes
		$player_thumbnail = wpcm_get_image_size( 'player_thumbnail' );
		$player_single	= wpcm_get_image_size( 'player_single' );
		$staff_thumbnail = wpcm_get_image_size( 'staff_thumbnail' );
		$staff_single	= wpcm_get_image_size( 'staff_single' );

		add_image_size( 'crest-large',  100, 100, false );
		add_image_size( 'crest-medium',  50, 50, false );
		add_image_size( 'crest-small',  25, 25, false );
		add_image_size( 'player_thumbnail', $player_thumbnail['width'], $player_thumbnail['height'], $player_thumbnail['crop'] );
		add_image_size( 'player_single', $player_single['width'], $player_single['height'], $player_single['crop'] );
		add_image_size( 'staff_thumbnail', $staff_thumbnail['width'], $staff_thumbnail['height'], $staff_thumbnail['crop'] );
		add_image_size( 'staff_single', $staff_single['width'], $staff_single['height'], $staff_single['crop'] );
	}

	/** Helper functions ******************************************************/

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the template path.
	 *
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'WPCM_TEMPLATE_PATH', 'wpclubmanager/' );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

}

endif;

if ( ! function_exists( 'WPCM' ) ):
/**
 * Returns the main instance of WPCM to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WPClubManager
 */
function WPCM() {
	return WPClubManager::instance();
}
endif;

WPCM();