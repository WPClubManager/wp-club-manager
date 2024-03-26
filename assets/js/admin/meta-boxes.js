jQuery( function($){

	// Chosen selects
	jQuery("select.chosen_select").chosen({
		disable_search_threshold: 10,
		width: '220px'
	});

	jQuery("select.chosen_select_outcome").chosen({
		disable_search_threshold: 10,
		width: 'auto',
		placeholder_text_single: ' '
	});

	jQuery("select.chosen_select_dob").chosen({
		disable_search_threshold: 32
	});

	jQuery("select.chosen_select_nostd").chosen({
		allow_single_deselect: 'true'
	});

	jQuery("select.wpcm-chosen-multiple").chosen({
		width: '100%'
	});

	// league table columns order
	if( jQuery('#input-order').length ) {
		jQuery('select.wpcm-chosen-multiple').on('change', function(e) {
			var selected = jQuery(this).get(0);
			setTimeout(function() {
				var selection = ChosenOrder.getSelectionOrder(selected);
				jQuery('#input-order').val(selection).toString().split(',');
			});
		});
		var selected = jQuery('select.wpcm-chosen-multiple').get(0);
		ChosenOrder.setSelectionOrder(selected, jQuery('#input-order').val().split(','), true);
	}

	// stats tabs
	jQuery('.wpcm_stats-tabs a').click(function(){
		var t = jQuery(this).attr('href');
		jQuery(this).parent().addClass('tabs').siblings('li').removeClass('tabs');
		jQuery(this).parent().parent().parent().find('.tabs-panel').hide();
		jQuery(t).show();
		return false;
	});

	// player stats season dropdown.
	jQuery( '.type_box' ).appendTo( '#wpclubmanager-player-stats .hndle' );

		jQuery( function() {
			// Prevent inputs in meta box headings opening/closing contents.
			jQuery( '#wpclubmanager-player-stats' ).find( '.hndle' ).unbind( 'click.postboxes' );

			jQuery( '#wpclubmanager-player-stats' ).on( 'click', '.hndle', function( event ) {

				// If the user clicks on some form input inside the h3 the box should not be toggled.
				if ( jQuery( event.target ).filter( 'input, option, label, select, .chosen-drop' ).length ) {
					return;
				}

				jQuery( '#wpclubmanager-player-stats' ).toggleClass( 'closed' );
			});
		});

	// player season select
	jQuery( document ).on('change', '.wpcm-player-season-select', function() {

		var target = jQuery(this).data('target');
		var show = jQuery("option:selected", this).data('show');
		jQuery(target).children().addClass('hidden');
		jQuery(show).removeClass('hidden');
	});
	jQuery('.wpcm-player-season-select').trigger('change');


	// match results box
	//jQuery('#wpclubmanager-match-report .postarea').hide();
	jQuery('#wpclubmanager-match-result').on('change', '#wpcm_played', function() {
		played = jQuery(this).prop('checked');
		if (played) {
			jQuery('#wpclubmanager-match-report').show('fast');
			jQuery('#wpclubmanager-match-result #results-table').show('fast');
			//jQuery('.post-type-wpcm_match #poststuff #postexcerpt').hide('fast');
		} else {
			//jQuery('.post-type-wpcm_match #poststuff #postexcerpt').show('fast');
			jQuery('#wpclubmanager-match-result #results-table').hide('fast');
			jQuery('#wpclubmanager-match-report').hide('fast');
		}
	});
	jQuery('#wpclubmanager-match-result #wpcm_played').change();

	jQuery('#wpclubmanager-match-result').on('change', '#wpcm_shootout', function() {
		shootout = jQuery(this).prop('checked');
		if (shootout) {
			jQuery('#wpclubmanager-match-result .wpcm-results-shootout').show('fast');
		} else {
			jQuery('#wpclubmanager-match-result .wpcm-results-shootout').hide('fast');
		}
	});
	jQuery('#wpclubmanager-match-result #wpcm_shootout').change();

	jQuery('#wpclubmanager-match-result').on('change', '#_wpcm_postponed', function() {
		postponed_result = jQuery('#_wpcm_postponed').prop('checked');
		if (postponed_result) {
			jQuery('.wpcm-postponed-result').show('fast');
			jQuery('#wpclubmanager-match-result #results-table').hide('fast');
		} else {
			jQuery('.wpcm-postponed-result').hide('fast');
			//jQuery('#wpclubmanager-match-result #results-table').show('fast');
		}
	});
	jQuery('#wpclubmanager-match-result #_wpcm_postponed').change();

	// match player lineup
	jQuery('#wpcm_players table .names input[type="checkbox"]').on('change', function() {
		player_id = jQuery(this).attr('data-player');
		jQuery(this).closest('tr').find('input[type="number"]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery(this).closest('tr').find('input[data-card="yellow"], input[data-card="red"]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery(this).closest('tr').find('select').prop('disabled', !jQuery(this).prop('checked'));
		jQuery(this).closest('tr').find('input[type="radio"]').prop('disabled', !jQuery(this).prop('checked'));
	});

	jQuery('#wpcm_players table td.mvp input[type="radio"]').click(function() {
	    jQuery('td.mvp input[type="radio"]').prop('checked', false);
	    jQuery(this).prop('checked', true);
	});
	jQuery('#wpcm_players table td.captain input[type="radio"]').click(function() {
	    jQuery('td.captain input[type="radio"]').prop('checked', false);
	    jQuery(this).prop('checked', true);
	});

	updateCounter = function() {
    	var len = jQuery("#wpcm_lineup input.player-select:checked").length;
    	var sublen = jQuery("#wpcm_subs input.player-select:checked").length;
		if(len>0) {
			jQuery("#wpcm_lineup .counter").text(''+len+'');
		}
		else {
			jQuery("#wpcm_lineup .counter").text('0');
		}
		if(sublen>0) {
			jQuery("#wpcm_subs .counter").text(''+sublen+'');
		}
		else {
			jQuery("#wpcm_subs .counter").text('0');
		}
	}
	jQuery("#wpcm_lineup .counter, #wpcm_subs .counter").text(function() {
		updateCounter();
	});
	jQuery("#wpcm_lineup input:checkbox, #wpcm_subs input:checkbox").on("change", function() {
		updateCounter();
	});

	jQuery('.wpcm-table-add-row').click(function(){
		var count = jQuery('#wpcm-table-stats table th input.stats-rows' ).val();
		var id = jQuery('#id option:selected').text();
		var val = jQuery('#id option:selected').val();
		var markup = '<tr class="count-row"><td><input type="checkbox" name="record"></td><td class="pos"></td><td><input type="hidden" name="wpcm_table_clubs[]" value="' + val + '">' + id + '</td><td colspan="' + count + '"></td></tr>';
		jQuery('#wpcm-table-stats table tbody').append(markup);
		jQuery('#wpcm-table-stats table tr.count-row').each(function (i) {
			jQuery("td", this).eq(1).html(i + 1);
		});
	});

	jQuery('.wpcm-table-delete-row').click(function(){
		jQuery('#wpcm-table-stats table tbody').find('input[name="record"]').each(function(){
			if(jQuery(this).is(':checked')){
				jQuery(this).parents('tr').prev('tr').remove();
				jQuery(this).parents('tr').next('tr').remove();
				jQuery(this).parents('tr').remove();
			}
		});
	});

	jQuery('#wpcm-table-stats input').change(function() {
		index = jQuery(this).attr('data-index');
		club = jQuery(this).closest('tr').attr('data-club');
		value = 0;
		jQuery(this).closest('table').find('tbody').each(function() {
			total = parseInt(jQuery(this).find('tr[data-club="' + club + '"] td.wpcm-table-stats-total input[data-index="' + index + '"]').val());
			auto = parseInt(jQuery(this).find('tr[data-club="' + club + '"] td.wpcm-table-stats-auto input[data-index="' + index + '"]').val());
			value = total - auto;
		});
		jQuery(this).closest('table').find('tr[data-club="' + club + '"] td.wpcm-table-stats-manual input[data-index="' + index + '"]').val(value);
	});

	jQuery('#wpcm-table-stats input[data-index="f"],#wpcm-table-stats input[data-index="a"]').change(function() {
		club = jQuery(this).closest('tr').attr('data-club');
		value = 0;
		jQuery(this).closest('table').find('tbody').each(function() {
			f = parseInt(jQuery(this).find('tr[data-club="' + club + '"] td.wpcm-table-stats-total input[data-index="f"]').val());
			a = parseInt(jQuery(this).find('tr[data-club="' + club + '"] td.wpcm-table-stats-total input[data-index="a"]').val());
			if (jQuery('body').hasClass('footy')) {
				value = (f / a) * 100;
			} else {
				value = f - a;
			}
		});
		jQuery(this).closest('table').find('tr[data-club="' + club + '"] td.wpcm-table-stats-total input[data-index="gd"]').val(value);

		jQuery(this).closest('table').find('tbody').each(function() {
			total = parseInt(jQuery(this).find('tr[data-club="' + club + '"] td.wpcm-table-stats-total input[data-index="gd"]').val());
			auto = parseInt(jQuery(this).find('tr[data-club="' + club + '"] td.wpcm-table-stats-auto input[data-index="gd"]').val());
			manual = total - auto;
		});
		jQuery(this).closest('table').find('tr[data-club="' + club + '"] td.wpcm-table-stats-manual input[data-index="gd"]').val(manual);

	});

	// player sorting
	var itemList = jQuery('.wpcm-sortable');

    itemList.sortable({
		handle: ".names .name, .names .dashicons-move",
    	cursor: 'move',
        update: function(event, ui) {
            jQuery('#loading-animation').show(); // Show the animate loading gif while waiting

            opts = {
                url: ajaxurl, // ajaxurl is defined by WordPress and points to /wp-admin/admin-ajax.php
                type: 'POST',
                async: true,
                cache: false,
                data:{
                    action: 'item_sort', // Tell WordPress how to handle this ajax request
	                wpcm_nonce: $('#wpclubmanager_meta_nonce').val(),
	                order: itemList.sortable('toArray').toString() // Passes ID's of list items in  1,3,2 format
				},
				dataType: 'JSON',
                success: function(response) {
                    jQuery('#loading-animation').hide(); // Hide the loading animation
                    return;
                },
                error: function(xhr,textStatus,e) {  // This can be expanded to provide more information
                    jQuery('#loading-animation').hide(); // Hide the loading animation
                    return;
                }
            };
            jQuery.ajax(opts);
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
		$(".wpcm-match-players-table td input").on("click", function() {
			$(this).focus();
		});
	});

});
