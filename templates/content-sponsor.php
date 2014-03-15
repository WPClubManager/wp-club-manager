<?php
/**
 * The template for displaying sponsors in the archive-sponsor.php template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-sponsor.php
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

global $wpclubmanager, $post;

$link_url = get_post_meta( $post->ID, 'wpcm_link_url', true );?>

<div class="wpcm-sponsor-post wpcm-row">

	<div class="wpcm-sponsor-image">

		<?php the_post_thumbnail(); ?>
				
	</div>

	<div class="wpcm-sponsor-details">

		<h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

		<?php if( $link_url ) : ?>

			<div class="wpcm-sponsor-meta">

				<a href="<?php echo $link_url; ?>"><?php _e('Visit website', 'wpclubmanager'); ?></a>

			</div>

		<?php endif; ?>

		<?php if ( get_the_content() ) : ?>

			<div class="wpcm-entry-content">

				<?php the_content( __('Read more...', 'wpclubmanager' ) ); ?>

			</div>

		<?php endif; ?>

	</div>

</div>