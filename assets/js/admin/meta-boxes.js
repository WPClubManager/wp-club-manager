jQuery(function( $ ) {

    // Chosen selects
    $( 'select.chosen_select' ).chosen({
        disable_search_threshold: 10,
        width: '220px'
    });

    $( 'select.chosen_select_outcome' ).chosen({
        disable_search_threshold: 10,
        width: 'auto',
        placeholder_text_single: ' '
    });

    $( 'select.chosen_select_dob' ).chosen({
        disable_search_threshold: 32
    });

    $( 'select.chosen_select_nostd' ).chosen({
        allow_single_deselect: 'true'
    });

    $( 'select.wpcm-chosen-multiple' ).chosen({
        width: '100%'
    });

    // league table columns order
    if ( $( '#input-order' ).length ) {
        $( 'select.wpcm-chosen-multiple' ).on( 'change', function( e ) {
            var selected = $( this ).get( 0 );

            setTimeout( function() {
                var selection = ChosenOrder.getSelectionOrder( selected );
                $( '#input-order' ).val( selection ).toString().split( ',' );
            });
        });

        var selected = $( 'select.wpcm-chosen-multiple' ).get( 0 );

        ChosenOrder.setSelectionOrder( selected, $( '#input-order' ).val().split( ',' ), true );
    }

    // stats tabs
    $( '.wpcm_stats-tabs a' ).click( function() {
        var t = $( this ).attr( 'href' );
        $( this ).parent().addClass( 'tabs' ).siblings( 'li' ).removeClass( 'tabs' );
        $( this ).parent().parent().parent().find( '.tabs-panel' ).hide();
        $( t ).show();
        return false;
    });

    // player stats season dropdown.
    $( '.type_box' ).appendTo( '#wpclubmanager-player-stats .hndle span' );

    $( function() {
        // Prevent inputs in meta box headings opening/closing contents.
        $( '#wpclubmanager-player-stats' ).find( '.hndle' ).unbind( 'click.postboxes' );

        $( '#wpclubmanager-player-stats' ).on( 'click', '.hndle', function( event ) {

            // If the user clicks on some form input inside the h3 the box should not be toggled.
            if ( $( event.target ).filter( 'input, option, label, select, .chosen-drop' ).length ) {
                return;
            }

            $( '#wpclubmanager-player-stats' ).toggleClass( 'closed' );
        });
    });

    // player season select
    $( document ).on( 'change', '.wpcm-player-season-select', function() {

        var target = $( this ).data( 'target' );
        var show = $( 'option:selected', this ).data( 'show' );
        $( target ).children().addClass( 'hidden' );
        $( show ).removeClass( 'hidden' );
    });
    $( '.wpcm-player-season-select' ).trigger( 'change' );


    // match results box
    //$('#wpclubmanager-match-report .postarea').hide();
    $( '#wpclubmanager-match-result' ).on( 'change', '#wpcm_played', function() {
        var played = $( this ).prop( 'checked' );
        if ( played ) {
            $( '#wpclubmanager-match-report' ).show( 'fast' );
            $( '#wpclubmanager-match-result #results-table' ).show( 'fast' );
            //$('.post-type-wpcm_match #poststuff #postexcerpt').hide('fast');
        } else {
            //$('.post-type-wpcm_match #poststuff #postexcerpt').show('fast');
            $( '#wpclubmanager-match-result #results-table' ).hide( 'fast' );
            $( '#wpclubmanager-match-report' ).hide( 'fast' );
        }
    });
    $( '#wpclubmanager-match-result #wpcm_played' ).change();

    $( '#wpclubmanager-match-result' ).on( 'change', '#wpcm_shootout', function() {
        var shootout = $( this ).prop( 'checked' );

        if ( shootout ) {
            $( '#wpclubmanager-match-result .wpcm-results-shootout' ).show( 'fast' );
        } else {
            $( '#wpclubmanager-match-result .wpcm-results-shootout' ).hide( 'fast' );
        }
    });
    $( '#wpclubmanager-match-result #wpcm_shootout' ).change();

    $( '#wpclubmanager-match-result' ).on( 'change', '#_wpcm_postponed', function() {
        var postponed_result = $( '#_wpcm_postponed' ).prop( 'checked' );

        if ( postponed_result ) {
            $( '.wpcm-postponed-result' ).show( 'fast' );
            $( '#wpclubmanager-match-result #results-table' ).hide( 'fast' );
        } else {
            $( '.wpcm-postponed-result' ).hide( 'fast' );
            //$('#wpclubmanager-match-result #results-table').show('fast');
        }
    });

    $( '#wpclubmanager-match-result #_wpcm_postponed' ).change();

    // match player lineup
    $( '#wpcm_players table .names input[type="checkbox"]' ).on( 'change', function() {
        var player_id = $( this ).attr( 'data-player' );
        $( this ).closest( 'tr' ).find( 'input[type="number"]' ).prop( 'disabled', !$( this ).prop( 'checked' ) );
        $( this ).closest( 'tr' ).find( 'input[data-card="yellow"], input[data-card="red"]' ).prop( 'disabled', !$( this ).prop( 'checked' ) );
        $( this ).closest( 'tr' ).find( 'select' ).prop( 'disabled', !$( this ).prop( 'checked' ) );
        $( this ).closest( 'tr' ).find( 'input[type="radio"]' ).prop( 'disabled', !$( this ).prop( 'checked' ) );
    });

    $( '#wpcm_players table td.mvp input[type="radio"]' ).click( function() {
        $( 'td.mvp input[type="radio"]' ).prop( 'checked', false );
        $( this ).prop( 'checked', true );
    });
    $( '#wpcm_players table td.captain input[type="radio"]' ).click( function() {
        $( 'td.captain input[type="radio"]' ).prop( 'checked', false );
        $( this ).prop( 'checked', true );
    });

    var updateCounter = function() {
        var len    = $( '#wpcm_lineup input.player-select:checked' ).length;
        var sublen = $( '#wpcm_subs input.player-select:checked' ).length;

        if ( len > 0 ) {
            $( '#wpcm_lineup .counter' ).text( '' + len + '' );
        } else {
            $( '#wpcm_lineup .counter' ).text( '0' );
        }
        if ( sublen > 0 ) {
            $( '#wpcm_subs .counter' ).text( '' + sublen + '' );
        } else {
            $( '#wpcm_subs .counter' ).text( '0' );
        }
    };
    $( '#wpcm_lineup .counter, #wpcm_subs .counter' ).text( function() {
        updateCounter();
    });
    $( '#wpcm_lineup input:checkbox, #wpcm_subs input:checkbox' ).on( 'change', function() {
        updateCounter();
    });

    $( '.wpcm-table-add-row' ).click( function() {
        var count  = $( '#wpcm-table-stats table th input.stats-rows' ).val(),
        	id     = $( '#id option:selected' ).text(),
        	val    = $( '#id option:selected' ).val(),
       		markup = '<tr class="count-row"><td><input type="checkbox" name="record"></td><td class="pos"></td><td><input type="hidden" name="wpcm_table_clubs[]" value="' + val + '">' + id + '</td><td colspan="' + count + '"></td></tr>';

      	$( '#wpcm-table-stats table tbody' ).append( markup );

      	$( '#wpcm-table-stats table tr.count-row' ).each( function( i ) {
            $( 'td', this ).eq( 1 ).html( i + 1 );
        });
    });

    $( '.wpcm-table-delete-row' ).click( function() {
        $( '#wpcm-table-stats table tbody' ).find( 'input[name="record"]' ).each( function() {
            if ( $( this ).is( ':checked' ) ) {
                $( this ).parents( 'tr' ).prev( 'tr' ).remove();
                $( this ).parents( 'tr' ).next( 'tr' ).remove();
                $( this ).parents( 'tr' ).remove();
            }
        });
    });

    $( '#wpcm-table-stats input' ).change( function() {
        var index = $( this ).data( 'index' ),
        	club  = $( this ).closest( 'tr' ).data( 'club' ),
        	value = 0;

        $( this ).closest( 'table' ).find( 'tbody' ).each( function() {
            var total = parseInt( $( this ).find( 'tr[data-club="' + club + '"] td.wpcm-table-stats-total input[data-index="' + index + '"]' ).val() ),
            	auto  = parseInt( $( this ).find( 'tr[data-club="' + club + '"] td.wpcm-table-stats-auto input[data-index="' + index + '"]' ).val() );

          	value = total - auto;
        });

        $( this ).closest( 'table' ).find( 'tr[data-club="' + club + '"] td.wpcm-table-stats-manual input[data-index="' + index + '"]' ).val( value );
    });

    $( '#wpcm-table-stats input[data-index="f"], #wpcm-table-stats input[data-index="a"]' ).change( function() {
        var club  = $( this ).closest( 'tr' ).data( 'club' ),
		    value = 0;

        $( this ).closest( 'table' ).find( 'tbody' ).each( function() {
            var f = parseInt( $( this ).find( 'tr[data-club="' + club + '"] td.wpcm-table-stats-total input[data-index="f"]' ).val() ),
            	a = parseInt( $( this ).find( 'tr[data-club="' + club + '"] td.wpcm-table-stats-total input[data-index="a"]' ).val() );

          	if ( $( 'body' ).hasClass( 'footy' ) ) {
                value = ( f / a ) * 100;
            } else {
                value = f - a;
            }
        });

        $( this ).closest( 'table' ).find( 'tr[data-club="' + club + '"] td.wpcm-table-stats-total input[data-index="gd"]' ).val( value );

        $( this ).closest( 'table' ).find( 'tbody' ).each( function() {
            var total  = parseInt( $( this ).find( 'tr[data-club="' + club + '"] td.wpcm-table-stats-total input[data-index="gd"]' ).val() ),
            		auto   = parseInt( $( this ).find( 'tr[data-club="' + club + '"] td.wpcm-table-stats-auto input[data-index="gd"]' ).val() ),
            		manual = total - auto;
        });

        $( this ).closest( 'table' ).find( 'tr[data-club="' + club + '"] td.wpcm-table-stats-manual input[data-index="gd"]' ).val( manual );
    });

    // player sorting
    var itemList = $( '.wpcm-sortable' );

    itemList.sortable({
        cursor: 'move',
        update: function( event, ui ) {
            $( '#loading-animation' ).show(); // Show the animate loading gif while waiting

            var opts = {
                url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
                type: 'POST',
                async: true,
                cache: false,
                dataType: 'json',
                data: {
                    action: 'item_sort', // Tell WordPress how to handle this ajax request
                    order: itemList.sortable( 'toArray' ).toString() // Passes ID's of list items in  1,3,2 format
                },
                success: function( response ) {
                    $( '#loading-animation' ).hide(); // Hide the loading animation
                    return;
                },
                error: function( xhr, textStatus, e ) { // This can be expanded to provide more information
                    alert( e );
                    alert( 'There was an error saving the updates' );
                    $( '#loading-animation' ).hide(); // Hide the loading animation
                    return;
                }
            };

            $.ajax( opts );
        }
	});

	// players roster selection
	jQuery('.wpcm-player-roster-add-row').click(function(){
		var id = jQuery('.player-id option:selected').text();
		var val = jQuery('.player-id option:selected').val();
		var markup = '<tr><td><input type="checkbox" name="record"></td><td><input type="hidden" name="wpcm_roster_players[]" value="' + val + '">' + id + '</td></tr>';
		jQuery('#wpcm-player-roster-stats table tbody').append(markup);
		jQuery('.wpcm-player-roster-delete-row').removeClass('hidden-button');
	});
	jQuery('.wpcm-player-roster-delete-row').click(function(){
		jQuery('#wpcm-player-roster-stats table tbody').find('input[name="record"]').each(function(){
			if(jQuery(this).is(':checked')){
				jQuery(this).parents('tr').remove();
			}
		});
	});

	// staff roster selection
	jQuery('.wpcm-staff-roster-add-row').click(function(){
		var id = jQuery('.staff-id option:selected').text();
		var val = jQuery('.staff-id option:selected').val();
		var markup = '<tr><td><input type="checkbox" name="record"></td><td><input type="hidden" name="wpcm_roster_staff[]" value="' + val + '">' + id + '</td></tr>';
		jQuery('#wpcm-staff-roster-stats table tbody').append(markup);
		jQuery('.wpcm-staff-roster-delete-row').removeClass('hidden-button');
	});
	jQuery('.wpcm-staff-roster-delete-row').click(function(){
		jQuery('#wpcm-staff-roster-stats table tbody').find('input[name="record"]').each(function(){
			if(jQuery(this).is(':checked')){
				jQuery(this).parents('tr').remove();
			}
		});
	});

    jQuery( '.colorpick' ).iris({
		change: function( event, ui ) {
			jQuery( this ).parent().find( '.colorpickpreview' ).css({ backgroundColor: ui.color.toString() });
		},
		hide: true,
		border: true
	}).click( function() {
		jQuery( '.iris-picker' ).hide();
		jQuery( this ).closest( 'p' ).find( '.iris-picker' ).show();
	});

	jQuery( 'body' ).click( function() {
		jQuery( '.iris-picker' ).hide();
	});

	jQuery( '.colorpick' ).click( function( event ) {
		event.stopPropagation();
	});

	// Video embed
	jQuery(".wpcm-add-video").click(function() {
		jQuery(this).closest("fieldset").hide().siblings(".wpcm-video-field").show();
		return false;
	});

	// Removing video embed
	jQuery(".wpcm-remove-video").click(function() {
		jQuery(this).closest("fieldset").hide().siblings(".wpcm-video-adder").show().siblings(".wpcm-video-field").find("input").val(null);
		return false;
	});

	// Date Picker
	$( document.body ).on( 'wpcm-init-datepickers', function() {
		$( '.wpcm-date-picker' ).datepicker( {
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true
		});
		$( '.wpcm-birth-date-picker' ).datepicker( {
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			yearRange:'-90:+0'
		});
	}).trigger( 'wpcm-init-datepickers' );

	$('.wpcm-time-picker').jquery_timepicker({
		timeFormat: 'H:i',
		step: '15'
	});

	$(document).ready(function(){
		$(".combify-input").combify();
	});
});