/**
 * WPClubManager Admin JS
 */
jQuery(function(){

	jQuery("body").click(function(){
		jQuery('.wpcm_error_tip').fadeOut('100', function(){ jQuery(this).remove(); } );
	});

	// Tooltips
	jQuery(".tips, .help_tip").tipTip({
    	'attribute' : 'data-tip',
    	'fadeIn' : 50,
    	'fadeOut' : 50,
    	'delay' : 200
    });

    // Chosen selects
	jQuery("select.chosen_select").chosen({
		width: '220px',
		disable_search_threshold: 18
	});

});