var Institution = {
	saveInquiry : function(_buttonElem) {
		_button = $(_buttonElem);
		_buttonHtml = _button.html();
		_button.attr('disabled', true)
		.html('Processing...');
		_modal = $('#'+_button.attr('data-modalId'));
		_formId = $(_button).attr('data-formId');
		_href = $(_formId).attr('action');
		_fieldParent = $(_button).attr('data-divParent');
		$.ajax({
            type: 'POST',
            url: _href,
            dataType: "json",
            data: $(_formId).serialize(),
            success: function(response) {
            	_button.html(_buttonHtml).attr('disabled', false);
            	_modal.modal('hide');
            	Institution.removeErrors(_formId);
            },
            error: function(json) {
            	Institution.removeErrors(_formId);
                _button.html(_buttonHtml).attr('disabled', false);
                if (json.status == 400) {
                    // invalid form
                	_json = $.parseJSON(json.responseText);
                	$.each(_json.html, function(key, item){
                		_field = _fieldParent+item.field;
                		$(_formId).find(_field).addClass('error');
                		$('div.alert-error').addClass('alert').append(item.error+"<br>");
                	});
                    
                }
                
            }
        });
		return this;
	},
	
	removeErrors: function(_formId) {
		_formId = $(_formId);
		_formId.find('.control-group').removeClass('error');
		_formId.find('.error').removeClass('error');
		$('div.alert-error').removeClass('alert').html('');
	},
	
	clearForm: function(_name) {
		_name.reset();
		_formId = $('#'+_name.id); 
		_formId.find('.control-group').removeClass('error');
		_formId.find('.error').removeClass('error');
		$('div.alert-error').removeClass('alert').html('');

		return this;
    },
}