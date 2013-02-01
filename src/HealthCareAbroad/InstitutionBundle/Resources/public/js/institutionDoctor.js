/**
 * Handler for client-side functionalities in institution profile page
 */
var InstitutionDoctor = {
		
    removePropertyUri: '',
    
    ajaxUrls: {
        'loadActiveMedicalCenters': '', 
        'loadInstitutionSpecialization':''
    },
    
    modals: {
    	'name': null,
    	'details': null,
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
    
    doctorTabbedContentElement: null,
    
    specializationTabbedContentElement: null,
    
    // jQuery DOM element for the tabbed content
    tabbedContentElement: null,

    initializeModals: function(_options) {
        $.each(_options, function(_key, _val){
        	InstitutionDoctor.modals[_key] = _val;
            InstitutionDoctor.modals[_key].dialog(InstitutionDoctor._commonDialogOptions);
        });
        
        return this;
    },
    
    openModal: function(_name) {
        //InstitutionDoctor.modals[_name].dialog("open");
        
        return this;
    },
    
    closeModal: function(_name) {
    	InstitutionDoctor.modals[_name].dialog('close');
        
        return this;
    },
    
    setAjaxUrls: function(_val){
        this.ajaxUrls = _val;
        
        return this;
    },
    
    setTabbedContentElement: function(_val) {
    	InstitutionDoctor.tabbedContentElement = _val;
        
        return this;
    },
    
    setDoctorTabbedContentElement: function(_val) {
    	InstitutionDoctor.doctorTabbedContentElement = _val;
        
        return this;
    },
    
    setSpecializationTabbedContentElement: function(_val) {
    	InstitutionDoctor.specializationTabbedContentElement = _val;
        
        return this;
    },
    
    switchTab: function(_tab_element_key)
    {
        switch (_tab_element_key) {
            case 'doctor':
            	InstitutionDoctor.tabbedContentElement.html(InstitutionDoctor.doctorTabbedContentElement.html());
                break;
            case 'specializations':
            	InstitutionDoctor.tabbedContentElement.html(InstitutionDoctor.specializationTabbedContentElement.html());
                break;
        }
        
        return this;
    },
    
    loadTabbedContentsOfMultipleCenterInstitution: function() {
        // institution services content
        $.ajax({
            url: InstitutionDoctor.ajaxUrls.loadInstitutionSpecialization,
            type: 'get',
            dataType: 'json',
            success: function(response){
            	$('#specializations').html(response.specializations.html);
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
    submitInstitutionSidebarForms: function(_domButtonElement) {
    	
		_button = $(_domButtonElement);
   	  	_form = _button.parents('.modal').find('form');
   	    _button.html("Processing...").attr('disabled', true);
    	
        $.ajax({
            url: _form.attr('action'),
            data: _form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(response) {
            	
                switch(_form.attr('id')){
                    case 'doctorsFullName':
                        $('#doctorsFullNameText').html(response.info.firstName +' '+ response.info.lastName);
                    	break;
    
                    case 'biography':
                        $('#doctorDescriptionText').html(response.info.details);
                        
                      	if(response.info.details){
                    		$('.descriptionLabel').html('Edit Doctor\'s Biography');
                    	}else{
                    		$('.descriptionLabel').html('Add Doctor\'s Biography');
                    	}
                        
                        break;
                } 
                _form.parents('div.modal').modal('hide');
                _button.html("Submit").attr('disabled', false);
            },
            error: function(response) {
                _button.html("Submit").attr('disabled', false);
                _responseJson = $.parseJSON(response.responseText);
                if (_responseJson.form_error) {
                    _form.prepend($(_responseJson.form_error_html));
                }
            }
        });
        return false;
    }
};
