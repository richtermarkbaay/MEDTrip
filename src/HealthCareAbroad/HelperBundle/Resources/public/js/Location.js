var Location = {
        
    loadCitiesUrl: '',
	
    /**
     * @param jQuery DOM object
     */
    loadCities : function(elem, selectedCityId)
    {
        var countryId = elem.val();
        
        var citiesElem = elem.parent().next().find('select');
		if (citiesElem.length == 0) {
			citiesElem = elem.parent().parent().next().find('select');
		}
		citiesElem.attr("disabled", true).html('<option value="0">Loading...</option>');
		
		$.ajax({
		   url:  Location.loadCitiesUrl,
		   data: {'countryId': countryId, 'selectedCityId': selectedCityId },
		   dataType: 'json',
		   type: 'get',
		   success: function(cities){
		       citiesElem.empty();
	            $.each(cities, function(e){
	                citiesElem.append('<option value="'+ this.id +'" '+(this.id==selectedCityId ? 'selected': '')+' >' + this.name + '</option>')
	            });
	            citiesElem.attr('disabled', false);
		   }
		});
	
	}
	
	
}