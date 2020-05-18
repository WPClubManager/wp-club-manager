jQuery(window).load(function(){

	// Edit prompt
	jQuery(function(){
		var changed = false;

		jQuery('input, textarea, select, checkbox').change(function(){
			changed = true;
		});

		jQuery('.wpcm-nav-tab-wrapper a').click(function(){
			if (changed) {
				window.onbeforeunload = function() {
				    return wpclubmanager_settings_params.i18n_nav_warning;
				}
			} else {
				window.onbeforeunload = '';
			}
		});

		jQuery('.submit input').click(function(){
			window.onbeforeunload = '';
		});
	});

	// Clear plugin transients button
	jQuery('button#wpcm_submit').click(function(e) {
		e.stopPropagation();
		jQuery('#wpcm_loading').show();
		jQuery('#wpcm_submit').attr('disabled', true);
		
		data = {
			action: 'wpcm_clear_transients',
			wpcm_nonce: wpclubmanager_settings_params.wpcm_nonce
		};

     	jQuery.post(ajaxurl, data, function (response) {
			jQuery('#wpcm_loading').hide();
			jQuery('button#wpcm_submit').attr('disabled', false);
		});	
		
		return false;
	});

	jQuery('.wpcm-default-time-picker').jquery_timepicker({
		timeFormat: 'H:i',
		step: '15',
		scrollDefault: '15:00'
	});
	
});