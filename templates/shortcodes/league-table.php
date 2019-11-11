<?php
/**
 * Standings
 *
 * @author 		Clubpress
 * @package 	WPClubManager/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<div class="wpcm-standings-shortcode wpcm-standings">

	<?php echo ( $title && ! $type == 'widget' ? '<h3>' . $title . '</h3>' : ''); ?>
	
	<table>
		<thead>
			<tr>
				<th></th>
				<th></th>
				<?php
				foreach( $columns as $column ) { ?>
					
					<th class="<?php echo $column; ?>"><?php echo $stats_labels[$column]; ?></th>
				
				<?php } ?>
			
			</tr>
		</thead>
		<tbody>
		
		<?php foreach ( $clubs as $club ) {
			
			$club_stats = $club->wpcm_stats; ?>
			
			<tr class="<?php echo ( $default_club == $club->ID ? 'highlighted ' : '' ); ?>">

				<td class="pos">
					<?php echo $club->place; ?>
				</td>

				<td class="club">
					<?php 
					echo $club->thumb;
					if( $default_club == $club->ID ) {
						if( $abbr == 1 ) {
							$club_abbr = get_club_abbreviation( $club->ID );
							echo $club_abbr;
						} else {
							if ( $team_label ) {
								echo $team_label;
							} else {
								echo $club->post_title;
							}
						}
					} else {
						if( $abbr == 1 ) {
							echo ( $link_club == 1 ? '<a href="' . get_the_permalink( $club->ID ) . '">' : '' );
							$club_abbr = get_club_abbreviation( $club->ID );
							echo $club_abbr;
							echo ( $link_club == 1 ? '</a>' : '' );
						} else {
							echo ( $link_club == 1 ? '<a href="' . get_the_permalink( $club->ID ) . '">' : '' );
							echo $club->post_title;
							echo ( $link_club == 1 ? '</a>' : '' );
						}
					} ?>
				</td>

				<?php foreach( $columns as $column ) { ?>
					
					<td class="<?php echo $column; ?>"><?php echo $club_stats[$column]; ?></td>

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