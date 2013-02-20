var Feedback = {
    submitFeedbackMessageForm: function (_domButtonElement) {
        _button = $(_domButtonElement);
        _buttonHtml = _button.html();
        _button.html("Processing...").attr('disabled', true);
        _form = _button.parents('.modal').find('form');
        _fieldParent = $(_button).attr('data-divParent');
        _data = _form.serialize();
        $.ajax({
            url: _form.attr('action'),
            data: _data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
            	 _form.parents('.modal').modal('hide');
                 _button.html("Send Feedback").attr('disabled', false);
            },
            error: function(json) {
                _button.html(_buttonHtml).attr('disabled', false);
                if (json.status == 400) {
                    // invalid form
                	_json = $.parseJSON(json.responseText);
                	$.each(_json.html, function(key, item){
                		_field = _fieldParent+item.field;
                		$('#feedbackForm').find(_field).addClass('error');
                		$('div.alert-error').addClass('alert').append(item.error+"<br>");
                	});
                    
                }
                
            }
        });
    }
};