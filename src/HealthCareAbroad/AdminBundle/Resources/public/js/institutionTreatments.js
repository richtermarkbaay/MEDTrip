var InstitutionSpecialization = {
        
    _processing: 'Processing...',
        
    removeTreatment: function(_linkElement) {
        var _linkElement = $(_linkElement);
        if (_linkElement.hasClass('disabled')) {
            return false;
        }
        _href = _linkElement.attr('href');
        _html = _linkElement.html();
        _linkElement.html(this._processing).addClass('disabled');
        
        $.ajax({
            url: _href,
            type: 'POST',
            success: function(response) {
                console.log(response);
            },
            error: function(response) {
                console.log(response);
            }
        });
        
        return false;
    },
    
    showAddTreatmentsForm: function(_linkElement) {
        _linkElement = $(_linkElement);
        _modal = $(_linkElement.attr('data-target'));
        _modal.modal('show');
        _modal.find('.ajax_loader').show();
        $.ajax({
           url: _linkElement.attr('href'),
           type: 'GET',
           dataType: 'json',
           success: function(response) {
               _modal.find('.ajax_loader').hide();
               _modal.find('div.ajax_content_container').html(response.html);
               _modal.find('button.submit_button').attr('disabled', false);
           },
           error: function(response) {
               _modal.find('.ajax_loader').hide();
               _modal.find('ajax_content_container').html('Failed loading treatments.');
               console.log(response);
           }
        });
    },
    
    submitAddTreatmentsForm: function(_buttonElement) {
        _el = $(_buttonElement);
        _modal = _el.parents('div.add_treatments_modal');
        _form = _modal.find('form.add_treatments_form');
        _html = _el.html();
        _el.html(this._processing).attr('disabled', true);
        $.ajax({
            url: _form.attr('action'),
            data: _form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                _modal.find('div.ajax_content_container').html('');
                _modal.find('.ajax_loader').hide();
                
                // replace the treatments list
                _modal.parents('div.specializations_block').find('.institution_specialization_treatments_container').html(response.html);
                _modal.modal('hide');
            },
            error: function (response) {
                _modal.find('div.ajax_content_container').html('');
                _modal.find('.ajax_loader').hide();
                _modal.modal('hide');
            }
        });
    }
};