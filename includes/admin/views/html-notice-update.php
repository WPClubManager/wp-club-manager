<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div id="message" class="updated wpclubmanager-message">
	<p><?php _e( '<strong>WP Club Manager Data Update Required</strong> &#8211; We just need to update your install to the latest version', 'wp-club-manager' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_wpclubmanager', 'true', admin_url( 'admin.php?page=wpcm-settings' ) ) ); ?>" class="wpcm-update-now button-primary"><?php _e( 'Run the updater', 'wp-club-manager' ); ?></a></p>
</div>
<script type="text/javascript">
	jQuery('.wpcm-update-now').click('click', function(){
		var answer = confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'wp-club-manager' ); ?>' );
		return answer;
	});
</script>
