<?php
/**
 * Club Stats
 *
 * Displays the club stats box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.4.5
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

			<p><?php _e('Choose a competition and season to edit the manual stats.', 'wp-club-manager'); ?></p>

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
							<?php self::wpcm_club_stats_table($stats, $comp->term_id, $season->term_id); ?>
						</div>
					<?php endforeach; endif; ?>
				</div>
				<div class="clear"></div>
			<?php
			}
		}
	}

	/**
	 * Club stats table.
	 *
	 * @access public
	 * @param array
	 * @param string $comp
	 * @param string $season
	 * @return mixed $output
	 */
	public static function wpcm_club_stats_table( $stats = array(), $comp = 0, $season = 0 ) {

		$wpcm_standings_stats_labels = wpcm_get_preset_labels( 'standings', 'label' );

		if ( array_key_exists( $comp, $stats ) ):

			if ( array_key_exists( $season, $stats[$comp] ) ):

				$stats = $stats[$comp][$season];

			endif;
		endif; ?>

		<table>
			<thead>
				<tr>
					<td>&nbsp;</td>
					<?php foreach( $wpcm_standings_stats_labels as $key => $val ): ?>
						<th><?php echo $val; ?></th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th align="right"><?php _e( 'Total', 'wp-club-manager' ); ?></th>
					<?php foreach( $wpcm_standings_stats_labels as $key => $val ): ?>
						<td><input type="text" data-index="<?php echo $key; ?>" value="<?php wpcm_stats_value( $stats, 'total', $key ); ?>" size="2" tabindex="-1" readonly /></td>
					<?php endforeach; ?>
				</tr>
			</tfoot>
			<tbody>
				<tr>
					<td align="right"><?php _e( 'Auto', 'wp-club-manager' ); ?></td>
					<?php foreach( $wpcm_standings_stats_labels as $key => $val ): ?>
							<td><input type="text" data-index="<?php echo $key; ?>" value="<?php wpcm_stats_value( $stats, 'auto', $key ); ?>" size="2" tabindex="-1" readonly /></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<td align="right"><?php _e( 'Manual', 'wp-club-manager' ); ?></td>
					<?php foreach( $wpcm_standings_stats_labels as $key => $val ): ?>
						<td><input type="text" data-index="<?php echo $key; ?>" name="wpcm_stats[<?php echo $comp; ?>][<?php echo $season; ?>][<?php echo $key; ?>]" value="<?php wpcm_stats_value( $stats, 'manual', $key ); ?>" size="2" /></td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>

	<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		if( isset( $_POST['wpcm_stats'] ) ){
			$stats = $_POST['wpcm_stats'];
		} else {
			$stats = array();
		}
		if( is_array( $stats ) ) array_walk_recursive( $stats, 'wpcm_array_values_to_int' );

		update_post_meta( $post_id, 'wpcm_stats', serialize( $stats ) );

		do_action( 'delete_plugin_transients' );
	}
}