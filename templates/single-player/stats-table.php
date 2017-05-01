<?php
/**
 * Single Player - Stats Table
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

if ( array_key_exists( $team, $stats ) ):
	if ( array_key_exists( $season, $stats[$team] ) ):
		$stats = $stats[$team][$season];
	endif;
endif;

$stats_labels = wpcm_get_player_stats_labels();

$custom_stats = get_post_meta( $post->ID, '_wpcm_custom_player_stats', true ); ?>

<table>
	<thead>
		<tr>
			<?php
			foreach( $stats_labels as $key => $val ) { 

				if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' && array_key_exists( $key, $custom_stats ) ) { ?>

					<th><?php echo $val; ?></th>

				<?php }

			} ?>
		</tr>
	</thead>
	<tbody>
		<tr>
			<?php
			foreach( $stats_labels as $key => $val ) {

				if( $key == 'appearances' ) {

					if( get_option( 'wpcm_show_stats_appearances' ) == 'yes' && array_key_exists( 'appearances', $custom_stats ) ) { 

						if( get_option( 'wpcm_show_stats_subs' ) == 'yes' ) { 
							$subs = get_player_subs_total( $post->ID, $season, $team );
							if( $subs > 0 ) {
								$sub = ' <span class="sub-appearances">(' . $subs . ')</span>';
							}else{
								$sub = '';
							}
						} ?>
				
						<td><span data-index="appearances"><?php wpcm_stats_value( $stats, 'total', 'appearances' ); ?><?php echo ( get_option( 'wpcm_show_stats_subs' ) == 'yes' ? $sub : '' ); ?></span></td>

					<?php
					}

				} elseif( $key == 'rating' ) {

					$rating = get_wpcm_stats_value( $stats, 'total', 'rating' );
					$apps = get_wpcm_stats_value( $stats, 'total', 'appearances' );
					$avrating = wpcm_divide( $rating, $apps );

					if( get_option( 'wpcm_show_stats_rating' ) == 'yes' && array_key_exists( 'rating', $custom_stats ) ) { ?>
				
						<td><span data-index="rating"><?php echo sprintf( "%01.2f", round($avrating, 2) ); ?></span></td>

					<?php
					}

				} else { 

					if( get_option( 'wpcm_show_stats_' . $key ) == 'yes' && array_key_exists( $key, $custom_stats ) ) { ?>

						<td><span data-index="<?php echo $key; ?>"><?php wpcm_stats_value( $stats, 'total', $key ); ?></span></td>
						
					<?php }
				}
			} ?>
			
		</tr>
	</tbody>
</table>