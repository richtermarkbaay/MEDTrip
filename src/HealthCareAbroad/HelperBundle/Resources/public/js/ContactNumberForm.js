/**
 * @author Chaztine Blance
 * Js for Country Dropdown in Contact Number field
 */
var ContactNumberForm = {
		
	_setJsonValue: '',
	
	dropdownCountryCode: function(_element, _inputId) {
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
        }else{
            $('#'+_inputId).val(_text_to_get); //append to input field
            $('#'+_inputId).next('input').val(_abbr.replace('flag16', ''));
        }
    },
    
    changeFlag: function(_key, _val, _code, _element){
    	if(_code){ //check if code passed is not empty
			_beforeParent = _element.parent('div.input-prepend').find('.dropdown-toggle');
			_abbr = _code.toLowerCase();
	    	_beforeParent.find('span.flag16').attr('class', 'flag16 '+ _abbr);
	    	_element.next('input').val(_abbr);
	    	
	    	return false;
    	}
    },
    
    invalidCountryCode: function(_element){
    	_beforeParent = _element.parent('div.input-prepend');
    	_beforeParent.attr('class', 'input-prepend row-field control-group error');
    	
    	return false;
    },
    
    clearErrors: function(_element) {
    	_beforeParent = _element.parent('div.input-prepend');
    	_beforeParent.removeClass('error');
    },
    
    contactInputField: function(_element){
    	_element.parent().find('.dropdown-menu').hide(); // hide dropdown field
    	ContactNumberForm.clearErrors(_element); 
    	_minlength = 2;
//    	lastChar = _element.val().charAt(_element.val().substring(1,15).length-1);
		if(_element.val().substring(0, 1) != '+' ){
			ContactNumberForm.invalidCountryCode(_element);
		}
		else{
			_selector = _element.val().replace('+', '');
			if (_selector.length >= _minlength ) {
		    	$.each( ContactNumberForm._setJsonValue, function(_key,_val){
		    		for (var i = 0; i <= _selector.length; i++) {
		    			_input = _selector.substring(0, i);
		    			if(_selector.length == _val.value.length){ //check if value entered has the same lenght on country code list
		    				if(_input == _val.value){ 
		    					ContactNumberForm.changeFlag(_selector, _val.value, _val.id, _element);
		    				}
		        		}
		    		}
		    	});
			}else{
				ContactNumberForm.invalidCountryCode(_element);
			}
		}
    },
}