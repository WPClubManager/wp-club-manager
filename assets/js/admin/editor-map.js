(function($) {
		tinymce.create('tinymce.plugins.map', {
	
				init : function(ed, url){
						ed.addButton('wpcm_map_button', {
								title : 'Insert Map',
								onclick : function() {
									// triggers the thickbox
									var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
									W = W - 80;
									H = H - 84;
									tb_show( 'Map', 'admin-ajax.php?action=wpcm_map_shortcode&width=' + W + '&height=' + H );
								}
						});
				}
		});
		
		tinymce.PluginManager.add('map', tinymce.plugins.map);
		
		// handles the click event of the submit button
		$('body').on('click', '#wpcm_map-form #option-submit', function() {
			form = $('#wpcm_map-form');
			// defines the options and their default values
			// again, this is not the most elegant way to do this
			// but well, this gets the job done nonetheless
			var options = {
				'width': '584',
				'height': '320',
				'address': '',
				'lat': '',
				'lng': '',
				'zoom': '13',
				'marker': '1'
				};
			var shortcode = '[wpcm_map';
			
			for( var index in options) {
				if ( index == 'marker' ) {
					values = form.find('[name="marker"]');
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
