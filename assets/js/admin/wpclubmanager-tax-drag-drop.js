/**
 *	YIKES Simple Taxonomy Ordering Script.
 */
( function( $ ){

	$( document ).ready( function() {
		const base_index = parseInt( wpcm_taxonomy_ordering_data.paged ) > 0 ? ( parseInt( wpcm_taxonomy_ordering_data.paged ) - 1 ) * parseInt( $( '#' + wpcm_taxonomy_ordering_data.per_page_id ).val() ) : 0;
		const tax_table  = $( '#the-list' );

		// If the tax table contains items.
		if ( ! tax_table.find( 'tr:first-child' ).hasClass( 'no-items' ) ) {
			
			tax_table.sortable({
				placeholder: "wpcm-drag-drop-tax-placeholder",
				axis: "y",

				// On start, set a height for the placeholder to prevent table jumps.
				start: function( event, ui ) {
					const item  = $( ui.item[0] );
					const index = item.index();
					$( '.wpcm-drag-drop-tax-placeholder' ).css( 'height', item.css( 'height' ) );
				},
				// Update callback.
				update: function( event, ui ) {
					const item = $( ui.item[0] );

					// Hide checkbox, append a preloader.
					item.find( 'input[type="checkbox"]' ).hide().after( '<img src="' + wpcm_taxonomy_ordering_data.preloader_url + '" class="wpcm-taxonomy-preloader" />' );

					const taxonomy_ordering_data = [];

					tax_table.find( 'tr.ui-sortable-handle' ).each( function() {
						const ele       = $( this );
						const term_data = {
							term_id: ele.attr( 'id' ).replace( 'tag-', '' ),
							order: parseInt( ele.index() ) + 1
						}
						taxonomy_ordering_data.push( term_data );
					});
					
					// AJAX Data.
					const data = {
						'action': 'wpcm_update_taxonomy_order',
						'taxonomy_ordering_data': taxonomy_ordering_data,
						'base_index': base_index,
						'term_order_nonce': wpcm_taxonomy_ordering_data.term_order_nonce
					};
					
					// Run the ajax request.
					$.ajax({
						type: 'POST',
						url: window.ajaxurl,
						data: data,
						dataType: 'JSON',
						success: function( response ) {
							console.log( response );
							$( '.wpcm-taxonomy-preloader' ).remove();
							item.find( 'input[type="checkbox"]' ).show();
						}
					});
				}
			});
		}
	});
})( jQuery );
