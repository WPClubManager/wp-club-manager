<?php
/**
 * Single Player Bio
 *
 * @author 		ClubPress
 * @package 	WPClubManager/Templates
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpclubmanager, $post;

$post_id = $post->ID;
$match = get_post( $post_id );
$comps = get_the_terms( $match->ID, 'wpcm_comp' ); ?>

<div class="wpcm-match-comp">

<?php if ( is_array( $comps ) ) { ?>
				
	<?php foreach ( $comps as $comp ) { ?>

	<span>

		<?php echo $comp->name; ?>

	</span>

	<?php }

} ?>

</div>