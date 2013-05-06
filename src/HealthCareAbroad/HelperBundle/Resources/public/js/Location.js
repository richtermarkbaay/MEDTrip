var Location = {
        
    loadCitiesUrl: '',
    loadNonGlobalCities: 0,
    
    citiesDropdownElement: null,
	
    /**
     * @param jQuery DOM object
     */
    loadCities : function(elem, selectedCityId)
    {
    	var countryId = elem.val();

    	customSelectWrapper = elem.parents('form').find('input[data-custom-type=city_dropdown]').parent();

    	valueElem = customSelectWrapper.find('input[type=hidden]');
    	inputElem = customSelectWrapper.find('input[type=text]');
    	customSelectList = customSelectWrapper.find('ul:first');

		inputElem.attr('disabled', true).val('Loading...').next().attr('disabled', true);
		customSelectList.empty().hide();
		//valueElem.focus();

		$.ajax({
		   url:  Location.loadCitiesUrl,
		   data: {'countryId': countryId, 'selectedCityId': selectedCityId, 'loadNonGlobalCities': Location.loadNonGlobalCities },
		   type: 'get',
		   success: function(response){
			   customSelectList.html(response.html);
		       // NOTE: in Chrome, dictionary key-value pairs are reordered if key is numeric 
			   /**
		       citiesElem.empty().append(emptyValue);
			   $.each(cities, function(e){
	               citiesElem.append('<option value="'+ this.id +'" '+(this.id==selectedCityId ? 'selected': '')+' >' + this.name + '</option>')
	           });
	           **/
			   inputElem.attr('disabled', false).next().attr('disabled', false);

			   customSelectList
			   	.children()
			   		.click(function(){
			   			inputElem.val($(this).text());
			   			valueElem.val($(this).attr('data-value'));
			   			$(this).siblings('.selected').removeClass('selected');
			   			$(this).addClass('selected');
			   			customSelectList.hide();
	               }).mouseover(function(){
		               $(this).siblings('.selected').removeClass('selected');
		               $(this).addClass('selected');
	               });

			   var selectedObj = customSelectList.find('li[data-value='+valueElem.val()+']');
			   if(typeof(selectedObj.attr('data-value')) != 'undefined') {
				   selectedObj.click();
			   } else {
				   customSelectList.children(':first').click();
			   }
		   }
		});
	}	
}