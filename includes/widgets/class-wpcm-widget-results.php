<?php
/**
 * Results Widget
 *
 * @author 		ClubPress
 * @category 	Widgets
 * @package 	WPClubManager/Widgets
 * @version 	1.0.0
 * @extends 	WP_Widget
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Results_Widget extends WP_Widget {

	var $wpcm_widget_cssclass;
	var $wpcm_widget_description;
	var $wpcm_widget_idbase;
	var $wpcm_widget_name;

	/**
	 * constructor
	 *
	 * @access public
	 * @return void
	 */
	function WPCM_Results_Widget() {

		/* Widget variable settings. */
		$this->wpcm_widget_cssclass = 'wpcm-widget widget-results';
		$this->wpcm_widget_description = __( 'Display most recent results.', 'wpclubmanager' );
		$this->wpcm_widget_idbase = 'wpcm-results-widget';
		$this->wpcm_widget_name = __( 'WPCM Results', 'wpclubmanager' );

		/* Widget settings. */
		$widget_ops = array( 'classname' => $this->wpcm_widget_cssclass, 'description' => $this->wpcm_widget_description );

		/* Create the widget. */
		$this->WP_Widget('wpcm_results', $this->wpcm_widget_name, $widget_ops);
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 * @access public
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		global $wpclubmanager;

		extract( $args );

		$limit = $instance['limit'];
		$comp = $instance['comp'];
		$season = $instance['season'];
		$team = $instance['team'];
		$club = get_option( 'wpcm_default_club' );
		$venue = $instance['venue'];
		$linktext = $instance['linktext'];
		$linkpage = $instance['linkpage'];
		$title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
    	$show_date = $instance['show_date'];
    	$show_time = $instance['show_time'];
    	$show_score = $instance['show_score'];
    	$show_comp = $instance['show_comp'];
    	$show_team = $instance['show_team'];

    	if ( $limit == 0 )
			$limit = -1;
		if ( $linkpage <= 0 )
			$linkpage = null;
		if ( $club <= 0  )
			$club = null;
		if ( $comp <= 0 )
			$comp = null;
		if ( $season <= 0  )
			$season = null;
		if ( $team <= 0  )
			$team = null;
		if ( $venue <= 0  )
			$venue = null;

		// get all corresponding matches
		$args = array(
			'tax_query' => array(),
			'numberposts' => $limit,
			'order' => 'DESC',
			'orderby' => 'post_date',
			'meta_query' => array(
				array(
					'key' => 'wpcm_played',
					'value' => true
				)
			),
			'post_type' => 'wpcm_match',
			'posts_per_page' => $limit
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
		$count = 0;

		echo $before_widget;

		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div class="wpcm-matches-widget clearfix"><ul>';
		
		if ( $size > 0 ) {
			
			foreach( $matches as $match ) {
				$count ++;
				$home_club = get_post_meta( $match->ID, 'wpcm_home_club', true );
				$away_club = get_post_meta( $match->ID, 'wpcm_away_club', true );
				$home_goals = get_post_meta( $match->ID, 'wpcm_home_goals', true );
				$away_goals = get_post_meta( $match->ID, 'wpcm_away_goals', true );
				$played = get_post_meta( $match->ID, 'wpcm_played', true );
				$timestamp = strtotime( $match->post_date );
				$gmt_offset = get_option( 'gmt_offset' );
				$date_format = get_option( 'date_format' );
				$time_format = get_option( 'time_format' );
				$comps = get_the_terms( $match->ID, 'wpcm_comp' );
				$teams = get_the_terms( $match->ID, 'wpcm_team' );

				echo '<li class="fixture">';

					echo '<div class="fixture-meta">';

						if ( $show_team && is_array( $teams ) ):
							echo '<div class="team">';
							foreach ( $teams as $team ):
								echo '<span>' . $team->name . '</span>';
							endforeach;
							echo '</div>';
						endif;
						if ( $show_comp && is_array( $comps ) ):
							echo '<div class="competition">';
							foreach ( $comps as $comp ):
								echo '<span>' . $comp->name . '</span>';
							endforeach;
							echo '</div>';
						endif;

					echo '</div>';

					echo '<a href="' . get_permalink( $match->ID ) . '">';
						echo '<div class="clubs">';
							echo '<div class="home-clubs">';
								echo '<div class="home-logo">' . get_the_post_thumbnail( $home_club, 'crest-medium', array( 'title' => get_the_title( $home_club ) ) ) . '</div>';
								echo get_the_title( $home_club );
								echo '<div class="score">' . $home_goals . '</div>';
							echo '</div>';
							echo '<div class="away-clubs">';
								echo '<div class="away-logo">' . get_the_post_thumbnail( $away_club, 'crest-medium', array( 'title' => get_the_title( $away_club ) ) ) . '</div>';
								echo get_the_title( $away_club );
								echo '<div class="score">' . $away_goals . '</div>';
							echo '</div>';
						echo '</div>';
					echo '</a>';

					echo '<div class="wpcm-date">';
						echo '<div class="kickoff">';
							if ( $show_date )
								echo date_i18n( $date_format, $timestamp );
							if ( $show_time )
								echo ', <time>' . date_i18n( $time_format, $timestamp ) . '</time>';
						echo '</div>';			
					echo '</div>';

				echo '</li>';

				wp_reset_postdata();
				
			}
		} else {
			echo '<li class="inner">'.__('No matches played yet.', 'wpclubmanager').'</li>';
		}
		echo '</ul>';
		if ( isset( $linkpage ) )
			echo '<a href="' . get_page_link( $linkpage ) . '" class="wpcm-view-link">' . $linktext . '</a>';
		echo '</div>';

		echo $after_widget;
	}

	/**
	 * update function.
	 *
	 * @see WP_Widget->update
	 * @access public
	 * @param array $new_instance
	 * @param array $old_instance
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;

		$instance['show_date'] = strip_tags( $new_instance['show_date'] );
		$instance['show_time'] = strip_tags( $new_instance['show_time'] );
		$instance['show_score'] = strip_tags( $new_instance['show_score'] );
		$instance['show_comp'] = strip_tags( $new_instance['show_comp'] );
		$instance['show_team'] = strip_tags( $new_instance['show_team'] );
		
		foreach( $new_instance as $key => $value ) {
			
			$instance[$key] = strip_tags( $value );
		}
		
		return $instance;
	}

	/**
	 * form function.
	 *
	 * @see WP_Widget->form
	 * @access public
	 * @param array $instance
	 * @return void
	 */
	function form( $instance ) {
		
		$defaults = array(
			'limit' => 5,
			'comp' => null,
			'team' => null,
			'season' => null,
			'club' => get_option( 'wpcm_default_club' ),
			'venue' => null,
			'linktext' => __( 'View all results', 'wpclubmanager' ),
			'linkpage' => null,
			'title' => __( 'Results', 'wpclubmanager' ),
			'show_date' => null,
			'show_time' => null,
			'show_score' => null,
			'show_comp' => null,
			'show_team' => null
		);
		
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>

		<?php $field = 'title'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Title', 'wpclubmanager') ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" /></p>
		
		<?php $field = 'limit'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Limit', 'wpclubmanager') ?>:</label>
		<input id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" size="3" /></p>
		
		<?php $field = 'comp'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Competition', 'wpclubmanager') ?>:</label>
		<?php
		wp_dropdown_categories(array(
			'show_option_none' => __( 'All' ),
			'hide_empty' => 0,
			'orderby' => 'title',
			'taxonomy' => 'wpcm_comp',
			'selected' => $instance[$field],
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		));
		?></p>
		
		<?php $field = 'season'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Season', 'wpclubmanager') ?>:</label>
		<?php
		wp_dropdown_categories(array(
			'show_option_none' => __( 'All' ),
			'hide_empty' => 0,
			'orderby' => 'title',
			'taxonomy' => 'wpcm_season',
			'selected' => $instance[$field],
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		));
		?></p>
		
		<?php $field = 'team'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Team', 'wpclubmanager') ?>:</label>
		<?php
		wp_dropdown_categories(array(
			'show_option_none' => __( 'All' ),
			'hide_empty' => 0,
			'orderby' => 'title',
			'taxonomy' => 'wpcm_team',
			'selected' => $instance[$field],
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		));
		?></p>
		
		<?php $field = 'venue'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Venue', 'wpclubmanager') ?>:</label>
		<?php
		wp_dropdown_categories(array(
			'show_option_none' => __( 'All' ),
			'hide_empty' => 0,
			'orderby' => 'title',
			'taxonomy' => 'wpcm_venue',
			'selected' => $instance[$field],
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		));
		?></p>
		
		<?php $field = 'linktext'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Link text', 'wpclubmanager') ?>:</label>
		<input class="widefat" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="<?php echo $instance[$field]; ?>" type="text" /></p>
		
		<?php $field = 'linkpage'; ?>
		<p><label for="<?php echo $this->get_field_id( $field ); ?>"><?php _e('Link page', 'wpclubmanager') ?>:</label>
		<?php
		wp_dropdown_pages( array(
			'show_option_none' => __( 'None' ),
			'selected' => $instance[$field],
			'name' => $this->get_field_name( $field ),
			'id' => $this->get_field_id( $field )
		) );
		?></p>
		
		<p><label><?php _e( 'Display Options', 'wpclubmanager' ); ?></label>
		<table>
			<tr>
				<?php $field = 'show_date'; ?>
				<td>
					<label class="selectit" for="<?php echo $this->get_field_id( $field ); ?>-<?php echo $key; ?>">
						<input type="checkbox" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="1"<?php if( $instance[$field] ) echo ' checked' ?> />
							<?php _e( 'Date', 'wpclubmanager' ); ?>
					</label>
				</td>
				<?php $field = 'show_time'; ?>
				<td>
					<label class="selectit" for="<?php echo $this->get_field_id( $field ); ?>-<?php echo $key; ?>">
						<input type="checkbox" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="1"<?php if( $instance[$field] ) echo ' checked' ?> />
							<?php _e( 'Kick Off', 'wpclubmanager' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<?php $field = 'show_score'; ?>
				<td>
					<label class="selectit" for="<?php echo $this->get_field_id( $field ); ?>-<?php echo $key; ?>">
						<input type="checkbox" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="1"<?php if( $instance[$field] ) echo ' checked' ?> />
							<?php _e( 'Score', 'wpclubmanager' ); ?>
					</label>
				</td>
				<?php $field = 'show_comp'; ?>
				<td>
					<label class="selectit" for="<?php echo $this->get_field_id( $field ); ?>-<?php echo $key; ?>">
						<input type="checkbox" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="1"<?php if( $instance[$field] ) echo ' checked' ?> />
							<?php _e( 'Competition', 'wpclubmanager' ); ?>
					</label>
				</td>
			</tr>
			<tr>
				<?php $field = 'show_team'; ?>
				<td>
					<label class="selectit" for="<?php echo $this->get_field_id( $field ); ?>-<?php echo $key; ?>">
						<input type="checkbox" id="<?php echo $this->get_field_id( $field ); ?>" name="<?php echo $this->get_field_name( $field ); ?>" value="1"<?php if( $instance[$field] ) echo ' checked' ?> />
							<?php _e( 'Team', 'wpclubmanager' ); ?>
					</label>
				</td>
			</tr>
		</table></p>

		<?php
	}
}

register_widget( 'WPCM_Results_Widget' );