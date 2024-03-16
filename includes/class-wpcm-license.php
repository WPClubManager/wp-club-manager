<?php
/**
 * License handler for WP Club Manager
 *
 * This class should simplify the process of adding license information
 * to new WPCM extensions.
 *
 * @version 1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPCM_License' ) ) :

	/**
	 * WPCM_License Class
	 */
	class WPCM_License {

		/**
		 * @var string
		 */
		private $file;

		/**
		 * @var string
		 */
		private $license;

		/**
		 * @var string
		 */
		private $item_name;

		/**
		 * @var string
		 */
		private $item_shortname;

		/**
		 * @var string
		 */
		private $version;

		/**
		 * @var string
		 */
		private $author;

		/**
		 * @var string
		 */
		private $api_url = 'https://wpclubmanager.com';

		/**
		 * Class constructor
		 *
		 * @param string $_file
		 * @param string $_item_name
		 * @param string $_version
		 * @param string $_author
		 * @param string $_optname
		 * @param string $_api_url
		 */
		public function __construct( $_file, $_item_name, $_version, $_author, $_optname = null, $_api_url = null ) {

			$this->file           = $_file;
			$this->item_name      = $_item_name;
			$this->item_shortname = 'wpcm_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
			$this->version        = $_version;
			$this->license        = trim( $this->item_shortname . '_license_key', '' );
			$this->author         = $_author;
			$this->api_url        = is_null( $_api_url ) ? $this->api_url : $_api_url;

			/**
			 * Allows for backwards compatibility with old license options,
			 * i.e. if the plugins had license key fields previously, the license
			 * handler will automatically pick these up and use those in lieu of the
			 * user having to reactive their license.
			 */
			if ( ! empty( $_optname ) && isset( $_optname ) && empty( $this->license ) ) {
				$this->license = trim( $_optname );
			}

			// Setup hooks
			$this->includes();
			$this->hooks();
			// $this->auto_updater();
		}

		/**
		 * Include the updater class
		 *
		 * @access  private
		 * @return  void
		 */
		private function includes() {
			if ( ! class_exists( 'WPCM_Plugin_Updater' ) ) {
				require_once 'WPCM_Plugin_Updater.php';
			}
		}

		/**
		 * Setup hooks
		 *
		 * @access  private
		 * @return  void
		 */
		private function hooks() {
			// Register settings
			add_filter( 'wpclubmanager_license_settings', array( $this, 'settings' ), 1 );

			// Activate license key on settings save
			add_action( 'admin_init', array( $this, 'activate_license' ) );

			// Deactivate license key
			add_action( 'admin_init', array( $this, 'deactivate_license' ) );

			add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

			add_action( 'admin_notices', array( $this, 'notices' ) );
		}

		/**
		 * Auto updater
		 *
		 * @access  private
		 * @global  array $wpclubmanager_options
		 * @return  void
		 */
		public function auto_updater() {
			// Setup the updater
			$wpcm_updater = new WPCM_Plugin_Updater(
				$this->api_url,
				$this->file,
				array(
					'version'   => $this->version,
					'license'   => $this->license,
					'item_name' => $this->item_name,
					'author'    => $this->author,
				)
			);
		}


		/**
		 * Add license field to settings
		 *
		 * @access  public
		 * @param array $settings
		 * @return  array
		 */
		public function settings( $settings ) {
			$wpcm_license_settings = array(
				array(
					'id'      => $this->item_shortname . '_license_key',
					/* translators: 1: item name. */
					'name'    => sprintf( __( '%1$s License Key', 'wp-club-manager' ), $this->item_name ),
					'desc'    => '',
					'type'    => 'license_key',
					'options' => array( 'is_valid_license_option' => $this->item_shortname . '_license_active' ),
				),
			);

			return array_merge( $settings, $wpcm_license_settings );
		}


		/**
		 * Activate the license key
		 *
		 * @access  public
		 * @return  void
		 */
		public function activate_license() {

			if ( ! isset( $_POST[ $this->item_shortname . '_license_key' ] ) ) {
				return;
			}

			foreach ( $_POST as $key => $value ) { // phpcs:ignore
				if ( false !== strpos( $key, 'license_key_deactivate' ) ) {
					// Don't activate a key when deactivating a different key
					return;
				}
			}

			$nonce = filter_input( INPUT_REQUEST, $this->item_shortname . '_license_key-nonce', FILTER_UNSAFE_RAW );
			if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), $this->item_shortname . '_license_key-nonce' ) ) {

				wp_die( esc_html__( 'Nonce verification failed', 'wp-club-manager' ), esc_html__( 'Error', 'wp-club-manager' ), array( 'response' => 403 ) );

			}

			if ( 'valid' == get_option( $this->item_shortname . '_license_active' ) ) {
				return;
			}

			$license = filter_input( INPUT_POST, $this->item_shortname . '_license_key', FILTER_UNSAFE_RAW );

			if ( empty( $license ) ) {
				return;
			}

			// Data to send to the API
			$api_params = array(
				'edd_action' => 'activate_license',
				'license'    => sanitize_text_field( $license ),
				'item_name'  => urlencode( $this->item_name ),
			);

			// Call the API
			$response = wp_remote_post(
				$this->api_url,
				array(
					'timeout'   => 15,
					'sslverify' => false,
					'body'      => $api_params,
				)
			);

			// Make sure there are no errors
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Tell WordPress to look for updates
			set_site_transient( 'update_plugins', null );

			// Decode license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			update_option( $this->item_shortname . '_license_active', $license_data->license );

			if ( ! (bool) $license_data->success ) {
				set_transient( 'wpclubmanager_license_error', $license_data, 1000 );
			} else {
				delete_transient( 'wpclubmanager_license_error' );
			}
		}


		/**
		 * Deactivate the license key
		 *
		 * @access  public
		 * @return  void
		 */
		public function deactivate_license() {

			if ( ! isset( $_POST[ $this->item_shortname . '_license_key' ] ) ) {
				return;
			}

			$nonce = filter_input( INPUT_REQUEST, $this->item_shortname . '_license_key-nonce', FILTER_UNSAFE_RAW );
			if ( ! wp_verify_nonce( sanitize_text_field( $nonce ), $this->item_shortname . '_license_key-nonce' ) ) {

				wp_die( esc_html__( 'Nonce verification failed', 'wp-club-manager' ), esc_html__( 'Error', 'wp-club-manager' ), array( 'response' => 403 ) );

			}

			// Run on deactivate button press
			if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate' ] ) ) {

				// Data to send to the API
				$api_params = array(
					'edd_action' => 'deactivate_license',
					'license'    => $this->license,
					'item_name'  => urlencode( $this->item_name ),
				);

				// Call the API
				$response = wp_remote_post(
					$this->api_url,
					array(
						'timeout'   => 15,
						'sslverify' => false,
						'body'      => $api_params,
					)
				);

				// Make sure there are no errors
				if ( is_wp_error( $response ) ) {
					return;
				}

				// Decode the license data
				$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				delete_option( $this->item_shortname . '_license_active' );

				if ( ! (bool) $license_data->success ) {
					set_transient( 'wpclubmanager_license_error', $license_data, 1000 );
				} else {
					delete_transient( 'wpclubmanager_license_error' );
				}
			}
		}

		/**
		 * Admin notices for errors
		 *
		 * @access  public
		 * @return  void
		 */
		public function notices() {

			if ( ! isset( $_GET['page'] ) || 'wpcm-settings' !== $_GET['page'] ) {
				return;
			}

			if ( ! isset( $_GET['tab'] ) || 'licenses' !== $_GET['tab'] ) {
				return;
			}

			$license_error = get_transient( 'wpclubmanager_license_error' );

			if ( false === $license_error ) {
				return;
			}

			if ( ! empty( $license_error->error ) ) {

				switch ( $license_error->error ) {

					case 'item_name_mismatch':
						$message = __( 'This license does not belong to the product you have entered it for.', 'wp-club-manager' );
						break;

					case 'no_activations_left':
						$message = __( 'This license does not have any activations left', 'wp-club-manager' );
						break;

					case 'expired':
						$message = __( 'This license key is expired. Please renew it.', 'wp-club-manager' );
						break;

					default:
						/* translators: 1: error code. */
						$message = sprintf( __( 'There was a problem activating your license key, please try again or contact support. Error code: %s', 'wp-club-manager' ), $license_error->error );
						break;

				}
			}

			if ( ! empty( $message ) ) {

				echo '<div class="error">';
				echo '<p>' . esc_html( $message ) . '</p>';
				echo '</div>';

			}

			delete_transient( 'wpclubmanager_license_error' );
		}
	}

endif; // end class_exists check
