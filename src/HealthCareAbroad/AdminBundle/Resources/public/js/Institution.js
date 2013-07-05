/**
 * @author Chaztine Blance
 */
var Institution = {
		
	addRemoveAncillaryService: function(_linkElement) {
        return this._doAncillaryServiceAction(_linkElement);
    },
    
    updateInstitutionStatus: function(_button) {
    	_button = $(_button);
    	_modal = _button.attr('data-modalId');
    	_formElement = $(_modal).find('form');
    	_button.attr('disabled', true).html('Processing...');
		href = _formElement.attr('action');
       
        $.ajax({
            type: 'POST',
            url: href,
            data: _formElement.serialize(),
            success: function(response) {
            	_button.removeAttr('disabled')
                .html('Submit');
            	$(_modal).modal('hide');
            	$('#'+_formElement.attr('data-element')).attr('data-status', response.status);
            	$('.alert').html(response.html).show();
            }
        });
        	
    },
    
    _doAncillaryServiceAction: function (_linkElement) {

    	if (_linkElement.hasClass('disabled')) {
            return false;
        }
        _href = _linkElement.attr('href');
        _html = _linkElement.html();
        _linkElement.html('Processing...').addClass('disabled');
        
        $.ajax({
            url: _href,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
            	_linkElement.removeClass('disabled')
            	_linkElement.attr('href', response.href);
            	_linkElement.text(response.label);
            	
            	//check if newly added service
            	if(response._isSelected == true ){
	            	_linkElement.prev('i').attr('class','icon-minus');
            	}else{
	            	_linkElement.prev('i').attr('class','icon-plus');
            	}
            }
        });
        
        return false;
    }
};
        