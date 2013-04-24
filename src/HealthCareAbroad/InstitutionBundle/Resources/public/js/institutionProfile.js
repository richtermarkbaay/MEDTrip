/**
 * Handler for client-side functionalities in institution profile page
 */
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
    	_element.hide();
    	_attr = _element.attr('href');
    	if(_attr == "#address"){
    		GoogleMap.initialize();
	        google.maps.event.trigger(GoogleMap.map, 'resize');
    	}
    	_element.next('div.show').hide();
    	$(_attr).show();
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
        _form = _button.parents('.hca-edit-box').find('form');
        _divToShow = _button.parents('section.hca-main-profile').find('div.show');
        _editButton = _button.parents('section.hca-main-profile').find('div.show').prev();
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
                        $('#institutionNameText').html(response.institution.name);
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
                                address.push(response.institution[_v]);
                            }
                        });
                        
                		$('.addressLabel').html('Edit Address');
                        _html = '<span class="address_part">' + address.join(',&nbsp;</span><span class="address_part">')+'</span>';
                        
                        $('.address_column').find('span.address_part').remove();
                        $('.address_column').prepend(_html);
                        
                        if(GoogleMap.map) { 
                        	GoogleMap.updateMap(_street_address + ',' + response.institution.city + ',' + response.institution.country);
                        }
                        
                        break;
    
                    case 'numberModalForm':
                        var number = response.institution.contactNumber.phone_number;
                        
                        $('#profileWebsitesText').html(response.institution.websitesString);
                       	$('#profileEmailText').html(response.institution.contactEmail);
                        $('#profileNumberText').html(number.number);
                        break;
                    case 'socialMediaForm':
                    	  var websites = response.institution.socialMediaSites, websitesString = ''; 
                    	  		websitesString += '<p><i class="icon-twitter"> </i> <b>'+  websites.twitter + "</b></p>";
                    	  		websitesString += '<p><i class="icon-facebook"> </i><b>'+ websites.facebook + "</b></p>";
                    	  		websitesString += '<p><i class="icon-google-plus"> </i> <b>'+ websites.googleplus + "</b></p>";
	                        $('#soclialMediaText').html(websitesString);
                    	break;
                    case 'serviceForm':
                    	$('#serviesText').html(response.html);
                    	break;
                    case 'awardsForm':
                    	$('#awardsText').html(response.html);
                    	break;
                } 
                _divToShow.show();
                _divToHide.hide();
                _editButton.show();
                _form.find('ul.text-error').remove();
                _button.html(_buttonHtml).attr('disabled', false);
            },
            error: function(response) {
                _button.html(_buttonHtml).attr('disabled', false);
                _responseJson = $.parseJSON(response.responseText);
                if (_responseJson.form_error) {
                    _form.prepend($(_responseJson.form_error_html));
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