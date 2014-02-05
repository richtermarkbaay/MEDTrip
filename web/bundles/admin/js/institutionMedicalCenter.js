/**
 * @author Alnie Jacobe
 */
var InstitutionMedicalCenter = {
        
    removePropertyUri: '',
    _updateStatusUri: '',  
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
    setUpdateStatusUri: function (_val) {
    	this._updateStatusUri = _val;
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
    closeDialog: function() {
    	$('#dialog-container').dialog("close");
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
    
    submitFancyBusinessHoursForm: function(_formElement) {
    	_href = $(_formElement).attr('href');
    	_formId = $(_formElement).attr('data-formId');
    	$(_formId).prev('.alert').hide();
    	$(_formElement).html('Processing...');
    	_formData = $(_formId).serialize();
    	$.ajax({
    		url: _href,
    		data: _formData,
    		type: "POST",
    		success: function(response) {
    			$(_formId).prev('.alert').show();
    			$(_formElement).html('Submit');
    		}
    	})
    	return false;
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
        _button.prop('disabled', true).html('Processing...');
        $.ajax({
            url: _formElement.attr('action'),
            data: _formElement.serialize(),
            type: 'POST',
            success: function(response){
            	$('#doctor_id_'+response.id).remove();
            	$('#_specialistModal').modal('hide');
            	_button.prop('disabled', false).html('Delete');
            	
            }
         });
    },
    updateMedicalCenterStatus: function(_button) {
    	_button = $(_button);
    	_formElement = $(_button.attr('data-formId'));
    	_modal = $(_button.attr('data-modalId'));
    	_button.attr('disabled', true)
            .html('Processing...');
        var href = _formElement.attr('action');
        $.ajax({
            type: 'POST',
            url: href,
            data: _formElement.serialize(),
            success: function(response) {
            	_button.removeAttr('disabled')
                .html('Submit');
            	_modal.modal('hide');
            	alert(response.success);

            },
            error: function(response){
            	alert($.parseJSON(response.responseText).error);
            	window.location.reload();
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
    
    addRemoveAncillaryService: function(_linkElement) {
        return this._doAncillaryServiceAction(_linkElement);
    },
    
    showInstitutionAncillaryServicesForm: function(_linkElement) {
    	_linkElement = $(_linkElement);
    	_href = _linkElement.attr('href');
    	_modal = $(_linkElement.attr('data-modalId'));
    	_ancillaryServicesTable = $(_linkElement.attr('data-tableId'));
    	_modalBtn = $(_linkElement.attr('data-modalBtnId'));
    	_modalBtn.removeAttr('disabled');
    	$.ajax({
            type: 'POST',
            url: _href,
            success: function(response) {
            	if(response.count == 0) {
            		_modalBtn.attr('disabled', true);
            	}
            	_ancillaryServicesTable.find('tbody').remove();
            	_ancillaryServicesTable.find('thead').after($(response.html));
            	_modal.modal('show');
            }
        });
    },
    
    portInstitutionAncillaryServices: function(_button) {
    	_button = $(_button);
    	_divId = $(_button.attr('data-divId'));
    	_button.attr('disabled',true).html('Processing...');
    	_href = _button.attr('data-path');
    	_modal = $(_button.attr('data-modalId'));
    	$.ajax({
            type: 'POST',
            url: _href,
            data: {'isCopy' : 1},
            success: function(response) {
            	_modal.modal('hide');
            	_divId.find('.boxcontent').remove();
            	_divId.find('h5').after($(response.html));
            	_button.removeAttr('disabled').html('Submit');
            }
        });
    	
    	return false;
    },
    
    showInstitutionAwardsForm: function(_elem) {
    	_elem = $(_elem);
    	_href = _elem.attr('href');
    	_modal = $(_elem.attr('data-modalId'));
    	_awardsTableElem = $(_elem.attr('data-tableId'));
    	_modalBtn = $(_elem.attr('data-modalBtnId'));
    	_modalBtn.removeAttr('disabled');
    	$(_elem).html('Processing...');
    	$.ajax({
            type: 'GET',
            url: _href,
            success: function(response) {
            	if(response.count == 0) {
            		_modalBtn.attr('disabled', true);
            	}
            	$(_elem).html('<i class="icon-plus"></i> Port Institution GlobalAwards');
            	_awardsTableElem.find('tbody').remove();
            	_awardsTableElem.find('thead').after($(response.html));
            	_modal.modal('show');
            }
        });
    },
    
    portInstitutionGlobalAwards: function(_btnElem) {
    	_btnElem = $(_btnElem);
    	_divId = $(_btnElem.attr('data-divId'));
    	_btnElem.attr('disabled',true).html('Processing...');
    	_href = _btnElem.attr('data-path');
    	_modal = $(_btnElem.attr('data-modalId'));
    	$.ajax({
            type: 'POST',
            url: _href,
            data: {'isCopy' : 1},
            success: function(response) {
            	_modal.modal('hide');
            	_btnElem.removeAttr('disabled').html('Submit');
            }
        });
    	
    	return false;
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
            	_linkElement.removeClass('disabled')
            	_linkElement.attr('href', response.href);
            	_linkElement.text(response.label);
            	//check if newly added service
            	if(response._isSelected == true ){
	            	_linkElement.prev('i').attr('class','icon-minus');
            	}else{
	            	_linkElement.prev('i').attr('class','icon-plus');
            	}
            }
        });
        
        return false;
    }
}

var InstitutionSpecialistAutocomplete = {
    _loadHtmlContentUri: '',
    _removePropertyUri: '',
    _loadMedicalSpecialistUri: '',
    autocompleteOptions: {
        'specialist':{
            source: null,
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
    setLoadMedicalSpecialistUri: function (_val) {
        this._loadMedicalSpecialistUri = _val;
        return this;
    },
    autocomplete: function() {
        $.each(InstitutionSpecialistAutocomplete.autocompleteOptions, function(_key, _val){
            if (_val.target) {
                _val.target.autocomplete({
                    minLength: 2,
                    source: function(_val, response) {
                    	$('#loader_ajax').show();
                    	$.ajax({
                            url: InstitutionSpecialistAutocomplete._loadMedicalSpecialistUri,
                            data: {'term':_val.term},
                            dataType: "json",
                            success: function(json) {
                            	$('#loader_ajax').hide();
                            	response($.each(json, function(item){
                            		return { label: item.label, value: item.value }
                            	}));
                            	
                            },
                            error: function(response) {
                            	$('#loader_ajax').hide();
                            }
                        });
                    },
                    select: function( event, ui) {
                    	InstitutionSpecialistAutocomplete._loadContent(ui.item.id, _val);
                        return false;
                    }
                });
            }
        });
    },
    _loadContent: function(_val, _option) {
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