(function($) {
		tinymce.create('tinymce.plugins.staff', {
	
				init : function(ed, url){
						ed.addButton('wpcm_staff_button', {
								title : 'Insert Staff',
								onclick : function() {
									// triggers the thickbox
									var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
									W = W - 80;
									H = H - 84;
									tb_show( 'Staff', 'admin-ajax.php?action=wpcm_staff_shortcode&width=' + W + '&height=' + H );
								}
						});
				}
		});
		
		tinymce.PluginManager.add('staff', tinymce.plugins.staff);
		
		// handles the click event of the submit button
		$('body').on('click', '#wpcm_staff-form #option-submit', function() {
			form = $('#wpcm_staff-form');
			// defines the options and their default values
			// again, this is not the most elegant way to do this
			// but well, this gets the job done nonetheless
			var options = { 
				'limit': '-1',
				'season': '-1',
				'team': '-1',
				'jobs': '-1',
				'orderby': 'name',
				'order': 'ASC',
				'linktext': '',
				'linkpage': '',
				'stats': 'flag,number,name,job,email,phone,age',
				'title': ''
				};
			var shortcode = '[wpcm_staff';
			
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
