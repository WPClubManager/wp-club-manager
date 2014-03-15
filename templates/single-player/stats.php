<?php
/**
 * Single Player Stats Table
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $wpclubmanager;

$stats = get_wpcm_player_stats( $post->ID );
$seasons = get_the_terms( $post->ID, 'wpcm_season' ); ?>

<ul class="stats-tabs">
				
	<li class="tabs"><a href="#wpcm_team-0_season-0"><?php printf( __( 'All %s', 'wpclubmanager' ), __( 'Seasons', 'wpclubmanager' ) ); ?></a></li>

	<?php if( is_array( $seasons ) ): foreach( $seasons as $season ): ?>

		<li><a href="#wpcm_team-0_season-<?php echo $season->term_id; ?>"><?php echo $season->name; ?></a></li>

	<?php endforeach; endif; ?>
	
</ul>
			
<?php if( is_array( $seasons ) ): foreach( $seasons as $season ): ?>
				
	<div id="wpcm_team-0_season-<?php echo $season->term_id; ?>" class="tabs-panel" style="display: none;">
					
		<?php wpcm_profile_stats_table( $stats, 0, $season->term_id ); ?>
				
	</div>
	
<?php endforeach; endif; ?>
			
<div id="wpcm_team-0_season-0" class="tabs-panel">
				
	<?php wpcm_profile_stats_table( $stats, 0, 0 ); ?>
			
</div>