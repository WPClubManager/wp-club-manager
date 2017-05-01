(function($) {
		tinymce.create('tinymce.plugins.players', {
	
				init : function(ed, url){
						ed.addButton('wpcm_players_button', {
								title : 'Insert Players',
								onclick : function() {
									// triggers the thickbox
									var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
									W = W - 80;
									H = H - 84;
									tb_show( 'Players', 'admin-ajax.php?action=wpcm_players_shortcode&width=' + W + '&height=' + H );
								}
						});
				}
		});
		
		tinymce.PluginManager.add('players', tinymce.plugins.players);
		
		// handles the click event of the submit button
		$('body').on('click', '#wpcm_players-form #option-submit', function() {
			form = $('#wpcm_players-form');
			// defines the options and their default values
			// again, this is not the most elegant way to do this
			// but well, this gets the job done nonetheless
			var options = { 
				'limit': '-1',
				'season': '-1',
				'team': '-1',
				'position': '-1',
				'orderby': 'number',
				'order': 'ASC',
				'linktext': '',
				'linkpage': '',
				'stats': 'flag,number,name,position,age',
				'title': ''
				};
			var shortcode = '[wpcm_players';
			
			for( var index in options) {
				if ( index == 'stats' ) {
					values = form.find('[name="stats[]"]');
					var stats = new Array();
					$.each( values, function( key, val) {
						if ( $(val).attr( 'checked' ))
							stats.push( $(val).val() );
					});
					value = stats.join( ',' );
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
