<?php
/**
 * The template for displaying content in the single-sponsor.php template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-single-sponsor.php
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$link_url = get_post_meta( $post->ID, 'wpcm_link_url', true ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

    <div class="wpcm-sponsor-thumbnail">

		<?php the_post_thumbnail(); ?>
				
	</div>

	<div class="wpcm-sponsor-details">

		<h2 class="entry-title"><?php the_title(); ?></h2>

		<?php if( $link_url ) : ?>

			<div class="wpcm-sponsor-meta">

				<a href="<?php echo $link_url; ?>"><?php _e('Visit website', 'wpclubmanager'); ?></a>

			</div>

		<?php endif; ?>

	</div>

	<?php if ( get_the_content() ) : ?>

		<div class="wpcm-entry-content">

			<?php the_content(); ?>

		</div>

	<?php endif; ?>

</article>