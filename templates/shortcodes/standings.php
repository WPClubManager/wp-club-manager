<?php
/**
 * Standings
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     1.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="wpcm-standings-shortcode wpcm-standings">

	<?php echo ( $title && ! $type == 'widget' ? '<h3>' . $title . '</h3>' : ''); ?>
	
	<table>
		<thead>
			<tr>
				<th></th>
				<th></th>
				<?php foreach( $stats as $stat ) { ?>
					
					<th class="<?php echo $stat; ?>"><?php echo $stats_labels[$stat]; ?></th>
				
				<?php } ?>
			
			</tr>
		</thead>
		<tbody>
		
		<?php
		$rownum = 0;
		foreach ( $clubs as $club ) {
			$rownum ++;
			$club_stats = $club->wpcm_stats; ?>
			
			<tr class="<?php echo ( $center == $club->ID ? 'highlighted ' : '' ) . ( $rownum % 2 == 0 ? 'even' : 'odd' ) . ( $rownum == $limit ? ' last' : '' ); ?>">

				<td class="pos">
					<?php echo $club->place; ?>
				</td>

				<td class="club">
					<?php 
					echo $club->thumb;
					echo ( $link_club == 1 ? '<a href="' . get_the_permalink( $club->ID ) . '">' : '' );
					echo $club->post_title;
					echo ( $link_club == 1 ? '</a>' : '' );
					?>
				</td>

				<?php foreach( $stats as $stat ) { ?>
					
					<td class="<?php echo $stat; ?>"><?php echo $club_stats[$stat]; ?></td>

				<?php } ?>

			</tr>

		<?php } ?>

		</tbody>
	</table>

	<?php if ( isset( $linkpage ) ) { ?>
		<a href="<?php echo get_page_link( $linkpage ); ?>" class="wpcm-view-link">
			<?php echo $linktext; ?>
		</a>
	<?php } ?>
	
</div>