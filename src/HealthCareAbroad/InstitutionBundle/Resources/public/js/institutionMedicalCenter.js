/**
 * @author Allejo Chris G. Velarde
 */
var InstitutionMedicalCenter = {
        
    removePropertyUri: '',
    _processing: 'Processing...',
        
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
    
    showCommonModal: function (_linkElement) {
        _linkElement = $(_linkElement);
        _modal = $(_linkElement.attr('data-target'));
        _modal.modal('show');
        
        return false;
    },
    
    // jQuery element for link opener
    openAjaxBootstrapModal: function(_opener) {
        _opener = $(_opener);
        _linkContainer = _opener.parents('div.delete-link-container');
        _modal = $(_opener.attr('data-target'));
        if (_modal.length > 0) {
            _linkContainer.fadeOut();
            $.ajax({
                url: _opener.attr('href'),
                type: 'get',
                dataType: 'json',
                success: function(response) {
                    _modal.modal();
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
    
    /**
     * Clicking on submit button of modal MedicalCenter Sidebar forms
     * 
     * @param DOMElement button
     */
    submitMedicalCenterSidebarForms: function(domButtonElement) {
        _button = $(domButtonElement);
        _buttonHtml = _button.html();
        _button.html(InstitutionMedicalCenter._processing).attr('disabled', true);
        _form = _button.parents('.modal').find('form');
        _data = _form.serialize();
        $.ajax({
            url: _form.attr('action'),
            data: _data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
            	switch(_form.attr('id')){
            	    case 'nameModalForm':
            	        $('#clinicNameText').html(response.institutionMedicalCenter.name);
                        break;
                    case 'descriptionModalForm':
                        $('#clinicDescriptionText').html(response.institutionMedicalCenter.description);
                        break;
                    case 'addressModalForm':
                        var _street_address = [];
                        $.each(response.institutionMedicalCenter.address, function(_k, _v){
                           if ($.trim(_v) != '') {
                               _street_address.push(_v);
                           } 
                        });
                        $('span.street_address_part').html(_street_address.length
                            ? _street_address.join(', ')+', '
                            : ''
                        );
                        break;
    
                    case 'numberModalForm':
                    	var number = response.institutionMedicalCenter.contactNumber;
                        $('#profileNumberText').html(number.country_code + '-' + number.area_code + '-' + number.number);
                        break;
                        
                    case 'emailModalForm':
                    	$('#profileEmailText').html(response.institutionMedicalCenter.contactEmail);
                        break;
                       
                    case 'websitesModalForm':
                        var websites = response.institutionMedicalCenter.websites, websitesString = ''; 
                        for(name in websites) {
                            websitesString += name + ': <a href="http://'+ websites[name] +'">' + websites[name] + "</a><br/>";
                        }
                        $('#profileWebsitesText').html(websitesString);
                        break;
                } 
                _form.parents('.modal').modal('hide');
                _button.html(_buttonHtml).attr('disabled', false);
            },
            error: function(response) {
                _button.html(_buttonHtml).attr('disabled', false);
            }
        });
        return false;
    },
    
    // this function is closely coupled to element structure in client admin
    //
    submitRemoveSpecializationForm: function(_formElement) {
        _button = _formElement.find('button.delete-button');
        _currentHtml = _button.html();
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
            	$('#doctor_id_'+response.id).remove();
            	$('#dialog-container').dialog("close");
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
    
    removeGlobalAward: function (_linkElement) {
        _linkElement = $(_linkElement);
        _id = _linkElement.attr('id').split('_')[1];
        $.ajax({
           type: 'POST',
           url: _linkElement.attr('href'),
           data: {id: _id},
           success: function(response) {
               _linkElement.parents('tr').remove();
           },
           error: function(response) {
               console.log(response);
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
	        },
	        'accreditation': {
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

var InstitutionSpecialistAutocomplete = {
    _loadHtmlContentUri: '',
    _removePropertyUri: '',
    autocompleteOptions: {
        'specialist':{
            source: '',
            target: null, // autocomplete target jQuery DOM element
            selectedDataContainer: null, // jQuery DOM element container of selected data
            loader: null,
            field: null
        }
    },
    removeProperty: function(_specialistId, _container) {
        _container.find('a.delete').attr('disabled',true);
        $.ajax({
            type: 'POST',
            url: InstitutionSpecialistAutocomplete.removePropertyUri,
            data: {'id': _specialistId},
            success: function(response) {
                _container.remove();
            }
        });
        
    },
    setAutocompleteOptions: function (_type, _options) {
        this.autocompleteOptions[_type] = _options;
        
        return this;
    },
    setRemovePropertyUri: function (_val) {
    	this._removePropertyUri = _val;
        return this;
    },
    setLoadHtmlContentUri: function (_val) {
        this._loadHtmlContentUri = _val;
        return this;
    },
    
    autocomplete: function() {
        $.each(InstitutionSpecialistAutocomplete.autocompleteOptions, function(_key, _val){
            if (_val.target) {
                _val.target.autocomplete({
                    minLength: 0,
                    source: _val.source,
                    select: function( event, ui) {
                    	InstitutionSpecialistAutocomplete._loadContent(ui.item.id, _val);
                        return false;
                    }
                });
            }
        });
    },
    
    _loadContent: function(_val, _option) {
        _option.loader.show();
        $.ajax({
            url: InstitutionSpecialistAutocomplete._loadHtmlContentUri,
            data: {'id':_val},
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                _option.selectedDataContainer.append(response.html);
                _option.target.find('option[value='+_val+']').hide();
                _option.loader.hide();
                _option.field.val('');
            },
            error: function(response) {
                _option.loader.hide();
                _option.field.val('');
            }
        });
    }
}


var ClinicBusinessHoursForm = {
    
    _formElement: null,
    
    _ajaxContentElement: null,
        
    _inputElements: {
        'isAlwaysOpen': null,
        'submitButton': null,
        'isClosed': null,
        'isOpenWholeDay': null
    },
    setAjaxContentElement: function (_el) {
        this._ajaxContentElement = _el;
        
        return this;
    },
    
    setFormElement: function(_el) {
        this._formElement = _el;
        
        return this;
    },
        
    initInputElements: function (_options) {
        this._inputElements = _options;
        
        return this;
    },
    
    _commonDailyToggle: function(_checkbox) {
        _weekdayContainer = _checkbox.parents('.weekday_container');
        _otherCheckbox = _checkbox.hasClass('closedToggle')
            ? _weekdayContainer.find('input.openWholeDayToggle:checked')
            : _weekdayContainer.find('input.closedToggle:checked');
        if (_checkbox.attr('checked')) {
            _otherCheckbox.attr('checked', false);
            _weekdayContainer.find('input.hour').spinner({ disabled: true });
        }
        else {
            _weekdayContainer.find('input.hour').spinner({ disabled: _otherCheckbox.attr('checked') });
        }
        _isAlwaysOpen = ClinicBusinessHoursForm._inputElements.isOpenWholeDay.not(':checked').length == 0;
        ClinicBusinessHoursForm._inputElements.isAlwaysOpen.attr('checked', _isAlwaysOpen);
    },
    
    initializeState: function() {
        
        // initialize handler when is closed checkbox is ticked
        this._inputElements.isClosed.change(function(){
            ClinicBusinessHoursForm._commonDailyToggle($(this));
            
        }).change();
        
        this._inputElements.isOpenWholeDay.change(function(){
            ClinicBusinessHoursForm._commonDailyToggle($(this));
            
        }).change();
        
        this._inputElements.isAlwaysOpen.change(function(){
            if (this.checked) {
                ClinicBusinessHoursForm._inputElements.isOpenWholeDay.not(':checked').attr('checked', true).change();
            }
        }).change();
        
        this._formElement.bind('submit', ClinicBusinessHoursForm.submit);
        this._inputElements.submitButton.click(function(){ 
            ClinicBusinessHoursForm._formElement.submit(); 
            return false; 
        });
    },
    
    submit: function(_event) {
        _form = $(this);
        _oldButtonHtml = ClinicBusinessHoursForm._inputElements.submitButton.html();
        ClinicBusinessHoursForm._inputElements.submitButton
            .html(ClinicBusinessHoursForm._inputElements.submitButton.attr('data-loader-text'))
            .attr('disabled', true);
        _modal = _form.parents('.modal');
        $.ajax({
            url: _form.attr('action'),
            data: _form.serialize(),
            type: 'post',
            dataType: 'json',
            success: function(response) {
                ClinicBusinessHoursForm._ajaxContentElement.html(response.html);
                ClinicBusinessHoursForm._inputElements.submitButton.html(_oldButtonHtml).attr('disabled', false);
                _modal.modal('hide');
            },
            error: function(response) {
                ClinicBusinessHoursForm._inputElements.submitButton.html(_oldButtonHtml).attr('disabled', false);
            }
        });
        
        return false;
    }
}




