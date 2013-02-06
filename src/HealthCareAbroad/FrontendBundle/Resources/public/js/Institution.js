var Institution = {
	saveInquiry : function(_buttonElem) {
		_button = $(_buttonElem);
		_buttonHtml = _button.html();
		_button.attr('disabled', true)
		.html('Processing...');
		_modal = $(_button.attr('data-modalId'));
		_formId = $(_button).attr('data-formId');
		_href = $(_formId).attr('action')
		$.ajax({
            type: 'POST',
            url: _href,
            dataType: "json",
            data: $(_formId).serialize(),
            success: function(response) {
            	_modal.modal('hide');
            },
            error: function(json) {
            	Institution.removeErrors(_formId);
                _button.html(_buttonHtml).attr('disabled', false);
                if (json.status == 400) {
                    // invalid form
                	_json = $.parseJSON(json.responseText);
                	$.each(_json.html, function(key, item){
                		$(_formId).find('.'+item.field).addClass('error').find('.help-inline').html(item.error)
                	});
                    
                }
                
            }
        });
	},
	
	removeErrors: function(_formId) {
		_formId = $(_formId);
		_formId.find('.help-inline').html('');
		_formId.find('.control-group').removeClass('error');	
	},
	
	clearForm: function(_name) {
		_name.reset();
		_formId = $('#'+_name.id); 
		_formId.find('.help-inline').html('');
		_formId.find('.control-group').removeClass('error');
        
        return this;
    },
}