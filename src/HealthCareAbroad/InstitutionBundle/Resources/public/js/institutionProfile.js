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

    	if(_attr == "#address"){
	        google.maps.event.trigger(HCAGoogleMap.map, 'resize');
    	}
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

    submitModalForm: function(_formElement, _successCallback) {
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
    submitInstitutionSidebarForms: function(domButtonElement) {
        _button = $(domButtonElement);
        _buttonHtml = _button.html();
        _button.html("Processing...").attr('disabled', true);
        _form = _button.parents('form');
        _parent = _button.parents('form');
        if(!_form.attr('action')){
        	_form = _button.parents('div#hca-edit-institution-name').find('form');
        	_parent = _button.parents('div#hca-edit-institution-name');
        }
        _divToShow = _button.parents('section.hca-main-profile').find('div.show');
        _editButton = _button.parents('section.hca-main-profile').find('div.show').prev();
    	_divToHide = _button.parents('section.hca-main-profile').find('div.hca-edit-box');
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
                       	if(response.institution.websites){
                    		$('#profileWebsitesText').html(' http://www.<b>'+ response.institution.websites +'</b>');
                    		$("#alertDiv").attr('class', ' ');
                    	}else{
                    		$('#profileWebsitesText').html('<b> no clinic website </b> added. <a onclick="InstitutionProfile.openWebsiteFormButton($(this)); return false;" class="btn btn-primary btn-small" href="#number" ><i class="icon-plus"></i> Add Clinic Website</a>');
                    		$("#alertDiv").attr('class', 'alert alert-block');
                    	}
                       	$('#profileEmailText').html(response.institution.contactEmail);
                        $('#PhoneNumberText').html(response.institution.contactDetails.phoneNumber);
                        break;
                    case 'socialMediaForm':
                    	  var websites = response.institution.socialMediaSites, websitesString = ''; 
	                      	if(websites.twitter){
		                  		websitesString += '<p><i class="icon-twitter"> </i> <b>'+  websites.twitter + "</b></p>";
		                  	}else{
		                  		websitesString += '<p class="alert-block"><i class="icon-twitter"> </i> <b> no account added</b></p>';
		                  	}
		                  	if(websites.facebook){
		                  		websitesString += '<p><i class="icon-facebook"> </i><b>'+ websites.facebook + "</b></p>";
							}else{
								websitesString += '<p class="alert-block"><i class="icon-facebook"> </i><b> no account added </b></p>';
							}
		                  	if(websites.googleplus){
		                  		websitesString += '<p><i class="icon-google-plus"> </i><b>'+ websites.googleplus + "</b></p>";
							}else{
								websitesString += '<p class="alert-block"><i class="icon-google-plus"> </i><b> no account added </b></p>';
							}
	                        $('#soclialMediaText').html(websitesString);
                    	break;
                    case 'serviceForm':
                    	$('#serviesText').html(response.html);
                    	break;
                    case 'awardsForm':
                    	$('#awardsText').html(response.html);
                    	break;
                } 
                $('.errorText').remove();
                _divToShow.show();
                _divToHide.hide();
                _editButton.show();
                _button.html(_buttonHtml).attr('disabled', false);
            },
            error: function(response) {
            	
                _button.html(_buttonHtml).attr('disabled', false);
                if (response.status==400) {
                    var errors = $.parseJSON(response.responseText).html;
                    if (errors.length) {
                        var _errorString = "";
                        $.each(errors, function(key, item){
                        	_parent.find('div.'+item.field).addClass('error');
                        	if(item.field == 'country' || item.field == 'city'){
                        		$('<ul class="errorText"><li>'+item.error+'</li></ul>').insertAfter(_parent.find('div.'+item.field+' > div'));
                        	}else{
                        		$('<ul><li class="errorText">'+item.error+'</li></ul>').insertAfter(_parent.find('div.'+item.field+' > input'));
                        	}
                        });
                        _parent.find('.alert-box').removeClass('alert alert-error alert-success').html("");
                        _parent.find('.alert-box').addClass('alert alert-error').html('We need you to correct some of your input. Please check the fields in red. ');
                    }
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