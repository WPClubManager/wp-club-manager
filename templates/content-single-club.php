<?php
/**
 * The template for displaying product content in the single-club.php template
 *
 * Override this template by copying it to yourtheme/wpclubmanager/content-single-club.php
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.4.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;

$address = '';
$cap = '';
$venues = get_the_terms( $post->ID, 'wpcm_venue' );

if ( is_array( $venues ) ) {
	$venue = reset($venues);
	$t_id = $venue->term_id;
	$venue_meta = get_option( "taxonomy_term_$t_id" );
	if( is_array( $venue_meta ) ) {
		if( array_key_exists('wpcm_address', $venue_meta) ) {
			$address = $venue_meta['wpcm_address'];
		}
		if( array_key_exists('wpcm_capacity', $venue_meta) ) {
			$cap = $venue_meta['wpcm_capacity'];
		}
	}
}

$color = get_post_meta( $post->ID, '_wpcm_club_primary_color', true );
if( $color ) {
	$primary_color_bg = ' style="background-color:' . $color . ';color:#fff;text-shadow: 0 0 3px #000;"';
} else {
	$primary_color_bg = '';
}
$honours = get_post_meta( $post->ID, '_wpcm_club_honours', true );
$formed = get_post_meta( $post->ID, '_wpcm_club_formed', true );

do_action( 'wpclubmanager_before_single_club' ); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="wpcm-club-details wpcm-row">

		<h2 class="entry-title">
			<span>
				<?php
				if ( has_post_thumbnail() ) {			
					the_post_thumbnail( 'crest-medium' );
				} else {			
					apply_filters( 'wpclubmanager_club_image', sprintf( '<img src="%s" alt="Placeholder" />', wpcm_placeholder_img_src() ), $post->ID );		
				} ?>
			</span>
			<?php the_title(); ?>
		</h2>

		<table>
			<tbody>
				<?php if( $formed ) { ?>
					<tr>
						<th><?php _e('Formed', 'wp-club-manager'); ?></th>
						<td><?php echo $formed; ?></td>
					</tr>
				<?php } ?>
				<tr>
					<th><?php _e('Ground', 'wp-club-manager'); ?></th>
					<td><?php echo $venue->name; ?></td>
				</tr>

				<?php
				if ( $cap ) { ?>
					<tr class="capacity">
						<th><?php _e('Capacity', 'wp-club-manager'); ?></th>
						<td><?php echo $cap; ?></td>
					</tr>
				<?php
				}

				if ( $address ) { ?>
					<tr class="address">
						<th><?php _e('Address', 'wp-club-manager'); ?></th>
						<td><?php echo stripslashes( nl2br( $address ) );?></td>
					</tr>
				<?php
				}

				if ( $venue->description ) { ?>
					<tr class="description">
						<th><?php _e('Ground Info', 'wp-club-manager'); ?></th>
						<td><?php echo stripslashes( nl2br( $venue->description ) ); ?></td>
					</tr>
				<?php
				} ?>
				<?php if( $honours ) { ?>
					<tr>
						<th><?php _e('Honours', 'wp-club-manager'); ?></th>
						<td><?php echo stripslashes( nl2br( $honours ) ); ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>

		<?php do_action( 'wpclubmanager_after_single_club_details' ); ?>

	</div>

	<?php if ( get_the_content() ) : ?>

		<div class="wpcm-entry-content">

			<?php the_content(); ?>

		</div>

	<?php endif; ?>

	<?php if( $address ) { ?>

		<div class="wpcm-club-map">

			<?php echo do_shortcode( '[wpcm_map address="' . $address . '" width="100%" height="260" marker="1"]' ); ?>

		</div>

	<?php } ?>

	<h3><?php printf( __( 'Matches against %s', 'wp-club-manager'), $post->post_title ); ?></h3>

	<?php
	$matches = wpcm_head_to_heads( $post->ID );
	$outcome = wpcm_head_to_head_count( $matches ); ?>

	<ul class="wpcm-h2h-list">
		<li class="wpcm-h2h-list-p"<?php echo $primary_color_bg; ?>>
			<span class="wpcm-h2h-list-count"><?php echo $outcome['total']; ?></span> <span class="wpcm-h2h-list-desc">games</span>
		</li>
		<li class="wpcm-h2h-list-w"<?php echo $primary_color_bg; ?>>
			<span class="wpcm-h2h-list-count"><?php echo $outcome['wins']; ?></span> <span class="wpcm-h2h-list-desc">wins</span>
		</li>
		<li class="wpcm-h2h-list-d"<?php echo $primary_color_bg; ?>>
			<span class="wpcm-h2h-list-count"><?php echo $outcome['draws']; ?></span> <span class="wpcm-h2h-list-desc">draws</span>
		</li>
		<li class="wpcm-h2h-list-l"<?php echo $primary_color_bg; ?>>
			<span class="wpcm-h2h-list-count"><?php echo $outcome['losses']; ?></span> <span class="wpcm-h2h-list-desc">losses</span>
		</li>
	</ul>

	<ul class="wpcm-matches-list">

	<?php foreach( $matches as $match ) {

		$played = get_post_meta( $match->ID, 'wpcm_played', true );
		$timestamp = strtotime( $match->post_date );
		$time_format = get_option( 'time_format' );
		$class = wpcm_get_match_outcome( $match->ID );	
		$comp = wpcm_get_match_comp( $match->ID );
		$sides = wpcm_get_match_clubs( $match->ID );
		$side1 = $sides[0];
		$side2 = $sides[1];
		$result = wpcm_get_match_result( $match->ID ); ?>

		<li class="wpcm-matches-list-item <?php echo $class; ?>">
			<a href="<?php echo get_post_permalink( $match->ID, false, true ); ?>" class="wpcm-matches-list-link">
				<span class="wpcm-matches-list-col wpcm-matches-list-date">
					<?php echo date_i18n( 'D d M', $timestamp ); ?>	
				</span>
				<span class="wpcm-matches-list-col wpcm-matches-list-club1">
					<?php echo $side1; ?>
				</span>
				<span class="wpcm-matches-list-col wpcm-matches-list-status">
					<span class="wpcm-matches-list-<?php echo ( $played ? 'result' : 'time' ); ?> <?php echo $class; ?>">
						<?php echo ( $played ? $result[0] : date_i18n( $time_format, $timestamp ) ); ?>
					</span>
				</span>
				<span class="wpcm-matches-list-col wpcm-matches-list-club2">
					<?php echo $side2; ?>
				</span>
				<span class="wpcm-matches-list-col wpcm-matches-list-info">
					<?php echo $comp[1]; ?>
				</span>
			</a>
		</li>

	<?php } ?>

	</ul>

	<?php do_action( 'wpclubmanager_after_single_club_content' ); ?>

</article>

<?php do_action( 'wpclubmanager_after_single_club' ); ?>