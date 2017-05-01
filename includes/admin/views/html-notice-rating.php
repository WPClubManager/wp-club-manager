<?php
/**
 * Admin View: Notice - Plugin Rating
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="wpcm-five-star update-nag" style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);">
	<p>Awesome, you've been using <strong>WP Club Manager</strong> plugin for a while now, I hope you've found it useful for your club.<br>
	Please would you consider giving it a <strong>5-star rating</strong> on Wordpress?</br>
    This will help to spread its popularity and ensure I can keep working on and improving the plugin.
    <br><br>Your help would be very much appreciated, thanks.<br>
    <em>~Leon</em>
    <ul>
        <li><a href="https://wordpress.org/support/plugin/wp-club-manager/reviews/?filter=5#new-post" class="thankyou button button-primary" target="_new" title="" style="margin-right:10px;">It's helped our club, I'd like to rate it</a></li>
        <li><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpcm-hide-notice', 'wpcm_rating' ), 'wpclubmanager_hide_notices_nonce', '_wpcm_notice_nonce' ) ); ?>" class="button" title="I already did" style="">I have already rated it</a></li>
        <li><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'wpcm-hide-notice', 'wpcm_rating' ), 'wpclubmanager_hide_notices_nonce', '_wpcm_notice_nonce' ) ); ?>" class="" title="No, not good enough">No thanks, i don't want to rate it!</a></li>
    </ul>
</div>