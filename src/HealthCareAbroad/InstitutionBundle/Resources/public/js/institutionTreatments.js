/**
 * Autocomplete handler for institution specialization selector
 * 
 * @author allejochrisvelarde
 */

var InstitutionSpecialization = {
        
    _processing: 'Processing...',
    
    specializationsListContainer: null,
        
    setSpecializationsListContainerElement: function (_element) {
        this.specializationsListContainer = _element;
        
        return this;
    },
        
    removeTreatment: function(_linkElement, _container) {
        if (_linkElement.hasClass('disabled')) {
            return false;
        }
        _href = _linkElement.attr('href');
        _html = _linkElement.html();
        _linkElement.html(_processing).addClass('disabled');
        
        $.ajax({
            url: _href,
            type: 'POST',
            success: function(response) {
                _linkElement.parents('tr').remove();
            },
            error: function(response) {
                console.log(response);
            }
        });
        
        return false;
    },
    
    showAddTreatmentsForm: function(_linkElement) {
	   	 _linkElement = $(_linkElement);
	   	 $('#new_specializationButton').attr("disabled", "disabled"); //set add new button to disabled
	     _linkElement.hide();
	     _modifiableDiv = _linkElement.attr('href');
	     _divToggle = $(_modifiableDiv).parent('#hca-specialization-content');
	     $('#specialization_list_block').children('div').addClass('disabled');
	     
		 if($(_modifiableDiv).html() == '' ){
			 $(_modifiableDiv).hide();
			 $(_divToggle.parents('.specializations-profile-listing')).addClass('process');
             $('#loader_text'+_linkElement.attr('data-target')).show();
          $.ajax({
	          url: _divToggle.attr('data-href'),
	          type: 'GET',
	          dataType: 'html',
	          success: function(response){
	        	  $(_divToggle).slideDown('slow', function() {     
		        	    $(_divToggle.parents('.specializations-profile-listing')).removeClass('disabled process');
						$(_modifiableDiv).show();
						$(_modifiableDiv).html(response);
						InstitutionSpecialization.treatmentsCheckBox();
						_linkElement.next('#treatments-save').show();
	        	  });
	          }
	      });
        } else{
        	 $(_divToggle).slideDown('slow', function() {  
	        	$(_divToggle.parents('.specializations-profile-listing')).removeClass('disabled');
	    	    _linkElement.next('#treatments-save').show();
        	 });
        }
    },
    
    submitAddTreatmentsForm: function(_buttonElement) {
    	$('#new_specializationButton').removeAttr('disabled');
    	_buttonElement = $(_buttonElement);
        _form = _buttonElement.attr('href');
        _divToggle = $(_form).parent('#hca-specialization-content');
        $(_divToggle.parents('.specializations-profile-listing')).addClass('disabled process');
        $.ajax({
            url: $(_form).attr('action'),
            data: $(_form).serialize(),
            type: 'POST',
            dataType: 'json',
            success: function (response) {
            	_buttonElement.hide();
            	InstitutionMedicalCenter.displayAlert('<b> Congratulations! </b> You have successfully added treatment' , 'success');
            	$(_divToggle.parents('.specializations-profile-listing')).removeClass('process');
            	_divToggle.prev('#treatment_list').html(response.html);
            	_buttonElement.prev('#specialization-button').show();
        		$(_divToggle).slideUp('slow', function() {
        		   	$('#specialization_list_block').children('div').removeClass('disabled');
					_divToggle.prev('#treatment_list').show();
    			});
            },
            error: function (response) {
            	$(_divToggle.parents('.specializations-profile-listing')).removeClass('disabled');
            	InstitutionMedicalCenter.displayAlert(response.responseText, 'error');
            }
        });
    },
    
    
    /**
     * Clicking on submit button of modal Add Specialization form
     * 
     * @param DOMElement button
     */
    submitAddSpecialization: function(domButtonElement) {
        _button = $(domButtonElement);
        _buttonHtml = _button.html();
        _button.html('Processing...').attr('disabled', true);
        _form = $(_button).parents('form#institutionSpecializationForm');
        _data = _form.serialize();
        $.ajax({
            url: _form.attr('action'),
            data: _data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
            	InstitutionMedicalCenter.displayAlert('<b> Congratulations! </b> You have successfully added specialization', 'success');
            	if($('#specialization_list_block').find('div.alert-block')){
            		$('#specialization_list_block').find('div.alert-block').hide();
            	}
            	  // insert new content after last specialization block
            	 $.each(response.html, function(_k, _v){
            		 if ($('#specialization_list_block').html() == ''){
            			 $('#specialization_list_block').html(_v);
            		 }else{
            			 $(_v).insertBefore($('#specialization_list_block div:first'));
            		 }
            	 });
                _form.hide();
                $('#new_specializationButton').show();
                _button.html(_buttonHtml).attr('disabled', false);
            },
            error: function(response) {
            	InstitutionMedicalCenter.displayAlert(response.responseText, 'error');
                _button.html(_buttonHtml).attr('disabled', false);
            }
        });
        
        return false;
    },
    
    toggle: function (_element){
    	_attr = $(_element.attr('data-toggle'));
    	$(_attr).show();
    	$(_attr.selector).html('<center><img src="/images/institution/spinner_large.gif" /></center>');
    	$(_attr).parents('div.edit-specializations').show();
    	$(_attr).parents('form').show();
    	_element.hide();
    	_element.next().find('.edit-specializations').show();
    		_href = _element.attr('data-href');
    	      $.ajax({
    	            url: _href,
    	            type: 'GET',
    	            dataType: 'json',
    	            success: function(response) {
    	            	$(_attr.selector).html(response.html);
    	            },
    	            error: function(response) {
    	                console.log(response);
    	            }
    	        });
    },
    
    treatmentsCheckBox: function (){
        _target = $('input:checkbox[id^="treatments"]:checked');
        if(_target.length >1){
        	
        	  $.each(_target, function(_k, _v){
              	_subSpecializationCheckbox = _v.parentNode.parentNode.parentNode.parentNode.children.item('label').children;
              	_subSpecializationCheckbox.subSpecialization.setAttribute("checked", "checked");
               });

        }else{
        	_subSpecializationCheckbox = _target.parent().parent().parent().parent().find('input:checkbox[name="subSpecialization"]');
           	_subSpecializationCheckbox.attr('checked', 'checked');
        }
    }
};

var InstitutionSpecializationAutocomplete = {
    removePropertyUri: '',
    singleSelectionOnly: false,
    _loadSpecializationFormUri : '',
    _loaderElement: null, // jQuery DOM element loader
    autocompleteOptions: {
        source: '',
        target: null, // autocomplete target jQuery DOM element
        selectedDataContainer: null // jQuery DOM element container of selected data
    },
    
    // set InstitutionSpecializationAutocomplete.autocompleteOptions
    setAutocompleteOptions: function (_val) {
        this.autocompleteOptions = _val;
        
        return this;
    },
    
    setLoaderElement: function (_el) {
        this._loaderElement = _el;
        
        return this;
    },
    
    setLoadSpecializationFormUri: function(_val) {
        this._loadSpecializationFormUri = _val;
        
        return this;
    },
    removeProperty: function(_treatmentId, _container) {
    	_container.find('a.delete').attr('disabled',true);
        $.ajax({
            type: 'POST',
            url: InstitutionSpecializationAutocomplete.removePropertyUri,
            data: {'id': _treatmentId},
            success: function(response) {
                _container.remove();
            }
        });
        
    },
    
    autocomplete: function(){
        
        // initialize accordion for data container
        //InstitutionSpecializationAutocomplete.autocompleteOptions.selectedDataContainer.accordion({active: false, collapsible: true, heightStyle: "content"});
        
        InstitutionSpecializationAutocomplete.autocompleteOptions.target.autocomplete({
            minLength: 2,
            source: InstitutionSpecializationAutocomplete.autocompleteOptions.source,
            select: function( event, ui) {
                InstitutionSpecializationAutocomplete._loadSpecializationForm(ui.item.id);
                
                return false;
            }
        });
        
        return this;
    },
    
    _loadSpecializationForm: function (_val) {
        
        InstitutionSpecializationAutocomplete._loaderElement.fadeIn();
        if (InstitutionSpecializationAutocomplete.singleSelectionOnly) {
            InstitutionSpecializationAutocomplete.autocompleteOptions.selectedDataContainer.html("");
        }
        
        $.ajax({
            url: InstitutionSpecializationAutocomplete._loadSpecializationFormUri,
            data: {'specializationId':_val},
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                InstitutionSpecializationAutocomplete.autocompleteOptions.target.val("");
                InstitutionSpecializationAutocomplete._loaderElement.hide();
                InstitutionSpecializationAutocomplete.autocompleteOptions.selectedDataContainer
                    .prepend(response.html);
                InstitutionSpecializationAutocomplete.autocompleteOptions.target.find('option[value='+_val+']').hide();
            },
            error: function(response) {
                InstitutionSpecializationAutocomplete._loaderElement.hide();
            }
        });
    }
    
};