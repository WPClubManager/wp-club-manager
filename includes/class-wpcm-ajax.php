<?php
/**
 * WPClubManager WPCM_AJAX
 *
 * AJAX Event Handler
 *
 * @class 		WPCM_AJAX
 * @version		1.1.2
 * @package		WPClubManager/Classes
 * @category	Class
 * @author 		ClubPress
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_AJAX {

	/**
	 * Hook into ajax events
	 */
	public function __construct() {

		add_action( 'wp_ajax_wpcm_club_buttons', array( $this, 'wpcm_club_buttons_ajax' ) );
		add_action( 'wp_ajax_wpcm_map_shortcode', array( $this, 'map_shortcode_ajax' ) );
		add_action( 'wp_ajax_wpcm_matches_shortcode', array( $this, 'matches_shortcode_ajax' ) );
		add_action( 'wp_ajax_wpcm_players_shortcode', array( $this, 'players_shortcode_ajax' ) );
		add_action( 'wp_ajax_wpcm_staff_shortcode', array( $this, 'staff_shortcode_ajax' ) );
		add_action( 'wp_ajax_wpcm_standings_shortcode', array( $this, 'standings_shortcode_ajax' ) );
	}

	/**
	* wpcm_club_buttons_ajax function.
	*/
	public function wpcm_club_buttons_ajax() {

		if ( !wp_verify_nonce( $_REQUEST['nonce'], 'wpcm_club_buttons_ajax_nonce')) {
			exit();
		}
		 
		$defaults = array(
			'eid' => 'wpcm_club'
		);
		$args = array_merge( $defaults, $_GET );
		?>
		<p>
			<?php
			$clubs = get_posts( array (
				'post_type' => 'wpcm_club',
				'orderby' => 'title',
				'order' => 'asc',
				'numberposts' => -1,
				'posts_per_page' => -1
			) );
			?>
			<?php
			foreach( $clubs as $club ) {
				$class = 'wpcm-club-medium-button';
				$id = $club->ID;
				if ( has_post_thumbnail( $club->ID ) ) {
					$crest = wp_get_attachment_image_src( get_post_thumbnail_id( $club->ID ), 'crest-large', true );
				}
				else {
					$crest = array( '', '', '' );
				}
				$crest_url = $crest[0];
				$crest_width = $crest[1];
				$crest_height = $crest[2];
				$title = get_the_title( $club->ID );
				echo "<a class='$class' id='$id' title='$title' data-crest-url='$crest_url' data-crest-width='$crest_width' data-crest-height='$crest_height'>" . get_the_post_thumbnail( $club->ID, 'crest-medium', array( 'title' => $title ) ) . ' <span class="ellipsis">' . $club->post_title . '</span></a>' . PHP_EOL;
			}
			?>
		</p>
		<script type="text/javascript">
			(function($) {
				var eid = '<?php echo $args['eid']; ?>';
				var side = '<?php echo $args['side']; ?>';
				$('.wpcm-club-medium-button').click(function () {
					var id = $(this).attr('id');
					var title = $(this).attr('title');
					var crest_url = $(this).attr('data-crest-url');
					var crest_width = $(this).attr('data-crest-width');
					var crest_height = $(this).attr('data-crest-height');
					tb_remove();
					var img = '';
					if (crest_url)
						img = '<img width="' + crest_width + '" height="' + crest_height + '" src="' + crest_url + '" class="attachment-crest-large wp-post-image" alt="' + title + '" title="<?php printf( __( 'Select %s', 'wpclubmanager' ), __( 'Club', 'wpclubmanager' ) ); ?>">';
					$('#' + eid + '_button').html(
						img + '<span class="ellipsis">' + title + '</span>'
					);
					$('#' + eid).val(id);
					$('#wpcm_' + side + '_club').change();
				});
			})(jQuery);
		</script>

		<?php die();
	}

	/**
	* wpcm_map_shortcode_ajax function.
	*/
	public function map_shortcode_ajax() {
		$defaults = array(
			'width' => '584',
			'height' => '320',
			'address' => false,
			'lat' => false,
			'lng' => false,
			'zoom' => '13',
			'marker' => 1
		);
		$args = array_merge( $defaults, $_GET );
		?>
			<div id="wpcm_map-form">
				<table id="wpcm_map-table" class="form-table">
					<tr>
						<?php $field = 'address'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Address', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" class="widefat" /></td>
					</tr>
					<tr>
						<?php $field = 'lat'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Latitude', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" size="3" /></td>
					</tr>
					<tr>
						<?php $field = 'lng'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Longtitude', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" size="3" /></td>
					</tr>
					<tr>
						<?php $field = 'width'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Width', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" size="3" />px</td>
					</tr>
					<tr>
						<?php $field = 'height'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Height', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" size="3" />px</td>
					</tr>
					<tr>
						<?php $field = 'zoom'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Zoom', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" size="3" /></td>
					</tr>
					<tr>
						<?php $field = 'marker'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Display Marker', 'wpclubmanager' ); ?></label></th>
						<td><input type="checkbox" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" checked /></td>
					</tr>
				</table>
				<p class="submit">
					<input type="button" id="option-submit" class="button-primary" value="<?php printf( __( 'Insert %s', 'wpclubmanager' ), __( 'Map', 'wpclubmanager' ) ); ?>" name="submit" />
				</p>
			</div>
		<?php
		exit();
	}

	/**
	* wpcm_matches_shortcode_ajax function.
	*/
	public function matches_shortcode_ajax() {

		$defaults = array(
			'comp' => null,
			'season' => null,
			'team' => null,
			'venue' => null,
			'linktext' => __( 'View all results', 'wpclubmanager' ),
			'linkpage' => null,
			'title' => __( 'Fixtures & Results', 'wpclubmanager' ),
			'thumb' => 1,
		);
		$args = array_merge( $defaults, $_GET );
		?>
			<div id="wpcm_matches-form">
				<table id="wpcm_matches-table" class="form-table">
					<tr>
						<?php $field = 'title'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Title', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" class="widefat" /></td>
					</tr>
					<tr>
						<?php $field = 'comp'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Competition', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories(array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_comp',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							));
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'season'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Season', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories(array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_season',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							));
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'team'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Team', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories(array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_team',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							));
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'venue'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Venue', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories( array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_venue',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							) );
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'thumb'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Show Thumbnail', 'wpclubmanager' ); ?></label></th>
						<td><input type="checkbox" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" checked /></td>
					</tr>
					<tr>
						<?php $field = 'linktext'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Link text', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" /></td>
					</tr>
					<tr>
						<?php $field = 'linkpage'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Link page', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_pages( array(
								'show_option_none' => __( 'None' ),
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							) );
							?>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="button" id="option-submit" class="button-primary" value="<?php printf( __( 'Insert %s', 'wpclubmanager' ), __( 'Fixtures & Results', 'wpclubmanager' ) ); ?>" name="submit" />
				</p>
			</div>
		
		<?php die();
	}

	/**
	* wpcm_club_buttons_ajax function.
	*/
	public function players_shortcode_ajax() {
		$defaults = array(
			'limit' => 0,
			'season' => null,
			'team' => null,
			'position' => null,
			'orderby' => 'number',
			'order' => 'ASC',
			'linktext' => __( 'View all players', 'wpclubmanager' ),
			'linkpage' => null,
			'stats' => 'flag,number,name,position,age',
			'title' => __( 'Players', 'wpclubmanager' )
		);
		$args = array_merge( $defaults, $_GET );
		
		$wpcm_player_stats_labels = wpcm_get_sports_stats_labels();
		
		$player_stats_labels = array_merge( array( 'appearances' => __( 'Appearances', 'wpclubmanager' ) ), $wpcm_player_stats_labels );
		$stats_labels = array_merge(
			array(
				'thumb' => __( 'Thumbnail', 'wpclubmanager' ),
				'flag' => __( 'Flag', 'wpclubmanager' ),
				'number' => __( 'Number', 'wpclubmanager' ),
				'name' => __( 'Name', 'wpclubmanager' ),
				'position' => __( 'Position', 'wpclubmanager' ),
				'age' => __( 'Age', 'wpclubmanager' ),
				'team' => __( 'Team', 'wpclubmanager' ),
				'season' => __( 'Season', 'wpclubmanager' ),
				'dob' => __( 'Date of Birth', 'wpclubmanager' ),
				'height' => __( 'Height', 'wpclubmanager' ),
				'weight' => __( 'Weight', 'wpclubmanager' ),
				'hometown' => __( 'Hometown', 'wpclubmanager' ),
				'joined' => __( 'Joined', 'wpclubmanager' )
			),
			$player_stats_labels
		);
		$stats = explode( ',', $args['stats'] );
		?>
			<div id="wpcm_players-form">
				<table id="wpcm_players-table" class="form-table">
					<tr>
						<?php $field = 'title'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Title', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" class="widefat" /></td>
					</tr>
					<tr>
						<?php $field = 'limit'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Limit', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" size="3" /> (<?php _e( '0 = no limit', 'wpclubmanager' ); ?>)</td>
					</tr>
					<tr>
						<?php $field = 'season'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Season', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories(array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_season',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							));
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'team'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Team', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories( array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_team',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							) );
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'position'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Position', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories( array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_position',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							) );
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'orderby'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Order by', 'wpclubmanager' ); ?></label></th>
						<td>
							<select id="option-<?php echo $field; ?>" name="<?php echo $field; ?>">
								<option id="number" value="number"<?php if ( $args[$field] == 'number' ) echo ' selected'; ?>><?php _e( 'Number', 'wpclubmanager' ); ?></option>
								<option id="menu_order" value="menu_order"<?php if ( $args[$field] == 'menu_order' ) echo ' selected'; ?>><?php _e( 'Page order' ); ?></option>
								<option id="name" value="name"<?php if ( $args[$field] == 'name' ) echo ' selected'; ?>><?php _e( 'Alphabetical' ); ?></option>
								<?php foreach ( $player_stats_labels as $key => $val ) { ?>
									<option id="<?php echo $key; ?>" value="<?php echo $key; ?>"<?php if ( $args[$field] == $key ) echo ' selected'; ?>><?php echo $val; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<?php $field = 'order'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Order', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							$wpcm_order_options = array(
								'ASC' => __( 'Lowest to highest', 'wpclubmanager' ),
								'DESC' => __( 'Highest to lowest', 'wpclubmanager' )
							);
							?>
							<select id="option-<?php echo $field; ?>" name="<?php echo $field; ?>">
								<?php foreach ( $wpcm_order_options as $key => $val ) { ?>
									<option id="<?php echo $key; ?>" value="<?php echo $key; ?>"<?php if ( $args[$field] == $key ) echo ' selected'; ?>><?php echo $val; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<?php $field = 'linktext'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Link text', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" /></td>
					</tr>
					<tr>
						<?php $field = 'linkpage'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Link page', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_pages( array(
								'show_option_none' => __( 'None' ),
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							) );
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'stats'; ?>
						<th><label><?php _e( 'Display options', 'wpclubmanager' ); ?></label></th>
						<td>
							<table>
								<tr>
									<?php
									$count = 0;
									foreach ( $stats_labels as $key => $value ) {
										$count++;
										if ( $count > 3 ) {
											$count = 1;
											echo '</tr><tr>';
										}
									?>
										<td>
											<label class="selectit" for="option-<?php echo $field; ?>-<?php echo $key; ?>">
												<input type="checkbox" id="option-<?php echo $field; ?>-<?php echo $key; ?>" name="<?php echo $field; ?>[]" value="<?php echo $key; ?>" <?php checked( in_array( $key, $stats ) ); ?> />
												<?php echo $value; ?>
											</label>
										</td>
									<?php } ?>
								</tr
							></table>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="button" id="option-submit" class="button-primary" value="<?php printf( __( 'Insert %s', 'wpclubmanager' ), __( 'Players', 'wpclubmanager' ) ); ?>" name="submit" />
				</p>
			</div>
		
		<?php die();
	}

	/**
	* wpcm_staff_shortcode_ajax function.
	*/
	public function staff_shortcode_ajax() {

		$defaults = array(
			'limit' => 0,
			'season' => null,
			'team' => null,
			'jobs' => null,
			'orderby' => 'name',
			'order' => 'ASC',
			'team' => null,
			'linktext' => __( 'View all staff', 'wpclubmanager' ),
			'linkpage' => null,
			'stats' => 'flag,number,name,job,age',
			'title' => __( 'Staff', 'wpclubmanager' ),
		);
		$args = array_merge( $defaults, $_GET );

		$stats_labels = array(
			'thumb' => __( 'Thumbnail', 'wpclubmanager' ),
			'flag' => __( 'Flag', 'wpclubmanager' ),
			'name' => __( 'Name', 'wpclubmanager' ),
			'job' => __( 'Job', 'wpclubmanager' ),
			'age' => __( 'Age', 'wpclubmanager' ),
			'team' => __( 'Team', 'wpclubmanager' ),
			'season' => __( 'Season', 'wpclubmanager' ),
			'joined' => __( 'Joined', 'wpclubmanager' )
		);
		$stats = explode( ',', $args['stats'] );
		?>
			<div id="wpcm_staff-form">
				<table id="wpcm_staff-table" class="form-table">
					<tr>
						<?php $field = 'title'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Title', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" class="widefat" /></td>
					</tr>
					<tr>
						<?php $field = 'limit'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Limit', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" size="3" /> (<?php _e( '0 = no limit', 'wpclubmanager' ); ?>)</td>
					</tr>
					<tr>
						<?php $field = 'season'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Season', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories(array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_season',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							));
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'team'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Team', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories( array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_team',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							) );
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'jobs'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Jobs', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories( array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_jobs',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							) );
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'orderby'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Order by', 'wpclubmanager' ); ?></label></th>
						<td>
							<select id="option-<?php echo $field; ?>" name="<?php echo $field; ?>">
								<option id="name" value="name"<?php if ( $args[$field] == 'name' ) echo ' selected'; ?>><?php _e( 'Alphabetical' ); ?></option>
								<option id="rand" value="rand"<?php if ( $args[$field] == 'rand' ) echo ' selected'; ?>><?php _e( 'Random' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<?php $field = 'order'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Order', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							$wpcm_order_options = array(
								'ASC' => __( 'Lowest to highest', 'wpclubmanager' ),
								'DESC' => __( 'Highest to lowest', 'wpclubmanager' )
							);
							?>
							<select id="option-<?php echo $field; ?>" name="<?php echo $field; ?>">
								<?php foreach ( $wpcm_order_options as $key => $val ) { ?>
									<option id="<?php echo $key; ?>" value="<?php echo $key; ?>"<?php if ( $args[$field] == $key ) echo ' selected'; ?>><?php echo $val; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<?php $field = 'linktext'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Link text', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" /></td>
					</tr>
					<tr>
						<?php $field = 'linkpage'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Link page', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_pages( array(
								'show_option_none' => __( 'None' ),
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							) );
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'stats'; ?>
						<th><label><?php _e( 'Display options', 'wpclubmanager' ); ?></label></th>
						<td>
							<table>
								<tr>
									<?php
									$count = 0;
									foreach ( $stats_labels as $key => $value ) {
										$count++;
										if ( $count > 3 ) {
											$count = 1;
											echo '</tr><tr>';
										}
									?>
										<td>
											<label class="selectit" for="option-<?php echo $field; ?>-<?php echo $key; ?>">
												<input type="checkbox" id="option-<?php echo $field; ?>-<?php echo $key; ?>" name="<?php echo $field; ?>[]" value="<?php echo $key; ?>" <?php checked( in_array( $key, $stats ) ); ?> />
												<?php echo $value; ?>
											</label>
										</td>
									<?php } ?>
								</tr
							></table>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="button" id="option-submit" class="button-primary" value="<?php printf( __( 'Insert %s', 'wpclubmanager' ), __( 'Staff', 'wpclubmanager' ) ); ?>" name="submit" />
				</p>
			</div>
		
		<?php die();
	}

	/**
	* wpcm_club_buttons_ajax function.
	*/
	public function standings_shortcode_ajax() {

		$defaults = array(
			'limit' => 7,
			'comp' => null,
			'season' => null,
			'orderby' => 'pts',
			'order' => 'DESC',
			'linktext' => __( 'View all standings', 'wpclubmanager' ),
			'linkpage' => null,
			'stats' => 'p,w,d,l,f,a,gd,pts',
			'title' => __( 'Standings', 'wpclubmanager' ),
			'thumb' => 1,
			'linkclub' => 1,
		);
		$args = array_merge( $defaults, $_GET );

		$wpcm_standings_stats_labels = array(
			'p' => get_option( 'wpcm_standings_p_label' ),
			'w' => get_option( 'wpcm_standings_w_label' ),
			'd' => get_option( 'wpcm_standings_d_label' ),
			'l' => get_option( 'wpcm_standings_l_label' ),
			'otw' => get_option( 'wpcm_standings_otw_label' ),
			'otl' => get_option( 'wpcm_standings_otl_label' ),
			'pct' => get_option( 'wpcm_standings_pct_label' ),
			'f' => get_option( 'wpcm_standings_f_label' ),
			'a' => get_option( 'wpcm_standings_a_label' ),
			'gd' => get_option( 'wpcm_standings_gd_label' ),
			'b' => get_option( 'wpcm_standings_bonus_label' ),
			'pts' => get_option( 'wpcm_standings_pts_label' )
		); ?>
			<div id="wpcm_standings-form">
				<table id="wpcm_standings-table" class="form-table">
					<tr>
						<?php $field = 'title'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Title', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" class="widefat" /></td>
					</tr>
					<tr>
						<?php $field = 'limit'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Limit', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" size="3" /> (<?php _e( '0 = no limit', 'wpclubmanager' ); ?>)</td>
					</tr>
					<tr>
						<?php $field = 'comp'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Competition', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories(array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_comp',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							));
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'season'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Season', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_categories(array(
								'show_option_none' => __( 'All' ),
								'hide_empty' => 0,
								'orderby' => 'title',
								'taxonomy' => 'wpcm_season',
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							));
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'orderby'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Order by', 'wpclubmanager' ); ?></label></th>
						<td>
							<select id="option-<?php echo $field; ?>" name="<?php echo $field; ?>">
								<?php foreach ( $wpcm_standings_stats_labels as $key => $val ) { ?>
									<option id="<?php echo $key; ?>" value="<?php echo $key; ?>"<?php if ( $args[$field] == $key ) echo ' selected'; ?>><?php echo $val; ?></option>
								<?php } ?>
								<option id="rand" value="rand"<?php if ( $args[$field] == 'rand' ) echo ' selected'; ?>><?php _e( 'Random', 'wpclubmanager' ); ?></option>
							</select>
						</td>
					</tr>
					<tr>
						<?php $field = 'order'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Order', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php $wpcm_order_options = array(
								'ASC' => __( 'Lowest to highest', 'wpclubmanager' ),
								'DESC' => __( 'Highest to lowest', 'wpclubmanager' )
							); ?>
							<select id="option-<?php echo $field; ?>" name="<?php echo $field; ?>">
								<?php foreach ( $wpcm_order_options as $key => $val ) { ?>
									<option id="<?php echo $key; ?>" value="<?php echo $key; ?>"<?php if ( $args[$field] == $key ) echo ' selected'; ?>><?php echo $val; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<?php $field = 'linktext'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Link text', 'wpclubmanager' ); ?></label></th>
						<td><input type="text" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" /></td>
					</tr>
					<tr>
						<?php $field = 'linkpage'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Link page', 'wpclubmanager' ); ?></label></th>
						<td>
							<?php
							wp_dropdown_pages( array(
								'show_option_none' => __( 'None' ),
								'selected' => $args[$field],
								'name' => $field,
								'id' => 'option-' . $field
							) );
							?>
						</td>
					</tr>
					<tr>
						<?php $field = 'thumb'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Show Thumbnail', 'wpclubmanager' ); ?></label></th>
						<td>
							<input type="checkbox" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" checked />
						</td>
					</tr>
					<tr>
						<?php $field = 'linkclub'; ?>
						<th><label for="option-<?php echo $field; ?>"><?php _e( 'Link to Clubs', 'wpclubmanager' ); ?></label></th>
						<td>
							<input type="checkbox" id="option-<?php echo $field; ?>" name="<?php echo $field; ?>" value="<?php echo $args[$field]; ?>" checked />
						</td>
					</tr>
					<tr>
						<?php $field = 'stats'; ?>
						<th><label><?php _e( 'Display columns', 'wpclubmanager' ); ?></label></th>
						<td>
							<table style="text-align: center;">
								<tr>
									<?php
									foreach ( $wpcm_standings_stats_labels as $key => $value ) {
									?>
										<td>
											<label class="selectit" for="option-<?php echo $field; ?>-<?php echo $key; ?>">
												<input type="checkbox" id="option-<?php echo $field; ?>-<?php echo $key; ?>" name="<?php echo $field; ?>[]" value="<?php echo $key; ?>" checked />
												<?php echo $value; ?>
											</label>
										</td>
									<?php } ?>
								</tr
							></table>
						</td>
					</tr>
				</table>
				<p class="submit">
					<input type="button" id="option-submit" class="button-primary" value="<?php printf( __( 'Insert %s', 'wpclubmanager' ), __( 'Standings', 'wpclubmanager' ) ); ?>" name="submit" />
				</p>
			</div>
		
		<?php die();
	}
}

new WPCM_AJAX();