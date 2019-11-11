<?php
/**
 * Fixtures Widget
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

global $post; ?>

<li class="fixture">
	<div class="fixture-meta">
		<?php if( $show_team ) { ?>
			<div class="team">
				<span><?php echo $team[0]; ?></span>
			</div>
		<?php }
		if( $show_comp ) { ?>
			<div class="competition">
				<span><?php echo $comp[0]; ?>&nbsp;<?php echo $comp[2]; ?></span>
			</div>
		<?php } ?>
	</div>
	<a href="<?php echo get_post_permalink( $post->ID, false, true ); ?>">
		<div class="clubs">
			<h4 class="home-clubs">
				<div class="home-logo"><?php echo $badges[0]; ?></div>
				<?php echo $sides[0]; ?>
			</h4>
			<h4 class="away-clubs">
				<div class="away-logo"><?php echo $badges[1]; ?></div>
				<?php echo $sides[1]; ?>
			</h4>
		</div>
	</a>
	<div class="wpcm-date">
		<div class="kickoff">
			<?php 
			echo ( $show_date ? the_time('j M Y') : '' );
			echo ( $show_time ? ' - ' : '' );
			echo ( $show_time ? the_time('g:i a') : '' );
			?>
		</div>
		<?php if( $show_countdown ) { ?>
			<div class="wpcm-countdown">
				<div class="wpcm-ticker-countdown" data-countdown="<?php echo $post->post_date; ?>"></div>
			</div>
		<?php } ?>			
	</div>
</li>