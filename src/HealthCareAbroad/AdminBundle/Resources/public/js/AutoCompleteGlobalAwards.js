var InstitutionGlobalAwards = {
		
	    showCommonModalId: function (_linkElement) {
	        _linkElement = $(_linkElement);
	        _id = _linkElement.data('id');
	        _name = $('#globalAwardRow_'+_id).find('h5').html();
	        _modal = $(_linkElement.attr('data-target'));
	        $('#id').val(_id);
	        $(".modal-body p strong").text(_name+'?');
	        
	        return false;
	    },
		
	    removeGlobalAward: function(_domButtonElement) {
	     	 _button = $(_domButtonElement);
	   	  	_form = _button.parents('.modal').find('form');
	   	    _button.html("Processing...").attr('disabled', true);

	   	    $.ajax({
	   	        url: _form.attr('action'),
	   	        data: _form.serialize(),
	   	        type: 'POST',
	   	        success: function(response){
	   	        	_form.parents('div.modal').modal('hide');
	   	        	_button.html("Delete").attr('disabled', false);
	   	        	$('#globalAwardRow_'+response.id).remove();
	   	        }
	   	     });
       },
	};