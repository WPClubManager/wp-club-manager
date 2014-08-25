<?php
/**
 * The template for displaying product content in the single-player.php template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-single-player.php
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php do_action( 'wpclubmanager_before_single_player' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="wpcm-player-info wpcm-row">

	    <?php
			/**
			 * wpclubmanager_single_player_image hook
			 *
			 * @hooked wpclubmanager_template_single_player_images - 5
			 */
			do_action( 'wpclubmanager_single_player_image' );
		?>

		<div class="wpcm-profile-meta">

			<?php
				/**
				 * wpclubmanager_single_player_info hook
				 *
				 * @hooked wpclubmanager_template_single_player_title - 5
				 * @hooked wpclubmanager_template_single_player_meta - 10
				 */
				do_action( 'wpclubmanager_single_player_info' );
			?>

		</div>

	</div>

	<div class="wpcm-profile-stats wpcm-row">

		<?php
			/**
			 * wpclubmanager_single_player_stats hook
			 *
			 * @hooked wpclubmanager_template_single_player_stats - 5
			 */
			do_action( 'wpclubmanager_single_player_stats' );
		?>

	</div>

	<div class="wpcm-profile-bio wpcm-row">

		<?php
			/**
			 * wpclubmanager_single_player_bio hook
			 *
			 * @hooked wpclubmanager_template_single_player_bio - 5
			 */
			do_action( 'wpclubmanager_single_player_bio' );
		?>

	</div>

	<?php do_action( 'wpclubmanager_after_single_player' ); ?>

</article>