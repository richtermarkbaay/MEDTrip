/**
 * Handler for client-side functionalities in institution profile page
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

var InstitutionProfile = {
		
    removePropertyUri: '',
    
    ajaxUrls: {
        'loadActiveMedicalCenters': '', 
        'loadInstitutionServices':'',
        'loadInstitutionAwards': '',
        'updateCoordinates': ''
    },
    
    modals: {
    	'name': null,
    	'description': null,
        'address': null,
        'contact_number': null,
        'contact_email': null,
        'websites': null
    },
    
    _commonDialogOptions: {
        position: ['center', 100],
        autoOpen: false,
        width: 'auto',
        height: 'auto',
        modal: true,
        resizable: false,
        close: function() {}
    },
    
    medicalCenterTabbedContentElement: null,
    
    servicesTabbedContentElement: null,
    
    awardsTabbedContentElement: null,
    
    // jQuery DOM element for the tabbed content
    tabbedContentElement: null,

    initializeModals: function(_options) {
        $.each(_options, function(_key, _val){
            InstitutionProfile.modals[_key] = _val;
            InstitutionProfile.modals[_key].dialog(InstitutionProfile._commonDialogOptions);
        });
        
        return this;
    },
    
    openModal: function(_name) {
        //InstitutionProfile.modals[_name].dialog("open");
        return this;
    },
    
    closeModal: function(_name) {
        //InstitutionProfile.modals[_name].dialog('close');
    	_name.reset();	
    	_formId = $('#'+_name.id); 
    	_formId.find('ul.text-error').remove();
    		
        return this;
    },
    
    setAjaxUrls: function(_val){
        this.ajaxUrls = _val;
        
        return this;
    },
    
    setTabbedContentElement: function(_val) {
        InstitutionProfile.tabbedContentElement = _val;
        
        return this;
    },
    
    setMedicalCentersTabbedContentElement: function(_val) {
        InstitutionProfile.medicalCenterTabbedContentElement = _val;
        
        return this;
    },
    
    
    setServicesTabbedContentElement: function(_val) {
        InstitutionProfile.servicesTabbedContentElement = _val;
        
        return this;
    },
    
    setAwardsTabbedContentElement: function(_val) {
        InstitutionProfile.awardsTabbedContentElement = _val;
        
        return this;
    },
    
    switchTab: function(_tab_element_key)
    {
    	
        switch (_tab_element_key) {
            case 'medical_centers':
                InstitutionProfile.tabbedContentElement.html(InstitutionProfile.medicalCenterTabbedContentElement.html());
                break;
            case 'services':
                InstitutionProfile.tabbedContentElement.html(InstitutionProfile.servicesTabbedContentElement.html());
                break;
            case 'awards':
                InstitutionProfile.tabbedContentElement.html(InstitutionProfile.awardsTabbedContentElement.html());
                break;
        }
        
        return this;
    },
    
    pagerMedicalCenter: function(_linkElement) {
    	 var _linkElement = $(_linkElement);

        _href = _linkElement.attr('href');
        $.ajax({
            url: _href,
            type: 'GET',
            success: function(response) {
            	$('#medicalCenterListing').html(response.output.html);
            },
            error: function(response) {
                console.log(response);
            }
        });
        
        return false;
    },
    
    
    loadTabbedContentsOfMultipleCenterInstitution: function() {
        // medical centers content
        $.ajax({
            url: InstitutionProfile.ajaxUrls.loadActiveMedicalCenters,
            type: 'get',
            dataType: 'json',
            success: function(response){
                InstitutionProfile.medicalCenterTabbedContentElement.html(response.output.html);
            }
        });
        
//        // institution services content
//        $.ajax({
//            url: InstitutionProfile.ajaxUrls.loadInstitutionServices,
//            type: 'get',
//            dataType: 'json',
//            success: function(response){
//                InstitutionProfile.servicesTabbedContentElement.html(response.services.html);
//            }
//        });
//        
//        // awards content
//        $.ajax({
//            url: InstitutionProfile.ajaxUrls.loadInstitutionAwards,
//            type: 'get',
//            dataType: 'json',
//            success: function(response){
//                InstitutionProfile.awardsTabbedContentElement.html(response.awards.html);
//            }
//        });
        
        return this;
    },
    
    /**
     * set up dialog box for edit instituion name
     * 
     * @parameter jQuery DOM element
     */
    setUpInstitutionNameDialog: function (_dialogContentElement) {
        this.institutionNameDialogElement = _dialogContentElement;
        this.institutionNameDialogElement.dialog({
            position: ['center', 100],
            autoOpen: false,
            width: 'auto',
            modal: true,
            resizable: false,
            close: function() {}
        });
        
        return this;
    },
    
    setUpInstitutionDescriptionDialog: function(_dialogContentElement) {
        this.institutionDescriptionDialogElement = _dialogContentElement;
        this.institutionDescriptionDialogElement.dialog({
            position: ['center', 100],
            autoOpen: false,
            width: 'auto',
            modal: true,
            resizable: false,
            close: function() {}
        });
        
        return this;
    },
    
    removeProperty: function(_propertyId, _container) {
        _container.find('a.delete').attr('disabled',true);
        $.ajax({
            type: 'POST',
            url: InstitutionProfile.removePropertyUri,
            data: {'id': _propertyId},
            success: function(response) {
                _container.remove();
            }
        });
        
    },
    openProfileForm: function(_element){
    	_element.toggle();
    	_show = $(_element.attr('data-toggle'));
    	_attr = _element.attr('href');
    	$('#'+_show.selector + ', ' + _attr).toggle();
    },
    
    openWebsiteFormButton: function(_element){
    	_attr = _element.attr('href');
    	_element.parents('div.show').hide();
    	_element.parents('div.show').prev().hide();
    	$(_attr).toggle();
    },
    
    closeProfileForm: function(_element){
    	_div = _element.parents('div.hca-edit-box').prev('div');
    	_div.show();
    	_div.prev().show();
    	_element.parent().hide();
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
        		
        		InstitutionProfile.filterAwardsList(elem );
        	} /* end of TODO: Temporary Fixed */
        	
        	viewElem.hide();
        	editElem.slideDown('slow', function(){
        		// Refresh Map for Edit Address
            	if(elem.attr('data-edit-elem') == "#address") {
        	        google.maps.event.trigger(HCAGoogleMap.map, 'resize');
            	}
        	});
        	elem.addClass('btn-link').removeClass('btn-misc').html('<i class="icon-remove"></i>');
        	
    	} else {
        	editElem.slideUp('slow', function(){
        		InstitutionProfile.undoChecked(editElem);
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

    submitModalForm: function(_formElement, _successCallback) {
        $.ajax({
           url: _formElement.attr('action'),
           data: _formElement.serialize(),
           type: 'POST',
           success: _successCallback
        });
    },
    
    /**
     * Clicking on submit button of modal 
     * 
     * @param DOMElement button
     */
    submitForm: function(_form) {

    	$('.control-group').removeClass('error');
    	$('.control-group > ul._error-list').remove();
    	
    	modalContainer = _form.parents('.modal:first'); 
    	if(modalContainer.length) {
    		_button = modalContainer.find('._submit-button:first');
    	} else {
        	_button = _form.find('button[type=submit]:first');    		
    	}

        _buttonHtml = _button.html();
        _button.html("Processing...").attr('disabled', true);

        $('.control-group.ajax-field.error').removeClass('error').find('ul.error_list').remove();

        if(_form.attr('id') == 'awardsForm'){
    		$("div[id^='show-']").animate({
    		    opacity: 0.25,
    		  });
    	}

        _editButton = _button.parents('section.hca-main-profile').find('.btn-edit');
        
        $.ajax({
            url: _form.attr('action'),
            data: _form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                switch(_form.attr('id')){
                    case 'nameModalForm':
                        $('#institutionNameText').html(ucwords(response.institution.name));
                        $('#networkName').html(ucwords(response.institution.medicalProviderGroups));
                        _form.parents('div.modal').modal('hide');
                    	break;
    
                    case 'descriptionModalForm':
                        $('#institutionDescriptionText').html(response.institution.description);
                        break;
    
                    case 'addressModalForm':
                    	var address = [];
                        var _street_address = [];
                        $.each(response.institution.address1, function(_k, _v){
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
                            if (response.institution[_v]) {
                                address.push(ucwords(response.institution[_v]));
                            }
                        });
                        
                		$('.addressLabel').html('Edit Address');
                        _html = '<span class="address_part">' + address.join(',&nbsp;</span><span class="address_part">')+'</span>';
                        
                        $('.address_column').find('span.address_part').remove();
                        $('.address_column').prepend(_html);
                                                
                        if(HCAGoogleMap.map) { 
                        	mapStaticUrl = 'http://maps.googleapis.com/maps/api/staticmap?center='+ response.institution.coordinates + '&zoom=15&size=260x200&sensor=false&markers=%7Alabel:S%7C' + response.institution.coordinates;
                        	$('#institution-static-map').prop('src', mapStaticUrl);
                        }
                        
                        break;
    
                    case 'numberModalForm':
                    	var emptyString = '<b>no <span>{FIELD_LABEL}</b> added. <a onclick="InstitutionProfile.toggleForm($(\'#institution-edit-contacts-btn\'))" class="btn btn-primary btn-small"><i class="icon-plus"></i> Add {FIELD_LABEL}';

                    	if(response.institution.websites == null || response.institution.contactEmail == null || response.institution.contactDetails.phoneNumber == ''){                    		
                    		$("#alertDiv").addClass('alert alert-block');
                    	}else{
                    		$("#alertDiv").removeClass('alert alert-block');
                    	}
                    	
                       	if(response.institution.websites){
                    		$('#profileWebsitesText').html('<b>http://'+response.institution.websites + '</b>');
                    	}else{
                    		$('#profileWebsitesText').html(emptyString.replace(/{FIELD_LABEL}/g,'hospital website'));
                    	}
                       	
                     	if(response.institution.contactEmail){
                     		$('#profileEmailText').html('<b>'+response.institution.contactEmail+ '</b>');
                    	}else{
                    		$('#profileEmailText').html(emptyString.replace(/{FIELD_LABEL}/g,'contact email'));
                    	}

                     	if(response.institution.contactDetails.phoneNumber){
                     		$('#PhoneNumberText').html('<b>'+response.institution.contactDetails.phoneNumber + '</b>');
                    	}else{
                    		$('#PhoneNumberText').html(emptyString.replace(/{FIELD_LABEL}/g,'phone number'));
                    	}

                        break;
                    case 'socialMediaForm':
                    	var websites = response.institution.socialMediaSites;
                    	var hasError = false;
                    	$.each(websites, function(type) {
	                    		if($.trim(websites[type]) != '') {
	                    				$('#institution-socialMediaSites > div').attr('class','').find('._' + type + '-wrapper').html('<b>'+websites[type] +'</b>');
	                    		}else{
	                    			hasError = true;
	                    			$('#institution-socialMediaSites > div').find('._'+ type + '-wrapper').html('<b>no '+type+' account.</b> added <a onclick="InstitutionProfile.toggleForm($(\'#institution-edit-socialmedia-btn\'))" class="btn btn-primary btn-small"><i class="icon-plus"></i> Add '+type+' Account');
	                        	}
                    	});
                    	
                    	if(hasError){
                    		$('#institution-socialMediaSites > div').addClass('alert alert-block');
                    	}
                    	
                    	break;
                    case 'serviceForm':
                    	$('#serviesText').html(response.html);
                    	break;
                    case 'awardsForm':

                    		/* NOTE: DO NOT REMOVE this line. This is a temporary fix for edit award's year. */
                    		$('#_edit-award-year-container').html($('#_edit-award-form'));
                    		/* End of NOTE: DO NOT REMOVE this line */

	                		$("div[id^='show-']").animate({opacity: 1});
	                    	 $.each(response.html, function(key, htmlContent){
	                       		$('#listing-'+key).find("input[type=checkbox].old:not(:checked)").removeClass('old');
	                    		$('#listing-'+key).find("input[type=checkbox]:checked:not(.old)").addClass('old');
	                        	$('#'+key+'sText').html(htmlContent);
	                         });
                    	break;
                } 

                _button.html(_buttonHtml).attr('disabled', false);
                _editButton.click();
                HCA.alertMessage('success', 'successfully updated!');
            },
            error: function(response) {
                _button.html(_buttonHtml).attr('disabled', false);
                if (response.status==400) {
                    var errors = $.parseJSON(response.responseText).html;
                    if (errors.length) {
                        $.each(errors, function(key, item){
                        	$('.control-group.' + item.field).addClass('error');
                        	$('<ul class="_error-list"><li>'+item.error+'</li></ul>').insertAfter(_form.find('div.'+item.field+' > ' + (item.field == 'city' ? '.custom-select' : 'input')));

                        });
                    }

                	HCA.alertMessage('error', 'We need you to correct some of your input. Please check the fields in red.');
                }
            }
        });
        return false;
    },
};

var InstitutionProfileEvents = {
    UPDATE_INSTITUTION_NAME_EVENT : $.Event('update_institution_name'),
    
    UPDATE_INSTITUTION_DESCRIPTION_EVENT : $.Event('update_institution_description')
};