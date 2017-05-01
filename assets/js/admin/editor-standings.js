(function($) {
		tinymce.create('tinymce.plugins.standings', {
	
				init : function(ed, url){
						ed.addButton('wpcm_standings_button', {
								title : 'Insert Standings',
								onclick : function() {
									// triggers the thickbox
									var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
									W = W - 80;
									H = H - 84;
									tb_show( 'Standings', 'admin-ajax.php?action=wpcm_standings_shortcode&width=' + W + '&height=' + H );
								}
						});
				}
		});
		
		tinymce.PluginManager.add('standings', tinymce.plugins.standings);
		
		// handles the click event of the submit button
		$('body').on('click', '#wpcm_standings-form #option-submit', function() {
			form = $('#wpcm_standings-form');
			// defines the options and their default values
			// again, this is not the most elegant way to do this
			// but well, this gets the job done nonetheless
			var options = { 
				'limit': '7',
				'comp': '-1',
				'season': '-1',
				'orderby': 'pts',
				'order': 'DESC',
				'linktext': '',
				'linkpage': '',
				'stats': 'p,w,d,l,f,a,gd,pts',
				'title': '',
				'thumb': 1,
				'linkclub': 1,
				'excludes': '',
				};
			var shortcode = '[wpcm_standings';
			
			for( var index in options) {
				if ( index == 'stats' ) {
					values = form.find('[name="stats[]"]');
					var stats = new Array();
					$.each( values, function( key, val) {
						if ( $(val).attr( 'checked' ))
							stats.push( $(val).val() );
					});
					value = stats.join( ',' );
				} else if ( index == 'thumb' ) {
					values = form.find('[name="thumb"]');
					var stats = new Array();
					$.each( values, function( key, val) {
						if ( $(val).attr( 'checked' ))
							stats.push( $(val).val() );
					});
					value = stats;
				} else if ( index == 'linkclub' ) {
					values = form.find('[name="linkclub"]');
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
