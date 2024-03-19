<?php
/**
 * WP Club Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	// Exit if accessed directly
	exit;
}

if ( ! class_exists( 'WPClubManager' ) ) :

	/**
	 * Main WPClubManager Class
	 *
	 * @class WPClubManager
	 */
	final class WP_Club_Manager {

		/**
		 * Plugin file path
		 *
		 * @var string
		 */
		protected $plugin_file;

		/**
		 * Plugin version
		 *
		 * @var string
		 */
		public $version = '2.2.11';

		/**
		 * @var WP_Club_Manager The single instance of the class
		 */
		protected static $instance = null;

		/**
		 * @var WPCM_Countries $countries
		 */
		public $countries = null;

		/**
		 * @var WPCM_Sports $sports
		 */
		public $sports = null;

		/**
		 * Main WP_Club_Manager Instance
		 *
		 * Ensures only one instance of WP_Club_Manager is loaded or can be loaded.
		 *
		 * @param string $plugin_file
		 * @param string $plugin_version
		 *
		 * @return WP_Club_Manager - Main instance
		 * @see   WPCM()
		 * @since 1.0.0
		 * @static
		 */
		public static function instance( $plugin_file, $plugin_version ) {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self( $plugin_file, $plugin_version );
				// Alias the old class name for backwards compatibility with addons
				class_alias( 'WP_Club_Manager', 'WPClubManager' );
			}
			return self::$instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 1.1.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-club-manager' ), '1.1.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 1.1.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-club-manager' ), '1.1.0' );
		}

		/**
		 * WP_Club_Manager Constructor.
		 *
		 * @param string $plugin_file
		 * @param string $plugin_version
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function __construct( $plugin_file, $plugin_version ) {
			$this->plugin_file = $plugin_file;
			$this->version     = $plugin_version;

			$this->constants();
			$this->includes();

			add_filter( 'plugin_action_links_' . plugin_basename( $this->plugin_file ), array( $this, 'plugin_action_links' ) );
			add_action( 'after_setup_theme', array( $this, 'compatibility' ) );
			add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
			add_action( 'after_setup_theme', array( $this, 'wpcm_template_debug_mode' ), 20 );
			add_action( 'init', array( $this, 'init' ), 0 );
			add_action( 'init', array( 'WPCM_Shortcodes', 'init' ) );
			add_action( 'tgmpa_register', array( $this, 'wp_club_manager_register_required_plugins' ) );

			do_action( 'wpclubmanager_loaded' );
		}

		/**
		 * Show action links on the plugin screen.
		 *
		 * @param   mixed $links Plugin Action links
		 * @return  array
		 */
		public function plugin_action_links( $links ) {
			$action_links = array(
				'settings' => '<a href="' . admin_url( 'admin.php?page=wpcm-settings' ) . '" title="' . esc_attr( __( 'View WP Club Manager Settings', 'wp-club-manager' ) ) . '">' . __( 'Settings', 'wp-club-manager' ) . '</a>',
				'docs'     => '<a href="' . esc_url( apply_filters( 'wpclubmanager_docs_url', 'https://wpclubmanager.com/documentation/', 'wp-club-manager' ) ) . '" title="' . esc_attr( __( 'View WP Club Manager Documentation', 'wp-club-manager' ) ) . '">' . __( 'Docs', 'wp-club-manager' ) . '</a>',
				'support'  => '<a href="' . esc_url( apply_filters( 'wpclubmanager_support_url', 'https://wpclubmanager.com/support/' ) ) . '" title="' . esc_attr( __( 'Support', 'wp-club-manager' ) ) . '">' . __( 'Support', 'wp-club-manager' ) . '</a>',
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
			define( 'WPCM_PLUGIN_FILE', $this->plugin_file );
			define( 'WPCM_VERSION', $this->version );

			if ( ! defined( 'WPCM_TEMPLATE_PATH' ) ) {
				define( 'WPCM_TEMPLATE_PATH', $this->template_path() );
			}
			if ( ! defined( 'WPCM_URL' ) ) {
				define( 'WPCM_URL', plugin_dir_url( $this->plugin_file ) );
			}
			if ( ! defined( 'WPCM_PATH' ) ) {
				define( 'WPCM_PATH', plugin_dir_path( $this->plugin_file ) );
			}
			if ( ! defined( 'WPCM_PLUGIN_BASENAME' ) ) {
				define( 'WPCM_PLUGIN_BASENAME', plugin_basename( $this->plugin_file ) );
			}
			if ( ! defined( 'WPCM_BASENAME' ) ) {
				define( 'WPCM_BASENAME', plugin_basename( $this->plugin_file ) );
			}
		}

		/**
		 * What type of request is this?
		 * string $type ajax, frontend or admin
		 *
		 * @param string $type
		 *
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'frontend':
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
			require_once WPCM_PATH . 'includes/class-wpcm-autoloader.php';
			require_once WPCM_PATH . 'includes/wpcm-core-functions.php';
			require_once WPCM_PATH . 'includes/wpcm-widget-functions.php';
			require_once WPCM_PATH . 'includes/class-wpcm-install.php';
			require_once WPCM_PATH . 'includes/class-wpcm-cache-helper.php';
			require_once WPCM_PATH . 'includes/class-wpcm-taxonomy-order.php';

			if ( $this->is_request( 'admin' ) ) {
				require_once WPCM_PATH . 'includes/admin/class-wpcm-admin.php';
			}

			if ( $this->is_request( 'ajax' ) ) {
				$this->ajax_includes();
			}

			if ( $this->is_request( 'frontend' ) ) {
				$this->frontend_includes();
			}

			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				require_once __DIR__ . '/class-wpcm-cli.php';

				\WP_CLI::add_command( 'clubmanager', WPCM_CLI::class );
			}

			require_once WPCM_PATH . 'includes/class-wpcm-post-types.php';
			require_once WPCM_PATH . 'includes/class-wpcm-countries.php';
			require_once WPCM_PATH . 'includes/class-wpcm-geocoder.php';
			require_once WPCM_PATH . 'includes/class-wpcm-license.php';
			require_once WPCM_PATH . 'includes/libraries/tgm-plugin-activation/class-tgm-plugin-activation.php';
		}

		/**
		 * Include required ajax files.
		 *
		 * @access public
		 * @return void
		 */
		public function ajax_includes() {
			require_once WPCM_PATH . 'includes/class-wpcm-ajax.php';
		}


		/**
		 * Include required frontend files.
		 *
		 * @access public
		 * @return void
		 */
		public function frontend_includes() {
			require_once WPCM_PATH . 'includes/wpcm-template-hooks.php';
			require_once WPCM_PATH . 'includes/class-wpcm-template-loader.php';
			require_once WPCM_PATH . 'includes/class-wpcm-frontend-scripts.php';
			require_once WPCM_PATH . 'includes/class-wpcm-shortcodes.php';

			require_once WPCM_PATH . 'includes/shortcodes/legacy/class-wpcm-shortcode-players.php';
			require_once WPCM_PATH . 'includes/shortcodes/legacy/class-wpcm-shortcode-matches.php';
			require_once WPCM_PATH . 'includes/shortcodes/legacy/class-wpcm-shortcode-staff.php';
			require_once WPCM_PATH . 'includes/shortcodes/legacy/class-wpcm-shortcode-standings.php';
		}


		/**
		 * Function used to Init WPCM Template Functions - This makes them pluggable by plugins and themes.
		 *
		 * @access public
		 * @return void
		 */
		public function include_template_functions() {
			require_once WPCM_PATH . 'includes/wpcm-template-functions.php';
		}

		/**
		 * Enables template debug mode.
		 *
		 * @access public
		 * @return void
		 */
		public function wpcm_template_debug_mode() {
			if ( ! defined( 'WPCM_TEMPLATE_DEBUG_MODE' ) ) {
				$status_options = get_option( 'wpclubmanager_status_options', array() );
				if ( ! empty( $status_options['template_debug_mode'] ) && current_user_can( 'manage_options' ) ) {
					define( 'WPCM_TEMPLATE_DEBUG_MODE', true );
				} else {
					define( 'WPCM_TEMPLATE_DEBUG_MODE', false );
				}
			}
		}

		/**
		 * Init WP_Club_Manager when WordPress Initialises.
		 *
		 * @access public
		 * @return void
		 */
		public function init() {

			// Before init action
			do_action( 'before_wpcm_init' );

			// Set up localisation
			$this->load_plugin_textdomain();

			// Load class instances
			$this->countries = new WPCM_Countries();
			$this->sports    = new WPCM_Sports();

			// Init action
			do_action( 'wpcm_init' );
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 *
		 * Locales found in:
		 *      - WP_LANG_DIR/wp-club-manager/wp-club-manager-LOCALE.mo
		 *      - WP_LANG_DIR/plugins/wp-club-manager-LOCALE.mo
		 */
		public function load_plugin_textdomain() {
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'wp-club-manager' );

			unload_textdomain( 'wp-club-manager' );
			load_textdomain( 'wp-club-manager', WP_LANG_DIR . '/wp-club-manager/wp-club-manager-' . $locale . '.mo' );
			load_plugin_textdomain( 'wp-club-manager', false, plugin_basename( dirname( WPCM_PLUGIN_FILE ) ) . '/languages' );
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
			$player_single    = wpcm_get_image_size( 'player_single' );
			$staff_thumbnail  = wpcm_get_image_size( 'staff_thumbnail' );
			$staff_single     = wpcm_get_image_size( 'staff_single' );
			$club_thumbnail   = wpcm_get_image_size( 'club_thumbnail' );
			$club_single      = wpcm_get_image_size( 'club_single' );

			add_image_size( 'crest-large', 100, 100, false );
			add_image_size( 'crest-medium', 50, 50, false );
			add_image_size( 'crest-small', 25, 25, false );
			add_image_size( 'player_thumbnail', $player_thumbnail['width'], $player_thumbnail['height'], $player_thumbnail['crop'] );
			add_image_size( 'player_single', $player_single['width'], $player_single['height'], $player_single['crop'] );
			add_image_size( 'staff_thumbnail', $staff_thumbnail['width'], $staff_thumbnail['height'], $staff_thumbnail['crop'] );
			add_image_size( 'staff_single', $staff_single['width'], $staff_single['height'], $staff_single['crop'] );
			add_image_size( 'club_thumbnail', $club_thumbnail['width'], $club_thumbnail['height'], $club_thumbnail['crop'] );
			add_image_size( 'club_single', $club_single['width'], $club_single['height'], $club_single['crop'] );
		}

		/**
		 * This file represents an example of the code that themes would use to register
		 * the required plugins.
		 *
		 * It is expected that theme authors would copy and paste this code into their
		 * functions.php file, and amend to suit.
		 *
		 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
		 *
		 * @package    TGM-Plugin-Activation
		 * @subpackage Example
		 * @version    2.6.1 for plugin Wp Club Manager
		 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
		 * @copyright  Copyright (c) 2011, Thomas Griffin
		 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
		 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
		 *
		 * @since 2.1.11
		 */
		public function wp_club_manager_register_required_plugins() {
			$plugins = array(

				array(
					'name'     => 'Classic Editor',
					'slug'     => 'classic-editor',
					'required' => true,
				),

			);

			$config = array(
				'id'           => 'wp-club-manager',
				'default_path' => '',
				'menu'         => 'tgmpa-install-plugins',
				'parent_slug'  => 'plugins.php',
				'capability'   => 'manage_options',
				'has_notices'  => true,
				'dismissable'  => true,
				'dismiss_msg'  => '',
				'is_automatic' => false,
				'message'      => '',
				'strings'      => array(
					/* translators: 1: plugin name(s). */
					'notice_can_install_required'    => _n_noop(
						'WP Club Manager requires the following plugin: %1$s.',
						'WP Club Manager requires the following plugins: %1$s.',
						'wp-club-manager'
					),
					/* translators: 1: plugin name(s). */
					'notice_can_install_recommended' => _n_noop(
						'WP Club Manager recommends the following plugin: %1$s.',
						'WP Club Manager recommends the following plugins: %1$s.',
						'wp-club-manager'
					),
					/* translators: 1: plugin name(s). */
					'notice_ask_to_update'           => _n_noop(
						'The following plugin needs to be updated to its latest version to ensure maximum compatibility with WP Club Manager: %1$s.',
						'The following plugins need to be updated to their latest version to ensure maximum compatibility with WP Club Manager: %1$s.',
						'wp-club-manager'
					),
					/* translators: 1: plugin name(s). */
					'plugin_needs_higher_version'    => __( 'Plugin not activated. A higher version of %s is needed for this plugin. Please update the plugin.', 'wp-club-manager' ),
				),
			);

			tgmpa( $plugins, $config );
		}

		/** Helper functions ******************************************************/

		/**
		 * Get the plugin url.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', $this->plugin_file ) );
		}

		/**
		 * Get the plugin path.
		 *
		 * @return string
		 */
		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( $this->plugin_file ) );
		}

		/**
		 * Get the template path.
		 *
		 * @return string
		 */
		public function template_path() {
			return apply_filters( 'wpcm_template_path', 'wpclubmanager/' );
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
