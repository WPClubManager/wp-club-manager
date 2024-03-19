<?php
/**
 * WPClubManager WPCM_AJAX
 *
 * AJAX Event Handler
 *
 * @class       WPCM_AJAX
 * @version     2.2.0
 * @package     WPClubManager/Classes
 * @category    Class
 * @author      ClubPress
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WPCM_AJAX
 */
class WPCM_AJAX {

	/**
	 * Hook into ajax events
	 */
	public function __construct() {

		$ajax_events = array(
			'match_list_shortcode'      => false,
			'match_opponents_shortcode' => false,
			'player_list_shortcode'     => false,
			'player_gallery_shortcode'  => false,
			'staff_list_shortcode'      => false,
			'staff_gallery_shortcode'   => false,
			'league_table_shortcode'    => false,
			'map_venue_shortcode'       => false,
			'rated'                     => false,

			// 'send_feedback' => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_wpclubmanager_' . $ajax_event, array( $this, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_wpclubmanager_' . $ajax_event, array( $this, $ajax_event ) );
			}
		}
	}

	/**
	 * Match_list_shortcode_ajax function.
	 */
	public function match_opponents_shortcode() {
		?>

		<div id="wpcm-thickbox-match_opponents" class="wrap wpcm-thickbox-content">
			<p>
				<label for="title"><?php esc_html_e( 'Title', 'wp-club-manager' ); ?></label>
				<input type="text" name="title" class="regular-text"/>
			</p>
			<p>
				<label for="format"><?php esc_html_e( 'Status', 'wp-club-manager' ); ?></label>
				<select id="format" name="format">
					<option value=""><?php esc_html_e( 'All', 'wp-club-manager' ); ?></option>
					<option value="fixtures"><?php esc_html_e( 'Fixtures', 'wp-club-manager' ); ?></option>
					<option value="results"><?php esc_html_e( 'Results', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<?php
			if ( is_league_mode() ) {
				?>
				<p>
					<label for="id"><?php esc_html_e( 'Club', 'wp-club-manager' ); ?></label>
					<?php
					wpcm_dropdown_posts( array(
						'name'      => 'id',
						'id'        => 'id',
						'post_type' => 'wpcm_club',
						'limit'     => - 1,
						'class'     => 'chosen_select',
					) );
					?>
				</p>
				<?php
			}
			?>
			<p>
				<label for="limit"><?php esc_html_e( 'Limit', 'wp-club-manager' ); ?></label>
				<input type="number" id="limit" name="limit" value="" size="3"/>
			</p>
			<p>
				<label for="comp"><?php esc_html_e( 'Competition', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'          => 'wpcm_comp',
					'show_option_none'  => esc_html__( 'All', 'wp-club-manager' ),
					'option_none_value' => '',
					'hide_empty'        => false,
					'meta_key'          => 'tax_position',
					'meta_compare'      => 'NUMERIC',
					'orderby'           => 'meta_value_num',
					'name'              => 'comp',
					'value_field'       => 'term_id',
					'class'             => 'chosen_select',
				);
				wp_dropdown_categories( $args );
				?>
			</p>
			<p>
				<label for="season"><?php esc_html_e( 'Season', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'          => 'wpcm_season',
					'show_option_none'  => esc_html__( 'All', 'wp-club-manager' ),
					'option_none_value' => '',
					'hide_empty'        => false,
					'meta_key'          => 'tax_position',
					'meta_compare'      => 'NUMERIC',
					'orderby'           => 'meta_value_num',
					'name'              => 'season',
					'value_field'       => 'term_id',
					'class'             => 'chosen_select',
				);
				wp_dropdown_categories( $args );
				?>
			</p>
			<p>
				<label for="team"><?php esc_html_e( 'Team', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'          => 'wpcm_team',
					'show_option_none'  => esc_html__( 'All', 'wp-club-manager' ),
					'option_none_value' => '',
					'hide_empty'        => false,
					'meta_key'          => 'tax_position',
					'meta_compare'      => 'NUMERIC',
					'orderby'           => 'meta_value_num',
					'name'              => 'team',
					'value_field'       => 'term_id',
					'class'             => 'chosen_select',
				);
				wp_dropdown_categories( $args );
				?>
			</p>
			<p>
				<?php
				$months = array(
					'1'  => __( 'January', 'wp-club-manager' ),
					'2'  => __( 'February', 'wp-club-manager' ),
					'3'  => __( 'March', 'wp-club-manager' ),
					'4'  => __( 'April', 'wp-club-manager' ),
					'5'  => __( 'May', 'wp-club-manager' ),
					'6'  => __( 'June', 'wp-club-manager' ),
					'7'  => __( 'July', 'wp-club-manager' ),
					'8'  => __( 'August', 'wp-club-manager' ),
					'9'  => __( 'September', 'wp-club-manager' ),
					'10' => __( 'October', 'wp-club-manager' ),
					'11' => __( 'November', 'wp-club-manager' ),
					'12' => __( 'December', 'wp-club-manager' ),
				);
				?>
				<label for="date_range"><?php esc_html_e( 'Date Range', 'wp-club-manager' ); ?></label>
				<select id="date_range" name="date_range">
					<option value=""><?php esc_html_e( 'None', 'wp-club-manager' ); ?></option>
					<option value="last_week"><?php esc_html_e( 'Last 7 Days', 'wp-club-manager' ); ?></option>
					<option value="next_week"><?php esc_html_e( 'Next 7 Days', 'wp-club-manager' ); ?></option>
					<?php foreach ( $months as $key => $val ) { ?>
						<option value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $val ); ?></option>
					<?php } ?>
				</select>
			</p>
			<p>
				<label for="venue"><?php esc_html_e( 'Venue', 'wp-club-manager' ); ?></label>
				<select id="venue" name="venue">
					<option value=""><?php esc_html_e( 'All', 'wp-club-manager' ); ?></option>
					<option value="home"><?php esc_html_e( 'Home', 'wp-club-manager' ); ?></option>
					<option value="away"><?php esc_html_e( 'Away', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<p>
				<label for="order"><?php esc_html_e( 'Order', 'wp-club-manager' ); ?></label>
				<select id="order" name="order">
					<option value="ASC"><?php esc_html_e( 'Ascending', 'wp-club-manager' ); ?></option>
					<option value="DESC"><?php esc_html_e( 'Descending', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<p class="wpcm-column-options">
				<span><?php esc_html_e( 'Columns', 'wp-club-manager' ); ?></span>
				<?php
				$columns = array(
					'show_abbr'  => __( 'Abbreviation', 'wp-club-manager' ),
					'show_thumb' => __( 'Badge', 'wp-club-manager' ),
					'show_comp'  => __( 'Competition', 'wp-club-manager' ),
				);
				if ( is_club_mode() ) {
					$columns['show_team'] = __( 'Team', 'wp-club-manager' );
				}
				$columns['show_venue'] = __( 'Venue', 'wp-club-manager' );

				foreach ( $columns as $key => $value ) {
					?>
					<label for="<?php echo esc_attr( $key ); ?>" class="button">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>"
							   value="<?php echo esc_html( $key ); ?>" <?php echo( 'show_abbr' == $key ? '' : 'checked="checked"' ); ?>><?php echo esc_html( $value ); ?>
					</label>
					<?php
				}
				?>
			</p>
			<p>
				<label for="linktext"><?php esc_html_e( 'Link text', 'wp-club-manager' ); ?></label>
				<input type="text" id="linktext" name="linktext"/>
			</p>
			<p>
				<label for="linkpage"><?php esc_html_e( 'Link page', 'wp-club-manager' ); ?></label>
				<?php
				wp_dropdown_pages( array(
					'show_option_none' => esc_html__( 'None', 'wp-club-manager' ),
					'name'             => 'linkpage',
					'id'               => 'linkpage',
				) );
				?>
			</p>

			<?php do_action( 'wpclubmanager_ajax_shortcode_form', 'match_opponents' ); ?>
			<p class="submit">
				<input type="button" id="option-submit" class="button-primary"
					   value="<?php esc_html_e( 'Insert Match Opponents', 'wp-club-manager' ); ?>"
					   onclick="insertWPClubManager('match_opponents');"/>
				<a class="button-secondary" onclick="tb_remove();"
				   title="<?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?>"><?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?></a>
			</p>
		</div>

		<?php
		self::scripts();
		die();
	}

	/**
	 * Match_list_shortcode_ajax function.
	 */
	public function match_list_shortcode() {

		?>

		<div id="wpcm-thickbox-match_list" class="wrap wpcm-thickbox-content">
			<p>
				<label for="title"><?php esc_html_e( 'Title', 'wp-club-manager' ); ?></label>
				<input type="text" name="title" class="regular-text"/>
			</p>
			<p>
				<label for="format"><?php esc_html_e( 'Status', 'wp-club-manager' ); ?></label>
				<select id="format" name="format">
					<option value=""><?php esc_html_e( 'All', 'wp-club-manager' ); ?></option>
					<option value="fixtures"><?php esc_html_e( 'Fixtures Only', 'wp-club-manager' ); ?></option>
					<option value="results"><?php esc_html_e( 'Results Only', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<p>
				<label for="limit"><?php esc_html_e( 'Limit', 'wp-club-manager' ); ?></label>
				<input type="number" id="limit" name="limit" value="" size="3"/>
			</p>
			<p>
				<label for="comp"><?php esc_html_e( 'Competition', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'          => 'wpcm_comp',
					'show_option_none'  => esc_html__( 'All', 'wp-club-manager' ),
					'option_none_value' => '',
					'hide_empty'        => false,
					'meta_key'          => 'tax_position',
					'meta_compare'      => 'NUMERIC',
					'orderby'           => 'meta_value_num',
					'name'              => 'comp',
					'value_field'       => 'term_id',
					'class'             => 'chosen_select',
				);
				wp_dropdown_categories( $args );
				?>
			</p>
			<p>
				<label for="season"><?php esc_html_e( 'Season', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'          => 'wpcm_season',
					'show_option_none'  => esc_html__( 'All', 'wp-club-manager' ),
					'option_none_value' => '',
					'hide_empty'        => false,
					'meta_key'          => 'tax_position',
					'meta_compare'      => 'NUMERIC',
					'orderby'           => 'meta_value_num',
					'name'              => 'season',
					'value_field'       => 'term_id',
					'class'             => 'chosen_select',
				);
				wp_dropdown_categories( $args );
				?>
			</p>
			<?php
			if ( is_club_mode() ) {
				?>
				<p>
					<label for="team"><?php esc_html_e( 'Team', 'wp-club-manager' ); ?></label>
					<?php
					$args = array(
						'taxonomy'          => 'wpcm_team',
						'show_option_none'  => esc_html__( 'All', 'wp-club-manager' ),
						'option_none_value' => '',
						'hide_empty'        => false,
						'meta_key'          => 'tax_position',
						'meta_compare'      => 'NUMERIC',
						'orderby'           => 'meta_value_num',
						'name'              => 'team',
						'value_field'       => 'term_id',
						'class'             => 'chosen_select',
					);
					wp_dropdown_categories( $args );
					?>
				</p>
				<?php
			}
			?>
			<p>
				<?php
				$months = array(
					'1'  => __( 'January', 'wp-club-manager' ),
					'2'  => __( 'February', 'wp-club-manager' ),
					'3'  => __( 'March', 'wp-club-manager' ),
					'4'  => __( 'April', 'wp-club-manager' ),
					'5'  => __( 'May', 'wp-club-manager' ),
					'6'  => __( 'June', 'wp-club-manager' ),
					'7'  => __( 'July', 'wp-club-manager' ),
					'8'  => __( 'August', 'wp-club-manager' ),
					'9'  => __( 'September', 'wp-club-manager' ),
					'10' => __( 'October', 'wp-club-manager' ),
					'11' => __( 'November', 'wp-club-manager' ),
					'12' => __( 'December', 'wp-club-manager' ),
				);
				?>
				<label for="date_range"><?php esc_html_e( 'Date Range', 'wp-club-manager' ); ?></label>
				<select id="date_range" name="date_range">
					<option value=""><?php esc_html_e( 'None', 'wp-club-manager' ); ?></option>
					<option value="last_week"><?php esc_html_e( 'Last 7 Days', 'wp-club-manager' ); ?></option>
					<option value="next_week"><?php esc_html_e( 'Next 7 Days', 'wp-club-manager' ); ?></option>
					<?php foreach ( $months as $key => $val ) { ?>
						<option value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $val ); ?></option>
					<?php } ?>
				</select>
			</p>
			<?php
			if ( is_club_mode() ) {
				?>
				<p>
					<label for="venue"><?php esc_html_e( 'Venue', 'wp-club-manager' ); ?></label>
					<select id="venue" name="venue">
						<option value=""><?php esc_html_e( 'Home and Away', 'wp-club-manager' ); ?></option>
						<option value="home"><?php esc_html_e( 'Home Only', 'wp-club-manager' ); ?></option>
						<option value="away"><?php esc_html_e( 'Away Only', 'wp-club-manager' ); ?></option>
					</select>
				</p>
				<?php
			}
			?>
			<p>
				<label for="order"><?php esc_html_e( 'Order', 'wp-club-manager' ); ?></label>
				<select id="order" name="order">
					<option value="ASC"><?php esc_html_e( 'Ascending', 'wp-club-manager' ); ?></option>
					<option value="DESC"><?php esc_html_e( 'Descending', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<p class="wpcm-column-options">
				<span><?php esc_html_e( 'Columns', 'wp-club-manager' ); ?></span>
				<?php
				$columns = array(
					'show_abbr'  => __( 'Abbreviation', 'wp-club-manager' ),
					'show_thumb' => __( 'Badge', 'wp-club-manager' ),
					'show_comp'  => __( 'Competition', 'wp-club-manager' ),
				);
				if ( is_club_mode() ) {
					$columns['show_team'] = __( 'Team', 'wp-club-manager' );
				}
				$columns['show_venue'] = __( 'Venue', 'wp-club-manager' );
				foreach ( $columns as $key => $value ) {
					?>
					<label for="<?php echo esc_attr( $key ); ?>" class="button">
						<input type="checkbox" name="<?php echo esc_attr( $key ); ?>"
							   value="<?php echo esc_html( $key ); ?>" <?php echo( 'show_abbr' == $key ? '' : 'checked="checked"' ); ?>><?php echo esc_html( $value ); ?>
					</label>
					<?php
				}
				?>
			</p>
			<p>
				<label for="linktext"><?php esc_html_e( 'Link text', 'wp-club-manager' ); ?></label>
				<input type="text" id="linktext" name="linktext"/>
			</p>
			<p>
				<label for="linkpage"><?php esc_html_e( 'Link page', 'wp-club-manager' ); ?></label>
				<?php
				wp_dropdown_pages( array(
					'show_option_none' => esc_html__( 'None', 'wp-club-manager' ),
					'name'             => 'linkpage',
					'id'               => 'linkpage',
				) );
				?>
			</p>

			<?php do_action( 'wpclubmanager_ajax_shortcode_form', 'match_list' ); ?>
			<p class="submit">
				<input type="button" id="option-submit" class="button-primary"
					   value="<?php esc_html_e( 'Insert Match List', 'wp-club-manager' ); ?>"
					   onclick="insertWPClubManager('match_list');"/>
				<a class="button-secondary" onclick="tb_remove();"
				   title="<?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?>"><?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?></a>
			</p>
		</div>

		<?php
		self::scripts();
		die();
	}

	/**
	 * player_list_shortcode_ajax function.
	 */
	public function player_list_shortcode() {

		$player_stats_names = wpcm_get_player_stats_names();
		$stats              = wpcm_get_player_all_names();
		$defaults           = array( 'number', 'name', 'thumb', 'position' )
		?>

		<div id="wpcm-thickbox-player_list" class="wrap wpcm-thickbox-content">
			<p>
				<label for="title"><?php esc_html_e( 'Title', 'wp-club-manager' ); ?></label>
				<input type="text" name="title" class="regular-text"/>
			</p>
			<?php if ( is_club_mode() ) { ?>
				<p>
					<label for="id"><?php esc_html_e( 'Roster', 'wp-club-manager' ); ?></label>
					<?php
					wpcm_dropdown_posts( array(
						'name'      => 'id',
						'id'        => 'id',
						'post_type' => 'wpcm_roster',
						'limit'     => - 1,
						'class'     => 'chosen_select',
					) );
					?>
				</p>
				<?php
			} else {
				?>
				<p>
					<label for="id"><?php esc_html_e( 'Club', 'wp-club-manager' ); ?></label>
					<?php
					wpcm_dropdown_posts( array(
						'name'              => 'id',
						'id'                => 'id',
						'post_type'         => 'wpcm_club',
						'limit'             => - 1,
						'class'             => 'chosen_select',
						'show_option_none'  => __( 'All', 'wp-club-manager' ),
						'option_none_value' => '',
					) );
					?>
				</p>
				<?php
			}
			?>
			<p>
				<label for="limit"><?php esc_html_e( 'Limit', 'wp-club-manager' ); ?></label>
				<input type="number" id="limit" name="limit" value="" size="3"/>
			</p>

			<p>
				<label for="position"><?php esc_html_e( 'Position', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'          => 'wpcm_position',
					'show_option_none'  => __( 'All', 'wp-club-manager' ),
					'option_none_value' => '',
					'hide_empty'        => false,
					'meta_key'          => 'tax_position',
					'meta_compare'      => 'NUMERIC',
					'orderby'           => 'meta_value_num',
					'name'              => 'position',
					'value_field'       => 'term_id',
					'class'             => 'chosen_select',
				);
				wp_dropdown_categories( $args );
				?>
			</p>
			<p>
				<label><?php esc_html_e( 'Sort by', 'wp-club-manager' ); ?></label>
				<select id="orderby" name="orderby">
					<?php
					if ( get_option( 'wpcm_player_profile_show_number', 'yes' ) == 'yes' ) {
						?>
						<option value="number"><?php esc_html_e( 'Number', 'wp-club-manager' ); ?></option>
						<?php
					}
					?>
					<option value="menu_order"><?php esc_html_e( 'Page order', 'wp-club-manager' ); ?></option>
					<option value="name"><?php esc_html_e( 'Alphabetical', 'wp-club-manager' ); ?></option>
					<?php foreach ( $player_stats_names as $key => $val ) { ?>
						<option value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $val ); ?></option>
					<?php } ?>
				</select>
			</p>
			<p>
				<label for="order"><?php esc_html_e( 'Order', 'wp-club-manager' ); ?></label>
				<select id="order" name="order">
					<option value="ASC"><?php esc_html_e( 'Ascending', 'wp-club-manager' ); ?></option>
					<option value="DESC"><?php esc_html_e( 'Descending', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<p>
				<label><?php esc_html_e( 'Name Format', 'wp-club-manager' ); ?></label>
				<select id="name_format" name="name_format">
					<option value="full"><?php esc_html_e( 'First Last', 'wp-club-manager' ); ?></option>
					<option value="last"><?php esc_html_e( 'Last', 'wp-club-manager' ); ?></option>
					<option value="initial"><?php esc_html_e( 'F. Last', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<div class="wpcm-column-options">
				<span><?php esc_html_e( 'Columns', 'wp-club-manager' ); ?></span>
				<div class="wpcm-column-options-labels">
					<?php
					foreach ( $stats as $key => $value ) {
						?>
						<label for="<?php echo esc_attr( $key ); ?>" class="button">
							<input type="checkbox" id="stats-<?php echo esc_attr( $key ); ?>" name="columns[]"
								   value="<?php echo esc_html( $key ); ?>"<?php echo( in_array( $key, $defaults ) ? ' checked' : '' ); ?>><?php echo esc_html( $value ); ?>
						</label>
					<?php } ?>
				</div>
			</div>
			<p>
				<label for="linktext"><?php esc_html_e( 'Link text', 'wp-club-manager' ); ?></label>
				<input type="text" id="linktext" name="linktext"/>
			</p>
			<p>
				<label for="linkpage"><?php esc_html_e( 'Link page', 'wp-club-manager' ); ?></label>
				<?php
				wp_dropdown_pages( array(
					'show_option_none' => esc_html__( 'None', 'wp-club-manager' ),
					'name'             => 'linkpage',
					'id'               => 'linkpage',
				) );
				?>
			</p>

			<?php do_action( 'wpclubmanager_ajax_shortcode_form', 'player_list' ); ?>
			<p class="submit">
				<input type="button" id="option-submit" class="button-primary"
					   value="<?php esc_html_e( 'Insert Player List', 'wp-club-manager' ); ?>"
					   onclick="insertWPClubManager('player_list');"/>
				<a class="button-secondary" onclick="tb_remove();"
				   title="<?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?>"><?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?></a>
			</p>

		</div>

		<?php
		self::scripts();
		die();
	}

	/**
	 * Player_list_shortcode_ajax function.
	 */
	public function player_gallery_shortcode() {

		$player_stats_names = wpcm_get_player_stats_names();
		?>

		<div id="wpcm-thickbox-player_gallery" class="wrap wpcm-thickbox-content">
			<p>
				<label for="title"><?php esc_html_e( 'Title', 'wp-club-manager' ); ?></label>
				<input type="text" name="title" class="regular-text"/>
			</p>
			<p>
				<label for="id"><?php esc_html_e( 'Roster', 'wp-club-manager' ); ?></label>
				<?php
				wpcm_dropdown_posts( array(
					'name'      => 'id',
					'id'        => 'id',
					'post_type' => 'wpcm_roster',
					'limit'     => - 1,
					'class'     => 'chosen_select',
				) );
				?>
			</p>
			<p>
				<label for="limit"><?php esc_html_e( 'Limit', 'wp-club-manager' ); ?></label>
				<input type="number" id="limit" name="limit" value="" size="3"/>
			</p>

			<p>
				<label for="position"><?php esc_html_e( 'Position', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'          => 'wpcm_position',
					'show_option_none'  => esc_html__( 'All', 'wp-club-manager' ),
					'option_none_value' => '',
					'hide_empty'        => false,
					'meta_key'          => 'tax_position',
					'meta_compare'      => 'NUMERIC',
					'orderby'           => 'meta_value_num',
					'name'              => 'position',
					'value_field'       => 'term_id',
					'class'             => 'chosen_select',
				);
				wp_dropdown_categories( $args );
				?>
			</p>
			<p>
				<label><?php esc_html_e( 'Sort by', 'wp-club-manager' ); ?></label>
				<select id="orderby" name="orderby">
					<option value="name"><?php esc_html_e( 'Alphabetical', 'wp-club-manager' ); ?></option>
					<option value="number"><?php esc_html_e( 'Number', 'wp-club-manager' ); ?></option>
					<option value="menu_order"><?php esc_html_e( 'Page order', 'wp-club-manager' ); ?></option>
					<?php foreach ( $player_stats_names as $key => $val ) { ?>
						<option value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $val ); ?></option>
					<?php } ?>
				</select>
			</p>
			<p>
				<label for="order"><?php esc_html_e( 'Order', 'wp-club-manager' ); ?></label>
				<select id="order" name="order">
					<option value="ASC"><?php esc_html_e( 'Ascending', 'wp-club-manager' ); ?></option>
					<option value="DESC"><?php esc_html_e( 'Descending', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<?php
			$columns = array(
				'2' => __( '2', 'wp-club-manager' ),
				'3' => __( '3', 'wp-club-manager' ),
				'4' => __( '4', 'wp-club-manager' ),
				'5' => __( '5', 'wp-club-manager' ),
				'6' => __( '6', 'wp-club-manager' ),
			);
			?>
			<p>
				<label for="columns"><?php esc_html_e( 'Number of Columns', 'wp-club-manager' ); ?></label>
				<select id="columns" name="columns">
					<?php foreach ( $columns as $key => $val ) { ?>
						<option value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $val ); ?></option>
					<?php } ?>
				</select>
			</p>
			<p>
				<label><?php esc_html_e( 'Name Format', 'wp-club-manager' ); ?></label>
				<select id="name_format" name="name_format">
					<option value="full"><?php esc_html_e( 'First Last', 'wp-club-manager' ); ?></option>
					<option value="last"><?php esc_html_e( 'Last', 'wp-club-manager' ); ?></option>
					<option value="initial"><?php esc_html_e( 'F. Last', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<p>
				<label for="linktext"><?php esc_html_e( 'Link text', 'wp-club-manager' ); ?></label>
				<input type="text" id="linktext" name="linktext"/>
			</p>
			<p>
				<label for="linkpage"><?php esc_html_e( 'Link page', 'wp-club-manager' ); ?></label>
				<?php
				wp_dropdown_pages( array(
					'show_option_none' => esc_html__( 'None', 'wp-club-manager' ),
					'name'             => 'linkpage',
					'id'               => 'linkpage',
				) );
				?>
			</p>

			<?php do_action( 'wpclubmanager_ajax_shortcode_form', 'player_gallery' ); ?>
			<p class="submit">
				<input type="button" id="option-submit" class="button-primary"
					   value="<?php esc_html_e( 'Insert Player Gallery', 'wp-club-manager' ); ?>"
					   onclick="insertWPClubManager('player_gallery');"/>
				<a class="button-secondary" onclick="tb_remove();"
				   title="<?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?>"><?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?></a>
			</p>

		</div>

		<?php
		self::scripts();
		die();
	}

	/**
	 * staff_list_shortcode_ajax function.
	 */
	public function staff_list_shortcode() {

		$columns = array(
			'flag'   => __( 'Flag', 'wp-club-manager' ),
			'name'   => __( 'Name', 'wp-club-manager' ),
			'thumb'  => __( 'Image', 'wp-club-manager' ),
			'job'    => __( 'Job', 'wp-club-manager' ),
			'email'  => __( 'Email', 'wp-club-manager' ),
			'phone'  => __( 'Phone', 'wp-club-manager' ),
			'age'    => __( 'Age', 'wp-club-manager' ),
			'joined' => __( 'Joined', 'wp-club-manager' ),
		);

		$defaults = array( 'name', 'thumb', 'job' );
		?>

		<div id="wpcm-thickbox-staff_list" class="wrap wpcm-thickbox-content">
			<p>
				<label for="title"><?php esc_html_e( 'Title', 'wp-club-manager' ); ?></label>
				<input type="text" name="title" class="regular-text"/>
			</p>
			<?php if ( is_club_mode() ) { ?>
				<p>
					<label for="id"><?php esc_html_e( 'Roster', 'wp-club-manager' ); ?></label>
					<?php
					wpcm_dropdown_posts( array(
						'name'      => 'id',
						'id'        => 'id',
						'post_type' => 'wpcm_roster',
						'limit'     => - 1,
						'class'     => 'chosen_select',
					) );
					?>
				</p>
				<?php
			} else {
				?>
				<p>
					<label for="id"><?php esc_html_e( 'Club', 'wp-club-manager' ); ?></label>
					<?php
					wpcm_dropdown_posts( array(
						'name'      => 'id',
						'id'        => 'id',
						'post_type' => 'wpcm_club',
						'limit'     => - 1,
						'class'     => 'chosen_select',
					) );
					?>
				</p>
				<?php
			}
			?>
			<p>
				<label for="limit"><?php esc_html_e( 'Limit', 'wp-club-manager' ); ?></label>
				<input type="number" id="limit" name="limit" value="" size="3"/>
			</p>

			<p>
				<label for="job"><?php esc_html_e( 'Job', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'          => 'wpcm_jobs',
					'show_option_none'  => esc_html__( 'All', 'wp-club-manager' ),
					'option_none_value' => '',
					'hide_empty'        => false,
					'meta_key'          => 'tax_position',
					'meta_compare'      => 'NUMERIC',
					'orderby'           => 'meta_value_num',
					'name'              => 'job',
					'value_field'       => 'term_id',
					'class'             => 'chosen_select',
				);
				wp_dropdown_categories( $args );
				?>
			</p>
			<p>
				<label><?php esc_html_e( 'Sort by', 'wp-club-manager' ); ?></label>
				<select id="orderby" name="orderby">
					<option value="name"><?php esc_html_e( 'Alphabetical', 'wp-club-manager' ); ?></option>
					<option value="menu_order"><?php esc_html_e( 'Page order', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<p>
				<label for="order"><?php esc_html_e( 'Order', 'wp-club-manager' ); ?></label>
				<select id="order" name="order">
					<option value="ASC"><?php esc_html_e( 'Ascending', 'wp-club-manager' ); ?></option>
					<option value="DESC"><?php esc_html_e( 'Descending', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<p>
				<label><?php esc_html_e( 'Name Format', 'wp-club-manager' ); ?></label>
				<select id="name_format" name="name_format">
					<option value="full"><?php esc_html_e( 'First Last', 'wp-club-manager' ); ?></option>
					<option value="last"><?php esc_html_e( 'Last', 'wp-club-manager' ); ?></option>
					<option value="initial"><?php esc_html_e( 'F. Last', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<div class="wpcm-column-options">
				<span><?php esc_html_e( 'Columns', 'wp-club-manager' ); ?></span>
				<div class="wpcm-column-options-labels">
					<?php
					foreach ( $columns as $key => $value ) {
						?>
						<label for="<?php echo esc_attr( $key ); ?>" class="button">
							<input type="checkbox" id="stats-<?php echo esc_attr( $key ); ?>" name="columns[]"
								   value="<?php echo esc_html( $key ); ?>"<?php echo( in_array( $key, $defaults ) ? ' checked' : '' ); ?>><?php echo esc_html( $value ); ?>
						</label>
					<?php } ?>
				</div>
			</div>
			<p>
				<label for="linktext"><?php esc_html_e( 'Link text', 'wp-club-manager' ); ?></label>
				<input type="text" id="linktext" name="linktext"/>
			</p>
			<p>
				<label for="linkpage"><?php esc_html_e( 'Link page', 'wp-club-manager' ); ?></label>
				<?php
				wp_dropdown_pages( array(
					'show_option_none' => esc_html__( 'None', 'wp-club-manager' ),
					'name'             => 'linkpage',
					'id'               => 'linkpage',
				) );
				?>
			</p>

			<?php do_action( 'wpclubmanager_ajax_shortcode_form', 'staff_list' ); ?>
			<p class="submit">
				<input type="button" id="option-submit" class="button-primary"
					   value="<?php esc_html_e( 'Insert Staff List', 'wp-club-manager' ); ?>"
					   onclick="insertWPClubManager('staff_list');"/>
				<a class="button-secondary" onclick="tb_remove();"
				   title="<?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?>"><?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?></a>
			</p>

		</div>

		<?php
		self::scripts();
		die();
	}

	/**
	 * staff_list_shortcode_ajax function.
	 */
	public function staff_gallery_shortcode() {

		?>

		<div id="wpcm-thickbox-player_gallery" class="wrap wpcm-thickbox-content">
			<p>
				<label for="title"><?php esc_html_e( 'Title', 'wp-club-manager' ); ?></label>
				<input type="text" name="title" class="regular-text"/>
			</p>
			<p>
				<label for="id"><?php esc_html_e( 'Roster', 'wp-club-manager' ); ?></label>
				<?php
				wpcm_dropdown_posts( array(
					'name'      => 'id',
					'id'        => 'id',
					'post_type' => 'wpcm_roster',
					'limit'     => - 1,
					'class'     => 'chosen_select',
				) );
				?>
			</p>
			<p>
				<label for="limit"><?php esc_html_e( 'Limit', 'wp-club-manager' ); ?></label>
				<input type="number" id="limit" name="limit" value="" size="3"/>
			</p>

			<p>
				<label for="position"><?php esc_html_e( 'Job', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'          => 'wpcm_jobs',
					'show_option_none'  => __( 'All', 'wp-club-manager' ),
					'option_none_value' => '',
					'hide_empty'        => false,
					'meta_key'          => 'tax_position',
					'meta_compare'      => 'NUMERIC',
					'orderby'           => 'meta_value_num',
					'name'              => 'jobs',
					'value_field'       => 'term_id',
					'class'             => 'chosen_select',
				);
				wp_dropdown_categories( $args );
				?>
			</p>
			<p>
				<label><?php esc_html_e( 'Sort by', 'wp-club-manager' ); ?></label>
				<select id="orderby" name="orderby">
					<option value="name"><?php esc_html_e( 'Alphabetical', 'wp-club-manager' ); ?></option>
					<option value="menu_order"><?php esc_html_e( 'Page order', 'wp-club-manager' ); ?></option>
					<?php foreach ( $player_stats_names as $key => $val ) { ?>
						<option value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $val ); ?></option>
					<?php } ?>
				</select>
			</p>
			<p>
				<label for="order"><?php esc_html_e( 'Order', 'wp-club-manager' ); ?></label>
				<select id="order" name="order">
					<option value="ASC"><?php esc_html_e( 'Ascending', 'wp-club-manager' ); ?></option>
					<option value="DESC"><?php esc_html_e( 'Descending', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<?php
			$columns = array(
				'2' => __( '2', 'wp-club-manager' ),
				'3' => __( '3', 'wp-club-manager' ),
				'4' => __( '4', 'wp-club-manager' ),
				'5' => __( '5', 'wp-club-manager' ),
				'6' => __( '6', 'wp-club-manager' ),
			);
			?>
			<p>
				<label for="columns"><?php esc_html_e( 'Number of Columns', 'wp-club-manager' ); ?></label>
				<select id="columns" name="columns">
					<?php foreach ( $columns as $key => $val ) { ?>
						<option value="<?php echo esc_html( $key ); ?>"><?php echo esc_html( $val ); ?></option>
					<?php } ?>
				</select>
			</p>
			<p>
				<label><?php esc_html_e( 'Name Format', 'wp-club-manager' ); ?></label>
				<select id="name_format" name="name_format">
					<option value="full"><?php esc_html_e( 'First Last', 'wp-club-manager' ); ?></option>
					<option value="last"><?php esc_html_e( 'Last', 'wp-club-manager' ); ?></option>
					<option value="initial"><?php esc_html_e( 'F. Last', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<p>
				<label for="linktext"><?php esc_html_e( 'Link text', 'wp-club-manager' ); ?></label>
				<input type="text" id="linktext" name="linktext"/>
			</p>
			<p>
				<label for="linkpage"><?php esc_html_e( 'Link page', 'wp-club-manager' ); ?></label>
				<?php
				wp_dropdown_pages( array(
					'show_option_none' => esc_html__( 'None', 'wp-club-manager' ),
					'name'             => 'linkpage',
					'id'               => 'linkpage',
				) );
				?>
			</p>

			<?php do_action( 'wpclubmanager_ajax_shortcode_form', 'staff_gallery' ); ?>
			<p class="submit">
				<input type="button" id="option-submit" class="button-primary"
					   value="<?php esc_html_e( 'Insert Staff Gallery', 'wp-club-manager' ); ?>"
					   onclick="insertWPClubManager('staff_gallery');"/>
				<a class="button-secondary" onclick="tb_remove();"
				   title="<?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?>"><?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?></a>
			</p>

		</div>

		<?php
		self::scripts();
		die();
	}

	/**
	 * Standings_table_shortcode_ajax function.
	 */
	public function league_table_shortcode() {

		$labels  = wpcm_get_preset_labels( 'standings', 'label' );
		$columns = explode( ',', get_option( 'wpcm_standings_columns_display' ) );
		foreach ( $columns as $column ) {
			if ( array_key_exists( $column, $labels ) ) {
				$stats[ $column ] = $labels[ $column ];
			}
		}
		?>

		<div id="wpcm-thickbox-standings_table" class="wrap wpcm-thickbox-content">
			<p>
				<label for="title"><?php esc_html_e( 'Title', 'wp-club-manager' ); ?></label>
				<input type="text" name="title" class="regular-text"/>
			</p>
			<p>
				<label for="id"><?php esc_html_e( 'League Table', 'wp-club-manager' ); ?></label>
				<?php
				wpcm_dropdown_posts( array(
					'name'      => 'id',
					'id'        => 'id',
					'post_type' => 'wpcm_table',
					'limit'     => - 1,
					'class'     => 'chosen_select',
				) );
				?>
			</p>
			<p>
				<label for="limit"><?php esc_html_e( 'Limit', 'wp-club-manager' ); ?></label>
				<input type="number" id="limit" name="limit" value="" size="3" min="1"/>
			</p>
			<p>
				<label for="focus"><?php esc_html_e( 'Focus', 'wp-club-manager' ); ?></label>
				<select id="focus" name="focus" disabled>
					<?php if ( is_club_mode() ) { ?>
						<option value=""><?php esc_html_e( 'Default Club', 'wp-club-manager' ); ?></option>
						<?php
					}
					?>
					<option value="top"><?php esc_html_e( 'Top', 'wp-club-manager' ); ?></option>
					<option value="bottom"><?php esc_html_e( 'Bottom', 'wp-club-manager' ); ?></option>
				</select>
			</p>
			<p>
				<label for="abbr"><?php esc_html_e( 'Club Abbreviations', 'wp-club-manager' ); ?></label>
				<input type="checkbox" id="abbr" name="abbr" value="1" checked/>
			</p>
			<p>
				<label for="thumb"><?php esc_html_e( 'Show club badge', 'wp-club-manager' ); ?></label>
				<input type="checkbox" id="thumb" name="thumb" value="1" checked/>
			</p>
			<p>
				<label for="notes"><?php esc_html_e( 'Display Notes', 'wp-club-manager' ); ?></label>
				<input type="checkbox" id="notes" name="notes" value="0"/>
			</p>
			<p>
				<label for="link_club"><?php esc_html_e( 'Link to club', 'wp-club-manager' ); ?></label>
				<input type="checkbox" id="link_club" name="link_club" value="1" checked/>
			</p>
			<p class="wpcm-column-options">
				<span><?php esc_html_e( 'Columns', 'wp-club-manager' ); ?></span>
				<?php foreach ( $stats as $key => $value ) { ?>
					<label class="button">
						<input type="checkbox" name="columns[]" id="columns-<?php echo esc_attr( $key ); ?>"
							   value="<?php echo esc_html( $key ); ?>" checked="checked"><?php echo esc_html( $value ); ?>
					</label>
				<?php } ?>
			</p>
			<p>
				<label for="linktext"><?php esc_html_e( 'Link text', 'wp-club-manager' ); ?></label>
				<input type="text" id="linktext" name="linktext"/>
			</p>
			<p>
				<label for="linkpage"><?php esc_html_e( 'Link page', 'wp-club-manager' ); ?></label>
				<?php
				wp_dropdown_pages( array(
					'show_option_none' => esc_html__( 'None', 'wp-club-manager' ),
					'name'             => 'linkpage',
					'id'               => 'linkpage',
				) );
				?>
			</p>

			<?php do_action( 'wpclubmanager_ajax_shortcode_form', 'league_table' ); ?>
			<p class="submit">
				<input type="button" id="option-submit" class="button-primary"
					   value="<?php esc_html_e( 'Insert League Table', 'wp-club-manager' ); ?>"
					   onclick="insertWPClubManager('league_table');"/>
				<a class="button-secondary" onclick="tb_remove();"
				   title="<?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?>"><?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?></a>
			</p>
		</div>

		<?php
		self::scripts();
		die();
	}

	/**
	 * map_map_shortcode_ajax function.
	 */
	public function map_venue_shortcode() {

		?>

		<div id="wpcm-thickbox-map_map" class="wrap wpcm-thickbox-content">
			<p>
				<label for="title"><?php esc_html_e( 'Title', 'wp-club-manager' ); ?></label>
				<input type="text" name="title" class="regular-text"/>
			</p>
			<p>
				<label for="venue"><?php esc_html_e( 'Venue', 'wp-club-manager' ); ?></label>
				<?php
				$args = array(
					'taxonomy'    => 'wpcm_venue',
					'name'        => 'id',
					'value_field' => 'term_id',
					'hide_empty'  => 0,

				);
				wp_dropdown_categories( $args );
				?>
			</p>
			<p>
				<label for="width"><?php esc_html_e( 'Width', 'wp-club-manager' ); ?></label>
				<input type="text" name="width" value="" size="3"/>
			</p>
			<p>
				<label for="height"><?php esc_html_e( 'Height', 'wp-club-manager' ); ?></label>
				<input type="text" name="height" value="" size="3"/>
			</p>

			<?php do_action( 'wpclubmanager_ajax_shortcode_form', 'map_venue' ); ?>
			<p class="submit">
				<input type="button" id="option-submit" class="button-primary"
					   value="<?php esc_html_e( 'Insert Venue Map', 'wp-club-manager' ); ?>"
					   onclick="insertWPClubManager('map_venue');"/>
				<a class="button-secondary" onclick="tb_remove();"
				   title="<?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?>"><?php esc_html_e( 'Cancel', 'wp-club-manager' ); ?></a>
			</p>
		</div>

		<?php
		self::scripts();
		die();
	}

	/**
	 * Scripts
	 *
	 * @return void
	 */
	public function scripts() {

		?>

		<script type="text/javascript">

			//jQuery(document).ready(function(){
			jQuery( '#wpcm-thickbox-standings_table p input#limit' ).on( 'input', function() {
				if ( jQuery( this ).val().length ) {
					jQuery( '#focus' ).prop( 'disabled', false );
				} else {
					jQuery( '#focus' ).prop( 'disabled', true );
				}
			} );

			//});

			function insertWPClubManager( type ) {
				var $div = jQuery( '.wpcm-thickbox-content' );

				// Initialize shortcode arguments
				var args = {};

				// Extract args based on type
				if ( 'match_opponents' == type ) {
					args.title = $div.find( '[name=title]' ).val();
					args.format = $div.find( '[name=format]' ).val();
					args.id = $div.find( '[name=id]' ).val();
					args.limit = $div.find( '[name=limit]' ).val();
					args.comp = $div.find( '[name=comp]' ).val();
					args.season = $div.find( '[name=season]' ).val();
					args.team = $div.find( '[name=team]' ).val();
					args.date_range = $div.find( '[name=date_range]' ).val();
					args.venue = $div.find( '[name=venue]' ).val();
					args.order = $div.find( '[name=order]' ).val();
					args.show_abbr = $div.find( '[name=show_abbr]:checked' ).length;
					args.show_thumb = $div.find( '[name=show_thumb]:checked' ).length;
					//args.link_club = $div.find('[name=link_club]:checked').length;
					args.show_comp = $div.find( '[name=show_comp]:checked' ).length;
					args.show_team = $div.find( '[name=show_team]:checked' ).length;
					args.show_venue = $div.find( '[name=show_venue]:checked' ).length;
					args.linktext = $div.find( '[name=linktext]' ).val();
					args.linkpage = $div.find( '[name=linkpage]' ).val();
				} else if ( 'match_list' == type ) {
					args.title = $div.find( '[name=title]' ).val();
					args.format = $div.find( '[name=format]' ).val();
					args.limit = $div.find( '[name=limit]' ).val();
					args.comp = $div.find( '[name=comp]' ).val();
					args.season = $div.find( '[name=season]' ).val();
					args.team = $div.find( '[name=team]' ).val();
					args.date_range = $div.find( '[name=date_range]' ).val();
					args.venue = $div.find( '[name=venue]' ).val();
					args.order = $div.find( '[name=order]' ).val();
					args.show_abbr = $div.find( '[name=show_abbr]:checked' ).length;
					args.show_thumb = $div.find( '[name=show_thumb]:checked' ).length;
					//args.link_club = $div.find('[name=link_club]:checked').length;
					args.show_comp = $div.find( '[name=show_comp]:checked' ).length;
					args.show_team = $div.find( '[name=show_team]:checked' ).length;
					args.show_venue = $div.find( '[name=show_venue]:checked' ).length;
					args.linktext = $div.find( '[name=linktext]' ).val();
					args.linkpage = $div.find( '[name=linkpage]' ).val();
				} else if ( 'player_list' == type ) {
					args.title = $div.find( '[name=title]' ).val();
					args.id = $div.find( '[name=id]' ).val();
					args.limit = $div.find( '[name=limit]' ).val();
					args.position = $div.find( '[name=position]' ).val();
					args.orderby = $div.find( '[name=orderby]' ).val();
					args.order = $div.find( '[name=order]' ).val();
					args.linktext = $div.find( '[name=linktext]' ).val();
					args.linkpage = $div.find( '[name=linkpage]' ).val();
					args.name_format = $div.find( '[name=name_format]' ).val();
					args.columns = $div.find( '[name="columns[]"]:checked' ).map( function() {
						return this.value;
					} ).get().join( ',' );
				} else if ( 'player_gallery' == type ) {
					args.title = $div.find( '[name=title]' ).val();
					args.id = $div.find( '[name=id]' ).val();
					args.limit = $div.find( '[name=limit]' ).val();
					args.position = $div.find( '[name=position]' ).val();
					args.orderby = $div.find( '[name=orderby]' ).val();
					args.order = $div.find( '[name=order]' ).val();
					args.linktext = $div.find( '[name=linktext]' ).val();
					args.linkpage = $div.find( '[name=linkpage]' ).val();
					args.columns = $div.find( '[name=columns]' ).val();
					args.name_format = $div.find( '[name=name_format]' ).val();
				} else if ( 'staff_list' == type ) {
					args.title = $div.find( '[name=title]' ).val();
					args.id = $div.find( '[name=id]' ).val();
					args.limit = $div.find( '[name=limit]' ).val();
					args.job = $div.find( '[name=job]' ).val();
					args.orderby = $div.find( '[name=orderby]' ).val();
					args.order = $div.find( '[name=order]' ).val();
					args.linktext = $div.find( '[name=linktext]' ).val();
					args.linkpage = $div.find( '[name=linkpage]' ).val();
					args.columns = $div.find( '[name="columns[]"]:checked' ).map( function() {
						return this.value;
					} ).get().join( ',' );
				} else if ( 'staff_gallery' == type ) {
					args.title = $div.find( '[name=title]' ).val();
					args.id = $div.find( '[name=id]' ).val();
					args.limit = $div.find( '[name=limit]' ).val();
					args.jobs = $div.find( '[name=jobs]' ).val();
					args.orderby = $div.find( '[name=orderby]' ).val();
					args.order = $div.find( '[name=order]' ).val();
					args.linktext = $div.find( '[name=linktext]' ).val();
					args.linkpage = $div.find( '[name=linkpage]' ).val();
					args.columns = $div.find( '[name=columns]' ).val();
					args.name_format = $div.find( '[name=name_format]' ).val();
				} else if ( 'league_table' == type ) {
					args.title = $div.find( '[name=title]' ).val();
					args.id = $div.find( '[name=id]' ).val();
					args.limit = $div.find( '[name=limit]' ).val();
					args.focus = $div.find( '[name=focus]' ).val();
					args.abbr = $div.find( '[name=abbr]:checked' ).length;
					args.thumb = $div.find( '[name=thumb]:checked' ).length;
					args.link_club = $div.find( '[name=link_club]:checked' ).length;
					args.columns = $div.find( '[name="columns[]"]:checked' ).map( function() {
						return this.value;
					} ).get().join( ',' );
					args.notes = $div.find( '[name=notes]:checked' ).length;
					args.linktext = $div.find( '[name=linktext]' ).val();
					args.linkpage = $div.find( '[name=linkpage]' ).val();
				} else if ( 'map_venue' == type ) {
					args.title = $div.find( '[name=title]' ).val();
					args.id = $div.find( '[name=id]' ).val();
					args.width = $div.find( '[name=width]' ).val();
					args.height = $div.find( '[name=height]' ).val();
				}

				<?php do_action( 'wpclubmanager_ajax_scripts_before_shortcode' ); ?>

				// Generate the shortcode
				var shortcode = '[' + type;
				for ( var key in args ) {
					if ( args.hasOwnProperty( key ) ) {
						shortcode += ' ' + key + '="' + args[ key ] + '"';
					}
				}
				shortcode += ']';

				// Send the shortcode to the editor
				window.send_to_editor( shortcode );
			}
		</script>

		<?php
	}

	/**
	 * Triggered when clicking the rating footer.
	 */
	public static function rated() {

		if ( ! current_user_can( 'manage_wpclubmanager' ) ) {
			die( - 1 );
		}
		update_option( 'wpclubmanager_admin_footer_text_rated', 1 );

		exit();
	}
}

new WPCM_AJAX();
