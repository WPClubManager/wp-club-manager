<?php
/**
 * The template for displaying league table content in the single-table.php template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-single-table.php
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'wpclubmanager_before_single_table' ); ?>

	<header class="entry-header">
		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
	</header>

	<div class="wpcm-table-details wpcm-row">
		<?php echo do_shortcode( '[league_table id="' . get_the_ID() . '"]' ); ?>
	</div>

	<?php do_action( 'wpclubmanager_after_single_table' ); ?>

</article>
