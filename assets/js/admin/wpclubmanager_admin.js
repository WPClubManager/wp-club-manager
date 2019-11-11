/**
 * WPClubManager Admin JS
 */
jQuery(function($){

	jQuery("body").click(function(){
		jQuery('.wpcm_error_tip').fadeOut('100', function(){ jQuery(this).remove(); } );
	});

    // Chosen selects
	jQuery("select.chosen_select").chosen({
		width: '220px',
		disable_search_threshold: 18
	});

	// Chosen multiselects
	jQuery("select.wpcm-chosen-multiple").chosen({
		width: '100%'
	});
	if( jQuery('#input-order').length ) {
		jQuery('select.wpcm-chosen-multiple').on('change', function(e) {
			var selected = jQuery(this).get(0);
			setTimeout(function() {
				var selection = ChosenOrder.getSelectionOrder(selected);
				jQuery('#input-order').val(selection).toString().split(',');
			});
		});
		var selected = jQuery('select.wpcm-chosen-multiple').get(0);
		ChosenOrder.setSelectionOrder(selected, jQuery('#input-order').val().split(','), true);
	}
		   

	jQuery('.wpcm_stats-tabs a').click(function(){
		var t = jQuery(this).attr('href');	
		jQuery(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
		jQuery(this).parent().parent().parent().find('.tabs-panel').hide();
		jQuery(t).show();
		return false;
	});

	jQuery(".wpcm-address").keypress(function(event) {
		return event.keyCode != 13;
	});

});