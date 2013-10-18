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
        widget.data('fancyAutocomplete').options.valueContainer.val(0);
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
                if (response.states.length){
                    Location.disableWidget(widget, false);
                    widget.data('fancyAutocomplete').setSource(response.states);
                }
                else {
                    widget.data('fancyAutocomplete').setSource([]);
                }
                widget.trigger('reloadedDataSource');
                widget.prop('placeholder', widgetPlaceholder);
            }
        });
    },
    
    loadCities: function(countryId, stateId, widget, selectedCityId){
        Location.disableWidget(widget, true);
        Location.resetWidgetValue(widget);
        var params = {};
        if(countryId) { params.country_id = countryId; }
        if(stateId) { params.state_id = stateId; }

        var widgetPlaceholder = widget.prop('placeholder');
        widget.prop('placeholder', 'loading cities...');
        
        $.ajax({
            url: Location.loadCitiesUrl,
            data: params,
            type: 'get',
            dataType: 'json',
            success: function(response){
                if (response.cities.length){
                    Location.disableWidget(widget, false);
                    widget.data('fancyAutocomplete').setSource(response.cities);
                    widget.trigger('reloadedDataSource');
                }

                widget.prop('placeholder', widgetPlaceholder);
            }
        });
    }	
}