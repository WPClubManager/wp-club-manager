<?php
/**
 * WPCM_Shortcodes class.
 *
 * @class 		WPCM_Shortcodes
 * @version		1.0.2
 * @package		WPClubManager/Classes
 * @category	Class
 * @author 		ClubPress
 */

class WPCM_Shortcodes {

	public function __construct() {

		add_action( 'wp_head', array( $this, 'wpcm_map_css' ) );
	}

	/**
	 * Init shortcodes
	 */
	public static function init() {

		// Define shortcodes
		$shortcodes = array(
			'wpcm_map'               		=> __CLASS__ . '::map_shortcode',
			'wpcm_matches'         			=> __CLASS__ . '::matches_shortcode',
			'wpcm_players'            		=> __CLASS__ . '::players_shortcode',
			'wpcm_staff'            		=> __CLASS__ . '::staff_shortcode',
			'wpcm_standings'              	=> __CLASS__ . '::standings_shortcode',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * Display google map shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function map_shortcode( $atts ) {

		$atts = shortcode_atts(
			array(
				'address' 	=> false,
				'width' 	=> '100%',
				'height' 	=> '400px'
			),
			$atts
		);

		$address = $atts['address'];

		if( $address ) :

			wp_print_scripts( 'google-maps-api' );

			$coordinates = self::wpcm_map_get_coordinates( $address );

			if( !is_array( $coordinates ) )
				return;

			$map_id = uniqid( 'wpcm_map_' ); // generate a unique ID for this map

			ob_start(); ?>
			<div class="wpcm-match-map-canvas" id="<?php echo esc_attr( $map_id ); ?>" style="height: <?php echo esc_attr( $atts['height'] ); ?>; width: <?php echo esc_attr( $atts['width'] ); ?>"></div>
		    <script type="text/javascript">
				var map_<?php echo $map_id; ?>;
				function wpcm_run_map_<?php echo $map_id ; ?>(){
					var location = new google.maps.LatLng("<?php echo $coordinates['lat']; ?>", "<?php echo $coordinates['lng']; ?>");
					var map_options = {
						zoom: 15,
						center: location,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					}
					map_<?php echo $map_id ; ?> = new google.maps.Map(document.getElementById("<?php echo $map_id ; ?>"), map_options);
					var marker = new google.maps.Marker({
					position: location,
					map: map_<?php echo $map_id ; ?>
					});
				}
				wpcm_run_map_<?php echo $map_id ; ?>();
			</script>
			<?php
		endif;
		return ob_get_clean();
	}

	public static function wpcm_map_get_coordinates( $address, $force_refresh = false ) {

	    $address_hash = md5( $address );

	    $coordinates = get_transient( $address_hash );

	    if ($force_refresh || $coordinates === false) {

	    	$args       = array( 'address' => urlencode( $address ), 'sensor' => 'false' );
	    	$url        = add_query_arg( $args, 'http://maps.googleapis.com/maps/api/geocode/json' );
	     	$response 	= wp_remote_get( $url );

	     	if( is_wp_error( $response ) )
	     		return;

	     	$data = wp_remote_retrieve_body( $response );

	     	if( is_wp_error( $data ) )
	     		return;

			if ( $response['response']['code'] == 200 ) {

				$data = json_decode( $data );

				if ( $data->status === 'OK' ) {

				  	$coordinates = $data->results[0]->geometry->location;

				  	$cache_value['lat'] 	= $coordinates->lat;
				  	$cache_value['lng'] 	= $coordinates->lng;
				  	$cache_value['address'] = (string) $data->results[0]->formatted_address;

				  	// cache coordinates for 3 months
				  	set_transient($address_hash, $cache_value, 3600*24*30*3);
				  	$data = $cache_value;

				} elseif ( $data->status === 'ZERO_RESULTS' ) {
				  	return __( 'No location found for the entered address.', 'pw-maps' );
				} elseif( $data->status === 'INVALID_REQUEST' ) {
				   	return __( 'Invalid request. Did you enter an address?', 'pw-maps' );
				} else {
					return __( 'Something went wrong while retrieving your map, please ensure you have entered the short code correctly.', 'pw-maps' );
				}

			} else {
			 	return __( 'Unable to contact Google API service.', 'pw-maps' );
			}

	    } else {
	       // return cached results
	       $data = $coordinates;
	    }

	    return $data;
	}

	/**
	 * Display matches fixtures function
	 *
	 * @access public
	 * @return string
	 */
	public static function wpcm_matches_fixtures() {

		global $wpclubmanager, $fixtures_data;

		$show_comp = get_option( 'wpcm_results_widget_show_comp' );
		$show_team = get_option( 'wpcm_results_widget_show_team' );
		$club = get_option( 'wpcm_default_club' );
		// get all corresponding matches
		$args = array(
			'tax_query' => array(),
			'numberposts' => '-1',
			'order' => 'ASC',
			'orderby' => 'post_date',
			'post_status' => 'future',
			'meta_query' => array(
				array(
					'key' => 'wpcm_played',
					'value' => false
				)
			),
			'post_type' => 'wpcm_match',
			'posts_per_page' => '-1'
		);
		$args['paged'] = get_query_var( 'paged' );
			$args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key' => 'wpcm_home_club',
					'value' => $club,
				),
				array(
					'key' => 'wpcm_away_club',
					'value' => $club,
				)
			);
		if ( isset( $fixtures_data['comp'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_comp',
				'terms' => $fixtures_data['comp'],
				'field' => 'term_id'
			);
		}
		if ( isset( $fixtures_data['season'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_season',
				'terms' => $fixtures_data['season'],
				'field' => 'term_id'
			);
		}
		if ( isset( $fixtures_data['team'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_team',
				'terms' => $fixtures_data['team'],
				'field' => 'term_id'
			);
		}
		if ( isset( $fixtures_data['venue'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_venue',
				'terms' => $fixtures_data['venue'],
				'field' => 'term_id'
			);
		}
		$fixtures = get_posts( $args );
		$size = sizeof( $fixtures );
		$output = '';
		$count = 0;

		if ( $size > 0 ) {
				foreach( $fixtures as $fixture ) {
					$count++;
					$home_club = get_post_meta( $fixture->ID, 'wpcm_home_club', true );
					$away_club = get_post_meta( $fixture->ID, 'wpcm_away_club', true );
					$default_club = get_option( 'wpcm_default_club' );
					$played = get_post_meta( $fixture->ID, 'wpcm_played', true );
					$timestamp = strtotime( $fixture->post_date );
					$gmt_offset = get_option( 'gmt_offset' );
					$date_format = get_option( 'date_format' );
					$time_format = get_option( 'time_format' );
					$comps = get_the_terms( $fixture->ID, 'wpcm_comp' );
					$teams = get_the_terms( $fixture->ID, 'wpcm_team' );

					$output .= '<tr data-url="' . get_permalink( $fixture->ID ) . '">';
					
					$output .= '<td class="wpcm-date"><a href="' . get_permalink( $fixture->ID ) . '">' . date_i18n( 'd M', $timestamp ) . ', ' . date_i18n( $time_format, $timestamp ) . '</a></td>';

						if ( $default_club == $home_club ) {
							$output .= '<td class="venue">' . __('H', 'wpclubmanager') . '</td><td class="away">' . get_the_title ( $away_club ) . '</td>';
						} elseif ( $default_club == $away_club ) {
							$output .= '<td class="venue">' . __('A', 'wpclubmanager') . '</td><td class="home">' . get_the_title ( $home_club ) . '</td>';
						}

						if ( $show_team ):
							$output .= '<td class="team">';
						 	if ( is_array( $teams ) ) {
								foreach ( $teams as $team ):
									$output .= $team->name . '<br />';
								endforeach;
							}
							$output .= '</td>';
						endif;

						if ( $show_comp ):
							$output .= '<td class="competition">';
						 	if ( is_array( $comps ) ) {
								foreach ( $comps as $comp ):
									$comp = reset($comps);
									$t_id = $comp->term_id;
									$comp_meta = get_option( "taxonomy_term_$t_id" );
									$comp_label = $comp_meta['wpcm_comp_label'];
									if ( $comp_label ) {
										$output .= $comp_label . '<br />';
									} else {
										$output .= $comp->name . '<br />';
									}
								endforeach;
							}
							$output .= '</td>';
						endif;

						$output .= '</td><td class="goals">&nbsp;</td></tr>';

				}
				
			}

		return $output;
	}

	/**
	 * List matches shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function matches_shortcode( $atts ) {
	
		extract( shortcode_atts( array(
			'comp' => null,
			'season' => null,
			'team' => null,
			'venue' => null,
			'linktext' => __( 'View all results', 'wpclubmanager' ),
			'linkpage' => null,
			'title' => __( 'Fixtures & Results', 'wpclubmanager' ),
			'type' => 'default'
		), $atts ) );
		// convert atts to something more useful
		if ( $linkpage <= 0 )
			$linkpage = null;
		if ( $comp <= 0 )
			$comp = null;
		if ( $season <= 0  )
			$season = null;
		if ( $team <= 0  )
			$team = null;
		if ( $venue <= 0  )
			$venue = null;
		$show_comp = get_option( 'wpcm_results_widget_show_comp' );
		$show_team = get_option( 'wpcm_results_widget_show_team' );
		$club = get_option( 'wpcm_default_club' );

		global $fixtures_data;

		$fixtures_data = array(
	    	'comp' => $comp,
			'season' => $season,
			'team' => $team,
			'venue' => $venue,
		);

		// get all corresponding matches
		$args = array(
			'tax_query' => array(),
			'numberposts' => '-1',
			'order' => 'ASC',
			'orderby' => 'post_date',
			'meta_query' => array(
				array(
					'key' => 'wpcm_played',
					'value' => true
				)
			),
			'post_type' => 'wpcm_match',
			'posts_per_page' => '-1'
		);
		$args['paged'] = get_query_var( 'paged' );
		if ( isset( $club ) ) {
			$args['meta_query'] = array(
				'relation' => 'OR',
				array(
					'key' => 'wpcm_home_club',
					'value' => $club,
				),
				array(
					'key' => 'wpcm_away_club',
					'value' => $club,
				)
			);
		}
		if ( isset( $comp ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_comp',
				'terms' => $comp,
				'field' => 'term_id'
			);
		}
		if ( isset( $season ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_season',
				'terms' => $season,
				'field' => 'term_id'
			);
		}
		if ( isset( $team ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_team',
				'terms' => $team,
				'field' => 'term_id'
			);
		}
		if ( isset( $venue ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_venue',
				'terms' => $venue,
				'field' => 'term_id'
			);
		}
		$matches = get_posts( $args );
		$size = sizeof( $matches );
		$output = '';
		$count = 0;

		$output = '<div class="wpcm-fixtures-shortcode">
			<h3>' . $title . '</h3>
			<table>
				<thead>';
			if ( $size > 0 ) {
				$output .= '
					<tr>
						<th class="wpcm-date">'.__('Date').'</th>';
				$output .= '
						<th class="venue">'.__('Venue', 'wpclubmanager').'</th>
						<th class="opponent">'.__('Opponent', 'wpclubmanager').'</th>';
				if ( $show_team )
					$output .= '
						<th class="team">'.__('Team', 'wpclubmanager').'</th>';
				if ( $show_comp )
						$output .= '
						<th class="competition">'.__('Competition', 'wpclubmanager').'</th>';
				$output .= '
						<th class="result">'.__('Results', 'wpclubmanager').'</th>
					</tr>';
			} else {
				$output .=
					'<tr>
						<th class="inner">'.__('No matches played yet.', 'wpclubmanager').'</div></th>
					</tr>';
			}
			$output .=
				'</thead>
			<tbody>';
			if ( $size > 0 ) {
				foreach( $matches as $match ) {
					$count++;
					$home_club = get_post_meta( $match->ID, 'wpcm_home_club', true );
					$away_club = get_post_meta( $match->ID, 'wpcm_away_club', true );
					$default_club = get_option( 'wpcm_default_club' );
					$home_goals = get_post_meta( $match->ID, 'wpcm_home_goals', true );
					$away_goals = get_post_meta( $match->ID, 'wpcm_away_goals', true );
					$played = get_post_meta( $match->ID, 'wpcm_played', true );
					$timestamp = strtotime( $match->post_date );
					$gmt_offset = get_option( 'gmt_offset' );
					$date_format = get_option( 'date_format' );
					$time_format = get_option( 'time_format' );
					$comps = get_the_terms( $match->ID, 'wpcm_comp' );
					$teams = get_the_terms( $match->ID, 'wpcm_team' );
					

					$output .=
					'<tr data-url="' . get_permalink( $match->ID ) . '">';
						$output .= '<td class="wpcm-date"><a href="' . get_permalink( $match->ID ) . '">' . date_i18n( 'd M', $timestamp ) . ', ' . date_i18n( $time_format, $timestamp ) . '</a></td>';

						if ( $default_club == $home_club ) {
							$output .= '<td class="venue">' . __('H', 'wpclubmanager') . '</td><td class="opponent away">' . get_the_title ( $away_club ) . '</td>';
						} elseif ( $default_club == $away_club ) {
							$output .= '<td class="venue">' . __('A', 'wpclubmanager') . '</td><td class="opponent home">' . get_the_title ( $home_club ) . '</td>';
						}

						if ( $show_team ):
							$output .= '<td class="team">';
						 	if ( is_array( $teams ) ) {
								foreach ( $teams as $team ):
									$output .= $team->name . '<br />';
								endforeach;
							}
							$output .= '</td>';
						endif;

						if ( $show_comp ):
							$output .= '<td class="competition">';
						 	if ( is_array( $comps ) ) {
								foreach ( $comps as $comp ):
									$comp = reset($comps);
									$t_id = $comp->term_id;
									$comp_meta = get_option( "taxonomy_term_$t_id" );
									$comp_label = $comp_meta['wpcm_comp_label'];
									if ( $comp_label ) {
										$output .= $comp_label . '<br />';
									} else {
										$output .= $comp->name . '<br />';
									}
								endforeach;
							}
							$output .= '</td>';
						endif;

							if ( $home_goals == $away_goals ) {
								$result = '<span class="draw"></span>';
								$status = ' draw';
							}

							if ( $default_club == $home_club ) {
								if ( $home_goals > $away_goals ) {
									$result = '<span class="win"></span>';
									$status = ' win';
								}
								if ( $home_goals < $away_goals ) {
									$result = '<span class="lose"></span>';
									$status = ' loss';
								}
							} else {
								if ( $home_goals > $away_goals ) {
									$result = '<span class="lose"></span>';
									$status = ' loss';
								}
								if ( $home_goals < $away_goals ) {
									$result = '<span class="win"></span>';
									$status = ' win';
								}
							}

					$output .= '<td class="result' . $status . '">' . ( $played ? $home_goals . ' ' . get_option( 'wpcm_match_goals_delimiter' ) . ' ' . $away_goals : '' ) . ' ' . ( $played ? $result : '' ) . '</td>';
					$output .= '</tr>';
				}
			}

			$output .= self::wpcm_matches_fixtures();

			$output .= '</tbody></table>';
			if ( isset( $linkpage ) )
				$output .= '<a href="'.get_page_link( $linkpage ) . '" class="wpcm-view-link">' . $linktext . '</a>';
			$output .= '</div>';

		return $output;
	}

	/**
	 * Display players table shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function players_shortcode( $atts ) {
	
		global $wpclubmanager;

		$wpcm_player_stats_labels = array(
			'goals' => get_option( 'wpcm_player_goals_label'),
			'assists' => get_option( 'wpcm_player_assists_label'),
			'yellowcards' => get_option( 'wpcm_player_yellowcards_label'),
			'redcards' => get_option( 'wpcm_player_redcards_label'),
			'rating' => get_option( 'wpcm_player_ratings_label'),
			'mvp' => get_option( 'wpcm_player_mvp_label')
		);

		$player_stats_labels = array_merge( array( 'appearances' => __( 'Apps', 'wpclubmanager' ) ), $wpcm_player_stats_labels );

		$stats_labels = array_merge(
			array(
				'flag' => '&nbsp;',
				'number' => '&nbsp;',
				'name' => __( 'Name', 'wpclubmanager' ),
				'position' => __( 'Position', 'wpclubmanager' ),
				'age' => __( 'Age', 'wpclubmanager' ),
				'team' => __( 'Team', 'wpclubmanager' ),
				'season' => __( 'Season', 'wpclubmanager' ),
				'dob' => __( 'Date of Birth', 'wpclubmanager' ),
				'hometown' => __( 'Hometown', 'wpclubmanager' ),
				'joined' => __( 'Joined', 'wpclubmanager' )
			),
			$player_stats_labels
		);
		extract( shortcode_atts( array(
			'limit' => -1,
			'season' => null,
			'team' => null,
			'position' => null,
			'orderby' => 'number',
			'order' => 'ASC',
			'show_flag' => get_option( 'wpcm_player_list_show_flag' ),
			'show_position' => get_option( 'wpcm_player_list_show_position' ),
			'show_age' => get_option( 'wpcm_player_list_show_age' ),
			'show_dob' => get_option( 'wpcm_player_list_show_dob' ),
			'show_name' => get_option( 'wpcm_player_gallery_show_name' ),
			'show_number' => get_option( 'wpcm_player_gallery_show_number' ),
			'linktext' => __( 'View all players', 'wpclubmanager' ),
			'linkpage' => null,
			'stats' => 'flag,number,name,position,age',
			'title' => __( 'Players', 'wpclubmanager' )
		), $atts ) );
		
		if ( $limit == 0 )
			$limit = -1;
		if ( $team <= 0 )
			$team = null;

		$stats = explode( ',', $stats );

		foreach( $stats as $key => $value ) {
			$stats[$key] = strtolower( trim( $value ) );
			if ( !array_key_exists( $stats[$key], $stats_labels ) )
				unset( $stats[$key] );
		}

		$numposts = $limit;

		if ( array_intersect_key( array_flip( $stats ), $player_stats_labels ) )
			$numposts = -1;
			$orderby = strtolower( $orderby );	
			$order = strtoupper( $order );
			$club = get_option( 'wpcm_default_club' );
			$output = '';
			$args = array(
				'post_type' => 'wpcm_player',
				'tax_query' => array(),
				'numposts' => $numposts,
				'posts_per_page' => $numposts,
				'orderby' => 'meta_value_num',
				'meta_key' => 'wpcm_number',
				'order' => $order
			);

		if ( $orderby == 'menu_order' ) {
		    $args['orderby'] = 'menu_order';
		}

		if ( $club ) {
			$args['meta_query'] = array(
				array(
					'key' => 'wpcm_club',
					'value' => $club,
				)
			);
		}

		if ( $season ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_season',
				'terms' => $season,
				'field' => 'term_id'
			);
		}

		if ( $team ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_team',
				'terms' => $team,
				'field' => 'term_id'
			);
		}

		if ( $position ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_position',
				'terms' => $position,
				'field' => 'term_id'
			);
		}

		$players = get_posts( $args );
		$count = 0;	
		
		if ( sizeof( $players ) > 0 ) {
			$output .= '<div class="wpcm-players-shortcode">';
				$output .=
				'<table>
					<thead>
						<tr>';
						foreach( $stats as $stat ) {
							$output .= '<th class="'. $stat . '">' . $stats_labels[$stat] .'</th>';
						}
						$output .= '</tr>
					</thead>
					<tbody>';

			$player_details = array();

			foreach( $players as $player ) {

				$player_details[$player->ID] = array();
				$count++;

				if ( array_intersect_key( array_flip( $stats ), $player_stats_labels ) )
					$player_stats = get_wpcm_player_stats( $player->ID );
					$number = get_post_meta( $player->ID, 'wpcm_number', true );
					$name = $player->post_title;
					$positions = get_the_terms( $player->ID, 'wpcm_position' );

				if ( is_array( $positions ) ) {
					$position = reset($positions);
					$position = $position->name;

				} else {

					$position = __( 'None', 'wpclubmanager' );

				}

				$dob = get_post_meta( $player->ID, 'wpcm_dob', true );
				$natl = get_post_meta( $player->ID, 'wpcm_natl', true );
				$hometown = get_post_meta( $player->ID, 'wpcm_hometown', true );

				foreach( $stats as $stat ) {

					if ( array_key_exists( $stat, $player_stats_labels ) )  {
						if ( $season ) {
							$player_details[$player->ID][$stat] = '';
							$player_details[$player->ID][$stat] .= $player_stats[0][ $season ]['total'][$stat];
						} else {
							$player_details[$player->ID][$stat] = '';
							$player_details[$player->ID][$stat] .= $player_stats[0][0]['total'][$stat];
						}
					} else {
						switch ( $stat ) {
						case 'flag':
							$player_details[$player->ID][$stat] = '';
							$player_details[$player->ID][$stat] .= '<img class="flag" src="' . WPCM_URL . 'assets/images/flags/' . $natl . '.png" />';
							break;
						case 'number':
							$player_details[$player->ID][$stat] = '';
							$player_details[$player->ID][$stat] .= $number;
							break;
						case 'name':
							$player_details[$player->ID][$stat] = '';
							$player_details[$player->ID][$stat] .= '<a href="' . get_permalink( $player->ID ) . '">' . $name . '</a>';
							break;
						case 'position':
							$player_details[$player->ID][$stat] = '';
							$positions = get_the_terms( $player->ID, 'wpcm_position' );
							if ( is_array( $positions ) ) {
								$player_positions = array();
								foreach ( $positions as $position ) {
									$player_positions[] = $position->name;
								}
								$player_details[$player->ID][$stat] .= implode( ', ', $player_positions );
							}
							break;
						case 'team':
							$player_details[$player->ID][$stat] = '';
							$teams = get_the_terms( $player->ID, 'wpcm_team' );
							if ( is_array( $teams ) ) {
								$player_teams = array();
								foreach ( $teams as $team ) {
									$player_teams[] = $team->name;
								}
								$player_details[$player->ID][$stat] .= implode( ', ', $player_teams );
							}
							break;
						case 'season':
							$player_details[$player->ID][$stat] = '';
							$seasons = get_the_terms( $player->ID, 'wpcm_season' );
							if ( is_array( $seasons ) ) {
								$player_seasons = array();
								foreach ( $seasons as $season ) {
									$player_seasons[] = $season->name;
								}
								$player_details[$player->ID][$stat] .= implode( ', ', $player_seasons );
							}
							break;
						case 'age':
							$player_details[$player->ID][$stat] = '';
							$player_details[$player->ID][$stat] .= get_age( get_post_meta( $player->ID, 'wpcm_dob', true ) );
							break;
						case 'dob':
							$player_details[$player->ID][$stat] = '';
							$player_details[$player->ID][$stat] .= date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $player->ID, 'wpcm_dob', true ) ) );
							break;
						case 'hometown':
							$player_details[$player->ID][$stat] = '';
							$player_details[$player->ID][$stat] .= '<img class="flag" src="'. WPCM_URL .'assets/images/flags/' . $natl . '.png" /> ' . $hometown;
							break;
						case 'joined':
							$player_details[$player->ID][$stat] = '';
							$player_details[$player->ID][$stat] = date_i18n( get_option( 'date_format' ), strtotime( $player->post_date ) );
							break;
						}
					}
				}
			}
			if ( array_key_exists( $orderby, $player_stats_labels ) ) {
				$player_details = subval_sort( $player_details, $orderby );
				if ( $order == 'DESC' )
					$player_details = array_reverse( $player_details );
			}
			$count = 0;
			foreach( $player_details as $player_detail ) {
				$count++;
				if ( $limit > 0 && $count > $limit )
					break;

				$output .=
				'<tr>';
				foreach( $stats as $stat ) {
					$output .= '<td class="'. $stat . '">';
					if ( $stat == 'rating' ) {
						if ( $player_detail['rating'] > 0 ) {
							$avrating = $player_detail['rating'] / $player_detail['appearances'];
							$output .= sprintf( "%01.2f", round($avrating, 2) );
						} else {
							$output .= '0';
						}
					} else {
						$output .= $player_detail[$stat];
					}
					$output .= '</td>';
				}
				$output .= '</tr>';
			}
			$output .= '</tbody></table>';
			
			$output .= '</div>';

			if ( isset( $linkpage ) && $linkpage ) $output .= '<a href="' . get_page_link( $linkpage ) . '" class="wpcm-view-link">' . $linktext . '</a>';

			wp_reset_postdata();

		} else {

			$output = '';
		}

		return $output;	
	}

	/**
	 * Display staff shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function staff_shortcode( $atts ) {
	
		extract( shortcode_atts( array(
			'id' => null,
			'limit' => -1,
			'season' => null,
			'team' => null,
			'jobs' => null,
			'title' => __( 'Staff', 'wpclubmanager' ),
			'linktext' => __( 'View all staff', 'wpclubmanager' ),
			'linkpage' => null
		), $atts ) );
		if ( $limit == 0 )
			$limit = -1;
		if ( $id <= 0 )
			$id = null;
		if ( $team <= 0 )
			$team = null;

		global $post;

		$show_dob = get_option( 'wpcm_staff_profile_show_dob' );
		$show_age = get_option( 'wpcm_staff_profile_show_age' );
		$show_season = get_option( 'wpcm_staff_profile_show_season' );
		$show_team = get_option( 'wpcm_staff_profile_show_team' );
		$show_natl = get_option( 'wpcm_staff_profile_show_nationality' );
		$show_jobs = get_option( 'wpcm_staff_profile_show_jobs' );

		$output = '';
		if ( $id ) {
			$post = get_post( $id );
			$posts = array();
			$posts[] = $post;
		} else {
			$args = array(
				'post_type' => 'wpcm_staff',
				'tax_query' => array(),
				'numposts' => $limit,
				'posts_per_page' => $limit
			);
			if ( $season ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_season',
					'terms' => $season,
					'field' => 'term_id'
				);
			}
			if ( $team ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_team',
					'terms' => $team,
					'field' => 'term_id'
				);
			}
			if ( $jobs ) {
				$args['tax_query'][] = array(
					'taxonomy' => 'wpcm_jobs',
					'terms' => $jobs,
					'field' => 'term_id'
				);
			}
			$posts = query_posts($args);
		}
		$count = 0;
		$size = sizeof($posts);

		if ($size > 0):

			while ( have_posts() ) : the_post();

				$output .= '<div class="wpcm-staff-shortcode row">';

				if (get_the_post_thumbnail( $post->ID, 'staff_single' ) != null) {

					$output .= '<div class="wpcm-staff-image">'.get_the_post_thumbnail( $post->ID, 'staff_single', array('title' => get_the_title()) ).'</div>';
				} else {

					$output .= '<div class="wpcm-staff-image">'.apply_filters( 'wpclubmanager_single_product_image', sprintf( '<img src="%s" alt="Placeholder" />', wpcm_placeholder_img_src() ), $post->ID ).'</div>';
				}
		
				$profile_details = array();

				// job title
				if ( $show_jobs == 'yes' ) {

					$jobs = get_the_terms( $post->ID, 'wpcm_jobs' );

					if ( is_array( $jobs ) ) {

						$staff_jobs = array();

						foreach ( $jobs as $value ) {

							$staff_jobs[] = $value->name;
						}

						$profile_details[ __('Job Title', 'wpclubmanager') ] = implode( ', ', $staff_jobs );
					}
				}

				// birthday
				if ( $show_dob == 'yes' )
					$profile_details[ __( 'Birthday', 'wpclubmanager' ) ] = date_i18n( get_option( 'date_format' ), strtotime( get_post_meta( $post->ID, 'wpcm_dob', true ) ) );
				
				// age
				if ( $show_age == 'yes' )
					$profile_details[__('Age', 'wpclubmanager')] = get_age( get_post_meta( $post->ID, 'wpcm_dob', true ) );
				
				// season
				if ( $show_season == 'yes' ) {
					$seasons = get_the_terms( $post->ID, 'wpcm_season' );
					if ( is_array( $seasons ) ) {
						$player_seasons = array();
						foreach ( $seasons as $value ) {
							$player_seasons[] = $value->name;
						}
						$profile_details[ __('Season', 'wpclubmanager') ] = implode( ', ', $player_seasons );
					}
				}
				
				// team
				if ( $show_team == 'yes' ) {
					$teams = get_the_terms( $post->ID, 'wpcm_team' );
					if ( is_array( $teams ) ) {
						$player_teams = array();
						foreach ( $teams as $team ) {
							$player_teams[] = $team->name;
						}
						$profile_details[ __('Team', 'wpclubmanager') ] = implode( ', ', $player_teams );
					}
				}

				// nationality
				if ( $show_natl == 'yes' ) {
					$natl = get_post_meta( $post->ID, 'wpcm_natl', true );
					$profile_details[ __( 'Nationality', 'wpclubmanager' ) ] = '<img class="flag" src="'. WPCM_URL .'assets/images/flags/' . $natl . '.png" />';
				}

				$output .= '<div class="wpcm-staff-info">
					<h1 class="entry-title">'.get_the_title($post->ID).'</h1>
					<div class="wpcm-staff-meta">';
					$count = 0;
					$size = sizeof( $profile_details );
					if ( $size > 0 ) {
						$output .= 
						'<table>' .
							'<tbody>';
						foreach ( $profile_details as $key => $value ) {
							$count++;
							$output .=
							'<tr>' .
								'<th>'.$key.'</th>' .
								'<td>'.$value.'</td>' .
							'</tr>';
						}
						$output .=
							'</tbody>' .
						'</table>';
					}
					$output .= '</div></div><div class="wpcm-staff-bio">' . apply_filters('the_content', get_the_content($post->ID)).'</div>';

					if ( isset( $linkpage ) && $linkpage ) $output .= '<a href="' . get_page_link( $linkpage ) . '" class="wpcm-view-link">' . $linktext . '</a>';

				$output .= '</div>';

			endwhile;

			wp_reset_postdata();

		endif;
		
		wp_reset_query();

		return $output;
	}

	/**
	 * Display standings shortcode
	 *
	 * @access public
	 * @param array $atts
	 * @return string
	 */
	public static function standings_shortcode( $atts ) {

		extract( shortcode_atts( array(
			'limit' => 7,
			'comp' => null,
			'season' => null,
			'orderby' => 'pts',
			'order' => 'DESC',
			'linktext' => __( 'View all standings', 'wpclubmanager' ),
			'linkpage' => null,
			'stats' => 'p,w,d,l,f,a,gd,pts',
			'title' => __( 'Standings', 'wpclubmanager' ),
			'type' => 'normal'
		), $atts ) );
		$wpcm_standings_stats_labels = array(
			'p' => get_option( 'wpcm_standings_p_label' ),
			'w' => get_option( 'wpcm_standings_w_label' ),
			'd' => get_option( 'wpcm_standings_d_label' ),
			'l' => get_option( 'wpcm_standings_l_label' ),
			'f' => get_option( 'wpcm_standings_f_label' ),
			'a' => get_option( 'wpcm_standings_a_label' ),
			'gd' => get_option( 'wpcm_standings_gd_label' ),
			'pts' => get_option( 'wpcm_standings_pts_label' )
		);
		// convert atts to something more useful
		$stats = explode( ',', $stats );
		foreach( $stats as $key => $value ) {
			$stats[$key] = strtolower( trim( $value ) );
			if ( !array_key_exists( $stats[$key], $wpcm_standings_stats_labels ) )
				unset( $stats[$key] );
		}
		if ( $limit == 0 )
			$limit = -1;
		if ( $comp <= 0 )
			$comp = null;
		if ( $season <= 0 )
			$season = null;
		$comp_slug = wpcm_get_term_slug( $comp, 'wpcm_comp' );
		$season_slug = wpcm_get_term_slug( $season, 'wpcm_season' );
		$club = get_option( 'wpcm_default_club' );
		$center = $club;
		$orderby = strtolower( $orderby );	
		$order = strtoupper( $order );
		if ( $linkpage <= 0 )
			$linkpage = null;
		// get all clubs from comp and season
		$args = array(
			'post_type' => 'wpcm_club',
			'tax_query' => array(),
			'numberposts' => -1,
			'posts_per_page' => -1
		);
		if ( $comp ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_comp',
				'terms' => $comp,
				'field' => 'term_id'
			);
		}
		if ( $season ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'wpcm_season',
				'terms' => $season,
				'field' => 'term_id'
			);
		}
		$clubs = get_posts( $args );
		$size = sizeof( $clubs );
		if ( $size == 0 )
			return false;
		if ( $limit == -1 )
			$limit = $size;
		// attach stats to each club
		foreach ( $clubs as $club ) {
			$club_stats = get_wpcm_club_total_stats( $club->ID, $comp, $season );		
			$club->wpcm_stats = $club_stats;
		}
		// sort clubs
		if ( $orderby == 'pts' ) {
			usort( $clubs, 'wpcm_club_standings_sort');
		} else {
			$clubs = wpcm_club_standings_sort_by( $orderby, $clubs );
		}
		if ( $order == 'ASC' ) {
			$clubs = array_reverse( $clubs );
		}
		// add places to clubs
		foreach ( $clubs as $key => $value ) {	
			$value->place = $key + 1;
		}
		// define center if null
		if ( !isset( $center ) )
			$center = $clubs[0]->ID;
		// if limit is smaller than table size, find range to display
		if ( $limit < $size ) {
			// find middle
			$middle = 0;
			foreach( $clubs as $key => $value ) {
				if ( $value->ID == $center ) $middle = $key;
			}
			// find range to display
			$before = floor( ( $limit - 1 ) / 2 );
			$first = $middle - $before;
			$actual = $size - $first;
			if ( $actual < $limit ) {
				$first -= ( $limit - $actual );
			}
			if ( $first < 0 ) {
				$first = 0;
			}
		} else {
			$first = 0;
			$limit = $size;
		}
		// slice array
		$clubs = array_slice( $clubs, $first, $limit );
		// initialize output
		$output = '';
		// table head
		$output .=
		'<div class="wpcm-standings-shortcode wpcm-standings-' . $type . '">
			<table>
				<thead>
					<tr>
						<th></th>
						<th></th>';
			foreach( $stats as $stat ) {
				$output .= '<th class="' . $stat . '">' . $wpcm_standings_stats_labels[$stat] . '</th>';
			}
			$output .=
					'</tr>
				</thead>
			<tbody>';
			// insert rows
			$rownum = 0;
			foreach ( $clubs as $club ) {
				$rownum ++;
				$club_stats = $club->wpcm_stats;
				$output .= '<tr class="' . ( $center == $club->ID ? 'highlighted ' : '' ) . ( $rownum % 2 == 0 ? 'even' : 'odd' ) . ( $rownum == $limit ? ' last' : '' ) . '">';

				$output .= '<td class="pos">' . $club->place . '</td>';

				$output .= '<td class="club">' . $club->post_title . '</td>';

				foreach( $stats as $stat ) {
					$output .= '<td class="' . $stat . '">' . $club_stats[$stat] . '</td>';
				}

				$output .= '</tr>';
			}
			$output.=
			'</tbody>
			</table>';
		$output .= '</div>';

		if ( isset( $linkpage ) )
			$output .= '<a href="' . get_page_link( $linkpage ) . '" class="wpcm-view-link">' . $linktext . '</a>';

		return $output;
	}
}