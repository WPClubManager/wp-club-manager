<?php
/**
 * Welcome Page Class
 *
 * Shows a feature overview for the new version (major) and credits.
 *
 * Adapted from code in EDD (Copyright (c) 2012, Pippin Williamson) and WP.
 *
 * @author 		Clubpress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     1.1.5
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * WPCM_Admin_Welcome class.
 */
class WPCM_Admin_Welcome {

	private $plugin;

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->plugin             = 'wpclubmanager/wpclubmanager.php';

		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Add admin menus/screens
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menus() {

		$welcome_page_title = __( 'Welcome to WP Club Manager', 'wpclubmanager' );

		// About
		$about = add_dashboard_page( $welcome_page_title, $welcome_page_title, 'manage_options', 'wpcm-about', array( $this, 'about_screen' ) );

		add_action( 'admin_print_styles-'. $about, array( $this, 'admin_css' ) );
	}

	/**
	 * admin_css function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_css() {
		wp_enqueue_style( 'wpclubmanager-activation', plugins_url(  '/assets/css/activation.css', WPCM_PLUGIN_FILE ), array(), WPCM_VERSION );
	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'wpcm-about' );

		// Badge for welcome page
		$badge_url = WPCM()->plugin_url() . '/assets/images/welcome/wpcm-badge.png';
		?>
		<style type="text/css">
			/*<![CDATA[*/
			.wpcm-badge {
				position: relative;;
				background: #fff url(<?php echo $badge_url; ?>) no-repeat center top;
				text-rendering: optimizeLegibility;
				padding-top: 158px;
				height: 42px;
				width: 165px;
				font-size: 14px;
				text-align: center;
				color: #333;
				margin: 5px 0 0 0;
				-webkit-box-shadow: 0 1px 3px rgba(0,0,0,.2);
				box-shadow: 0 1px 3px rgba(0,0,0,.2);
			}
			.about-wrap .wpcm-badge {
				position: absolute;
				top: 0;
				right: 0;
			}
			.about-wrap .wpcm-feature {
				overflow: visible !important;
				*zoom:1;
			}
			.about-wrap .wpcm-feature:before,
			.about-wrap .wpcm-feature:after {
				content: " ";
				display: table;
			}
			.about-wrap .wpcm-feature:after {
				clear: both;
			}
			.about-wrap .feature-rest div {
				width: 50% !important;
				padding-right: 100px;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
				margin: 0 !important;
			}
			.about-wrap .feature-rest div.last-feature {
				padding-left: 100px;
				padding-right: 0;
			}
			.about-integrations {
				background: #fff;
				margin: 20px 0;
				padding: 1px 20px 10px;
			}
			/*]]>*/
		</style>
		<?php
	}

	/**
	 * Into text/links shown on all about pages.
	 *
	 * @access private
	 * @return void
	 */
	private function intro() {

		// Flush after upgrades
		if ( ! empty( $_GET['wpcm-updated'] ) || ! empty( $_GET['wpcm-installed'] ) )
			flush_rewrite_rules();

		// Drop minor version if 0
		$major_version = substr( WPCM()->version, 0, 3 );
		?>
		<h1><?php printf( __( 'Welcome to WP Club Manager', 'wpclubmanager' ), $major_version ); ?></h1>

		<div class="about-text wpclubmanager-about-text">
			<?php
				if ( ! empty( $_GET['wpcm-installed'] ) )
					$message = __( 'Thanks, all done!', 'wpclubmanager' );
				elseif ( ! empty( $_GET['wpcm-updated'] ) )
					$message = __( 'Thank you for updating to the latest version of', 'wpclubmanager' );
				else
					$message = __( 'Thanks for installing', 'wpclubmanager' );

				printf( __( '%s WP Club Manager, the complete solution for managing your clubs website. Version %s is more powerful and stable than ever before. We hope you enjoy it.', 'wpclubmanager' ), $message, $major_version );
			?>
		</div>

		<div class="wpcm-badge"><?php printf( __( 'Version %s', 'wpclubmanager' ), WPCM()->version ); ?></div>

		<p class="wpclubmanager-actions">
			<?php if ( false ): ?><a href="<?php echo admin_url( add_query_arg( array( 'page' => 'wpclubmanager' ), 'admin.php' ) ); ?>" class="button button-primary"><?php _e( 'Settings', 'wpclubmanager' ); ?></a><?php endif; ?>
		</p>

		<?php if ( false ): ?>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $_GET['page'] == 'wpcm-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpcm-about' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Get Started', 'wpclubmanager' ); ?>
			</a>
		</h2>
		<?php
		endif;
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		?>
		<div class="wrap about-wrap about-wpclubmanager-wrap">

			<?php $this->intro(); ?>

			<!--<div class="changelog point-releases"></div>-->

			<div class="changelog">
				<h3><?php _e( 'Get Started', 'wpclubmanager' ); ?></h3>
			
				<?php
				// Save settings
				if ( isset( $_POST['wpcm_sport'] ) && ! empty( $_POST['wpcm_sport'] ) && get_option( 'wpcm_sport', null ) != $_POST['wpcm_sport'] ):
					$sport = WPCM()->sports->$_POST['wpcm_sport'];
					WPCM_Admin_Settings::configure_sport( $sport );
					update_option( 'wpcm_sport', $_POST['wpcm_sport'] );
		    	endif;
		    	if ( isset( $_POST['wpcm_default_country'] ) ):
		    		update_option( 'wpcm_default_country', $_POST['wpcm_default_country'] );
		    		update_option( '_wpcm_needs_welcome', 1 );
		    	endif;
		    	if ( isset( $_POST['wpcm_default_club'] ) && ! empty( $_POST['wpcm_default_club'] ) && get_option( 'wpcm_default_club', null ) != $_POST['wpcm_default_club'] ):
		    		update_option( 'wpcm_default_club', $_POST['wpcm_default_club'] );
				?>

				<div id="message" class="updated wpclubmanager-message">
					<p><strong><?php _e( 'Your settings have been saved.', 'wpclubmanager' ); ?></strong></p>
				</div>

				<?php endif; ?>
				<div class="wpcm-feature feature-section col three-col">
					<div>
						<form method="post" id="mainform" action="" enctype="multipart/form-data">
							<div class="stuffbox">
								<h4><?php _e( 'Configure Plugin', 'wpclubmanager' ); ?></h4>
								<div class="inside">

									<h5><?php printf( __( 'Select %s', 'wpclubmanager' ), __( 'Sport', 'wpclubmanager' ) ); ?></h5>
									<p>
									<?php
										$sport_options = wpcm_get_sport_options();
										$class = 'chosen_select' . ( is_rtl() ? ' chosen-rtl' : '' );
										$settings = array( array(
											'id'        => 'wpcm_sport',
											'default'   => 'soccer',
											'type'      => 'select',
											'class' 	=> $class,
											'options'   => $sport_options,
										));
										WPCM_Admin_Settings::output_fields( $settings );
									?>
									</p>

									<?php
									if( isset( $_POST['wpcm_default_club'] ) ) {

										$title = $_POST['wpcm_default_club'];
										$post = array(
											'post_title'  => $title,
											'post_type'   => 'wpcm_club',
											'post_status' => 'publish'
										);
										$wpcm_default_club = wp_insert_post( $post );

										update_option('wpcm_default_club', $wpcm_default_club );

									} else {
										$title = '';
									}

									if( get_option('wpcm_default_club') == null ) { ?>

										<h5><?php _e( 'Create Default Club', 'wpclubmanager' ); ?></h5>

										<p>
											<?php
											$settings = array( array(
												'id'        => 'wpcm_default_club',
												'default'   => '',
												'type'      => 'text',
											));
											WPCM_Admin_Settings::output_fields( $settings );
											?>
										</p>
									<?php
									} ?>

									<h5><?php _e( 'Base Location', 'wpclubmanager' ); ?></h5>
									<?php
									$selected = (string) get_option( 'wpcm_default_country', 'EN' );
							    	?>
							    	<p>
										<select name="wpcm_default_country" data-placeholder="<?php _e( 'Choose a country&hellip;', 'wpclubmanager' ); ?>" title="Country" class="chosen_select<?php if ( is_rtl() ): ?> chosen-rtl<?php endif; ?>">
							        		<?php WPCM()->countries->country_dropdown_options( $selected ); ?>
							        	</select>
							        </p>
							        <p class="submit wpclubmanager-actions">
							        	<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'wpclubmanager' ); ?>" />
							        	<input type="hidden" name="subtab" id="last_tab" />
							        	<?php wp_nonce_field( 'wpclubmanager-settings' ); ?>
							        </p>
								</div>
							</div>
						</form>
					</div>
					<div>
						<div class="stuffbox">
							<h4><?php _e( 'Next Steps', 'wpclubmanager' ); ?></h4>
							<div class="inside">
								<ul class="wpclubmanager-steps">
									<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'taxonomy' => 'wpcm_comp', 'post_type' => 'wpcm_club' ), 'edit-tags.php' ) ) ); ?>" class="welcome-icon welcome-add-comps"><?php _e( 'Add Competitions', 'wpclubmanager' ); ?></a></li>
									<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'taxonomy' => 'wpcm_season', 'post_type' => 'wpcm_club' ), 'edit-tags.php' ) ) ); ?>" class="welcome-icon welcome-add-seasons"><?php _e( 'Add Seasons', 'wpclubmanager' ); ?></a></li>
									<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'post_type' => 'wpcm_player' ), 'post-new.php' ) ) ); ?>" class="welcome-icon welcome-add-player"><?php _e( 'Add Players', 'wpclubmanager' ); ?></a></li>
									<li><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'post_type' => 'wpcm_club' ), 'post-new.php' ) ) ); ?>" class="welcome-icon welcome-add-club"><?php _e( 'Add More Clubs', 'wpclubmanager' ); ?></a></li>
								</ul>
								<p>
									<?php _e('Please check out our <a href="http://wpclubmanager.com/docs/">plugin documentation</a> if you need help setting up or managing your club.', 'wpclubmanager'); ?>
								</p>
							</div>
						</div>
					</div>
					<div class="last-feature">
						<div class="stuffbox">
							<h4><?php _e( 'Translators', 'wpclubmanager' ); ?></h4>
							<div class="inside">
								<p><?php _e( 'WP Club Manager has been translated by our generous translation teams, listed below. If you can help translate WPCM please sign up for a free account on <a href="https://www.transifex.com/">Transifex</a> and visit the <a href="https://www.transifex.com/projects/p/wp-club-manager/">WP Club Manager project</a>.', 'wpclubmanager' ); ?></p>
								<?php
								$translator_handles = array( 'Clubpress', 'King3R', 'rychu_cmg', 'fvottendorf', 'Spirossmil', 'lucabarbetti', 'baldovi', 'thegreat', 'Vadim_C', 'hushiea', 'Pirolla', 'hatasu', 'piotr01' );
								$translator_links = array();
								foreach ( $translator_handles as $handle ):
									$translator_links[] = '<li><a href="https://www.transifex.com/accounts/profile/' . $handle . '">' . $handle . '</a></li>';
								endforeach;
								?>
								<ul class="wp-credits-list">
									<?php echo implode( '', $translator_links ); ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
				<div class="wpcm-feature">
					
						<div class="stuffbox wpcm-themes">
							<h4><span>NEW</span> Scoreline - Enhance your club website</h4>
							<div class="inside">
								<p>Feature packed and fully responsive, <a href="https://wpclubmanager.com/themes/scoreline/">Scoreline</a> is a new theme built for the WP Club Manager plugin. It's highly customizable and includes a new players gallery shortcode and sliding player stats widget.</p>
								<p>Stylish match pages and player/staff profiles will make your clubs website stand out from the crowd and by using the styling options it can be cusomized to your club colors easily.</p>
								<p><a href="https://wpclubmanager.com/themes/scoreline/">Check out the themes features &rarr;</a></p>
							</div>
						</div>
					
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpcm-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to WP Club Manager Settings', 'wpclubmanager' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Sends user to the welcome page on first activation
	 */
	public function welcome() {

		// Bail if no activation redirect transient is set
	    if ( ! get_transient( '_wpcm_activation_redirect' ) )
			return;

		// Delete the redirect transient
		delete_transient( '_wpcm_activation_redirect' );

		// Bail if we are waiting to install or update via the interface update/install links
		if ( get_option( '_wpcm_needs_update' ) == 1 || get_option( '_wpcm_needs_welcome' ) == 1 )
			return;

		// Bail if activating from network, or bulk, or within an iFrame
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) )
			return;

		if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'wpclubmanager.php' ) ) )
			return;

		wp_redirect( admin_url( 'index.php?page=wpcm-about' ) );
		exit;
	}
}

new WPCM_Admin_Welcome();
