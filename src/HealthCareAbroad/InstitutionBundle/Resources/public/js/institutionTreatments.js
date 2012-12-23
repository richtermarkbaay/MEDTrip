/**
 * Autocomplete handler for institution specialization selector
 * 
 * @author allejochrisvelarde
 */

var InstitutionSpecialization = {
    removeTreatment: function(_linkElement) {
        return this._doCommonTreatmentAction(_linkElement)
    },
    
    addTreatment: function(_linkElement) {
        return this._doCommonTreatmentAction(_linkElement);
    },
    
    _doCommonTreatmentAction: function(_linkElement) {
        if (_linkElement.hasClass('disabled')) {
            return false;
        }
        _href = _linkElement.attr('href');
        _html = _linkElement.html();
        _linkElement.html('Processing...').addClass('disabled');
        
        $.ajax({
            url: _href,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                _linkElement.removeClass('disabled')
                    .html(response.link.html)
                    .attr('href', response.link.href);
                _linkElement.parents('div.treatment_action').find('i')
                    .attr('class', response.icon);
            },
            error: function(response) {
                console.log(response);
            }
        });
        
        return false;
    }
};

var InstitutionSpecializationAutocomplete = {
    _loadSpecializationFormUri : '',
    _loaderElement: null, // jQuery DOM element loader
    autocompleteOptions: {
        source: '',
        target: null, // autocomplete target jQuery DOM element
        selectedDataContainer: null // jQuery DOM element container of selected data
    },
    
    // set InstitutionSpecializationAutocomplete.autocompleteOptions
    setAutocompleteOptions: function (_val) {
        this.autocompleteOptions = _val;
        
        return this;
    },
    
    setLoaderElement: function (_el) {
        this._loaderElement = _el;
        
        return this;
    },
    
    setLoadSpecializationFormUri: function(_val) {
        this._loadSpecializationFormUri = _val;
        
        return this;
    },
    
    autocomplete: function(){
        
        // initialize accordion for data container
        InstitutionSpecializationAutocomplete.autocompleteOptions.selectedDataContainer.accordion({active: false, collapsible: true, heightStyle: "content"});
        
        InstitutionSpecializationAutocomplete.autocompleteOptions.target.autocomplete({
            minLength: 0,
            source: InstitutionSpecializationAutocomplete.autocompleteOptions.source,
            select: function( event, ui) {
                InstitutionSpecializationAutocomplete._loadSpecializationForm(ui.item.id);
                
                return false;
            }
        });
        
        return this;
    },
    
    _loadSpecializationForm: function (_val) {
        InstitutionSpecializationAutocomplete._loaderElement.fadeIn();
        $.ajax({
            url: InstitutionSpecializationAutocomplete._loadSpecializationFormUri,
            data: {'specializationId':_val},
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                InstitutionSpecializationAutocomplete._loaderElement.hide();
                InstitutionSpecializationAutocomplete.autocompleteOptions.selectedDataContainer
                    .prepend(response.html).
                    accordion('destroy').
                    accordion({heightStyle: "content"});
                InstitutionSpecializationAutocomplete.autocompleteOptions.target.find('option[value='+_val+']').hide();
            }
        });
    }
    
};