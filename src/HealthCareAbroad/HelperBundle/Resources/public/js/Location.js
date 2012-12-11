var Location = {
        
    loadCitiesUrl: '',
    
    citiesDropdownElement: null,
	
    /**
     * @param jQuery DOM object
     */
    loadCities : function(elem, selectedCityId)
    {
        var countryId = elem.val();
        citiesElem = Location.citiesDropdownElement;
        if (!citiesElem) {
            citiesElem = elem.parents('form').find('select.city_dropdown').first();
        }
		var emptyValue = citiesElem.children(':first');

		citiesElem.attr("disabled", true).html('<option value="0">Loading...</option>');

		$.ajax({
		   url:  Location.loadCitiesUrl,
		   data: {'countryId': countryId, 'selectedCityId': selectedCityId },
		   dataType: 'json',
		   type: 'get',
		   success: function(cities){
			   citiesElem.empty().append(emptyValue);
	           $.each(cities, function(e){
	               citiesElem.append('<option value="'+ this.id +'" '+(this.id==selectedCityId ? 'selected': '')+' >' + this.name + '</option>')
	           });
	            citiesElem.attr('disabled', false);
		   }
		});
	}	
}