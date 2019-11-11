/**
 * WPClubManager Admin JS
 */
jQuery(document).ready(function() {
	// Next Match countdown
	jQuery('[data-countdown]').each(function() {
		var $this = jQuery(this), finalDate = jQuery(this).data('countdown');
		$this.countdown(finalDate, function(event) {
			var $this = jQuery(this).html(event.strftime(''
			+ '<div class="countdown-unit"><span>%-D</span> '+wpclubmanager_L10n.days+'%!D</div> '
			+ '<div class="countdown-unit"><span>%H</span> '+wpclubmanager_L10n.hrs+'</div> '
			+ '<div class="countdown-unit"><span>%M</span> '+wpclubmanager_L10n.mins+'</div> '
			+ '<div class="countdown-unit"><span>%S</span> '+wpclubmanager_L10n.secs+'</div> '));
		});
	});
});

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