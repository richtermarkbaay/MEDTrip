/**
 * Autocomplete handler for institution specialization selector
 * 
 * @author allejochrisvelarde
 */

var InstitutionSpecialization = {
      
    specializationsListContainer: null,
        
    setSpecializationsListContainerElement: function (_element) {
        this.specializationsListContainer = _element;
        
        return this;
    },
        
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
    },
    
    /**
     * Clicking on submit button of modal Add Specialization form
     * 
     * @param DOMElement button
     */
    submitAddSpecialization: function(domButtonElement) {
        _button = $(domButtonElement);
        _buttonHtml = _button.html();
        // change button html and disable it
        _button.html("Processing...").attr('disabled', true);
        _modal = $(_button).parents('div.modal_form_container');
        // Note. this is tightly coupled with html element structures
        _form = _modal.find('form.modal_form');
        _data = _form.serialize();
        $.ajax({
            url: _form.attr('action'),
            data: _data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                _lastSpecializationBlock = InstitutionSpecialization.specializationsListContainer.find('div.specializations_block').last();
                // insert new content after last specialization block
                _lastSpecializationBlock.after($(response.html));
                _modal.modal('hide');
                _button.html(_buttonHtml).attr('disabled', false);
            },
            error: function(response) {
                console.log(response);
                _button.html(_buttonHtml).attr('disabled', false);
            }
        });
        
        return false;
    }
};

var InstitutionSpecializationAutocomplete = {
    singleSelectionOnly: false,
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
        //InstitutionSpecializationAutocomplete.autocompleteOptions.selectedDataContainer.accordion({active: false, collapsible: true, heightStyle: "content"});
        
        InstitutionSpecializationAutocomplete.autocompleteOptions.target.autocomplete({
            minLength: 2,
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
        if (InstitutionSpecializationAutocomplete.singleSelectionOnly) {
            InstitutionSpecializationAutocomplete.autocompleteOptions.selectedDataContainer.html("");
        }
        
        $.ajax({
            url: InstitutionSpecializationAutocomplete._loadSpecializationFormUri,
            data: {'specializationId':_val},
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                InstitutionSpecializationAutocomplete.autocompleteOptions.target.val("");
                InstitutionSpecializationAutocomplete._loaderElement.hide();
                InstitutionSpecializationAutocomplete.autocompleteOptions.selectedDataContainer
                    .prepend(response.html);
                InstitutionSpecializationAutocomplete.autocompleteOptions.target.find('option[value='+_val+']').hide();
            },
            error: function(response) {
                InstitutionSpecializationAutocomplete._loaderElement.hide();
            }
        });
    }
    
};