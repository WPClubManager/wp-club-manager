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
	
});