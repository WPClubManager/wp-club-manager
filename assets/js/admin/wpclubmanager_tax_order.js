/*
*	YIKES Simple Taxonomy Ordering Scripts
*	@compiled by YIKES & Evan Herman
*	@since v0.1
*/
jQuery( document ).ready( function() {

	// if the tax table contains items
	if( ! jQuery( '#the-list' ).find( 'tr:first-child' ).hasClass( 'no-items' ) ) {
		
		jQuery( '#the-list' ).sortable({
			placeholder: "wpcm-drag-drop-tax-placeholder",
			axis: "y",
			// on start set a height for the placeholder to prevent table jumps
			start: function(event, ui) {
				var height = jQuery( ui.item[0] ).css( 'height' );
				jQuery( '.wpcm-drag-drop-tax-placeholder' ).css( 'height', height );
				console.log( height );
			},
			// update callback
			update: function( event, ui ) {
				// hide checkbox, append a preloader
				jQuery( ui.item[0] ).find( 'input[type="checkbox"]' ).hide().after( '<img src="' + localized_data.preloader_url + '" class="wpcm-simple-taxonomy-preloader" />' );
				
				// empty array				
				var updated_array = [];
				
				// store the updated tax ID
				jQuery( '#the-list' ).find( 'tr' ).each( function() {
					var tax_id = jQuery( this ).attr( 'id' ).replace( 'tag-', '' );
					updated_array.push( [ tax_id, jQuery( this ).index() ] );
				});
				
				// build the ajax data
				var data = {
					'action': 'update_taxonomy_order',
					'updated_array': updated_array 
				};
				
				// Run the ajax request
				jQuery.post( localized_data.ajax_url, data, function( response ) {
					jQuery( '.wpcm-simple-taxonomy-preloader' ).remove();
					jQuery( ui.item[0] ).find( 'input[type="checkbox"]' ).show();
				});
			},
		});
	
		jQuery( "#the-list" ).disableSelection();
			
	}

}); 