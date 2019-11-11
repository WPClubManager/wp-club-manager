<?php
/**
 * Admin Dashboard Widgets
 *
 * @author      WPClubManager
 * @category    Admin
 * @package     WPClubManager/Admin
 * @version     1.3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WPCM_Admin_Dashboard_Widgets' ) ) :

/**
 * WPCM_Admin_Dashboard Class
 */
class WPCM_Admin_Dashboard_Widgets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Only hook in admin parts if the user has admin access
		if ( current_user_can( 'manage_wpclubmanager' ) ) {
			add_action( 'wp_dashboard_setup', array( $this, 'init' ) );
		}
	}

	/**
	 * Init dashboard widgets
	 */
	public function init() {

		wp_add_dashboard_widget( 'wpclubmanager_dashboard_upcoming', __( 'This Week\'s Upcoming Matches', 'wp-club-manager' ), array( $this, 'upcoming_matches_widget' ) );
		add_filter( 'dashboard_glance_items', array( $this, 'glance_items' ), 10, 1 );
	}

	/**
	 * Add WPCM Post type counts to At a glance widget
	 *
	 * @author Daniel J Griffiths
	 * @since 1.3
	 * @return void
	 */
	public function glance_items( $items ) {

        $post_types = apply_filters( 'wpclubmanager_glance_items', array( 'wpcm_match', 'wpcm_player', 'wpcm_staff', 'wpcm_sponsor' ) );

        foreach ( $post_types as $type ):

	        $num_posts = wp_count_posts( $type );

			if ( $num_posts && $num_posts->publish ) {

				$post_type = get_post_type_object( $type );

				$text = _n( '%s ' . $post_type->labels->singular_name, '%s ' . $post_type->labels->name, $num_posts->publish, 'wp-club-manager' );

				$text = sprintf( $text, number_format_i18n( $num_posts->publish ) );

				if ( current_user_can( $post_type->cap->edit_posts ) ) {
					$text = sprintf( '<a class="' . $post_type->name . '-count" href="edit.php?post_type=' . $post_type->name . '">%1$s</a>', $text );
				} else {
					$text = sprintf( '<span class="' . $post_type->name . '-count">%1$s</span>', $text );
				}

				$items[] = $text;
			}

		endforeach;

		return $items;
    }

	/**
	 * Show status widget
	 */
	public function upcoming_matches_widget() {

		$club = get_default_club();
		$format = get_match_title_format();
		$year = date('Y');
		$week = date('W');

		// get matches
		$query_args = array(
			'numberposts' => '-1',
			'order' => 'ASC',
			'orderby' => 'post_date',
			'post_type' => 'wpcm_match',
			'post_status' => array('future'),
			'posts_per_page' => '-1',
			'date_query' => array(
				array(
					'year' => $year,
					'week' => $week
				),
			)
		);
		
		$query_args['meta_query'] = array(
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

		$matches = get_posts( $query_args ); ?>

		<ul class="wpcm-matches-list">
		
		<?php
		if ( $matches ) {

			foreach( $matches as $match ) {

				$home_club = get_post_meta( $match->ID, 'wpcm_home_club', true );
				$away_club = get_post_meta( $match->ID, 'wpcm_away_club', true );
				$timestamp = strtotime( $match->post_date );
				$time_format = get_option( 'time_format' );
				$neutral = get_post_meta( $match->ID, 'wpcm_neutral', true );
				$comps = get_the_terms( $match->ID, 'wpcm_comp' );
				$comp_status = get_post_meta( $match->ID, 'wpcm_comp_status', true );
				$separator = get_option('wpcm_match_clubs_separator');

				if( $format == '%home% vs %away%' ) {
					$side1 = $home_club;
					$side2 = $away_club;
				} else {
					$side1 = $away_club;
					$side2 = $home_club;
				}
				
				if ( is_array( $comps ) ) {
					foreach ( $comps as $comp ):

						$comp = reset($comps);
						$t_id = $comp->term_id;
						$comp_meta = get_option( "taxonomy_term_$t_id" );
						$comp_label = $comp_meta['wpcm_comp_label'];

						$competition = $comp->name . '&nbsp;' . $comp_status;

					endforeach;
				} ?>

				<li class="wpcm-matches-list-item">

					<div class="wpcm-matches-list-link">
				
						<span class="wpcm-matches-list-col wpcm-matches-list-club1">
							<?php if( $club == $side1 ) { echo '<strong>'; }
							echo wpcm_get_team_name( $side1, $match->ID );
							if( $club == $side1 ) { echo '</strong>'; } ?>
						</span>

						<span class="wpcm-matches-list-col wpcm-matches-list-status">
							<span class="wpcm-matches-list-sep">
								<?php echo $separator; ?>
							</span>
						</span>

						<span class="wpcm-matches-list-col wpcm-matches-list-club2">
							<?php if( $club == $side2 ) { echo '<strong>'; }
							echo wpcm_get_team_name( $side2, $match->ID );
							if( $club == $side2 ) { echo '</strong>'; } ?>
						</span>

					</div>

					<a href="<?php echo get_edit_post_link( $match->ID ); ?>" class="wpcm-matches-list-additional">

						<span class="wpcm-matches-list-additional-col wpcm-matches-list-date">
							<?php echo date_i18n( 'l jS F', $timestamp ); ?>
						</span>

						<span class="wpcm-matches-list-additional-col wpcm-matches-list-status">
							<span class="wpcm-matches-list-time">
								<?php echo date_i18n( $time_format, $timestamp ); ?>
							</span>
						</span>

						<span class="wpcm-matches-list-additional-col wpcm-matches-list-info">
							<?php echo $competition; ?>
						</span>

					</a>
				
				</li>

			<?php
			}

		} else { ?>

			<li><?php _e('No upcoming matches.', 'wp-club-manager'); ?></li>

		<?php }
		wp_reset_postdata(); ?>

		</ul>

		<div class="add-new-match-link">
			<a class="button btn" href="post-new.php?post_type=wpcm_match"><?php _e( 'Add New Match', 'wp-club-manager' ); ?></a>
		</div>

	<?php }
}

endif;

return new WPCM_Admin_Dashboard_Widgets();