<?php
/**
 * The Template for displaying sponsor archives.
 *
 * Override this template by copying it to yourtheme/wpclubmanager/archive-spsonor.php
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header(); ?>

	<?php
		/**
		 * wpclubmanager_before_main_content hook
		 *
		 * @hooked wpclubmanager_output_content_wrapper - 10
		 */
		do_action( 'wpclubmanager_before_main_content' );
	?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<h1 class="wpcm-entry-title"><?php _e('Sponsors', 'wpclubmanager') ?></h1>

		<?php if ( have_posts() ) : ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php wpclubmanager_get_template_part( 'content', 'sponsor' ); ?>

			<?php endwhile; ?>

		<?php else : ?>

			<?php __('There are no sponsors to display yet!', 'wpclubmanager'); ?>

		<?php endif; ?>

	</article>

	<?php
		/**
		 * wpclubmanager_after_main_content hook
		 *
		 * @hooked wpclubmanager_output_content_wrapper_end - 10
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

<?php get_footer(); ?>