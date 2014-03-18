/**
 * @author Chaztine Blance
 *  Js for FLag Dropdown in Contact Number field
 */

(function($){
	
	$.ContactNumberForm = {
	        _editForm: null
	    };
	
	$.ContactNumberFormAction = {
		'_JsonValue': '',
		
		'_setJsonValue': function (_val) {
	        this._JsonValue = _val;
	        
	        return this;
	    },
	    
		'dropdownCountryCode': function(_element, _inputId) {
	        _spanCode = _element.find('.code').html();
	        _abbr = _element.find('span').attr('class'); //get country code
	        
	        _start_pos = _spanCode.indexOf('(') + 1;
	        _end_pos = _spanCode.indexOf(')',_start_pos);
	        _text_to_get = _spanCode.substring(_start_pos,_end_pos)
	       
	        _beforeParent = _element.parent().prev();
	        _beforeParent.find('span.flag16').attr('class', _abbr); //change button flag with newly selected abbr
	        _element.parent().hide();
	        
	        if(_inputId == ''){
	        	_inputId = _element.parent().siblings('input[type=text]:first'); //append to input field
	        	_inputId.val(_text_to_get);
	        	_inputId.next('input').val(_abbr.replace('flag16', ''));
	        	
	        	_countryInput = _element.parent().siblings('input[type=text]:third'); //get phone number /mobile number country code input field
	        	_countryInput.val(_text_to_get); //append country code
	        	
	        }else{
	            $('#'+_inputId).val(_text_to_get); //append to input field
	            $('#'+_inputId).next('input').val(_abbr.replace('flag16', ''));
	            
	            _countryInput = $('#'+_inputId).next().next('input');  //get phone number /mobile number country code input field
	        	_countryInput.val(_text_to_get); //append country code
	        }
	    },
	    
	    'changeFlag': function(_key, _val, _code, _element){
	    	if(_code){ //check if code passed is not empty
	    		
				_beforeParent = _element.parent('div.input-prepend').find('.dropdown-toggle');
				_abbr = _code.toLowerCase();
		    	_beforeParent.find('span.flag16').attr('class', 'flag16 '+ _abbr);
		    	_element.next('input').val(_abbr);
		    	_element.next().next('input').val(_val);
		    	return false;
	    	}
	    },
	    'invalidCountryCode': function(_element){
	    	_beforeParent = _element.parent('div.input-prepend');
	    	_beforeParent.attr('class', 'input-prepend row-field control-group error');
	    	
	    	return false;
	    },
	    
	    'clearErrors': function(_element){
	    	_beforeParent = _element.parent('div.input-prepend');
	    	_beforeParent.removeClass('error');
	    },
	};

	$.ContactNumberForm.actions = {
	
    'contactInputField': function (_self) {
    	_self.bind('keyup', function (event){
    		_this = $( this );
				_this.parent().find('.dropdown-menu').hide(); // hide dropdown field
				$.ContactNumberFormAction.clearErrors(_this); 
		    	_minlength = 2;
				if(_this.val().substring(0, 1) != '+' ){
					$.ContactNumberFormAction.invalidCountryCode(_this);
				}
				else{
					_selector = _this.val().replace('+', '');
					if (_selector.length >= _minlength ) {
				    	$.each( $.ContactNumberFormAction._JsonValue, function(_key,_val){
				    		if(_selector == _val.value){ 
		    					$.ContactNumberFormAction.changeFlag(_selector, _val.value, _val.id, _this);
		    				}
				    	});
					}else{
						$.ContactNumberFormAction.invalidCountryCode(_this);
					}
				}
    	 }).bind( "keydown", function( event ) {
    		 if (!(event.keyCode == 8                                // backspace
                   || event.keyCode == 9                               // tab
                   || event.keyCode == 46                              // delete
                   || event.keyCode == 61								// +
                   || (event.keyCode >= 35 && event.keyCode <= 40)     // arrow keys/home/end
                   || (event.keyCode >= 48 && event.keyCode <= 57)     // numbers on keyboard
                   || (event.keyCode >= 96 && event.keyCode <= 105)    // number on keypad
                   || (event.keyCode == 65 && (event.ctrlKey || event.shiftKey)))          // ctrl + a, on same control
                   || ((event.keyCode >= 48 && event.keyCode <= 58 || event.keyCode == 173 ) && event.shiftKey)  //shift and ! to - on same control
                   || ((event.keyCode >= 48 && event.keyCode <= 58 || event.keyCode == 173 ) && event.altKey)  //altKey and ! to - on same control
               ) {
				event.preventDefault();					
			}			
		})
    	 ;
    	return false;
	    },
	};

    $.fn.ContactNumberForm = function(_action){
        
        return $.ContactNumberForm.actions[_action]
            ? $.ContactNumberForm.actions[_action](this)
            : this;
    };
	
})(jQuery);
