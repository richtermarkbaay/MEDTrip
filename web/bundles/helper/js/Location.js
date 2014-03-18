var Location = {
        
    loadCitiesUrl: '',
    
    loadStatesUrl: '',
    
    loadNonGlobalCities: 0,
    
    citiesDropdownElement: null,
    
    disableWidget: function(widget, disabled){
        widget.prop('disabled', disabled);
        $(widget.data('autocomplete-trigger')).prop('disabled', disabled);
    },

    resetWidgetValue: function(widget){
        widget.val('');
        widget.data('fancyAutocomplete').options.valueContainer.val("");
    },
	
    loadStatesOfCountry: function(countryId, widget, selectedStateId){
        Location.disableWidget(widget, true);
        Location.resetWidgetValue(widget);
        var widgetPlaceholder = widget.prop('placeholder');
        widget.prop('placeholder', 'loading states...');
        $.ajax({
            url: Location.loadStatesUrl,
            data: {country_id: countryId},
            type: 'get',
            dataType: 'json',
            success: function(response){
                if (response.data.length){
                    Location.disableWidget(widget, false);
                    widget.data('fancyAutocomplete').setSource(response.data);
                }
                else {
                    widget.data('fancyAutocomplete').setSource([]);
                    if(widget.data('fancyAutocomplete').options.acceptCustomValue) {
                        Location.disableWidget(widget, false);
                        widget.data('fancyAutocomplete').dropdownTrigger.prop('disabled', true);
                    }
                }
                widget.trigger('reloadedDataSource');
                widget.prop('placeholder', widgetPlaceholder);
            }
        });
    },
    
    loadCities: function(countryId, stateId, widget, selectedCityId){
        if(!selectedCityId) {
            Location.disableWidget(widget, true);
            Location.resetWidgetValue(widget);            
        }

        var params = {key_value: 1, state_id: stateId, country_id: countryId};
        var widgetPlaceholder = widget.prop('placeholder');
        widget.prop('placeholder', 'loading cities...');
        $.ajax({
            url: Location.loadCitiesUrl,
            data: params,
            type: 'get',
            dataType: 'json',
            success: function(response){

                if (response.data.length){
                    Location.disableWidget(widget, false);
                    widget.data('fancyAutocomplete').setSource(response.data);
                    //widget.trigger('reloadedDataSource');
                } else {
                    widget.data('fancyAutocomplete').setSource([]);
                    if(widget.data('fancyAutocomplete').options.acceptCustomValue) {
                        Location.disableWidget(widget, false);
                        widget.data('fancyAutocomplete').dropdownTrigger.prop('disabled', true);
                    }
                }

                widget.prop('placeholder', widgetPlaceholder);
            }
        });
    }	
}