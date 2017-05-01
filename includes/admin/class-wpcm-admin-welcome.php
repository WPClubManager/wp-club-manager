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
 * @version     1.4.7
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
		$this->plugin             = 'wp-club-manager/wpclubmanager.php';

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
		if ( empty( $_GET['page'] ) ) {
			return;
		}

		$welcome_page_name  = __( 'About WP Club Manager', 'wp-club-manager' );
		$welcome_page_title = __( 'Welcome to WP Club Manager', 'wp-club-manager' );

		switch ( $_GET['page'] ) {
			case 'wpcm-about' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'wpcm-about', array( $this, 'about_screen' ) );
				add_action( 'admin_print_styles-'. $page, array( $this, 'admin_css' ) );
			break;
			case 'wpcm-getting-started' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'wpcm-getting-started', array( $this, 'getting_started_screen' ) );
				add_action( 'admin_print_styles-'. $page, array( $this, 'admin_css' ) );
			break;
			case 'wpcm-changelog' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'wpcm-changelog', array( $this, 'changelog_screen' ) );
				add_action( 'admin_print_styles-'. $page, array( $this, 'admin_css' ) );
			break;
			case 'wpcm-translators' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'wpcm-translators', array( $this, 'translators_screen' ) );
				add_action( 'admin_print_styles-'. $page, array( $this, 'admin_css' ) );
			break;
		}
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
		remove_submenu_page( 'index.php', 'wpcm-getting-started' );
		remove_submenu_page( 'index.php', 'wpcm-changelog' );
		remove_submenu_page( 'index.php', 'wpcm-translators' );

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
			.about-wrap .about-description {
				margin-bottom: 2em;
			}
			.about-wrap .feature-section {
				margin-top: 20px;
			}
			.about-wrap .changelog {
				margin-bottom: 40px;
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
			.about-integrations {
				background: #fff;
				margin: 20px 0;
				padding: 1px 20px 10px;
			}
			.wpcm-welcome-screenshots {
				float: right;
				margin: 10px!important;
				-webkit-box-shadow: 0px 0px 8px 0px rgba(0, 0, 0, 0.2);
				-moz-box-shadow:    0px 0px 8px 0px rgba(0, 0, 0, 0.2);
				box-shadow:         0px 0px 8px 0px rgba(0, 0, 0, 0.2);
			}
			#mc_embed_signup {
				clear:left;
				width: 400px;
			}
			#mc_embed_signup form {
				padding-left: 0;
			}
			#mc_embed_signup input.email {
				border: 1px solid #ddd;
				-webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
				box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
				background-color: #fff;
				color: #333;
				transition: .05s border-color ease-in-out;				
				margin: 1px 1px 10px;
				padding: 3px 5px;
				width: 100%;
			}
			#mc_embed_signup input.button {
				clear: both;
				background-color: #2ea2cc;
				border-color: #0074a2;
				-webkit-box-shadow: inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);
				box-shadow: inset 0 1px 0 rgba(120,200,230,.5),0 1px 0 rgba(0,0,0,.15);
				color: #fff;
				cursor: pointer;
				display: inline-block;
				font-size: 13px;
				font-weight: normal;
				line-height: 26px;
				margin: 0;
				padding: 0 10px 1px;
				text-align: center;
				text-decoration: none;
				border-width: 1px;
				border-style: solid;
				-webkit-appearance: none;
				-webkit-border-radius: 3px;
				border-radius: 3px;
				white-space: nowrap;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
				width: auto;
			}
			#mc_embed_signup input.button:hover {
				background: #1e8cbe;
				border-color: #0074a2;
				-webkit-box-shadow: inset 0 1px 0 rgba(120,200,230,.6);
				box-shadow: inset 0 1px 0 rgba(120,200,230,.6);
				color: #fff;
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
		<h1><?php printf( __( 'Welcome to WP Club Manager %s', 'wp-club-manager' ), $major_version ); ?></h1>

		<div class="about-text wpclubmanager-about-text">
			<?php
				if ( ! empty( $_GET['wpcm-installed'] ) )
					$message = __( 'Thanks, all done!', 'wp-club-manager' );
				elseif ( ! empty( $_GET['wpcm-updated'] ) )
					$message = __( 'Thank you for updating to the latest version of', 'wp-club-manager' );
				else
					$message = __( 'Thanks for installing', 'wp-club-manager' );

				printf( __( '%s WP Club Manager, the complete solution for managing your sports club website. Version %s is more powerful and stable than ever before. We hope you enjoy it.', 'wp-club-manager' ), $message, $major_version );
			?>
		</div>

		<div class="wpcm-badge"><?php printf( __( 'Version %s', 'wp-club-manager' ), WPCM()->version ); ?></div>

		<p class="wpclubmanager-actions">
			<a href="<?php echo admin_url('admin.php?page=wpcm-settings'); ?>" class="button button-primary"><?php _e( 'Settings', 'wp-club-manager' ); ?></a>
			<a class="docs button button-primary" href="<?php echo esc_url( apply_filters( 'wpclubmanager_docs_url', 'http://docs.wpclubmanager.com', 'wp-club-manager' ) ); ?>"><?php _e( 'Docs', 'wp-club-manager' ); ?></a>
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="https://wpclubmanager.com" data-text="An open-source (free) #sports club management plugin for #WordPress." data-via="WPClubManager" data-size="large" data-hashtags="WPCM">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</p>
	<?php
	}

	/**
	 * Output the about screen.
	 */
	public function tabs() { ?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $_GET['page'] == 'wpcm-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpcm-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'wp-club-manager' ); ?>
			</a>
			<a class="nav-tab <?php if ( $_GET['page'] == 'wpcm-getting-started' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpcm-getting-started' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Getting Started', 'wp-club-manager' ); ?>
			</a>
			<a class="nav-tab <?php if ( $_GET['page'] == 'wpcm-translators' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpcm-translators' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Translators', 'wp-club-manager' ); ?>
			</a>
			<a class="nav-tab <?php if ( $_GET['page'] == 'wpcm-changelog' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpcm-changelog' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Changelog', 'wp-club-manager' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() { ?>

		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<?php $this->tabs(); ?>

			<!--<div class="changelog point-releases"></div>-->

			<p class="about-description"><?php _e( 'Welcome to version 1.5 of WP Club Manager, bringing you more new features to provide even more detailed match info and more personalised player stats so your visitors can get even closer to their club! Check out the changelog for a full list of improvements.', 'wp-club-manager' ); ?></p>

			<div class="changelog">

				<h3><?php _e( 'What\'s New In This Version', 'wp-club-manager' ); ?></h3>

				<div class="wpcm-feature feature-section col three-col">

					<img src="<?php echo WPCM_URL . 'assets/images/welcome/15-stats.png'; ?>" class="wpcm-welcome-screenshots"/>

					<h4><?php _e( 'Control Each Players Stats', 'wp-club-manager' );?></h4>
					<p><?php _e( 'You can set which stats to display for each player, overwriting the global player stats display options. ', 'wp-club-manager' );?></p>

					<h4><?php _e( 'New Match Stats Display Options', 'wp-club-manager' );?></h4>
					<p><?php _e( 'Similarly, you can also overwrite the global player stats options for matches so you have more control over what to display on your match pages. ', 'wp-club-manager' ); ?></p>

					<div style="clear:both;width:100%;"></div>

					<img src="<?php echo WPCM_URL . 'assets/images/welcome/15-subs.png'; ?>" class="wpcm-welcome-screenshots"/>

					<h4><?php _e( 'Display Unused Substitutes', 'wp-club-manager' );?></h4>
					<p><?php _e( 'Now unused substitutes get the recognition they deserve! You can now select players that were unused in matches so they will be included in match pages. ', 'wp-club-manager' );?></p>

					<h4><?php _e( 'Better Overtime and Shootout Handling', 'wp-club-manager' );?></h4>
					<p><?php _e( 'This version introduces more detailed information when those cup games go to the wire by displaying extra-time status and the outcome of shootouts. ', 'wp-club-manager' );?></p>

				</div>

			</div>
			<div class="changelog">
				<h3><?php _e( 'Stay Up to Date', 'wp-club-manager' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Get Notified of New Extensions and Plugin Updates','wpclubmanager' );?></h4>
					<p><?php _e( 'WP Club Manager is getting better every day with new features, extensions and themes being created. Stay in touch with the latest developments, newest extensions and get special offers by subscribing to our newsletter.', 'wp-club-manager' );?></p>
					<!-- Begin MailChimp Signup Form -->
					<div id="mc_embed_signup">
					<form action="//wpclubmanager.us1.list-manage.com/subscribe/post?u=5af3e709ddae81bdc7aa50610&amp;id=909adae053" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						
						<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="Enter your email address" required>
					    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
					    <div style="position: absolute; left: -5000px;"><input type="text" name="b_5af3e709ddae81bdc7aa50610_909adae053" tabindex="-1" value=""></div>
					    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
					</form>
					</div>

					<!--End mc_embed_signup-->

				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpcm-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to WP Club Manager Settings', 'wp-club-manager' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Output the getting started screen
	 */
	public function getting_started_screen() {

		// Save settings
		if ( isset( $_POST['wpcm_sport'] ) && ! empty( $_POST['wpcm_sport'] ) && get_option( 'wpcm_sport', null ) != $_POST['wpcm_sport'] ):
			$sport = WPCM()->sports->$_POST['wpcm_sport'];
			WPCM_Admin_Settings::configure_sport( $sport );
			update_option( 'wpcm_sport', $_POST['wpcm_sport'] );
    	endif;
    	if ( isset( $_POST['wpcm_default_club'] ) && ! empty( $_POST['wpcm_default_club'] ) && get_option( 'wpcm_default_club', null ) != $_POST['wpcm_default_club'] ):
    		update_option( 'wpcm_default_club', $_POST['wpcm_default_club'] );
    	endif;
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e( 'WP Club Manager has been built with the focus on simplicity and ease of use so it won\'t be long before your club website is up and ready for action.', 'wp-club-manager' ); ?></p>

			<div id="message" class="updated wpclubmanager-message">
				<p><strong><?php _e( 'Your settings have been saved.', 'wp-club-manager' ); ?></strong></p>
			</div>

			<div class="changelog">
				<form method="post" id="mainform" action="" enctype="multipart/form-data">
					<h3><?php _e( 'Quick and Easy Setup', 'wp-club-manager' );?></h3>

					<div class="feature-section">

						<img src="<?php echo WPCM_URL . 'assets/images/welcome/12-wpcm-settings.png'; ?>" class="wpcm-welcome-screenshots"/>

						<h4><?php _e( '1. Choose Your Sport', 'wp-club-manager' );?></h4>
						<p><?php printf( __( 'Take your pick from 13 supported team sports. Each sport has preset player positions and statistics so it\'s a good idea to set this up before anything else. To select your default sport goto <a href="%s">General Settings</a> or use the form below.' , 'wp-club-manager' ), admin_url( 'admin.php?page=wpcm-settings' ) );?></p>
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
							<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'wp-club-manager' ); ?>" />
			        		<input type="hidden" name="subtab" id="last_tab" />
				        	<?php wp_nonce_field( 'wpclubmanager-settings' ); ?>
						</p>

						<h4><?php _e( '2. Create Your Club', 'wp-club-manager' ); ?></h4>
						<p><?php printf( __( 'The Clubs menu is where you create and edit clubs, including your own and create Competitions, Seasons, Teams and Venues. To create a club, click <em><a href="%s">Add New</a></em> and then fill out the club details. <strong>Your club will need to be set as the Default Club in <a href="%s">General Settings</a>.</strong> Alternatively, you can enter your default club in the box below.', 'wp-club-manager' ), admin_url( 'post-new.php?post_type=wpcm_club' ), admin_url( 'admin.php?page=wpcm-settings' ) ); ?></p>
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

							<p>
								<?php
								$settings = array( array(
									'id'        => 'wpcm_default_club',
									'default'   => '',
									'type'      => 'text',
								));
								WPCM_Admin_Settings::output_fields( $settings );
								?>
								<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'wp-club-manager' ); ?>" />
								<input type="hidden" name="subtab" id="last_tab" />
								<?php wp_nonce_field( 'wpclubmanager-settings' ); ?>
							</p>
						<?php
						} ?>

						<h4><?php _e( '3. Seasons, Competitions and Teams', 'wp-club-manager' );?></h4>
						<p><?php printf( __( 'Before adding your players and opposition clubs you should create <a href="%s">Competitions</a> and <a href="%s">Seasons</a>. If your club has more than one team, for example, firsts, reserves, youth team etc. you will need to create <a href="%s">Teams</a> for each one. When creating players or adding more clubs you will need to choose the competitions, seasons and teams which they compete in.', 'wp-club-manager' ), admin_url( 'edit-tags.php?taxonomy=wpcm_comp&post_type=wpcm_club' ), admin_url( 'edit-tags.php?taxonomy=wpcm_season&post_type=wpcm_club' ), admin_url( 'edit-tags.php?taxonomy=wpcm_team&post_type=wpcm_club' ) );?></p>
						<p><?php _e( '<strong>If you have already created your default club you will need to edit your club to add competitions, seasons and teams.</strong>', 'wp-club-manager' );?></p>

						<img src="<?php echo WPCM_URL . 'assets/images/welcome/12-create-players.png'; ?>" class="wpcm-welcome-screenshots"/>

						<h4><?php _e( '4. Add Your Players', 'wp-club-manager' ); ?></h4>
						<p><?php printf( __( 'Now you should be ready to add your clubs players. The Players Menu is where you can create and edit your clubs players and player positions. To create a player, simply click <em><a href="%s">Add New</a></em> and then fill out the player details, including competitions, seasons and teams.', 'wp-club-manager' ), admin_url( 'post-new.php?post_type=wpcm_player' ) );?></p>

						<h4><?php _e( '5. Play A Match', 'wp-club-manager' ); ?></h4>
						<p><?php printf( __( 'Now you should be ready to add your clubs players. The Players Menu is where you can create and edit your clubs players and player positions. To create a player, simply click <em><a href="%s">Add New</a></em> and then fill out the player details, including competitions, seasons and teams.', 'wp-club-manager' ), admin_url( 'post-new.php?post_type=wpcm_match' ) );?></p>
						<p><?php printf( __('Don\'t forget to run through the <a href="%s">WP Club Manager settings</a> where you can set visibility options for player stats, set league standing options and loads more.', 'wp-club-manager' ), admin_url( 'admin.php?page=wpcm-settings' ) );?></p>

					</div>
				</form>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Need Help?', 'wp-club-manager' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Plugin Documentation', 'wp-club-manager' );?></h4>
					<p><?php _e( 'If you need more help setting up WP Club Manager, using the shortcodes and widgets or customizing the plugin templates then visit our <a href="http://docs.wpclubmanager.com">documentation</a>.', 'wp-club-manager' );?></p>

					<h4><?php _e( 'Friendly Support','wpclubmanager' );?></h4>
					<p><?php _e( 'We do our best to provide the best support we can. If you encounter a problem or have a question, just ask for <a href="https://wpclubmanager.com/support">support</a>.', 'wp-club-manager' );?></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Stay Up to Date', 'wp-club-manager' );?></h3>

				<div class="feature-section">

					<h4><?php _e( 'Get Notified of New Extensions and Plugin Updates','wpclubmanager' );?></h4>
					<p><?php _e( 'WP Club Manager is getting better every day with new features, extensions and themes being created. Stay in touch with the latest developments, newest extensions and get special offers by subscribing to our newsletter.', 'wp-club-manager' );?></p>
					<!-- Begin MailChimp Signup Form -->
					<div id="mc_embed_signup">
					<form action="//wpclubmanager.us1.list-manage.com/subscribe/post?u=5af3e709ddae81bdc7aa50610&amp;id=909adae053" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
						
						<input type="email" value="" name="EMAIL" class="email" id="mce-EMAIL" placeholder="Enter your email address" required>
					    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
					    <div style="position: absolute; left: -5000px;"><input type="text" name="b_5af3e709ddae81bdc7aa50610_909adae053" tabindex="-1" value=""></div>
					    <div class="clear"><input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
					</form>
					</div>

					<!--End mc_embed_signup-->

				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpcm-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to WP Club Manager Settings', 'wp-club-manager' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Output the translators screen
	 */
	public function translators_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php _e( 'WP Club Manager has been translated by our generous translation teams, listed below. If you can help translate WPCM please sign up for a free account on <a href="https://www.transifex.com/">Transifex</a> and visit the <a href="https://www.transifex.com/projects/p/wp-club-manager/">WP Club Manager project</a>.', 'wp-club-manager' ); ?></p>

			<div class="changelog">

				<h3><?php _e( 'A Massive Thanks to the Following Translators', 'wp-club-manager' ); ?>:</h3>

				<div class="wpcm-feature feature-section col three-col">

					<?php
					$translator_handles = array( 'ClubPress', 'King3R', 'rychu_cmg', 'fvottendorf', 'Spirossmil', 'lucabarbetti', 'baldovi', 'thegreat', 'Vadim_C', 'hushiea', 'Pirolla', 'hatasu', 'piotr01', 'cherreman', 'Wuko', 'sigma66699969669', 'Kauge', 'SzJanos', 'Abdulelah', 'mwestrik', 'GhiMax', 'lasiton', 'Akinshin', 'retbys', 'sivasspor', 'cengizyilmaz', 'cemsacan', 'muhahmetkara', 'basse_99', 'dimych13', 'tonttuu', 'nunoluisazevedo', 'morbri63', 'vibevi', 'lfrodines', 'filippos.sdr', 'lsvk072', 't00kie', 'wolforg', 'BBruns', 'fengshengxie', 'Shvark', 'grzegorzj74', 'AnaDimova', 'tkausch', 'excllab', 'zemocz', 'JuicyPolyglot', 'shuuichiakai', 'mbobetic','Veaullus', 'mzooroofchi', 'as.giedrius', 'swissroach' );
					$translator_links = array();
					foreach ( $translator_handles as $handle ):
						$translator_links[] = '<a href="https://www.transifex.com/accounts/profile/' . $handle . '">' . $handle . '</a>';
					endforeach;
					?>
					<p class="wp-credits-list">
						<?php echo implode( ', ', $translator_links ); ?>
					</p>

				</div>

			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpcm-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to WP Club Manager Settings', 'wp-club-manager' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Contributors List
	 *
	 * @access public
	 * @return string $contributor_list HTML formatted list of contributors.
	 */
	public function changelog_screen() { ?>

		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<?php $this->tabs(); ?>

			<div class="changelog">
				<h3><?php _e( 'Full Changelog', 'wp-club-manager' );?></h3>

				<div class="feature-section">
					<?php echo $this->parse_readme(); ?>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wpcm-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to WP Club Manager Settings', 'wp-club-manager' ); ?></a>
			</div>
		</div>
	<?php
	}

	/**
	 * Parse the WPCM readme.txt file
	 *
	 * @since 2.0.3
	 * @return string $readme HTML formatted readme file
	 */
	public function parse_readme() {
		$file = file_exists( WPCM_PATH . 'readme.txt' ) ? WPCM_PATH . 'readme.txt' : null;

		if ( ! $file ) {
			$readme = '<p>' . __( 'No valid changlog was found.', 'wp-club-manager' ) . '</p>';
		} else {
			$readme = file_get_contents( $file );
			$readme = nl2br( esc_html( $readme ) );

			$readme = end( explode( '== Changelog ==', $readme ) );

			$readme = preg_replace( '/`(.*?)`/', '<code>\\1</code>', $readme );
			$readme = preg_replace( '/[\040]\*\*(.*?)\*\*/', ' <strong>\\1</strong>', $readme );
			$readme = preg_replace( '/[\040]\*(.*?)\*/', ' <em>\\1</em>', $readme );
			$readme = preg_replace( '/= (.*?) =/', '<h4>\\1</h4>', $readme );
			$readme = preg_replace( '/\[(.*?)\]\((.*?)\)/', '<a href="\\2">\\1</a>', $readme );
		}

		return $readme;
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
		if ( WPCM_Admin_Notices::has_notice( 'update' ) ) {
			return;
		}

		// Bail if activating from network, or bulk, or within an iFrame
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) ) {
			return;
		}

		// if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) || ( ! empty( $_GET['page'] ) && $_GET['page'] === 'wpcm-about' ) ) {
		// 	return;
		// }

		// wp_redirect( admin_url( 'index.php?page=wc-about' ) );
		// exit;

		$upgrade = get_option( 'wpcm_version_upgraded_from' );

		if( ! $upgrade ) { // First time install
			wp_safe_redirect( admin_url( 'index.php?page=wpcm-getting-started' ) ); exit;
		} else { // Update
			wp_safe_redirect( admin_url( 'index.php?page=wpcm-about' ) ); exit;
		}
	}
}

new WPCM_Admin_Welcome();
