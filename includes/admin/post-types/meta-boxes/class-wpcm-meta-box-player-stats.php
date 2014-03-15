<?php
/**
 * Player Stats
 *
 * Displays the player stats box.
 *
 * @author 		ClubPress
 * @category 	Admin
 * @package 	WPClubManager/Admin/Meta Boxes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class WPCM_Meta_Box_Player_Stats {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		global $post_id;

		$stats = get_wpcm_player_stats( $post_id );
		$seasons = get_the_terms( $post_id, 'wpcm_season' ); ?>

		<div class="statsdiv">
			<ul class="wpcm_stats-tabs">
				<li class="tabs"><a href="#wpcm_team-0_season-0" tabindex="3"><?php printf( __( 'All %s', 'wpclubmanager' ), __( 'Seasons', 'wpclubmanager' ) ); ?></a></li>
				<?php if( is_array( $seasons ) ): foreach( $seasons as $season ): ?>
					<li class="hide-if-no-js22"><a href="#wpcm_team-0_season-<?php echo $season->term_id; ?>" tabindex="3"><?php echo $season->name; ?></a></li>
				<?php endforeach; endif; ?>
			</ul>
			<?php if( is_array( $seasons ) ): foreach( $seasons as $season ): ?>
				<div id="wpcm_team-0_season-<?php echo $season->term_id; ?>" class="tabs-panel stats-table-season" style="display: none;">
					<?php wpcm_player_stats_table( $stats, 0, $season->term_id ); ?>
				</div>
			<?php endforeach; endif; ?>
			<div id="wpcm_team-0_season-0" class="tabs-panel">
				<?php wpcm_player_stats_table( $stats, 0, 0 ); ?>
			</div>
		</div>
		<div class="clear"></div>
		<script type="text/javascript">
			(function($) {

				var sum = 0;
				$('.stats-table-season .player-stats-manual-appearances').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-manual-appearances').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-manual-goals').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-manual-goals').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-manual-assists').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-manual-assists').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-manual-yellowcards').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-manual-yellowcards').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-manual-redcards').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-manual-redcards').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-manual-rating').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-manual-rating').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-manual-mvp').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-manual-mvp').val(sum);



				var sum = 0;
				$('.stats-table-season .player-stats-auto-appearances').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-auto-appearances').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-auto-goals').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-auto-goals').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-auto-assists').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-auto-assists').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-auto-yellowcards').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-auto-yellowcards').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-auto-redcards').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-auto-redcards').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-auto-rating').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-auto-rating').val(sum);

				var sum = 0;
				$('.stats-table-season .player-stats-auto-mvp').each(function(){
				    sum += Number($(this).val());
				});
				$('#wpcm_team-0_season-0 .player-stats-auto-mvp').val(sum);
			
			})(jQuery);
		</script>
	<?php }

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {

		$stats = $_POST['wpcm_stats'];
		array_walk_recursive( $stats, 'wpcm_array_values_to_int' );
		
		update_post_meta( $post_id, 'wpcm_stats', serialize( $stats ) );
	}
}