/**
 * Autocomplete handler for institution specialization selector
 * 
 * @author allejochrisvelarde
 */

var InstitutionSpecialization = {
        
    _processing: 'Processing...',
    
    specializationsListContainer: null,
        
    setSpecializationsListContainerElement: function (_element) {
        this.specializationsListContainer = _element;
        
        return this;
    },
        
    removeTreatment: function(_linkElement, _container) {
        if (_linkElement.hasClass('disabled')) {
            return false;
        }
        _href = _linkElement.attr('href');
        _html = _linkElement.html();
        _linkElement.html(_processing).addClass('disabled');
        
        $.ajax({
            url: _href,
            type: 'POST',
            success: function(response) {
                _linkElement.parents('tr').remove();
            },
            error: function(response) {
                console.log(response);
            }
        });
        
        return false;
    },

    showAddTreatmentsForm: function(_linkElement) {
        $('#new_specializationButton').attr("disabled", "disabled"); //set add new button to disabled
        parentElem = _linkElement.parents('.specializations-profile-listing:first');
        accordionContent = $(_linkElement.attr('href'));
        _divToggle = parentElem.find('.hca-hidden-content:first');
        HCA.closeAlertMessage();

        if(!accordionContent.find('ul.specialization-wrapper:first').length) {
        	_divToggle.show();
        	accordionContent.hide();
            $('#specialization_list_block').children('div').addClass('disabled');
        	parentElem.addClass('process');
            $.ajax({
                url: _linkElement.attr('data-load-url'),
                type: 'GET',
                dataType: 'json',
                success: function(response){
                	accordionContent.append(response.html).slideDown('slow');
                	parentElem.removeClass('disabled process');
                    $.each(parentElem.find('.sub-specialization-wrapper'), function(){
                        if($(this).find('input[type=checkbox].treatments:checked').length) {
                        	$(this).find('input[name=subSpecialization]').attr('checked', 'checked');
                        }                    	
                    });

                    _linkElement.hide().next().show();
                }
            });
        } else {
            _divToggle.slideDown('slow', function() {  
            	parentElem.siblings().addClass('disabled');
            	_linkElement.hide().next().show();
            });
        }
    },
    
    submitAddTreatmentsForm: function(_buttonElement) {
        _form = $(_buttonElement.attr('href'));

        if(!_form.find('.specialization-wrapper input:checked').length) {
        	_buttonElement.prev().prev().click();
        	return;
        }

        parentElem = _buttonElement.parents('.specializations-profile-listing:first').addClass('disabled process');
        _divToggle = parentElem.find('.hca-hidden-content:first').scrollTop(0);
        
        deleteTreatments = [];
        $.each(_form.find('input[type=checkbox].treatments.old:not(:checked)'), function(){
        	deleteTreatments.push($(this).val());
        });

        _form.find('input[name=deleteTreatments]').val(deleteTreatments.join(','));

        $.ajax({
            url: _form.attr('action'),
            data: _form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                $('#new_specializationButton').removeAttr('disabled');

                _form.find('input[type=checkbox].treatments:checked:not(.old)').addClass('old');
                _form.find('input[type=checkbox].treatments.old:not(:checked)').removeClass('old');
                
                _buttonElement.hide().prev().show();
                $('#specialization_list_block').children().removeClass('disabled process');
                _divToggle.slideUp('slow');

                HCA.alertMessage('success', 'You have successfully updated treatments.');
            },
            error: function (response) {
            	$('#specialization_list_block').children().removeClass('disabled process');
                HCA.alertMessage('error', response.responseText);
            }
        });
    },

    /**
     * Clicking on submit button of modal Add Specialization form
     * 
     * @param DOMElement button
     */
    submitAddSpecialization: function(_button) {
        _buttonHtml = _button.html();
        _button.html('Processing...').attr('disabled', true);
        _form = _button.parents('form:first');

        $.ajax({
            url: _form.attr('action'),
            data: _form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(response) {
            	HCA.alertMessage('success', 'You have successfully added specializations!');
                $('#specialization_list_block').find('div.alert-block').hide();

                  // insert new content after last specialization block
                 $.each(response.html, function(_k, _v){
                     if ($('#specialization_list_block').html() == ''){
                         $('#specialization_list_block').html(_v);
                     }else{
                         $(_v).insertBefore($('#specialization_list_block div:first'));
                     }
                 });
                _form.parents('#add-specialization-wrapper').hide();
                $('#specialization_list_block').slideDown().prev().slideDown();
                _button.html(_buttonHtml).attr('disabled', false);
            },
            error: function(response) {
            	console.log(response);
            	HCA.alertMessage('error', response.responseText);
                _button.html(_buttonHtml).attr('disabled', false);
            }
        });
        
        return false;
    },
    
    toggle: function (elem){
        _attr = $(elem.attr('data-toggle'));
        elem.parent().slideUp().next().slideUp();
        $('#add-specialization-wrapper').show();
        _attr.html('<div class="align-center" style="padding:10px"><img src="/images/institution/spinner_large.gif"/></div>').show();
        $(_attr).parents('div.edit-specializations').show();
        elem.next().find('.edit-specializations').show();
        HCA.closeAlertMessage();

        $.ajax({
        	url: elem.attr('data-href'),
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                $(_attr.selector).html(response.html);
            },
            error: function(response) { 
                
            }
        });
    },
    
    treatmentsCheckBox: function (){
        _target = $('input:checkbox[id^="treatments"]:checked');
        if(_target.length >1){
            
              $.each(_target, function(_k, _v){
                  _subSpecializationCheckbox = _v.parentNode.parentNode.parentNode.parentNode.children.item('label').children;
                  _subSpecializationCheckbox.subSpecialization.setAttribute("checked", "checked");
               });

        }else{
            _subSpecializationCheckbox = _target.parent().parent().parent().parent().find('input:checkbox[name="subSpecialization"]');
               _subSpecializationCheckbox.attr('checked', 'checked');
        }
    }
};

