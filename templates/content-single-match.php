<?php
/**
 * The template for displaying match details in the single-match.php template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-single-match.php
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$played = get_post_meta( $post->ID, 'wpcm_played', true ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="<?php ( $played ? 'result' : 'fixture' ); ?>">

	    <div class="wpcm-match-info wpcm-row">

	     	<?php
				/**
				 * wpclubmanager_single_match_info hook
				 *
				 * @hooked wpclubmanager_template_single_match_home_club_badge - 5
				 * @hooked wpclubmanager_template_single_match_date - 10
				 * @hooked wpclubmanager_template_single_match_comp - 20
				 * @hooked wpclubmanager_template_single_match_away_club_badge - 30
				 */
				do_action( 'wpclubmanager_single_match_info' );
			?>

	    </div>

	    <div class="wpcm-match-fixture wpcm-row">

	    	<?php
				/**
				 * wpclubmanager_single_match_fixture hook
				 *
				 * @hooked wpclubmanager_template_single_match_home_club - 5
				 * @hooked wpclubmanager_template_single_match_score - 10
				 * @hooked wpclubmanager_template_single_match_away_club - 20
				 */
				do_action( 'wpclubmanager_single_match_fixture' );
			?>

	    </div>

	    <div class="wpcm-match-meta wpcm-row">

			<div class="wpcm-match-meta-left">

				<?php
					/**
					 * wpclubmanager_single_match_venue hook
					 *
					 * @hooked wpclubmanager_template_single_match_venue - 5
					 * @hooked wpclubmanager_template_single_match_attendance - 10
					 */
					do_action( 'wpclubmanager_single_match_venue' );
				?>

			</div>

			<div class="wpcm-match-meta-right">

				<?php
					/**
					 * wpclubmanager_single_match_meta hook
					 *
					 * @hooked wpclubmanager_template_single_match_team - 5
					 * @hooked wpclubmanager_template_single_match_referee - 20
					 */
					do_action( 'wpclubmanager_single_match_meta' );
				?>

			</div>

	    </div>

	    <div class="wpcm-match-details wpcm-row">

			<?php
				/**
				 * wpclubmanager_single_match_report hook
				 *
				 * @hooked wpclubmanager_template_single_match_report - 5
				 * @hooked wpclubmanager_template_single_match_video - 10
				 */
				do_action( 'wpclubmanager_single_match_report' );
			?>

			<?php
				/**
				 * wpclubmanager_single_match_details hook
				 *
				 * @hooked wpclubmanager_template_single_match_lineup - 5
				 * @hooked wpclubmanager_template_single_match_venue_info - 10
				 */
				do_action( 'wpclubmanager_single_match_details' );
			?>

	    </div>

	</div>

</article>