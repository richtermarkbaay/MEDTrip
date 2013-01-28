var AutocompleteGlobalAwards = {
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
	        $.each(AutocompleteGlobalAwards.autocompleteOptions, function(_key, _val){
	            if (_val.target) {
	                _val.target.autocomplete({
	                    minLength: 0,
	                    source: _val.source,
	                    select: function( event, ui) {
	                    	AutocompleteGlobalAwards._loadContent(ui.item.id, _val);
	                        return false;
	                    }
	                });
	            }
	        });
	    },
	    
	    _loadContent: function(_val, _option) {
	        _option.loader.show();
	        $.ajax({
	            url: AutocompleteGlobalAwards._loadHtmlContentUri,
	            data: {'id':_val},
	            type: 'POST',
	            dataType: 'json',
	            success: function(response) {
	            	_new_row = $(response.html); 
	            	_new_row.find('a.edit_global_award').bind('click', $.globalAward._clickEdit);
	                _option.selectedDataContainer.append(_new_row);
	                _option.target.find('option[value='+_val+']').hide();
	                _option.loader.hide();
	            },
	            error: function(response) {
	                _option.loader.hide();
	            }
	        });
	    }
	}

var InstitutionGlobalAwards = {
		
	    showCommonModalId: function (_linkElement) {
	        _linkElement = $(_linkElement);
	        _id = _linkElement.data('id');
	        _name = $('#globalAwardRow_'+_id).find('h5').html();
	        _modal = $(_linkElement.attr('data-target'));
	        $('#id').val(_id);
	        $(".modal-body p strong").text(_name+'?');
	        
	        return false;
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
	   	        	$('#globalAwardRow_'+response.id).remove();
	   	        }
	   	     });
       },
	};