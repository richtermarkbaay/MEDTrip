var ErrorReport = {
		
    showCommonModal: function (_linkElement) {
        _linkElement = $(_linkElement);
        _modal = $(_linkElement.attr('data-target'));
        $('.alert-success').hide();
        $('#ExceptionForm_details').val('');
//        _modal.modal('show');
        
//        return false;
    
    },
	submitReportForm: function(_domButtonElement) {
	        _button = $(_domButtonElement);
	        _buttonHtml = _button.html();
	        _form = _button.parents('.modal').find('form');
	    _data = _form.serialize();
	    if($('#ExceptionForm_details').val() == '' ){
	    	return;
	    }
	    $.ajax({
	        url: _form.attr('action'),
	        data: _data,
	        type: 'POST',
	        dataType: 'json',
	        success: function(response) {
	        	_button.parents('.modal').find('.alertContainer').html('<div class="alert alert-success"><strong>' +response +'</strong></div>');
	            _button.html("Sent").attr('disabled', false);
	        	_form.parents('.modal').fadeOut(2000);
	        	$('.modal-backdrop').fadeOut(2000);
	        },
	        error: function(response) {
	            _button.html("Send Now").attr('disabled', true);
	        }
	    });
	    return false;
	}
};