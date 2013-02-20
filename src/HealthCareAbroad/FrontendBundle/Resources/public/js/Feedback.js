var Feedback = {
		
    showModal: function (_linkElement) {
        _linkElement = $(_linkElement);
        _modal = $(_linkElement.attr('data-target'));
        _modal.find('.ajax_loader').show();
        _url = _linkElement.data('id');
	        $.ajax({
               url: _url,
               type: 'GET',
               dataType: 'json',
               success: function(response){
            	   _modal.find('.ajax_loader').hide();
            	   _modal.find('.modal-body').html(response.html);
               }
            });
    },
    
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
            	Feedback.removeErrors("#feedbackForm");
                _button.html(_buttonHtml).attr('disabled', false);
                if (json.status == 400) {
                    // invalid form
                	_json = $.parseJSON(json.responseText);
                	$.each(_json.html, function(key, item){
                		_field = _fieldParent+item.field;
                		$('#feedbackForm').find(_field).addClass('error');
                	});
                	$('div.alert-error').addClass('alert').append("Please fill up the form properly.");
                }
                
            }
        });
    },
	removeErrors: function(_formId) {
		_formId = $(_formId);
		_formId.find('.control-group').removeClass('error');
		_formId.find('.error').removeClass('error');
		$('div.alert-error').removeClass('alert').html('');
	},
};