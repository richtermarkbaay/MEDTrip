var viewMedicalCenterManager = {
	// jQuery element for toggle-accordion
    showSpecialization: function(_linkElement) {
    	_linkElement = $(_linkElement);
    	var div_id = _linkElement.attr('data-target');
        var _url = _linkElement.attr('href');
        var _hiddenInputField = _linkElement.attr('data-hidden-field');
        var specializationDiv = $(_linkElement.attr('data-specialization-div'));
        if($(_hiddenInputField).val() == 1) {
        	$(div_id).collapse('hide');
        	$(_hiddenInputField).val(0);
        }
        else {
            $('div.accordion-body.in').collapse('hide');
        	$(div_id).collapse('toggle');
        	$(_hiddenInputField).val(1);
        	if($.trim(specializationDiv.html()) == '') {
        		specializationDiv.html('<div class="ajax-loading">loading...</div>');
            	$.ajax({
                    url: _url,
                    type: 'GET',
                    success: function(response) {
                    	specializationDiv.html(response);
                    }
                });
            }
        }
    }
};