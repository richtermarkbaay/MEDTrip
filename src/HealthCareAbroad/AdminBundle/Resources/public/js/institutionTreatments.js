var InstitutionSpecialization = {
        
    _processing: 'Processing...',
        
    removeTreatment: function(_linkElement) {
        var _linkElement = $(_linkElement);
        if (_linkElement.hasClass('disabled')) {
            return false;
        }
        _href = _linkElement.attr('href');
        _html = _linkElement.html();
        _linkElement.html(this._processing).addClass('disabled');
        
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
        _modal = $(_linkElement.attr('data-target'));
        _modal.modal('show');
        _modal.find('.ajax_loader').show();
        $.ajax({
           url: _linkElement.attr('href'),
           type: 'GET',
           dataType: 'json',
           success: function(response) {
               _modal.find('.ajax_loader').hide();
               _modal.find('div.ajax_content_container').html(response.html);
               _modal.find('button.submit_button').attr('disabled', false);
           },
           error: function(response) {
               _modal.find('.ajax_loader').hide();
               _modal.find('ajax_content_container').html('Failed loading treatments.');
               console.log(response);
           }
        });
    },
    
    submitAddTreatmentsForm: function(_buttonElement) {
        _el = $(_buttonElement);
        _modal = _el.parents('div.add_treatments_modal');
        _form = _modal.find('form.add_treatments_form');
        _html = _el.html();
        _el.html(this._processing).attr('disabled', true);
        $.ajax({
            url: _form.attr('action'),
            data: _form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                _modal.find('div.ajax_content_container').html('');
                _modal.find('.ajax_loader').hide();
                
                // replace the treatments list
                _modal.parents('div.maincontentblock').find('.institution_specialization_treatments_container').html(response.html);
                _modal.modal('hide');
            },
            error: function (response) {
                _modal.find('div.ajax_content_container').html('');
                _modal.find('.ajax_loader').hide();
                _modal.modal('hide');
            }
        });
    },
    submitAddTreatmentsForm: function(_buttonElement) {
        _el = $(_buttonElement);
        _modal = _el.parents('div.add_treatments_modal');
        _form = _modal.find('form.add_treatments_form');
        _html = _el.html();
        _el.html(this._processing).attr('disabled', true);
        $.ajax({
            url: _form.attr('action'),
            data: _form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                _modal.find('div.ajax_content_container').html('');
                _modal.find('.ajax_loader').hide();
                
                // replace the treatments list
                _modal.parents('div.maincontentblock').find('.institution_specialization_treatments_container').html(response.html);
                _modal.modal('hide');
            },
            error: function (response) {
                _modal.find('div.ajax_content_container').html('');
                _modal.find('.ajax_loader').hide();
                _modal.modal('hide');
            }
        });
    },
    
    removeSpecialization: function(_linkElement) {
        var _linkElement = $(_linkElement);
        if (_linkElement.hasClass('disabled')) {
            return false;
        }
        _href = _linkElement.attr('href');
        _html = _linkElement.html();
        _linkElement.html(this._processing).addClass('disabled');
        
        $.ajax({
            url: _href,
            type: 'POST',
            success: function(response) {
            	_linkElement.parents('div#specialization_block_'+response.id).remove();
            },
            error: function(response) {
                console.log(response);
            }
        });
        
        return false;
    },
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
	            source: function (request, res) {
		        		InstitutionSpecializationAutocomplete._loaderElement.fadeIn();
		    			$.ajax({
		    				url: InstitutionSpecializationAutocomplete.autocompleteOptions.source,
		    				data: {term: request.term},
		    				success: function(response){
		    					res(response);
		    					InstitutionSpecializationAutocomplete._loaderElement.hide();
		    				}
		    			});
	              },
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