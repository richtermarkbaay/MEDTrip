/**
 * JS for institution users profile
 * @author Chaztine Blance
 */
var InstitutionUserProfile = {
		
   submitInstitutionProfileForm: function(domButtonElement) {
        _button = $(domButtonElement);
        _buttonHtml = _button.html();
        _button.html("Processing...").attr('disabled', true);
        _form = _button.parents('form');
        _data = _form.serialize();
        $.ajax({
            url: _form.attr('action'),
            data: _data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                switch(_form.attr('id')){
                    case 'editProfile':
                        $('.account-info').html(response.html);
                    	break;
                    case 'changePassword':
                        $('.institution-info').html(response.html);
                    	break;
                } 
                // Display Modal after submit
                $('#transactions').modal('show');
                $('#alert-meassage').html(response.alert);
                _button.html(_buttonHtml).attr('disabled', false);
            },
            error: function(response) {
            	// Display Modal after submit
        		 $('#transactions').modal('show');
                 $('#alert-meassage').html(response.alert);
                _button.html(_buttonHtml).attr('disabled', false);
            }
        });
        return false;
    }
};