/**
 * @author Allejo Chris G. Velarde
 */

/**
 * Created a function to capitalize every first text return
 * @author: Chaztine Blance
 */
function ucwords (str) {
  return (str + '').replace(/^([a-z\u00E0-\u00FC])|\s+([a-z\u00E0-\u00FC])/g, function ($1) {
    return $1.toUpperCase();
  });
}

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
    		google.maps.event.trigger(HCAGoogleMap.map, 'resize');
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
    	if(_attr = '#address'){
    		google.maps.event.trigger(HCAGoogleMap.map, 'resize');
    	}
    	
    },

    /* Added by: Adelbert Silla toggle edit/view mode */
    toggleForm: function(elem) {
    	viewElem = $(elem.attr('data-view-elem'));
    	editElem = $(elem.attr('data-edit-elem'));
    	
    	if(viewElem.is(':visible')) {
    		/* TODO: Temporary Fixed */
        	if(elem.attr('id') == 'clinic-edit-awards-btn') {
        		
        		_type = editElem.attr('data-filter-list').replace('#listing-', '');
        		$('#awardTypeKey').val(_type);
        		
        		InstitutionMedicalCenter.filterAwardsList(elem );
        	}
        	/* end of TODO: Temporary Fixed */
    		
        	viewElem.hide();
        	editElem.slideDown('slow');
        	elem.addClass('btn-link').removeClass('btn-misc').html('<i class="icon-remove"></i>');
        	
    	} else {
    		
        	editElem.slideUp('slow', function(){
        		InstitutionMedicalCenter.undoChecked(editElem);
        		viewElem.fadeIn();
            	elem.addClass('btn-misc').removeClass('btn-link').html('Edit');
        	});
    	}
    },
    
    /**
     * if container is closed without saving undo changes
     */
    undoChecked: function(_editElem) {
    	
    	if($(_editElem.attr('data-filter-list'))){
			_list = $(_editElem.attr('data-filter-list'));
			 _list.find("li.hca-highlight input:checkbox.new").click();
			 _list.find("li.hca-highlight input:checkbox.new").attr('class', '');
			 _list.find("li[class=''] .old").click();
		}
    },
    
    filterAwardsList: function(elem ) {
    	
    	elem.parent().find('.hca-edit-box:first').html($('#awardsForm'));
    	$('#awardsForm .control-group > .awards-listing').hide();
    	$($('#awardsForm').parent().attr('data-filter-list')).show();
    	$('#awardsForm h3.awards-heading').hide();
    	$.each($('.hca-main-profile').find('#clinic-edit-awards-btn'), function(_k, value) {
    		
        	if(value.text === '') {
        		if( viewElem.attr('id') != value.getAttribute('data-view-elem')){
        			_toHide = value.getAttribute('data-edit-elem');
        			InstitutionMedicalCenter.undoChecked($(_toHide));
        			$(value.getAttribute('data-view-elem')).fadeIn();
	    			$(_toHide).slideUp('slow', function(){
	    				$(value.getAttribute('data-view-elem')).parent().find('#clinic-edit-awards-btn').addClass('btn-misc').removeClass('btn-link').html('Edit');
		        	});
        		}
    		}
    	});
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
        _name = $(_linkElement).parent().find('h4').html();
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
        _parent = _button.parents('form');
        if(!_form.attr('action')){
        	_form = _button.parents('div#edit-medical-center-name').find('form');
        	_parent = _button.parents('div#edit-medical-center-name');
        }
        _editButton = _button.parents('section.hca-main-profile').find('a.btn-edit');
        _data = _form.serialize();
        _parent.find('.alert-box').removeClass('alert alert-error alert-success').html("");
        _parent.find('.error').removeClass('error');
        $('.errorText').remove();
        $.ajax({
            url: _form.attr('action'),
            data: _data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
            	switch(_form.attr('id')){
            	    case 'nameModalForm':
            	        $('#clinicNameText').html(ucwords(response.institutionMedicalCenter.name));
            	        _form.parents('div.modal').modal('hide');
                        break;
                    case 'descriptionForm':
                        $('#clinicDescriptionText').html(response.institutionMedicalCenter.description);
                        if($('#clinicDescriptionText').parent('p').next('.alert')){
                        	if(response.institutionMedicalCenter.description){
                        		$('#clinicDescriptionText').parent('p').next('.alert').hide();
                        	}else{
                        		$('#clinicDescriptionText').parent('p').next('.alert').show();
                        	}
                        }
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
                            address.push(ucwords(_street_address.join(', ')));
                        } else {
                        	_street_address = '';
                        }
                        _keys = ['city', 'state', 'country', 'zipCode'];
                        $.each(_keys, function(_k, _v){
                            if (response.institutionMedicalCenter[_v]) {
                                address.push(ucwords(response.institutionMedicalCenter[_v]));
                            }
                        });
                        
                		$('.addressLabel').html('Edit Address');
                        _html = '<span class="address_part">' + address.join(',&nbsp;</span><span class="address_part">')+'</span>';
                        
                        $('.address_column').find('span.address_part').remove();
                        $('.address_column').prepend(_html);
                        
                        if(HCAGoogleMap.map) { 
                        	mapStaticUrl = 'http://maps.googleapis.com/maps/api/staticmap?center='+ response.institutionMedicalCenter.coordinates + '&zoom=15&size=260x200&sensor=false&markers=%7Alabel:S%7C' + response.institutionMedicalCenter.coordinates;
                        	$('#medical-center-static-map').prop('src', mapStaticUrl);
                        }
                        
                        break;
    
                    case 'contactForm':
                    	if(response.institutionMedicalCenter.websites){
                    		$('#profileWebsitesText').html(' http://www.<b>'+ response.institutionMedicalCenter.websites +'</b>');
                    		$("#alertDiv").attr('class', ' ');
                    	}else{
                    		$('#profileWebsitesText').html('<b> no clinic website </b> added. <a onclick="InstitutionMedicalCenter.toggleForm($(\'#institution-edit-contacts-btn\')); " class="btn btn-primary btn-small"><i class="icon-plus"></i> Add Website</a>');
                    		$("#alertDiv").attr('class', 'alert alert-block');
                    	}
						$('#profileEmailText').html(response.institutionMedicalCenter.contactEmail);
						$('#PhoneNumberText').html(response.institutionMedicalCenter.contactDetails.phoneNumber);
						
                        break;
                    case 'socicalMediaSitesForm':
                    	var websites = response.institutionMedicalCenter.socialMediaSites;
                    	$.each(websites, function(type) {
                    		if($.trim(websites[type]) != '') {
                    			$('#view-socialMediaSites > p.' + type + '-wrapper').removeClass('alert-block').find('b').html(websites[type]);
                    		} else {
                    			$('#view-socialMediaSites > p.' + type + '-wrapper').addClass('alert-block').find('b').html('no account added. ');
                    		}
                    	});
                  	break;
                       
                    case 'servicesForm':
                    	$('#serviceTable').html(response.html);
                    	break;
                    case 'awardsForm':
                    	$('#listing-'+response.type).find("li.hca-highlight input:checkbox.new").attr('class', 'old');
                		$('#listing-'+response.type).find("li[class=''] .old").attr('class', '');
                    	$('#'+response.type+'sText').html(response.html);
                    	break;
                } 
                _button.html(_buttonHtml).attr('disabled', false);
                _editButton.click();
                // Display Callout Message
//                InstitutionMedicalCenter.displayCallout(response);
            },
            error: function(response) {
                _button.html(_buttonHtml).attr('disabled', false);
                if (response.status==400) {
                    var errors = $.parseJSON(response.responseText).html;
                    if (errors.length) {
                        var _errorString = "We need you to correct some of your input. Please check the fields in red.";
                        $.each(errors, function(key, item){
                        	if(item.field){
	                        	_parent.find('div.'+item.field).addClass('error');
	                        	if(item.field == 'country' || item.field == 'city'){
	                        		$('<ul class="errorText"><li>'+item.error+'</li></ul>').insertAfter(_parent.find('div.'+item.field+' > div'));
	                        	}else{
	                        		$('<ul><li class="errorText">'+item.error+'</li></ul>').insertAfter(_parent.find('div.'+item.field+' > input'));
	                        	}
                        	}
                        });
                    }
                    else{
                    	var _errorString = errors.error;
                	}
                    _parent.find('.alert-box').removeClass('alert alert-error alert-success').html("");
                	_parent.find('.alert-box').addClass('alert alert-error').html(_errorString);
                }
            }
        });
        return false;
    },
    
    submitAddNewMedicalCenter: function(domButtonElement) {
        _button = $(domButtonElement);
        _buttonHtml = _button.html();
        _button.html(InstitutionMedicalCenter._processing).attr('disabled', true);
        _form = _button.parents('div#add-new-center').find('form');
        _data = _form.serialize();
        $.ajax({
            url: _form.attr('action'),
            data: _data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
            	window.location = response.redirect;
            },
            error: function(response) {
                _button.html(_buttonHtml).attr('disabled', false);
                if (response.status==400) {
                    var errors = $.parseJSON(response.responseText).html;
                    if (errors.length) {
                        var _errorString = "";
                        $.each(errors, function(key, item){
                        	_errorString += item.error+"<br>";
                        	_button.parents('div#add-new-center').find('div.'+item.field).addClass('error');
                        });
                        _button.parents('div#add-new-center').find('.alert-box').removeClass('alert alert-error alert-success').html("");
                        _button.parents('div#add-new-center').find('.alert-box').addClass('alert alert-error').html(_errorString);
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
            	$('#specialization_list_block').children('div').removeClass('disabled');
                _formElement.parents('div.modal').modal('hide');
                $('#specialization_'+response.id).remove();
                InstitutionMedicalCenter.displayAlert('You have successfully remove specialization' , 'success');
                $('#new_specializationButton').removeAttr('disabled');
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
    },
    
    /**
     * DISPLAY alerts after success submission
     */
    displayAlert: function(message, status) {
    	_confirmMessageElem = $('#confirmation-message');
    	_confirmMessageElem.find('div.confirmation-box').attr('class', 'confirmation-box fixed');
    	_confirmMessageElem.find('.confirmation-box').html('<div class="alert alert-'+status+'"><p>' + message + '.</p></div>');
    	_confirmMessageElem.show();
    	setTimeout(function() {_confirmMessageElem.fadeOut("slow")}, 5000);
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

