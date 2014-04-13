<?php
/**
 * Club Stats
 *
 * Displays the club stats box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.0.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Club_Stats {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		wp_nonce_field( 'wpclubmanager_save_data', 'wpclubmanager_meta_nonce' );

		$comps = get_the_terms( $post->ID, 'wpcm_comp' );
		$seasons = get_the_terms( $post->ID, 'wpcm_season' );
		$stats = get_wpcm_club_stats( $post );

		if( is_array( $comps ) ) { ?>

			<p><?php _e('Choose a competition and season to edit the manual stats.', 'wpclubmanager'); ?></p>

			<?php
			foreach( $comps as $comp ) {

				$name = $comp->name;

				if ( $comp->parent ) {
					$parent_comp = get_term( $comp->parent, 'wpcm_comp');
					$name .= ' (' . $parent_comp->name . ')';
				} ?>

				<div class="statsdiv">
					<h4><?php echo $name; ?></h4>
					<ul class="wpcm_stats-tabs">
						<?php if(is_array($seasons)): foreach($seasons as $season): ?>
							<li class="hide-if-no-js">
								<a href="#wpcm_comp-<?php echo $comp->term_id; ?>_season-<?php echo $season->term_id; ?>" tabindex="3">
									<?php echo $season->name; ?>
								</a>
							</li>
						<?php endforeach; endif; ?>
					</ul>

					<?php if(is_array($seasons)): foreach($seasons as $season): ?>
						<div id="wpcm_comp-<?php echo $comp->term_id; ?>_season-<?php echo $season->term_id; ?>" class="tabs-panel" style="display: none;">
							<?php wpcm_club_stats_table($stats, $comp->term_id, $season->term_id); ?>
						</div>
					<?php endforeach; endif; ?>
				</div>
				<div class="clear"></div>
			<?php
			}
		}
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		$stats = $_POST['wpcm_stats'];
		if( is_array( $stats ) ) array_walk_recursive( $stats, 'wpcm_array_values_to_int' );
		update_post_meta( $post_id, 'wpcm_stats', serialize( $stats ) );
	}
}