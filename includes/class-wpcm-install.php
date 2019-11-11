<?php
/**
 * Installation related functions and actions.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Classes
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Install' ) ) :

class WPCM_Install {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {

		register_activation_hook( WPCM_PLUGIN_FILE, array( $this, 'install' ) );

		add_action( 'admin_init', array( $this, 'install_actions' ) );
		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
		add_action( 'in_plugin_update_message-wp-club-manager/wpclubmanager.php', array( $this, 'in_plugin_update_message' ) );
	}

	/**
	 * check_version function.
	 *
	 * @access public
	 * @return void
	 */
	public function check_version() {

		if ( ! defined( 'IFRAME_REQUEST' ) && version_compare( get_option( 'wpclubmanager_version' ), WPCM()->version, '<' ) ) {

			$this->install();

			do_action( 'wpclubmanager_updated' );
		}
	}

	/**
	 * Install actions such as installing pages when a button is clicked.
	 */
	public function install_actions() {
		
		if ( ! empty( $_GET['do_update_wpclubmanager'] ) ) {

			$this->updates();

			// Update complete
			//WPCM_Admin_Notices::remove_notice( 'update' );

		}

		if ( ! empty( $_GET['force_update_wpclubmanager'] ) ) {

			// What's new redirect
			//delete_transient( '_wpcm_activation_redirect' );
			//wp_redirect( admin_url( 'index.php?page=wpcm-about&wpcm-updated=true' ) );
			wp_safe_redirect( admin_url( 'admin.php?page=wpcm-settings' ) );
			exit;
		}
	}

	private function is_new_install() {
		return is_null( get_option( 'wpclubmanager_version', null ) );
	}

	/**
	 * Install WPCM
	 */
	public function install() {

		if ( ! defined( 'WPCM_INSTALLING' ) ) {
			define( 'WPCM_INSTALLING', true );
		}

		// Ensure needed classes are loaded
		include_once( 'admin/class-wpcm-admin-notices.php' );

		$this->remove_roles();
		$this->create_roles();

		// Register post types
		include_once( 'class-wpcm-post-types.php' );
		WPCM_Post_Types::register_post_types();
		WPCM_Post_Types::register_taxonomies();

		$this->create_options();

		if ( apply_filters( 'wpclubmanager_enable_setup_wizard', self::is_new_install() ) ) {
			WPCM_Admin_Notices::add_notice( 'install' );
			set_transient( '_wpcm_activation_redirect', 1, 30 );
		}

		// Queue upgrades
		$current_version = get_option( 'wpclubmanager_version', null );
		if ( $current_version ) {
			update_option( 'wpcm_version_upgraded_from', $current_version );
		}

		$this->updates( $current_version );
		
		// Update version
		//delete_option( 'wpclubmanager_version' );
		update_option( 'wpclubmanager_version', WPCM()->version );

		//add_option( 'wpcm_install_date', date( 'Y-m-d h:i:s' ) );
		//add_option( 'wpcm_rating', 'no' );

		// Flush rules after install
		flush_rewrite_rules();

		// Redirect to welcome screen
		// if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
		// 	set_transient( '_wpcm_activation_redirect', 1, 30 );
		// }

		// Trigger action
		do_action( 'wpclubmanager_installed' );

	}

	/**
	 * Handle updates
	 */
	public function updates( $version = null ) {
		
		if ( empty( $version ) ) return;

		if ( version_compare( $version, '1.1.0', '<' ) ) {
			include( 'updates/wpclubmanager-update-1.1.0.php' );
		}

		if ( version_compare( $version, '1.5.0', '<' ) ) {
			include_once( 'updates/wpclubmanager-update-1.5.0.php' );
		}

		if ( version_compare( $version, '1.5.4', '<' ) ) {
			include_once( 'updates/wpclubmanager-update-1.5.0.php' );
		}

		if ( version_compare( $version, '1.5.5', '<' ) ) {
			include_once( 'updates/wpclubmanager-update-1.5.5.php' );
		}

		if ( version_compare( $version, '2.0.0', '<' ) ) {
			include_once( 'updates/wpclubmanager-update-2.0.0.php' );
		}
	}

	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 *
	 * @access public
	 */
	function create_options() {
		// Include settings so that we can run through defaults
		include_once( 'admin/class-wpcm-admin-settings.php' );

		$settings = WPCM_Admin_Settings::get_settings_pages();

		foreach ( $settings as $section ) {
			foreach ( $section->get_settings() as $value ) {
				if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
					$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
					add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
				}
			}
		}

		if ( ! get_option( 'wpclubmanager_installed' ) ) {
			add_option( 'wpcm_mode', 'club' );
			// Configure default sport
			$post = 'soccer';
			$sport = WPCM()->sports->$post;
			WPCM_Admin_Settings::configure_sport( $sport );
			add_option( 'wpcm_sport', $post );
			add_option( 'wpcm_default_country', 'EN' );
			add_option( 'wpclubmanager_installed', 1 );
		}
	}

	/**
	 * Create roles and capabilities
	 */
	public function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		// Player role
		add_role( 'player', __( 'Player', 'wp-club-manager' ), array(
			'read' 						=> true
		) );

		add_role( 'staff', __( 'Staff', 'wp-club-manager' ), array(
            'level_9'                => true,
			'level_8'                => true,
			'level_7'                => true,
			'level_6'                => true,
			'level_5'                => true,
			'level_4'                => true,
			'level_3'                => true,
			'level_2'                => true,
			'level_1'                => true,
			'level_0'                => true,
			'read'                   => true,
			'read_private_pages'     => true,
			'read_private_posts'     => true,
			'edit_users'             => true,
			'edit_posts'             => true,
			'edit_pages'             => true,
			'edit_published_posts'   => true,
			'edit_published_pages'   => true,
			'edit_private_pages'     => true,
			'edit_private_posts'     => true,
			'edit_others_posts'      => true,
			'edit_others_pages'      => true,
			'publish_posts'          => true,
			'publish_pages'          => true,
			'delete_posts'           => true,
			'delete_pages'           => true,
			'delete_private_pages'   => true,
			'delete_private_posts'   => true,
			'delete_published_pages' => true,
			'delete_published_posts' => true,
			'delete_others_posts'    => true,
			'delete_others_pages'    => true,
			'manage_categories'      => true,
			'manage_links'           => true,
			'moderate_comments'      => true,
			'unfiltered_html'        => true,
			'upload_files'           => true,
			'export'                 => true,
			'import'                 => true,
			'list_users'             => true
		) );

		$capabilities = $this->get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'staff', $cap );
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Get capabilities for WP Club Manager - these are assigned to admin/shop manager during installation or reset
	 *
	 * @access public
	 * @return array
	 */
	public function get_core_capabilities() {

		$capabilities = array();

		$capabilities['core'] = array( 'manage_wpclubmanager' );

		$capability_types = array( 'wpcm_club', 'wpcm_player', 'wpcm_staff', 'wpcm_match', 'wpcm_table', 'wpcm_sponsor', 'wpcm_roster' );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type
				'edit_' . $capability_type,
				'read_' . $capability_type,
				'delete_' . $capability_type,
				'edit_' . $capability_type . 's',
				'edit_others_' . $capability_type . 's',
				'publish_' . $capability_type . 's',
				'read_private_' . $capability_type . 's',
				'delete_' . $capability_type . 's',
				'delete_private_' . $capability_type . 's',
				'delete_published_' . $capability_type . 's',
				'delete_others_' . $capability_type . 's',
				'edit_private_' . $capability_type . 's',
				'edit_published_' . $capability_type . 's',

				// Terms
				'manage_' . $capability_type . '_terms',
				'edit_' . $capability_type . '_terms',
				'delete_' . $capability_type . '_terms',
				'assign_' . $capability_type . '_terms'
			);
		}

		return $capabilities;
	}

	/**
	 * wpclubmanager_remove_roles function.
	 *
	 * @access public
	 * @return void
	 */
	public function remove_roles() {
		
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) ) {
			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles();
			}
		}

		if ( is_object( $wp_roles ) ) {

			$capabilities = $this->get_core_capabilities();

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->remove_cap( 'staff', $cap );
					$wp_roles->remove_cap( 'administrator', $cap );
				}
			}

			remove_role( 'player' );
			remove_role( 'staff' );
		}
	}

	/**
	 * Active plugins pre update option filter
	 *
	 * @param string $new_value
	 * @return string
	 */
	function pre_update_option_active_plugins( $new_value ) {
		$old_value = (array) get_option( 'active_plugins' );

		if ( $new_value !== $old_value && in_array( W3TC_FILE, (array) $new_value ) && in_array( W3TC_FILE, (array) $old_value ) ) {
			$this->_config->set( 'notes.plugins_updated', true );
			try {
				$this->_config->save();
			} catch( Exception $ex ) {}
		}

		return $new_value;
	}

	/**
	 * Show plugin changes. Code adapted from W3 Total Cache.
	 *
	 * @return void
	 */
	function in_plugin_update_message() {
		$response = wp_remote_get( 'https://plugins.svn.wordpress.org/wp-club-manager/trunk/readme.txt' );

		if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {

			// Output Upgrade Notice
			$matches = null;
			$regexp = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( WPCM_VERSION ) . '\s*=|$)~Uis';

			if ( preg_match( $regexp, $response['body'], $matches ) ) {
				$version = trim( $matches[1] );
				$notices = (array) preg_split('~[\r\n]+~', trim( $matches[2] ) );

				if ( version_compare( WPCM_VERSION, $version, '<' ) ) {

					echo '<div style="font-weight: normal; background: #cc99c2; color: #fff !important; border: 1px solid #b76ca9; padding: 9px; margin: 9px 0;">';

					foreach ( $notices as $index => $line ) {
						echo '<p style="margin: 0; font-size: 1.1em; color: #fff; text-shadow: 0 1px 1px #b574a8;">' . wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line ) ) . '</p>';
					}

					echo '</div> ';
				}
			}

			// Output Changelog
			$matches = null;
			$regexp = '~==\s*Changelog\s*==\s*=\s*[0-9.]+\s*-(.*)=(.*)(=\s*' . preg_quote( WPCM_VERSION ) . '\s*-(.*)=|$)~Uis';

			if ( preg_match( $regexp, $response['body'], $matches ) ) {
				$changelog = (array) preg_split( '~[\r\n]+~', trim( $matches[2] ) );

				_e( 'What\'s new:', 'wp-club-manager' ) . '<div style="font-weight: normal;">';

				$ul = false;

				foreach ( $changelog as $index => $line ) {
					if ( preg_match('~^\s*\*\s*~', $line ) ) {
						if ( ! $ul ) {
							echo '<ul style="list-style: disc inside; margin: 9px 0 9px 20px; overflow:hidden; zoom: 1;">';
							$ul = true;
						}
						
						$line = preg_replace( '~^\s*\*\s*~', '', htmlspecialchars( $line ) );
						
						echo '<li style="width: 50%; margin: 0; float: left; ' . ( $index % 2 == 0 ? 'clear: left;' : '' ) . '">' . esc_html( $line ) . '</li>';
					} else {

						$version = trim( current( explode( '-', str_replace( '=', '', $line ) ) ) );

						if ( version_compare( WPCM_VERSION, $version, '>=' ) ) {
							break;
						}

						if ( $ul ) {
							echo '</ul>';
							$ul = false;
						}

						echo '<p style="margin: 9px 0;">' . esc_html( htmlspecialchars( $line ) ) . '</p>';
					}
				}

				if ( $ul ) {
					echo '</ul>';
				}

				echo '</div>';
			}
		}
	}
}

endif;

return new WPCM_Install();