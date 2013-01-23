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

var InstitutionGlobalAwards = {
		
	    removeGlobalAward: function(_linkElement) {
	        _linkElement = $(_linkElement);
	        _id = _linkElement.attr('id').split('_')[1];
	        $.ajax({
	           type: 'POST',
	           url: _linkElement.attr('href'),
	           data: {id: _id},
	           success: function(response) {
	               _linkElement.parents('tr').remove();
	           },
	           error: function(response) {
	               console.log(response);
	           }
	        });
	    },
	};