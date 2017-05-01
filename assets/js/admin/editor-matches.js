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
				'title': '',
				'type': '1',
				'format': '1',
				'limit': '0',
				'comp': '-1',
				'season': '-1',
				'team': '-1',
				'month': '-1',
				'venue': '-1',
				'show_team': 0,
				'show_comp': 1,
				'linktext': '',
				'linkpage': '',
				'thumb': 1,
				'link_club': 1,
				};
			var shortcode = '[wpcm_matches';

			for( var index in options) {
				if ( index == 'thumb' ) {
					values = form.find('[name="thumb"]');
					var stats = new Array();
					$.each( values, function( key, val) {
						if ( $(val).attr( 'checked' ))
							stats.push( $(val).val() );
					});
					value = stats;
				} else if ( index == 'link_club' ) {
					values = form.find('[name="link_club"]');
					var stats = new Array();
					$.each( values, function( key, val) {
						if ( $(val).attr( 'checked' ))
							stats.push( $(val).val() );
					});
					value = stats;
				} else if ( index == 'show_team' ) {
					values = form.find('[name="show_team"]');
					var stats = new Array();
					$.each( values, function( key, val) {
						if ( $(val).attr( 'checked' ))
							stats.push( $(val).val() );
					});
					value = stats;
				}else if ( index == 'show_comp' ) {
					values = form.find('[name="show_comp"]');
					var stats = new Array();
					$.each( values, function( key, val) {
						if ( $(val).attr( 'checked' ))
							stats.push( $(val).val() );
					});
					value = stats;
				} else {
					var value = form.find('#option-' + index).val();
				}
				
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
