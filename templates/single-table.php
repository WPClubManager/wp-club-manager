<?php
/**
 * The Template for displaying all single league tables.
 *
 * Override this template by copying it to yourtheme/wpclubmanager/single-table.php
 *
 * @author      ClubPress
 * @package     WPClubManager/Templates
 * @version     2.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header(); ?>

	<?php
		/**
		 * wpclubmanager_before_main_content hook
		 *
		 * @hooked wpclubmanager_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked wpclubmanager_breadcrumb - 20
		 */
		do_action( 'wpclubmanager_before_main_content' );
	?>

		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<?php wpclubmanager_get_template_part( 'content', 'single-table' ); ?>

		<?php endwhile; // end of the loop. ?>

	<?php
		/**
		 * wpclubmanager_after_main_content hook
		 *
		 * @hooked wpclubmanager_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'wpclubmanager_after_main_content' );
	?>

	<?php
		/**
		 * wpclubmanager_sidebar hook
		 *
		 * @hooked wpclubmanager_get_sidebar - 10
		 */
		do_action( 'wpclubmanager_sidebar' );
	?>

<?php
get_footer();
