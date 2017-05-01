jQuery(window).load(function(){
	jQuery('#wpcm-form').submit(function() {
		jQuery('#wpcm_loading').show();
		jQuery('#wpcm_submit').attr('disabled', true);
		
      data = {
      	action: 'wpcm_clear_transients',
      	wpcm_nonce: wpcm_vars.wpcm_nonce
      };

     	jQuery.post(ajaxurl, data, function (response) {
			jQuery('#wpcm_loading').hide();
			jQuery('#wpcm_submit').attr('disabled', false);
		});	
		
		return false;
	});
});