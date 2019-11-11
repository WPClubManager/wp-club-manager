/*global ajaxurl, inlineEditPost, inlineEditL10n, wpclubmanager_admin */
jQuery(function( $ ) {
	$( '#the-list' ).on( 'click', '.editinline', function() {

		inlineEditPost.revert();

		var post_id = $( this ).closest( 'tr' ).attr( 'id' );

		post_id = post_id.replace( 'post-', '' );

		var $wpcm_inline_data = $( '#wpclubmanager_inline_' + post_id );

		var team        = $wpcm_inline_data.find( '.team' ).text(),
			comp  		= $wpcm_inline_data.find( '.comp' ).text(),
			season     	= $wpcm_inline_data.find( '.season').text(),
			played      = $wpcm_inline_data.find( '.played' ).text(),
			venue       = $wpcm_inline_data.find( '.venue' ).text();
			home_goals  = $wpcm_inline_data.find( '.home-goals' ).text();
			away_goals  = $wpcm_inline_data.find( '.away-goals' ).text();
			referee     = $wpcm_inline_data.find( '.referee' ).text();
			attendance  = $wpcm_inline_data.find( '.attendance' ).text();
			friendly    = $wpcm_inline_data.find( '.friendly' ).text(),

			fname    	= $wpcm_inline_data.find( '.fname' ).text(),
			lname    	= $wpcm_inline_data.find( '.lname' ).text(),
			player_club = $wpcm_inline_data.find( '.player_club' ).text(),
			staff_club  = $wpcm_inline_data.find( '.staff_club' ).text(),


		$( 'input[name="wpcm_goals[total][home]"]', '.inline-edit-row' ).val( home_goals );
		$( 'input[name="wpcm_goals[total][away]"]', '.inline-edit-row' ).val( away_goals );
		$( 'input[name="wpcm_referee"]', '.inline-edit-row' ).val( referee );
		$( 'input[name="wpcm_attendance"]', '.inline-edit-row' ).val( attendance );
		$( 'select[name="wpcm_team"] option, select[name="wpcm_season"] option, select[name="wpcm_comp"] option, select[name="wpcm_venue"] option' ).removeAttr( 'selected' );
		$( 'select[name="wpcm_team"] option[value="' + team + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
		$( 'select[name="wpcm_season"] option[value="' + season + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
		$( 'select[name="wpcm_comp"] option[value="' + comp + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
		$( 'select[name="wpcm_venue"] option[value="' + venue + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );

		$( 'input[name="_wpcm_firstname"]', '.inline-edit-row' ).val( fname );
		$( 'input[name="_wpcm_lastname"]', '.inline-edit-row' ).val( lname );
		$( 'select[name="_wpcm_player_club"] option' ).removeAttr( 'selected' );
		$( 'select[name="_wpcm_player_club"] option[value="' + player_club + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );
		$( 'select[name="_wpcm_staff_club"] option' ).removeAttr( 'selected' );
		$( 'select[name="_wpcm_staff_club"] option[value="' + staff_club + '"]', '.inline-edit-row' ).attr( 'selected', 'selected' );


		if ( '1' === played ) {
			$( 'input[name="wpcm_played"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
		} else {
			$( 'input[name="wpcm_played"]', '.inline-edit-row' ).removeAttr( 'checked' );
		}

		if ( '1' === friendly ) {
			$( 'input[name="wpcm_friendly"]', '.inline-edit-row' ).attr( 'checked', 'checked' );
		} else {
			$( 'input[name="wpcm_friendly"]', '.inline-edit-row' ).removeAttr( 'checked' );
		}

	});
	

	$( '#wpbody' ).on( 'click', '#doaction, #doaction2', function() {
		$( 'input.text', '.inline-edit-row' ).val( '' );
		$( '#wpclubmanager-fields' ).find( 'select' ).prop( 'selectedIndex', 0 );
		$( '#wpclubmanager-fields-bulk' ).find( '.inline-edit-group .change-input' ).hide();
	});

});
