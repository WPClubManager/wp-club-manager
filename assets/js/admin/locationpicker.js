jQuery(document).ready(function($){
	jQuery(".wpcm-location-picker").locationpicker({
		location: {
			latitude: Number(jQuery(".wpcm-latitude").val()),
			longitude: Number(jQuery(".wpcm-longitude").val())
		},
		radius: 0,
		inputBinding: {
	        latitudeInput: jQuery(".wpcm-latitude"),
	        longitudeInput: jQuery(".wpcm-longitude"),
	        locationNameInput: jQuery(".wpcm-address")
	    },
	    addressFormat: null,
	    enableAutocomplete: true
	});
});