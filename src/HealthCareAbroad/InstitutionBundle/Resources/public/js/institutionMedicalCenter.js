/**
 * @author Allejo Chris G. Velarde
 */
var InstitutionMedicalCenter = {
    isEditView: false,
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
    	}
    },
    
    institutionInputs: {
   	 'address': null,
        'unit': null,
        'bldg': null,
        'hint': null
   },
   
    setInstitutionInputs: function(_val){
        this.institutionInputs = _val;
        
        return this;
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
    
    toggleAddress: function(_element){
		 _attr = $(_element.attr('data-toggle'));
		 if( _attr.selector == '#sameAddress') {
			 _attr.show()
			 $('#newAddress').hide();
		 }if( _attr.selector == '#newAddress') {
			 _attr.show()
			 $('#sameAddress').hide();
			 _attr.find(':input:not([disabled=disabled])').val('');
		 } 
    },
    
    openProfileForm: function(_element){
    	_element.toggle();
    	_attr = _element.attr('href');
    	_element.next('div.show').hide();
    	$(_attr).toggle();
    	if(_attr = '#address'){
    		GoogleMap.initialize();
    		google.maps.event.trigger(GoogleMap.map, 'resize');
    	}
    },
    closeProfileForm: function(_element){
    	_div = _element.parents('div.hca-edit-box').prev('div');
    	_div.show();
    	_div.prev().show();
    	_element.parent().hide();
    },
    
    openWebsiteFormButton: function(_element){
    	_attr = _element.attr('href');
    	_element.parents('div.show').hide();
    	_element.parents('div.show').prev().hide();
    	$(_attr).toggle();
    	
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
        /*this._modals[_name].dialog("close");*/
        _name.reset();	
    	_formId = $('#'+_name.id); 
    	_formId.find('ul.text-error').remove();
    		
        return this;
    },
    
    showCommonModal: function (_linkElement) {
        _linkElement = $(_linkElement);
        _modal = $(_linkElement.attr('data-target'));
        _modal.modal('show');
        
        return false;
    },
    
    showCommonSpecializationModal:  function (_linkElement) {
    	
        _linkElement = $(_linkElement);
        _id = _linkElement.data('id');
        _name = $(_linkElement).parent().find('b').html();
        _modal = $(_linkElement.attr('data-target'));
        $(".modal-body p strong").text(_name+'?');
        _modal.modal('show');
        
        return false;
    },
    
    showCommonTreatmentModal:  function (_linkElement) {
        _linkElement = $(_linkElement);
        _id = _linkElement.data('id');
        _name = $('.treatment_name_'+_id).html();
        _modal = $(_linkElement.attr('data-target'));
        _modal.find('#tId').val(_id);
        $(".modal-body p strong").text(_name+'?');
        
        return false;
    },
    
    showSpecialistCommonModalId: function (_linkElement) {
        _linkElement = $(_linkElement);
        _id = _linkElement.data('id');
        _name = $('.specialist_name_'+ _id).html();
        $('.doctorHiddenId').val(_id);
        _modal = $(_linkElement.attr('data-target'));
        $(".modal-body p strong").text(_name+'?');
        
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
        _form = _button.parents('form');
        _divToShow = _button.parents('section.hca-main-profile').find('div.show');
    	_divToHide = _button.parents('section.hca-main-profile').find('div.hca-edit-box');
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
                    case '.':
                        $('#clinicDescriptionText').html(response.institutionMedicalCenter.description);
                        break;
                    case 'addressForm':
                    	var address = [];
                        var _street_address = [];
                        $.each(response.institutionMedicalCenter.address, function(_k, _v){
                           if ($.trim(_v) != '') {
                               _street_address.push(_v);
                           } 
                        });
                        if (_street_address.length) {
                            address.push(_street_address.join(', '));
                        } else {
                        	_street_address = '';
                        }
                        _keys = ['city', 'state', 'country', 'zipCode'];
                        $.each(_keys, function(_k, _v){
                            if (response.institutionMedicalCenter[_v]) {
                                address.push(response.institution[_v]);
                            }
                        });
                        
                		$('.addressLabel').html('Edit Address');
                        _html = '<span class="address_part">' + address.join(',&nbsp;</span><span class="address_part">')+'</span>';
                        
                        $('.address_column').find('span.address_part').remove();
                        $('.address_column').prepend(_html);
                        
//                        //HCAGoogleMap.updateMap();
//                        if(HCAGoogleMap.map) { 
//                            HCAGoogleMap.updateMap(_street_address + ',' + HCAGoogleMap.defaultAddress);
//                        }

                        break;
    
                    case 'contactForm':
                    	if(response.institutionMedicalCenter.websites){
                    		$('#profileWebsitesText').html(' http://www.<b>'+ response.institutionMedicalCenter.websites +'</b>');
                    		$("#alertDiv").attr('class', ' ');
                    	}else{
                    		$('#profileWebsitesText').html('<b> no clinic website </b> added. <a onclick="InstitutionMedicalCenter.openWebsiteFormButton($(this)); return false;" class="btn btn-primary btn-small" href="#contactNumber" ><i class="icon-plus"></i> Add Clinic Website</a>');
                    		$("#alertDiv").attr('class', 'alert alert-block');
                    	}
						$('#profileEmailText').html(response.institutionMedicalCenter.contactEmail);
						$('#PhoneNumberText').html(response.institutionMedicalCenter.contactDetails.phoneNumber);
						
                        break;
                    case 'socicalMediaSitesForm':
                  	  var websites = response.institutionMedicalCenter.socialMediaSites, websitesString = ''; 
                  	  		websitesString += '<p><i class="icon-twitter"> </i> <b>'+  websites.twitter + "</b></p>";
                  	  		websitesString += '<p><i class="icon-facebook"> </i><b>'+ websites.facebook + "</b></p>";
                  	  		websitesString += '<p><i class="icon-google-plus"> </i> <b>'+ websites.googleplus + "</b></p>";
	                        $('#soclialMediaDiv').html(websitesString);
	                        $('#alertSocialDiv').hide();
                  	break;
                       
                    case 'servicesForm':
                    	$('#servicesTable').html(response.html);
                    	break;
                    	
                    case 'awardsForm':
                    	$('#awardsText').html(response.html);
                    	break;
                } 
            	 _form.find('.alert-box').removeClass('alert alert-error alert-success').html("");
                 _form.find('.error').removeClass('error');
                _button.html(_buttonHtml).attr('disabled', false);
                _divToShow.prev().show();
                _divToShow.show();
                _divToHide.hide();
                // Display Callout Message
//                InstitutionMedicalCenter.displayCallout(response);
            },
            error: function(response) {
                _button.html(_buttonHtml).attr('disabled', false);
                if (response.status==400) {
                    var errors = $.parseJSON(response.responseText).html;
                    if (errors.length) {
                        var _errorString = "";
                        $.each(errors, function(key, item){
                        	_errorString += item.error+"<br>";
                        	_form.find('div.'+item.field).addClass('error');
                        });
                        _form.find('.alert-box').removeClass('alert alert-error alert-success').html("");
                        _form.find('.alert-box').addClass('alert alert-error').html(_errorString);
                    }
                }
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
                $('#specialization_'+response.id).remove();
                InstitutionMedicalCenter.displayCallout(response);
            }
         });
    },
    
    submitRemoveSpecializationTeatmentForm: function(domButtonElement) {
    	 _button = $(domButtonElement);
         _form = _button.parents('.modal').find('form');
        _button.html("Processing...").attr('disabled', true);
        $.ajax({
            url: _form.attr('action'),
            data: _form.serialize(),
            type: 'POST',
            success: function(response){
            	_form.parents('div.modal').modal('hide');
            	_button.html("Delete").attr('disabled', false);
            	$('#treatment_id_'+response.id).remove();
            	InstitutionMedicalCenter.displayCallout(response);
            }
         });
    },

    submitRemoveMedicalSpecialistForm: function(_domButtonElement) {
  	 _button = $(_domButtonElement);
	  	_form = _button.parents('.modal').find('form');
	  	
	    _button.html("Processing...").attr('disabled', true);

	    $.ajax({
	        url: _form.attr('action'),
	        data: _form.serialize(),
	        type: 'POST',
	        success: function(response){
	        	_form.parents('div.modal').modal('hide');
	        	_button.html("Delete").attr('disabled', false);
	        	$('#doctor_id_'+response.id).remove();

   	        	// Display Message Callout
   	        	if(InstitutionMedicalCenter.isEditView) {
   	        		InstitutionMedicalCenter.displayCallout(response);
   	        	}
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
    },
    
    displayCallout: function(response) {
        if(typeof response.calloutView !== "undefined") {
            if($('#content').prev().attr('id') == 'featured') {
            	$('#content').prev().html(response.calloutView);
            } else {
                $(response.calloutView).insertBefore($('#content'));
            }                	
        }

        $('#featured').hide().fadeIn(2000);
    }
}

var InstitutionSpecialistAutocomplete = {
    _loadHtmlContentUri: '',
    _removePropertyUri: '',
    _loadMedicalSpecialistUri: '',
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
                
                // Display Callout in editMode
   	        	if(InstitutionMedicalCenter.isEditView) {
   	        		InstitutionMedicalCenter.displayCallout(response);
   	        	}
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
                InstitutionMedicalCenter.displayCallout(response);
            },
            error: function(response) {
                ClinicBusinessHoursForm._inputElements.submitButton.html(_oldButtonHtml).attr('disabled', false);
            }
        });
        
        return false;
    }
};

