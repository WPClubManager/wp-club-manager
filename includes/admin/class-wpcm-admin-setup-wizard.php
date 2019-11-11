<?php
/**
 * Setup Wizard Class
 *
 * Takes new users through some basic steps to setup their club.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin
 * @version     2.0.0
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WPCM_Admin_Setup_Wizard class.
 */
class WPCM_Admin_Setup_Wizard {

	/** @var string Current Step */
	private $step   = '';

	/** @var array Steps for the setup wizard */
	private $steps  = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		if ( apply_filters( 'wpclubmanager_enable_setup_wizard', true ) && current_user_can( 'manage_wpclubmanager' ) ) {
			add_action( 'admin_menu', array( $this, 'admin_menus' ) );
			add_action( 'admin_init', array( $this, 'setup_wizard' ) );
		}
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		add_dashboard_page( '', '', 'manage_options', 'wpcm-setup', '' );
	}

	/**
	 * Show the setup wizard.
	 */
	public function setup_wizard() {
		if ( empty( $_GET['page'] ) || 'wpcm-setup' !== $_GET['page'] ) {
			return;
		}
		$this->steps = array(
			'introduction' => array(
				'name'    =>  __( 'Intro', 'wp-club-manager' ),
				'view'    => array( $this, 'wpcm_setup_introduction' ),
				'handler' => ''
			),
			'general' => array(
				'name'    =>  __( 'General', 'wp-club-manager' ),
				'view'    => array( $this, 'wpcm_setup_general' ),
				'handler' => array( $this, 'wpcm_setup_general_save' )
			),
			'club' => array(
				'name'    =>  __( 'Club', 'wp-club-manager' ),
				'view'    => array( $this, 'wpcm_setup_club' ),
				'handler' => array( $this, 'wpcm_setup_club_save' )
			),
			'venue' => array(
				'name'    =>  __( 'Venue', 'wp-club-manager' ),
				'view'    => array( $this, 'wpcm_setup_venue' ),
				'handler' => array( $this, 'wpcm_setup_venue_save' )
			),
			'next_steps' => array(
				'name'    =>  __( 'Ready!', 'wp-club-manager' ),
				'view'    => array( $this, 'wpcm_setup_ready' ),
				'handler' => ''
			)
		);
		$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

		wp_register_script( 'google-maps', 'https://tinyurl.com/yalamujh', '' );

		wp_register_script( 'jquery-locationpicker', WPCM()->plugin_url() . '/assets/js/locationpicker.jquery.js', array( 'jquery', 'google-maps' ), '0.1.16', true );

		wp_enqueue_style( 'wpcm-setup-css', WPCM()->plugin_url() . '/assets/css/wpcm-setup.css', '' );

		wp_register_script( 'wpcm-setup-js', WPCM()->plugin_url() . '/assets/js/admin/wpcm-setup.min.js', array( 'jquery-locationpicker' ) );

		if ( ! empty( $_POST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
			call_user_func( $this->steps[ $this->step ]['handler'] );
		}

		ob_start();
		$this->setup_wizard_header();
		$this->setup_wizard_steps();
		$this->setup_wizard_content();
		$this->setup_wizard_footer();
		exit;
	}

	public function get_next_step_link() {
		$keys = array_keys( $this->steps );
		return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ] );
	}

	/**
	 * Setup Wizard Header.
	 */
	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width" />
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
			<title><?php _e( 'WP Club Manager &rsaquo; Setup Wizard', 'wp-club-manager' ); ?></title>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php wp_print_scripts( 'wpcm-setup-js' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body>
			<div class="ui middle aligned center aligned grid">
                <div class="column">
                    <h2 class="ui image header">
                        <img class="image" src="<?php echo WPCM()->plugin_url(); ?>/assets/images/wpcm-badge.png" alt="WP Club Manager" />
                        <div class="content"><?php _e( 'WP Club Manager', 'wpclubmanager' ); ?>
                            <div class="sub header"><?php _e( 'Setup Wizard', 'wpclubmanager' ); ?></div>
                        </div>
                    </h2>
		<?php
	}

	/**
	 * Setup Wizard Footer.
	 */
	public function setup_wizard_footer() {
		?>
					<?php if ( 'next_steps' === $this->step ) : ?>
						<a class="wpcm-return-to-dashboard" href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Return to the WordPress Dashboard', 'wp-club-manager' ); ?></a>
					<?php endif; ?>
					</div>
				</div>
			</body>
		</html>
		<?php
	}

	/**
	 * Output the steps.
	 */
	public function setup_wizard_steps() {
		$ouput_steps = $this->steps;
		?>
		<div class="ui five tiny steps">
			<?php foreach ( $ouput_steps as $step_key => $step ) : ?>
				<div class="<?php
					if ( $step_key === $this->step ) {
						echo 'active';
					} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
						echo 'completed';
					}
					?> step">
					<div class="content">
                        <div class="title">
							<?php echo esc_html( $step['name'] ); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
	}

	/**
	 * Output the content for the current step.
	 */
	public function setup_wizard_content() {
		echo '<div class="ui left aligned padded clearing segment wpcm-setup-content">';
		call_user_func( $this->steps[ $this->step ]['view'] );
		echo '</div>';
	}

	/**
	 * Introduction step.
	 */
	public function wpcm_setup_introduction() {
		?>
		<h2><?php _e( 'Welcome to WP Club Manager', 'wp-club-manager' ); ?></h2>
		<h4><?php _e( 'Thank you for choosing WP Club Manager to power your club or league website!', 'wp-club-manager' ); ?></h4>
		<p><?php _e( 'This quick setup wizard will help you configure the basic settings to get your club or league website up and running as quickly as possible. It’s completely optional and should only take a couple of minutes at most.', 'wp-club-manager' ); ?></p>
		<p><?php _e( 'No time right now? If you don’t want to go through the wizard now, you can skip and return to the WordPress dashboard. Come back anytime if you change your mind!', 'wp-club-manager' ); ?></p>

		<div class="ui hidden section divider"></div>

		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="ui large right floated primary button button-next"><?php _e( 'Let\'s Go!', 'wp-club-manager' ); ?></a>
		<a href="<?php echo esc_url( admin_url() ); ?>" class="ui large right floated button"><?php _e( 'Not right now', 'wp-club-manager' ); ?></a>
	
		<?php
	}

	/**
	 * Page setup.
	 */
	public function wpcm_setup_general() {
		$mode          = get_option( 'wpcm_mode', 'club' );
		$country       = get_option( 'wpcm_default_country', 'EN' );
		$sport         = get_option( 'wpcm_sport', 'soccer' );
		$sport_options = wpcm_get_sport_options();
		?>
		<h2><?php _e( 'General Setup', 'wp-club-manager' ); ?></h2>

		<p><?php _e( 'Your club site needs a few essential settings.', 'wp-club-manager' ); ?></p>

		<div class="ui hidden divider"></div>

		<form class="ui form" method="post">

			<div class="inline field">
				<label for="plugin_mode"><?php _e( 'Choose your plugin mode:', 'wp-club-manager' ); ?></label>
				<div class="plugin_mode">
					<div class="field">
						<div class="ui radio checkbox">
							<input type="radio" name="plugin_mode" value="club"<?php echo ( $mode == 'club' ? ' checked=""' : '' ); ?>> 
							<label><?php _e( 'Club mode', 'wp-club-manager' ); ?></label>
						</div>
					</div>
					<div class="field">
						<div class="ui radio checkbox">
							<input type="radio" name="plugin_mode" value="league"<?php echo ( $mode == 'league' ? ' checked=""' : '' ); ?>> 
							<label><?php _e( 'League mode', 'wp-club-manager' ); ?></label>
						</div>
					</div>
				</div>
			</div>

			<div class="inline field">
                <label for="club_location"><?php _e( 'Choose your default country:', 'wp-club-manager' ); ?></label>
                <select id="club_location" name="club_location" class="ui search dropdown">
					<?php WPCM()->countries->country_dropdown_options( $country ); ?>
				</select>
			</div>

			<div class="inline field">
                <label for="club_sport"><?php _e( 'Choose your default sport:', 'wp-club-manager' ); ?></label>
                <select id="club_sport" name="club_sport" class="ui search dropdown">
					<?php
					foreach( $sport_options as $key => $val ) {
						echo '<option value="' . $key . '" ' . ($key == $sport ? 'selected' : '' ) . '>' . $val . '</option>';
					}
					?>
				</select>
			</div>

			<div class="ui hidden section divider"></div>

			<input type="submit" class="ui large right floated primary button button-next" value="<?php esc_attr_e( 'Save &amp; continue', 'wp-club-manager' ); ?>" name="save_step" />
			<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="ui large right floated button"><?php _e( 'Skip this step', 'wp-club-manager' ); ?></a>
			<?php wp_nonce_field( 'wpcm-setup' ); ?>

		</form>
		<?php
	}

	/**
	 * Save Page Settings.
	 */
	public function wpcm_setup_general_save() {
		check_admin_referer( 'wpcm-setup' );

		if( isset( $_POST['plugin_mode'] ) ){
			$plugin_mode = $_POST['plugin_mode'];
			update_option( 'wpcm_mode', $plugin_mode );
		}
		if( isset( $_POST['club_location'] ) ){
			$club_location = sanitize_text_field( $_POST['club_location'] );
			update_option( 'wpcm_default_country', $club_location );
		}
		if( isset( $_POST['club_sport'] ) && ! empty( $_POST['club_sport'] ) && get_option( 'wpcm_sport' ) != $_POST['club_sport'] ) {
			$post = $_POST['club_sport'];
			$sport = WPCM()->sports->$post;
			WPCM_Admin_Settings::configure_sport( $sport );
			$club_sport = sanitize_text_field( $_POST['club_sport'] );
			update_option( 'wpcm_sport', $club_sport );
		}

		wpcm_flush_rewrite_rules();

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Locale settings.
	 */
	public function wpcm_setup_club() {

		$current = date( 'Y' );
		$next = date( 'y') + 1;
		$season_input = _x( 'eg.', 'example', 'wp-club-manager' ) . ' ' . $current . '/' . $next;
		?>
		<h2><?php _e( 'Club Setup', 'wp-club-manager' ); ?></h2>

		<p><?php _e( 'Add your club, competition and season so get started.', 'wp-club-manager' ); ?></p>

		<div class="ui hidden divider"></div>

		<form class="ui form" method="post">

			<?php
			if( is_club_mode() ) { ?>
				<div class="inline field">
					<label for="default_club"><?php _e( 'Enter your club name:', 'wp-club-manager' ); ?></label>
					<input type="text" id="default_club" name="default_club" placeholder="<?php esc_attr_e( 'eg. West Ham United', 'wp-club-manager' ); ?>" />
				</div>
			<?php
			} ?>

			<div class="inline field">
				<label for="setup_comp"><?php _e( 'Enter a competition:', 'wp-club-manager' ); ?></label>
				<input type="text" id="setup_comp" name="setup_comp" placeholder="<?php esc_attr_e( 'eg. Division One', 'wp-club-manager' ); ?>" />
			</div>

			<div class="inline field">
				<label for="setup_season"><?php _e( 'Enter a season:', 'wp-club-manager' ); ?></label>
				<input type="text" id="setup_season" name="setup_season" placeholder="<?php echo $season_input; ?>" />
			</div>

			<div class="inline field">
				<?php
				if( is_club_mode() ) { ?>
					<label for="setup_opponent"><?php _e( 'Add your next opponent:', 'wp-club-manager' ); ?></label>
				<?php
				}elseif( is_league_mode() ) { ?>
					<label for="setup_opponent"><?php _e( 'Add a club:', 'wp-club-manager' ); ?></label>
				<?php
				} ?>
				<input type="text" id="setup_opponent" name="setup_opponent" placeholder="<?php esc_attr_e( 'eg. Manchester United', 'wp-club-manager' ); ?>" />
			</div>

			<div class="ui hidden section divider"></div>

			<input type="submit" class="ui large right floated primary button button-next" value="<?php esc_attr_e( 'Save &amp; continue', 'wp-club-manager' ); ?>" name="save_step" />
			<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="ui large right floated button"><?php _e( 'Skip this step', 'wp-club-manager' ); ?></a>
			
			<?php wp_nonce_field( 'wpcm-setup' ); ?>

		</form>
		<?php
	}

	/**
	 * Save Locale Settings.
	 */
	public function wpcm_setup_club_save() {

		check_admin_referer( 'wpcm-setup' );

		if( isset( $_POST['setup_season'] ) ){
			$season = sanitize_text_field( $_POST['setup_season'] );
			$season_id = wp_insert_term( $season, 'wpcm_season' );
		}
		
		if( isset( $_POST['setup_comp'] ) ){
			$comp = sanitize_text_field( $_POST['setup_comp'] );
			$comp_id = wp_insert_term( $comp, 'wpcm_comp' );
		}

		if( is_club_mode() ) {
			if( isset( $_POST['default_club'] ) && ! empty( $_POST['default_club'] ) && get_option( 'wpcm_default_club', null ) != $_POST['default_club'] ){
				$title = sanitize_text_field( $_POST['default_club'] );
				$post = array(
					'post_title'  => $title,
					'post_type'   => 'wpcm_club',
					'post_status' => 'publish'
				);
				$wpcm_default_club = wp_insert_post( $post );
				update_option( 'wpcm_default_club', $wpcm_default_club );
				
				//wpcm_flush_rewrite_rules();

				$team = __( 'First Team', 'wp-club-manager' );
				$team_id = wp_insert_term( $team, 'wpcm_team' );

				if( isset( $_POST['setup_opponent'] ) ){
					$opponent = sanitize_text_field( $_POST['setup_opponent'] );
					$args = array(
						'post_title'  => $opponent,
						'post_type'   => 'wpcm_club',
						'post_status' => 'publish'
					);
					$opponent_id = wp_insert_post( $args );
				}

				if( isset( $_POST['setup_season'] ) && isset( $_POST['setup_comp'] ) ) {

					if( empty( $_POST['setup_opponent'] ) ){
						$opponent_id = null;
					}

					$title = $comp . ' -- ' . $season;
					$league_table = array(
						'post_title'  => $title,
						'post_type'   => 'wpcm_table',
						'post_status' => 'publish',
						'tax_input'	  => array(
							'wpcm_season' => $season_id,
							'wpcm_comp'	  => $comp_id,
							'wpcm_team'   => $team_id
						)
					);
					$table_id = wp_insert_post( $league_table );
					$clubs = array( $wpcm_default_club, $opponent_id );
					update_post_meta( $table_id, '_wpcm_table_clubs', serialize( $clubs ) );


					$title = $team . ' -- ' . $season;
					$roster = array(
						'post_title'  => $title,
						'post_type'   => 'wpcm_roster',
						'post_status' => 'publish',
						'tax_input'	  => array(
							'wpcm_season' => $season_id,
							'wpcm_team'   => $team_id
						)
					);
					wp_insert_post( $roster );
				}
			}
		}elseif( is_league_mode() ) {

			if( isset( $_POST['setup_season'] ) && isset( $_POST['setup_comp'] ) ) {
				
				if( empty( $_POST['setup_opponent'] ) ){
					$opponent_id = null;
				}

				$title = $comp . ' -- ' . $season;
				$league_table = array(
					'post_title'  => $title,
					'post_type'   => 'wpcm_table',
					'post_status' => 'publish',
					'tax_input'	  => array(
						'wpcm_season' => $season_id,
						'wpcm_comp'	  => $comp_id
					)
				);
				$table_id = wp_insert_post( $league_table );
				$clubs = array( $opponent_id );
				update_post_meta( $table_id, '_wpcm_table_clubs', serialize( $clubs ) );
			}
		}

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );

		exit;
	}

	/**
	 * Locale settings.
	 */
	public function wpcm_setup_venue() {
		?>
		<h2><?php _e( 'Venue Setup', 'wp-club-manager' ); ?></h2>

		<p><?php _e( 'Setup your clubs home venue.', 'wp-club-manager' ); ?></p>

		<div class="ui hidden divider"></div>

		<form class="ui form" method="post">
		
			<div class="inline field">
				<label><?php _e( 'Home venue name:', 'wp-club-manager' ); ?></label>
				<input type="text" id="setup_home" name="setup_home" placeholder="<?php esc_attr_e( 'eg. London Stadium', 'wp-club-manager' ); ?>" />
			</div>

			<div class="inline field">
				<label><?php _e( 'Home venue address:', 'wp-club-manager' ); ?></label>
				<input type="text" name="term_meta[wpcm_address]" class="wpcm-address" placeholder="<?php esc_attr_e( 'London Stadium, London E20 2ST', 'wp-club-manager' ); ?>" />
			</div>

			<input type="hidden" name="term_meta[wpcm_longitude]" class="wpcm-longitude" value="-0.016526945751934363" />
			<input type="hidden" name="term_meta[wpcm_latitude]" class="wpcm-latitude" value="51.5391098892326" />

			<div id="wpcm-location-picker"></div>

			<div class="ui hidden section divider"></div>

			<input type="submit" class="ui large right floated primary button button-next" value="<?php esc_attr_e( 'Save &amp; continue', 'wp-club-manager' ); ?>" name="save_step" />
			<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="ui large right floated button"><?php _e( 'Skip this step', 'wp-club-manager' ); ?></a>

			<?php wp_nonce_field( 'wpcm-setup' ); ?>

		</form>
		<?php
	}

	/**
	 * Save Locale Settings.
	 */
	public function wpcm_setup_venue_save() {

		check_admin_referer( 'wpcm-setup' );

		if( isset( $_POST['setup_home'] ) ){
			$home = sanitize_text_field( $_POST['setup_home'] );
			$post_id = get_option( 'wpcm_default_club' );
			$terms = wp_insert_term( $home, 'wpcm_venue' );
			wp_set_object_terms( $post_id, $terms['term_id'], 'wpcm_venue' );

			if( isset( $_POST['term_meta'] ) ){
				$t_id = $terms['term_id'];
				$term_meta = get_option( "taxonomy_term_$t_id" );
				$cat_keys = array_keys( $_POST['term_meta'] );
				foreach ( $cat_keys as $key ){
					if ( isset( $_POST['term_meta'][$key] ) ){
						$term_meta[$key] = $_POST['term_meta'][$key];
					}
				}
				update_option( "taxonomy_term_$t_id", $term_meta );
			}
		}

		wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
		exit;
	}

	/**
	 * Final step.
	 */
	public function wpcm_setup_ready() { ?>

		<h2><?php _e( 'Your website is almost ready to go!', 'wp-club-manager' ); ?></h2>
						
		<div class="ui hidden divider"></div>

		<div class="ui two column stackable left aligned grid">
			<div class="row">
				<div class="column">
					<h4 class="ui header"><?php _e( 'What Next?', 'wp-club-manager' ); ?></h4>
					<p>
						<?php _e( "The Setup Wizard has created your first season and setup some basic settings but before you can start adding matches we suggest that you go to the plugin settings and configure them to your needs.", 'wp-club-manager' ); ?>
					</p>
					<p>
						<a class="ui button" href="<?php echo esc_url( admin_url( 'admin.php?page=wpcm-settings') ); ?>"><?php _e( 'Go to plugin settings', 'wp-club-manager' ); ?></a>
					</p>
				</div>
				<div class="column">
					<h4 class="ui header"><?php _e( 'Need help?', 'wp-club-manager' ); ?></h4>
					<p>
						<?php _e( 'Our documentation provides all the information you need to setup and run your club or league website.', 'wp-club-manager' ); ?>
					</p>
					<p>
						<?php _e( 'If you need further help or need to report an issue please visit our support forum.', 'wp-club-manager' ); ?>
					</p>
					<div class="ui bulleted list">
						<a class="item" href="https://docs.wpclubmanager.com/" target="_blank"><?php _e( 'WP Club Manager documentation', 'wp-club-manager' ); ?></a>
						<a class="item" href="https://docs.wpclubmanager.com/" target="_blank"><?php _e( 'Getting started checklist', 'wp-club-manager' ); ?></a>
						<a class="item" href="https://wordpress.org/support/plugin/wp-club-manager" target="_blank"><?php _e( 'Community support forum', 'wp-club-manager' ); ?></a>
					</div>
				</div>
			</div>
		</div>

		<div class="ui hidden section divider"></div>

		<?php
	}
}

new WPCM_Admin_Setup_Wizard();
