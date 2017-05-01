<?php
/**
 * Results Widget
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<li class="fixture">
	<div class="fixture-meta">
		<?php if ( $show_team ) { ?>
			<div class="team">
				<span><?php echo $team[0]; ?></span>
			</div>
		<?php }
		if ( $show_comp ) { ?>
			<div class="competition">
				<span><?php echo $comp[0]; ?>&nbsp;<?php echo $comp[2]; ?></span>
			</div>
		<?php } ?>
	</div>
	<a href="<?php echo get_permalink(); ?>">
		<div class="clubs">
			<h4 class="home-clubs">
				<div class="home-logo"><?php echo $badges[0]; ?></div>
				<?php echo $sides[0]; ?>
				<div class="score"><?php echo ( $played && $show_score ? $score[1] : '' ); ?></div>
			</h4>
			<h4 class="away-clubs">
				<div class="away-logo"><?php echo $badges[1]; ?></div>
				<?php echo $sides[1]; ?>
				<div class="score"><?php echo ( $played && $show_score ? $score[2] : '' ); ?></div>
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
	</div>
</li>