/**
 * WPClubManager Admin JS
 */
jQuery(function(){

	jQuery('.stats-tabs a').click(function(){
		var t = jQuery(this).attr('href');
		
		jQuery(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
		jQuery(this).parent().parent().parent().find('.tabs-panel').hide();
		jQuery(t).show();

		return false;
	});

	jQuery('.wpcm-fixtures-shortcode tbody tr').click(function() {
    	if(jQuery(this).attr('data-url') !== undefined) {
    		document.location = jQuery(this).attr('data-url');
		}
	});

});