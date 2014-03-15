(function($) {
		tinymce.create('tinymce.plugins.matches', {
	
				init : function(ed, url){
						ed.addButton('wpcm_matches_button', {
								title : 'Insert Fixtures & Results',
								onclick : function() {
									// triggers the thickbox
									var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
									W = W - 80;
									H = H - 84;
									tb_show( 'Matches', 'admin-ajax.php?action=wpcm_matches_shortcode&width=' + W + '&height=' + H );
								}
						});
				}
		});
		
		tinymce.PluginManager.add('matches', tinymce.plugins.matches);
		
		// handles the click event of the submit button
		$('body').on('click', '#wpcm_matches-form #option-submit', function() {
			form = $('#wpcm_matches-form');
			// defines the options and their default values
			// again, this is not the most elegant way to do this
			// but well, this gets the job done nonetheless
			var options = { 
				'comp': '-1',
				'season': '-1',
				'team': '-1',
				'venue': '-1',
				'linktext': '',
				'linkpage': '',
				'title': ''
				};
			var shortcode = '[wpcm_matches';
			
			for( var index in options) {
				var value = form.find('#option-' + index).val();
				
				// attaches the attribute to the shortcode only if it's different from the default value
				if ( value !== options[index] )
					shortcode += ' ' + index + '="' + value + '"';
			}
			
			shortcode += ']';
			
			// inserts the shortcode into the active editor
			tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);
			
			// closes Thickbox
			tb_remove();
		});
		
		
		
})(jQuery);
