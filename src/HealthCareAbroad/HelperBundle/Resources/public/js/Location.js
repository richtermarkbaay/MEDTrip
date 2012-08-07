var Location = {
	
	loadCities : function(elem)
	{
		var countryId = elem.val();
		var citiesElem = elem.parent().next().find('select');
		if (citiesElem.length == 0) {
			citiesElem = elem.parent().parent().next().find('select');
		}
		$.getJSON('/app_dev.php/location/load-cities/' + countryId, function(cities){
			var len = cities.length;
			citiesElem.empty();
			for(var i=0; i< len; i++) {
				citiesElem.append('<option value="'+ cities[i].id +'">' + cities[i].name + '</option>');
			}
			citiesElem.attr('disabled', false);

		});
	}
}