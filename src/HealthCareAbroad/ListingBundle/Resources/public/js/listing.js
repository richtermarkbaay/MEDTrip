var Listing = {
	
	test : function(elem) {
		alert(elem.attr('id'));
	},

	loadCities : function(countryId)
	{
		$('#listing_locations_0_country').attr('disabled', false);
	}

}