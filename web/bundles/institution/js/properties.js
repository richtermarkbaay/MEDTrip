/**
 * @author allejochrisvelarde
 */

(function($){
	
	var isMedicalCenterEditPage = false;
	
    function split( val ) {
        return val.split( /,\s*/ );
    }
    
    function extractLast( term ) {
        return split( term ).pop();
    }
    
    function _showModal(_modal) {
        _modal.modal('show');
    }
    
    function _hideModal(_modal) {
        _modal.modal('hide');
    }
    
    /***
     * handler for Global Award related js functionalites
     */
    $.globalAward = {
        _editForm: null
    };
    
    $.globalAward.options = {
        'year_acquired_json_key': 'year_acquired',
        'edit': {
            'modal': null, // jQuery element for the modal container
            'input_extraValueAutocomplete_json': 'input.extraValueAutocomplete_json', // identifier of the hidden input element that will hold the JSON value of the extraValue field
            'input_extraValueAutocomplete': 'input.extraValueAutocomplete', // identifier of the input text element that will hold the  value of the extraValue
            'submit_button': '#year_submit', // identifier of the submit button
            'year_acquired_column': '.yearAcquired'
        },
        'autocompleteYear': {
            'minimumYear': 1920
        },
        'autocompleteAward': {
        	'remoteUrl': '',
        	'selectedDataContainer': '',
        	'minLength': 1,
        	'loader' : 'tr.loader',
        	'field' : ''
        },
    };
    
    $.GlobalAutocompleteAction = {
    		
		'_loadHtmlContentUri' :'',
		
		'setLoadHtmlContentUri': function (_val) {
	        this._loadHtmlContentUri = _val;
	        
	        return this;
	    },
	    
	    'removeGlobalAward': function(_domButtonElement) {
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
	   	        	
	   	        	// Display Message Callout
	   	        	if(InstitutionMedicalCenter.isEditView) {
	   	        		InstitutionMedicalCenter.displayCallout(response);
	   	        	}
	   	        }
	   	     });
	       },
	       
	       'showCommonModalId': function (_linkElement) {
	           _linkElement = $(_linkElement);
	           _id = _linkElement.data('id');
	           _name = $('#globalAwardRow_'+_id).find('h5').html();
	           _modal = $(_linkElement.attr('data-target'));
	           $('#id').val(_id);
	           $(".modal-body p strong").text(_name+'?');
	           
	           return false;
	       },
    };
    
    $.globalAward.actions = {
        'edit': function (_self) {
            $.globalAward._editForm = $.globalAward.options.edit.modal.find('form'); 
            
            // bind the form event
            $.globalAward._editForm.submit($.globalAward._submitEditForm);
            
         // bind click event
            $(_self).click($.globalAward._clickEdit);
            return _self;
        },
        'autocompleteYear': function (_self) {
            // autocomplete jquery plugin
            _self.bind('keydown', function (event){
            	 if (!(event.keyCode == 8                                // backspace
                         || event.keyCode == 9                               // tab
                         || event.keyCode == 46                              // delete
                         || event.keyCode == 61								// +
                         || event.keyCode == 188						 // comma
                         || (event.keyCode >= 35 && event.keyCode <= 40)     // arrow keys/home/end
                         || (event.keyCode >= 48 && event.keyCode <= 57)     // numbers on keyboard
                         || (event.keyCode >= 96 && event.keyCode <= 105)    // number on keypad
                         || (event.keyCode == 65 && (event.ctrlKey || event.shiftKey)))          // ctrl + a, on same control
                         || ((event.keyCode >= 48 && event.keyCode <= 58 || event.keyCode == 173 ) && event.shiftKey)  //shift and ! to - on same control
                         || ((event.keyCode >= 48 && event.keyCode <= 58 || event.keyCode == 173 ) && event.altKey)  //altKey and ! to - on same control
                     ) {
      				event.preventDefault();					
      			}	
            });
            
            return _self;
        },
        'autocompleteAward': function (_self) {
			_self.bind('keydown', function (event){
    				_this = $( this );
    	            if ( event.keyCode === $.ui.keyCode.TAB && $( this ).data( "autocomplete" ).menu.active ){
    	                event.preventDefault();
    	            }
	        }).autocomplete({
	        	'minLength': $.globalAward.options.autocompleteAward.minLength,
	        	'source': function (request, res) {
	        		_loader = $(_this.attr('data-fieldId'));
	        		_loader.show();
	    			$.ajax({
	    				url: $.globalAward.options.autocompleteAward.remoteUrl,
	    				data: {term: request.term, 'type': _this.attr('data-globalAwardType')},
	    				success: function(response){
	    					_loader.hide();
	    					res($.ui.autocomplete.filter(response, extractLast( request.term ) ) );
	    				}
	    			});
	    		},
	    		'select': function( event, ui) {
	    			$(_this.attr('data-globalAwardContainer')).find('.loader').show();
	    			
	       	        $.ajax({
	       	            url: $.GlobalAutocompleteAction._loadHtmlContentUri,
	       	            data: {'id':ui.item.id},
	       	            type: 'POST',
	       	            dataType: 'json',
	       	            success: function(response) {
	       	            	_new_row = $(response.html); 
	       	            	_new_row.find('a.edit_global_award').bind('click', $.globalAward._clickEdit);
	       	            	_table = $(_this.attr('data-globalAwardContainer'));
	       	            	_table.append(_new_row);
	       	            	_table.find('.loader').hide();

	       	            	// Display Message Callout
	       	            	if(InstitutionMedicalCenter.isEditView) {
	       	            		InstitutionMedicalCenter.displayCallout(response);
	       	            	}

	       	            },
	       	            error: function(response) {
	       	            	$(_this.attr('data-globalAwardContainer')).find('.loader').hide();
	       	            }
	       	        });
	              	
	                  return false;
	              }
	        });
        }
    };
    
    $.globalAward._clickEdit = function(_event) {
        _el = $(this);
        _form = _el.parents('li').find($.globalAward._editForm);
        if(_form.find('input#institution_global_award_form_extraValueAutocomplete').length <= 0){
        	_input = $('div#show-awards').find('form:first').find('#institution_global_award_form_extraValueAutocomplete');
        	_extraValue = $('div#show-awards').find('form:first').find('input#institution_global_award_form_extraValue');
        	_form_value = $('div#show-awards').find('form:first').find('input#institution_global_award_form_value');
    		_token = $('div#show-awards').find('form:first').find('input#institution_global_award_form__token');
    		
        	_input.clone().insertBefore(_form.find("#year_submit"));
        	_extraValue.clone().insertAfter(_form.find("#year_submit"));
        	_form_value.clone().insertAfter(_form.find("#year_submit"));
        	_token.clone().insertAfter(_form.find("#year_submit"));
        }
        $.globalAward._editForm.attr('action', _el.attr('href'));
        _el.parents('li').find('span#containerRow').hide();
        _el.parents('li').find($.globalAward._editForm).show();
        if(_el.parents('li').find('span.yearAcquired').html() == " "){
        	_el.parents('li').find($.globalAward.options.edit.input_extraValueAutocomplete).val('');
        }else{
        	_el.parents('li').find($.globalAward.options.edit.input_extraValueAutocomplete).val(_el.parents('li').find('span.yearAcquired').html());
        }
        return false;
    };
    // submit edit form handler
    $.globalAward._submitEditForm = function(_event) {
        _form = _event.parent('form');
        _button = _form.find($.globalAward.options.edit.submit_button);
        _buttonHtml = _button.html();
        _autocomplete = _form.find($.globalAward.options.edit.input_extraValueAutocomplete);
        // convert autocomplete value to JSON
        _form.hide();
        _form.prev('img#loader_ajax').show(); //display loading image
        
        _year = _autocomplete.val().replace(/,+/g, ',');
        _year = $.trim(_year);
        _year = $.unique(_year.split(",")).filter(function(e){ return e.length}).join(",");
        _newValYear = _year.replace(/, ,/g,',');
  
        var _b = {
            'year_acquired': _autocomplete ? split(_newValYear) : []
        };
        // NOTE: JSON is only available in modern browsers, IE8, FF, Chrome
        _extraValueJSON = window.JSON.stringify(_b);
        // update value of hidden extraValue field
        _form.find($.globalAward.options.edit.input_extraValueAutocomplete_json).val(_extraValueJSON);
        _form.find('input.globalAwardId').val(_form.find('input[name="globalAwardId"]').val());
        $.ajax({
            url: _form.attr('action'),
            type: 'post',
            data: _form.serialize(),
            dataType: 'json',
            success: function(response) {
                _currentRow = $(response.targetRow);
                // currently only replace year acquired
	                _currentRow.find($.globalAward.options.edit.year_acquired_column).html(response.html);
                if(response.html != ''){
                	_currentRow.find($.globalAward.options.edit.year_acquired_column).next('a.edit_global_award').html('<i class="icon-edit"></i>');
                }else{
                	_currentRow.find($.globalAward.options.edit.year_acquired_column).next('a.edit_global_award').html('Add Year');
                }
                _currentRow.find('img#loader_ajax').hide();
                _currentRow.find('span#containerRow').show();
//                // Display Message Callout
//                if(InstitutionMedicalCenter.isEditView) {
//                	InstitutionMedicalCenter.displayCallout(response);
//                }
            },
            error: function(response) {
                _button.html(_buttonHtml).attr('disabled', false);
            }
        });
        
        return false;
    };
    
    $.fn.globalAward = function(_action, _options){
        
        $.extend($.globalAward.options[_action] || {}, _options);
        
        return $.globalAward.actions[_action]
            ? $.globalAward.actions[_action](this)
            : this;
    };
    
    /** ==============================
     * end of global award related JS functionality
     */
    
})(jQuery);
