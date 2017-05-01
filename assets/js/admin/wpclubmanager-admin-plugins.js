var strict;

jQuery(document).ready(function ($) {

    /**
     * DEACTIVATION FEEDBACK FORM
     */
    // show overlay when clicked on "deactivate"
    wpcm_deactivate_link = $('.wp-admin.plugins-php tr[data-slug="wp-club-manager"] .row-actions .deactivate a');
    wpcm_deactivate_link_url = wpcm_deactivate_link.attr('href');

    wpcm_deactivate_link.click(function (e) {
        e.preventDefault();

        // only show feedback form once per 30 days
        var c_value = wpcm_admin_get_cookie("wpcm_hide_deactivate_feedback");

        if (c_value === undefined) {
            $('#wpcm-feedback-overlay').show();
        } else {
            // click on the link
            window.location.href = wpcm_deactivate_link_url;
        }
    });
    // show text fields
    $('#wpcm-feedback-content input[type="radio"]').click(function () {
        // show text field if there is one
        $(this).parents('li').next('li').children('input[type="text"], textarea').show();
    });
    // send form or close it
    $('#wpcm-feedback-content .button').click(function (e) {
        e.preventDefault();
        // set cookie for 30 days
        var exdate = new Date();
        exdate.setSeconds(exdate.getSeconds() + 2592000);
        document.cookie = "wpcm_hide_deactivate_feedback=1; expires=" + exdate.toUTCString() + "; path=/";

        $('#wpcm-feedback-overlay').hide();
        if ('wpcm-feedback-submit' === this.id) {
            // Send form data
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'wpcm_send_feedback',
                    data: $('#wpcm-feedback-content form').serialize()
                },
                complete: function (MLHttpRequest, textStatus, errorThrown) {
                    // deactivate the plugin and close the popup
                    $('#wpcm-feedback-overlay').remove();
                    window.location.href = wpcm_deactivate_link_url;

                }
            });
        } else {
            $('#wpcm-feedback-overlay').remove();
            window.location.href = wpcm_deactivate_link_url;
        }
    });
    // close form without doing anything
    $('.wpcm-feedback-not-deactivate').click(function (e) {
        $('#wpcm-feedback-overlay').hide();
    });
    
    function wpcm_admin_get_cookie (name) {
	var i, x, y, wpcm_cookies = document.cookie.split( ";" );
	for (i = 0; i < wpcm_cookies.length; i++)
	{
		x = wpcm_cookies[i].substr( 0, wpcm_cookies[i].indexOf( "=" ) );
		y = wpcm_cookies[i].substr( wpcm_cookies[i].indexOf( "=" ) + 1 );
		x = x.replace( /^\s+|\s+$/g, "" );
		if (x === name)
		{
			return unescape( y );
		}
	}
}

}); // document ready