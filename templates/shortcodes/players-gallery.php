<?php
/**
 * Players Gallery
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div id="wpcm-players-gallery">

	<?php echo ( $title && ! $type == 'widget' ? '<h3>' . $title . '</h3>' : '' ); ?>

	<ul class="small-block-grid-2 medium-block-grid-<?php echo $columns; ?>">

		<?php
		$count = 0;
		foreach( $player_details as $player_detail ) {
			$count++;
			if ( $limit > 0 && $count > $limit )
				break; ?>

			<li class="wpcm-players-gallery-li">

				<div>

					<?php echo $player_detail['image']; ?>

					<h4><?php echo $player_detail['title']; ?></h4>

					<?php 
					if( $orderby != 'name' && $orderby != 'number' && $orderby != 'menu_order' ) { ?>
					
						<span class="victory-player-module-stat">

							<?php echo wpcm_get_player_stat( $player_detail, $orderby ); ?>

						</span>
					
					<?php
					} ?>

				</div>			

			</li>
		<?php
		} ?>

	</ul>

	<?php if ( isset( $linkpage ) ) { ?>
		<a href="<?php echo get_page_link( $linkpage ); ?>" class="wpcm-view-link">
			<?php echo $linktext; ?>
		</a>
	<?php } ?>
</div>