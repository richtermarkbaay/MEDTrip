var Institution = {
	saveInquiry : function(_buttonElem) {
		_button = $(_buttonElem);
		_button.attr('disabled', true)
		.html('Processing...');
		_modal = $(_button.attr('data-modalId'));
		_formId = $(_button).attr('data-formId');
		_href = $(_formId).attr('action')
		$.ajax({
            type: 'POST',
            url: _href,
            data: $(_formId).serialize(),
            success: function(response) {
            	_modal.modal('hide');
            }
        });
	}
}