/**
 * Handler for client-side functionalities in institution profile page
 */
var InstitutionProfile = {
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
        _form = _button.parents('.modal-content').find('form');
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
                    var address = response.institution.address1; 
                    $('#profileAddressText').html(address.room_number + ', ' + address.building + ', '+ address.street);
                    $('#profileCityText').html(response.institution.city ? response.institution.city + ', ' : '');
                    $('#profileZipcodeText').html(response.institution.zipCode ? response.institution.zipCode + ', ' : '');
                    $('#profileStateText').html(response.institution.state ? response.institution.state + ', ' : '');
                    $('#profileCountryText').html(response.institution.country);
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
            }
        });
        return false;
    }
};

var InstitutionProfileEvents = {
    UPDATE_INSTITUTION_NAME_EVENT : $.Event('update_institution_name'),
    
    UPDATE_INSTITUTION_DESCRIPTION_EVENT : $.Event('update_institution_description')
};