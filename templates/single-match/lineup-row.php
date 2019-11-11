<?php
/**
 * Single Match - Lineup Row
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post; ?>

<tr>
	<?php if( get_option('wpcm_lineup_show_shirt_numbers') == 'yes' ) { ?>
		<th class="shirt-number"><?php echo $count; ?></th>
	<?php } ?>
	
	<th class="name">
		<div>
			<?php if( get_option('wpcm_player_profile_show_number') == 'yes' && get_post_meta( $key, 'wpcm_number', true ) == true) {
				echo get_post_meta( $key, 'wpcm_number', true );
			}
			if( get_option('wpcm_results_show_image') == 'yes' ) {
				echo wpcm_get_player_thumbnail( $key, 'player_thumbnail', array( 'class' => 'lineup-thumb' ) );
			} ?>
			<a href="<?php echo get_permalink( $key ); ?>"><?php echo get_player_title( $key, get_option( 'wpcm_name_format' ) ); ?></a>
			<?php echo ( get_post_meta( $post->ID, '_wpcm_match_captain', true ) == $key ? ' (c) ' : '' );
			if ( isset( $value['mvp'] ) ) { ?>
				<span class="mvp" title="<?php _e( 'Player of Match', 'wp-club-manager' ); ?>">&#9733;</span>
			<?php }

			if ( array_key_exists( 'sub', $value ) && $value['sub'] > 0 ) { ?>
				<span class="sub">&larr; <?php echo get_player_title( $value['sub'], get_option( 'wpcm_name_format' ) ); ?></span>
			<?php } ?>
		</div>
	</th>

	<?php
	foreach( $value as $key => $stat ) {
		
		if( $stat == '0' || $stat == null ) {
			$stat = '&mdash;';
		}
		
		if( ! in_array( $key, wpcm_exclude_keys() ) && get_option( 'wpcm_show_stats_' . $key ) == 'yes' && get_option( 'wpcm_match_show_stats_' . $key ) == 'yes' ) { ?>

			<td class="stats <?php echo $key; ?>"><?php echo $stat; ?></td>

		<?php
		}
	}

	if( get_option( 'wpcm_show_stats_greencards' ) == 'yes' || get_option( 'wpcm_show_stats_yellowcards' ) == 'yes' || get_option( 'wpcm_show_stats_blackcards' ) == 'yes' || get_option( 'wpcm_show_stats_redcards' ) == 'yes' ) { ?>

		<td class="notes">

			<?php if( get_option( 'wpcm_show_stats_greencards' ) == 'yes' && isset( $value['greencards'] ) && get_option( 'wpcm_show_stats_greencards' ) == 'yes' ) { ?>
					
				<span class="greencard" title="<?php _e( 'Green Card', 'wp-club-manager' ); ?>"><?php _e( 'Green Card', 'wp-club-manager' ); ?></span>

			<?php }
			
			if ( get_option( 'wpcm_show_stats_yellowcards' ) == 'yes' && isset( $value['yellowcards'] ) && get_option( 'wpcm_show_stats_yellowcards' ) == 'yes' ) { ?>
				
				<span class="yellowcard" title="<?php _e( 'Yellow Card', 'wp-club-manager' ); ?>"><?php _e( 'Yellow Card', 'wp-club-manager' ); ?></span>

			<?php }

			if ( get_option( 'wpcm_show_stats_blackcards' ) == 'yes' && isset( $value['blackcards'] ) && get_option( 'wpcm_show_stats_blackcards' ) == 'yes' ) { ?>
				
				<span class="blackcard" title="<?php _e( 'Black Card', 'wp-club-manager' ); ?>"><?php _e( 'Black Card', 'wp-club-manager' ); ?></span>

			<?php }
			
			if ( get_option( 'wpcm_show_stats_redcards' ) == 'yes' && isset( $value['redcards'] ) && get_option( 'wpcm_show_stats_redcards' ) == 'yes' ) { ?>

				<span class="redcard" title="<?php _e( 'Red Card', 'wp-club-manager' ); ?>"><?php _e( 'Red Card', 'wp-club-manager' ); ?></span>

			<?php } ?>

		</td>

	<?php } ?>

</tr>