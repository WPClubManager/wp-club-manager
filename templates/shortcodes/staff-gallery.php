<?php
/**
 * Staff Gallery
 *
 * @author      Clubpress
 * @package     WPClubManager/Templates
 * @version     2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly ?>

<div id="wpcm-staff-gallery">

	<?php echo ( $title && ! $type == 'widget' ? '<h3>' . $title . '</h3>' : '' ); ?>

	<ul class="small-block-grid-2 medium-block-grid-<?php echo $columns; ?>">

		<?php
		$count = 0;
		foreach ( $employee_details as $employee_detail ) {
			++$count;
			if ( $limit > 0 && $count > $limit ) {
				break;
			}
			?>

			<li class="wpcm-staff-gallery-li">

				<div>

					<?php echo $employee_detail['image']; ?>

					<h4><?php echo $employee_detail['title']; ?></h4>

				</div>			

			</li>
			<?php
		}
		?>

	</ul>

	<?php if ( isset( $linkpage ) ) { ?>
		<a href="<?php echo get_page_link( $linkpage ); ?>" class="wpcm-view-link">
			<?php echo $linktext; ?>
		</a>
	<?php } ?>
</div>