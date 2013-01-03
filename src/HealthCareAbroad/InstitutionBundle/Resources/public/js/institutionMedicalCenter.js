/**
 * @author Allejo Chris G. Velarde
 */
var InstitutionMedicalCenter = {
        
    removePropertyUri: '',
        
    _modals: {
        'name': null,
        'description': null
    },
    
    tabbedContent: {
        container: null, 
        tabs: {
            'specializations' : {'url': '', 'container': null},
            'services' : {'url': '', 'container': null},
            'awards' : {'url': '', 'container': null},
            'medical_specialists' : {'url': '', 'container': null}
        }
    },
    
    _commonDialogOptions: {
        position: ['center', 100],
        autoOpen: false,
        width: 'auto',
        modal: true,
        resizable: false,
        close: function() {}
    },
    
    _callbacks: {},
    
    /**
     * Set the jQuery element for tabbed content container InstitutionMedicalCenter.tabbedContent.container
     */
    setTabbedContentContainerElement: function (_el) {
        InstitutionMedicalCenter.tabbedContent.container = _el;
        
        return this;
    },
    
    /**
     * Set the options for tabs, InstitutionMedicalCenter.tabbedContent.tabs 
     */
    initializeTabs: function(_tabOptions) {
        InstitutionMedicalCenter.tabbedContent.tabs = _tabOptions;
        
        return this;
    },
    
    initializeTabbedContentContainerElement: function (_el) {
        InstitutionMedicalCenter.tabbedContent.container = _el;
        
        return this;
    },
    
    loadTabbedContents: function(){
        $.each(InstitutionMedicalCenter.tabbedContent.tabs, function(_key, _val){
            $.ajax({
               url: _val.url,
               type: 'GET',
               dataType: 'json',
               success: function(response){
                   _val.container.html(response[_key].html);
                   if (_key == 'specializations') {
                       //InstitutionMedicalCenter.switchTab(_key);
                   }
                   
               }
            });
        }) ;
            
        return this;
    },
    
    switchTab: function(_tabType) {
        this.tabbedContent.container.html(this.tabbedContent.tabs[_tabType].container.html());
        
        return this;
    },

    initializeModals: function(_modalOptions){
        if (_modalOptions.name) {
            this._modals.name = _modalOptions.name;
            this._modals.name.dialog(this._commonDialogOptions);
        }
        
        if (_modalOptions.description) {
            this._modals.description = _modalOptions.description;
            this._modals.description.dialog(this._commonDialogOptions);
        }
        
        return this;
    },
    
    openModal: function(_name) {
        this._modals[_name].dialog("open");
        
    },
    
    closeModal: function(_name) {
        this._modals[_name].dialog("close");
    },
    
    // jQuery element for link opener
    openAjaxBootstrapModal: function(_opener) {
        _modal = $(_opener.attr('data-target'));
        _modal.modal();
        if (_modal.length > 0) {
            $.ajax({
                url: _opener.attr('href'),
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    _modal.html(response.html);
                },
                error: function(response) {
                    console.log(response);
                }
            });
        }
        
        return false;
    },
    
    submitModalForm: function(_formElement, _successCallback){
        $.ajax({
            url: _formElement.attr('action'),
            data: _formElement.serialize(),
            type: 'POST',
            success: _successCallback
         });
    },
    
    // this function is closely coupled to element structure in client admin
    //
    submitRemoveSpecializationForm: function(_formElement) {
        _button = _formElement.find('button.delete-button');
        _button.attr('disabled', true)
            .html('Processing...');
        $.ajax({
            url: _formElement.attr('action'),
            data: _formElement.serialize(),
            type: 'POST',
            success: function(response){
                _formElement.parents('div.modal').modal('hide');
                $('#specialization_block_'+response.id).remove();
            }
         });
    },

    submitRemoveMedicalSpecialistForm: function(_formElement) {
        _button = _formElement.find('button.delete-button');
        _button.attr('disabled', true)
            .html('Processing...');
        $.ajax({
            url: _formElement.attr('action'),
            data: _formElement.serialize(),
            type: 'POST',
            success: function(response){
            	$('#dialog-container').dialog("close");
            	$('#doctor_block_'+response.id).remove();
            }
         });
    },
    
    removeProperty: function(_propertyId, _container) {
        _container.find('a.delete').attr('disabled',true);
        $.ajax({
            type: 'POST',
            url: InstitutionMedicalCenter.removePropertyUri,
            data: {'id': _propertyId},
            success: function(response) {
                _container.remove();
            }
        });
        
    },
    
    addAncillaryService: function(_linkElement) {
        return this._doAncillaryServiceAction(_linkElement);
    },
    
    removeAncillaryService: function(_linkElement) {
        return this._doAncillaryServiceAction(_linkElement);
    },
    
    _doAncillaryServiceAction: function (_linkElement) {
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
                _linkElement.parents('tr.ancillaryancillaryServices_row').html($(response.html).html());
            },
            error: function(response) {
                console.log(response);
            }
        });
        
        return false;
    }
}


var InstitutionGlobalAwardAutocomplete = {
    _loadHtmlContentUri: '',
    
    autocompleteOptions: {
        'award':{
            source: '',
            target: null, // autocomplete target jQuery DOM element
            selectedDataContainer: null, // jQuery DOM element container of selected data
            loader: null
        },
        'certificate': {
            source: '',
            target: null, // autocomplete target jQuery DOM element
            selectedDataContainer: null, // jQuery DOM element container of selected data
            loader: null
        },
        'affiliation': {
            source: '',
            target: null, // autocomplete target jQuery DOM element
            selectedDataContainer: null, // jQuery DOM element container of selected data
            loader: null
        }
    },
    
    setAutocompleteOptions: function (_type, _options) {
        this.autocompleteOptions[_type] = _options;
        
        return this;
    },
    
    setLoadHtmlContentUri: function (_val) {
        this._loadHtmlContentUri = _val;
        
        return this;
    },
    
    autocomplete: function() {
        $.each(InstitutionGlobalAwardAutocomplete.autocompleteOptions, function(_key, _val){
            if (_val.target) {
                _val.target.autocomplete({
                    minLength: 0,
                    source: _val.source,
                    select: function( event, ui) {
                        InstitutionGlobalAwardAutocomplete._loadContent(ui.item.id, _val);
                        return false;
                    }
                });
            }
        });
    },
    
    _loadContent: function(_val, _option) {
        _option.loader.show();
        $.ajax({
            url: InstitutionGlobalAwardAutocomplete._loadHtmlContentUri,
            data: {'id':_val},
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                _option.selectedDataContainer.append(response.html);
                _option.target.find('option[value='+_val+']').hide();
                _option.loader.hide();
            },
            error: function(response) {
                _option.loader.hide();
            }
        });
    }
}

