<?php 
$reasons = array(
    		1 => '<li><label><input type="radio" name="wpcm_disable_reason" value="temporary"/>' . __('It\'s only temporary', 'wp-club-manager') . '</label></li>',
		2 => '<li><label><input type="radio" name="wpcm_disable_reason" value="stopped showing social buttons"/>' . __('The club doesn\'t need a website anymore', 'wp-club-manager') . '</label></li>',
		3 => '<li><label><input type="radio" name="wpcm_disable_reason" value="missing feature"/>' . __('I miss a feature', 'wp-club-manager') . '</label></li>
		<li><input type="text" name="wpcm_disable_text[]" value="" placeholder="Please describe the feature you need"/></li>',
		4 => '<li><label><input type="radio" name="wpcm_disable_reason" value="technical issue"/>' . __('Technical Issue', 'wp-club-manager') . '</label></li>
		<li><textarea name="wpcm_disable_text[]" placeholder="' . __('Can we help? Please describe your problem', 'wp-club-manager') . '"></textarea></li>',
		5 => '<li><label><input type="radio" name="wpcm_disable_reason" value="other plugin"/>' . __('I switched to another plugin', 'wp-club-manager') .  '</label></li>
		<li><input type="text" name="wpcm_disable_text[]" value="" placeholder="Name of the plugin"/></li>',
		6 => '<li><label><input type="radio" name="wpcm_disable_reason" value="other"/>' . __('Other reason', 'wp-club-manager') . '</label></li>
		<li><textarea name="wpcm_disable_text[]" placeholder="' . __('Please specify, if possible', 'wp-club-manager') . '"></textarea></li>',
    );
shuffle($reasons);
?>


<div id="wpcm-feedback-overlay" style="display: none;">
    <div id="wpcm-feedback-content">
	<form action="" method="post">
	    <h3><strong><?php _e('If you have a moment, can you please let us know why you are deactivating, your feedback will help us improve the plugin, thanks.', 'wp-club-manager'); ?></strong></h3>
	    <ul>
                <?php 
                foreach ($reasons as $reason){
                    echo $reason;
                }
                ?>
	    </ul>
	    <?php if ($email) : ?>
    	    <input type="hidden" name="wpcm_disable_from" value="<?php echo $email; ?>"/>
	    <?php endif; ?>
	    <input id="wpcm-feedback-submit" class="button button-primary" type="submit" name="wpcm_disable_submit" value="<?php _e('Submit & Deactivate', 'wp-club-manager'); ?>"/>
	    <a class="button"><?php _e('Only Deactivate', 'wp-club-manager'); ?></a>
	    <a class="wpcm-feedback-not-deactivate" href="#"><?php _e('Don\'t deactivate', 'wp-club-manager'); ?></a>
	</form>
    </div>
</div>