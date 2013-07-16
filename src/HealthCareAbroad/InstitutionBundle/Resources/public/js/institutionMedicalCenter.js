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
    	if(elem.hasClass('disabled')) {
    		return false;
    	}
    	
    	viewElem = $(elem.attr('data-view-elem'));
    	editElem = $(elem.attr('data-edit-elem'));
    	
    	if(viewElem.is(':visible')) {

        	if(elem.hasClass('edit-awards')) { /* TODO: Temporary Fixed */
        		$('section.hca-main-profile .edit-awards').addClass('disabled');
        		elem.removeClass('disabled');

        		_type = editElem.attr('data-filter-list').replace('#listing-', '');
        		$('#awardTypeKey').val(_type);
        		
        		InstitutionMedicalCenter.filterAwardsList(elem );
        	} /* end of TODO: Temporary Fixed */
        	
        	viewElem.hide();
        	editElem.slideDown('slow', function(){
            	if(elem.attr('data-edit-elem') == "#address") {
        	        google.maps.event.trigger(HCAGoogleMap.map, 'resize');
            	}
        	});
        	elem.addClass('btn-link').removeClass('btn-misc').html('<i class="icon-remove"></i>');
        	
    	} else {
        	editElem.slideUp('slow', function(){
        		InstitutionMedicalCenter.undoChecked(editElem);
        		viewElem.fadeIn();
            	elem.addClass('btn-misc').removeClass('btn-link').html('<i class="icon-edit"></i> Edit');
            	$('section.hca-main-profile .edit-awards').removeClass('disabled');
        	});
    	}
    },
    
    
    /**
     * if container is closed without saving undo changes
     */
    undoChecked: function(editElem) {
    	$(editElem.attr('data-filter-list')).find('input[type=checkbox]:checked:not(.old)').removeAttr('checked');
    	$(editElem.attr('data-filter-list')).find('input[type=checkbox].old:not(:checked)').attr('checked', 'checked');
    },
    
    filterAwardsList: function(elem ) {
    	elem.parent().find('.hca-edit-box:first').html($('#awardsForm'));
    	$('#awardsForm .control-group > .awards-listing').hide();
    	$($('#awardsForm').parent().attr('data-filter-list')).show();
    	$('#awardsForm h3.awards-heading').hide();
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
        _modal.modal('show').appendTo('body');
        
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
    	
    	$('.control-group').removeClass('error');
    	$('.control-group > ul._error-list').remove();

        _button = $(domButtonElement);
        _buttonHtml = _button.html();
        _button.html(InstitutionMedicalCenter._processing).attr('disabled', true);
        _form = _button.parents('form');
        _parent = _button.parents('form');
        if(!_form.attr('action')){
        	_form = _button.parents('div#edit-medical-center-name').find('form');
        	_parent = _button.parents('div#edit-medical-center-name');
        }
        _data = _form.serialize();
        _parent.find('.alert-box').removeClass('alert alert-error alert-success').html("");
        _parent.find('.error').removeClass('error');
        $('.errorText').remove();
        
     	if(_form.attr('id') == 'awardsForm'){
    		$("div[id^='show-']").animate({
    		    opacity: 0.25,
    		  });
    	}
     	 _editButton = _button.parents('section.hca-main-profile').find('a.btn-edit');
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
                    	var emptyString = '<b>no <span>{FIELD_LABEL}</b> added. <a onclick="InstitutionMedicalCenter.toggleForm($(\'#clinic-edit-contacts-btn\'))" class="btn btn-primary btn-small"><i class="icon-plus"></i> Add {FIELD_LABEL}';

                		if(response.institutionMedicalCenter.websites == null || response.institutionMedicalCenter.contactEmail == null || response.institutionMedicalCenter.contactDetails.phoneNumber == ''){
                    		$("#alertDiv").addClass('alert alert-block');
                    	}else{
                    		$("#alertDiv").removeClass('alert alert-block');
                    	}
                    	
                       	if(response.institutionMedicalCenter.websites){
                    		$('#profileWebsitesText').html('<b>http://'+ response.institutionMedicalCenter.websites +'</b>');
                    	}else{
                    		$('#profileWebsitesText').html(emptyString.replace(/{FIELD_LABEL}/g,'clinic website'));
                    	}
                       	
                     	if(response.institutionMedicalCenter.contactEmail){
                     		$('#profileEmailText').html('<b>'+response.institutionMedicalCenter.contactEmail+'</b>');
                    	}else{
                    		$('#profileEmailText').html(emptyString.replace(/{FIELD_LABEL}/g,'contact email'));
                    	}
                     	
                    	if(response.institutionMedicalCenter.contactDetails.phoneNumber){
                     		$('#PhoneNumberText').html('<b>'+response.institutionMedicalCenter.contactDetails.phoneNumber+'</b>');
                    	}else{
                    		$('#PhoneNumberText').html(emptyString.replace(/{FIELD_LABEL}/g,'phone number'));
                    	}
                        break;

                    case 'socicalMediaSitesForm':
                    	var websites = response.institutionMedicalCenter.socialMediaSites;
                    	$.each(websites, function(type) {
                    		if($.trim(websites[type]) != '') {
                    			if($('._twitter-wrapper').html() == 'no account added.'){
                    				$('#view-socialMediaSites > div').attr('class','alert alert-block').find('._' + type + '-wrapper').html('<b>'+websites[type] +'</b>');
                    			}else{
                					$('#view-socialMediaSites > div').attr('class','').find('._' + type + '-wrapper').html('<b>'+websites[type] +'</b>');
                    			}
                    		} else {
                    			$('#view-socialMediaSites > div').addClass('alert alert-block').find('._'+ type + '-wrapper').html('<b>no '+type+' account.</b> added <a onclick="InstitutionMedicalCenter.toggleForm($(\'#clinic-edit-mediaSites-btn\'))" class="btn btn-primary btn-small"><i class="icon-plus"></i> Add '+type+' Account');
                    		}
                    	});
                  	break;
                       
                    case 'servicesForm':
                    	$('#servicesTable').html(response.html);
                    	break;
                    case 'awardsForm':

                		/* NOTE: DO NOT REMOVE this line. This is a temporary fix for edit award's year. */
                		$('#_edit-award-form-container').html($('#_edit-award-form'));
                		/* End of NOTE: DO NOT REMOVE this line */

                		$("div[id^='show-']").animate({opacity: 1});
                    	 $.each(response.html, function(key, htmlContent){
                      		$('#listing-'+key).find("input[type=checkbox].old:not(:checked)").removeClass('old');
                    		$('#listing-'+key).find("input[type=checkbox]:checked:not(.old)").addClass('old');
                        	
                        	$('#'+key+'sText').html(htmlContent);
                         });
                    
                    	break;
                    	
                    case 'businessHoursForm':
                        InstitutionMedicalCenter.displayBusinessHoursView();
                        break;
                }

                _button.html(_buttonHtml).attr('disabled', false);
                _editButton.click();
                
                // Display Callout Message

                HCA.alertMessage('success', 'Clinic Profile has been updated!');
            },

            error: function(response) {
                _button.html(_buttonHtml).attr('disabled', false);

                if (response.status==400) {
                    var errors = $.parseJSON(response.responseText).html;
                    if (errors.length) {
                        $.each(errors, function(key, item){
                        	$('.control-group.' + item.field).addClass('error');
                        	$('<ul class="_error-list"><li>'+item.error+'</li></ul>').insertAfter(_form.find('div.'+item.field+' > input'));
                        });
                    }

                	HCA.alertMessage('error', 'We need you to correct some of your input. Please check the fields in red.');
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
    submitRemoveSpecializationForm: function(elem) {
        currentHtml = elem.html();
        elem.attr('disabled', true).html('Processing...');
        deleteFormElem = $('#_delete-specialization-form-' + elem.data('specialization-id'));

        $.ajax({
            url: deleteFormElem.attr('action'),
            data: deleteFormElem.serialize(),
            type: 'POST',
            success: function(response){
            	$('#specialization_list_block').children('div').removeClass('disabled');
            	elem.parents('.modal:first').modal('hide');
                $('#specialization_'+response.id).remove();
                $('#new_specializationButton').removeAttr('disabled');

                HCA.alertMessage('success', 'You have successfully remove specialization');
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

}; // end InstitutionMedicalCenter

InstitutionMedicalCenter.displayBusinessHoursView = function(){
    // FIXME: this is just a quick patch
    var selectedBusinessHourEls = $('#fbh_data_container').find('.hca-workingday-details');
    $('#businessHoursView').html('');
    $.each(selectedBusinessHourEls, function(_k, _el){
        var _li = $('<li></li>');
        $(_el).clone().appendTo(_li);
        _li.find('a').remove();
        $('#businessHoursView').append(_li);
    });
    // end busineshour code
};

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

