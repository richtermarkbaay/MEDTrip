/**
 * Handler for client-side functionalities in institution profile page
 */
var InstitutionProfile = {
		
    removePropertyUri: '',
    
    ajaxUrls: {
        'loadActiveMedicalCenters': '', 
        'loadInstitutionServices':'',
        'loadInstitutionAwards': ''
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
    
    showCommonModalId: function (_linkElement) {
        _linkElement = $(_linkElement);
        _id = _linkElement.data('id');
        _name = $('#award'+_id).find('h5').html();
        _modal = $(_linkElement.attr('data-target'));
        $('#id').val(_id);
        $(".modal-body p strong").text(_name+'?');
        
        return false;
    },
    
    closeModal: function(_name) {
        InstitutionProfile.modals[_name].dialog('close');
        
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
        $('.progress').show();
        $('.medical_centers').hide();
        $.ajax({
            url: _href,
            type: 'GET',
            success: function(response) {
            	$('.progress').hide();
            	$('.medical_centers').show();
            	$('#medical_centers').html(response.html);
            },
            error: function(response) {
                console.log(response);
            }
        });
        
        return false;
    },
    
    
    loadTabbedContentsOfMultipleCenterInstitution: function() {
        // medical centers content
        /**$.ajax({
            url: InstitutionProfile.ajaxUrls.loadActiveMedicalCenters,
            type: 'get',
            dataType: 'json',
            success: function(response){
                InstitutionProfile.medicalCenterTabbedContentElement.html(response.medicalCenters.html);
                InstitutionProfile.switchTab('medical_centers');
            }
        });**/
        
        // institution services content
        $.ajax({
            url: InstitutionProfile.ajaxUrls.loadInstitutionServices,
            type: 'get',
            dataType: 'json',
            success: function(response){
                InstitutionProfile.servicesTabbedContentElement.html(response.services.html);
            }
        });
        
        // awards content
        $.ajax({
            url: InstitutionProfile.ajaxUrls.loadInstitutionAwards,
            type: 'get',
            dataType: 'json',
            success: function(response){
                InstitutionProfile.awardsTabbedContentElement.html(response.awards.html);
            }
        });
        
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
    
    removeGlobalAward: function(_domButtonElement) {
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
	        	$('#award'+response.id).remove();
	        }
	     });
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
                        }
                        _keys = ['city', 'state', 'country', 'zipCode'];
                        $.each(_keys, function(_k, _v){
                            if (response.institution[_v]) {
                                address.push(response.institution[_v]);
                            }
                        });
                        _html = '<span class="address_part">' + address.join(',&nbsp;</span><span class="address_part">')+'</span>';
                        
                        $('.address_column').find('span.address_part').remove();
                        $('.address_column').prepend(_html);
                        
                        break;
    
                    case 'numberModalForm':
                        var number = response.institution.contactNumber;
                        $('#profileNumberText').html(number.country_code + '-' + number.area_code + '-' + number.number);
                        break;
    
                    case 'emailModalForm':
                        $('#profileEmailText').html(response.institution.contactEmail);
                        break;
    
                    case 'websitesModalForm':
                        var websites = response.institution.websites, websitesString = ''; 
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
                _responseJson = $.parseJSON(response.responseText);
                if (_responseJson.form_error) {
                    _form.prepend($(_responseJson.form_error_html));
                }
            }
        });
        return false;
    }
    
 
};

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

var InstitutionProfileEvents = {
    UPDATE_INSTITUTION_NAME_EVENT : $.Event('update_institution_name'),
    
    UPDATE_INSTITUTION_DESCRIPTION_EVENT : $.Event('update_institution_description')
};