/**
 * Handler for client-side functionalities in institution profile page
 */

var InstitutionProfile = {

	/**
	 * Currently Not Being Used!
	 */
    removePropertyUri: '',
    
    ajaxUrls: {
        'loadActiveMedicalCenters': '', 
        'loadInstitutionServices':'',
        'loadInstitutionAwards': '',
        'updateCoordinates': ''
    },

    removeProperty: function(_propertyId, _container) {
        _container.find('a.delete').attr('disabled',true);
        $.ajax({
            type: 'POST',
            url: InstitutionProfile.removePropertyUri,
            data: {'id': _propertyId},
            success: function(response) {
                _container.remove();
            }
        });
        
    },
	/**
	 * End Of Currently Not Being Used!
	 */


    /* Added by: Adelbert Silla toggle edit/view mode */
    toggleForm: function(elem) {
    	if(elem.hasClass('disabled')) {
    		return false;
    	}
    	
    	viewElem = $(elem.attr('data-view-elem'));
    	editElem = $(elem.attr('data-edit-elem'));
    	
    	if(viewElem.is(':visible')) {
        	if(elem.hasClass('edit-awards')) { /* TODO: Temporary Fixed */
        		$('section.hca-main-profile .edit-awards').addClass('disabled');
        		elem.removeClass('disabled');

        		_type = editElem.attr('data-filter-list').replace('#listing-', '');
        		$('#awardTypeKey').val(_type);
        		
        		InstitutionProfile.filterAwardsList(elem );
        	} /* end of TODO: Temporary Fixed */
        	
        	viewElem.hide();
        	editElem.slideDown('slow', function(){
        		// Refresh Map for Edit Address
            	if(elem.attr('data-edit-elem') == "#address") {
        	        google.maps.event.trigger(HCAGoogleMap.map, 'resize');
            	}
        	});
        	elem.addClass('btn-link').removeClass('btn-misc').html('<i class="icon-remove"></i>');
        	HCA.closeAlertMessage();

    	} else {
        	editElem.slideUp('slow', function(){
        		InstitutionProfile.undoChecked(editElem);
        		viewElem.fadeIn();
            	elem.addClass('btn-misc').removeClass('btn-link').html('<i class="icon-edit"></i> Edit');
            	$('section.hca-main-profile .edit-awards').removeClass('disabled');
        	});
    	}
    },
    
    /**
     * if container is closed without saving undo changes
     */
    undoChecked: function(editElem) {    	
    	$(editElem.attr('data-filter-list')).find('input[type=checkbox]:checked:not(.old)').removeAttr('checked');
    	$(editElem.attr('data-filter-list')).find('input[type=checkbox].old:not(:checked)').attr('checked', 'checked');
    },

    filterAwardsList: function(elem ) {
    	elem.parent().find('.hca-edit-box:first').html($('#awardsForm'));
    	$('#awardsForm .control-group > .awards-listing').hide();
    	$($('#awardsForm').parent().attr('data-filter-list')).show();
    	$('#awardsForm h3.awards-heading').hide();
    },
    
    /**
     * Clicking on submit button of modal 
     * 
     * @param DOMElement button
     */
    submitForm: function(_form) {
        tinyMCE.triggerSave();
    	HCA.closeAlertMessage();
    	$('.control-group').removeClass('error').children('ul.error').remove();

        if(_form.attr('id') == 'serviceForm') {
        	if(!_form.find('ul.services-listing > li input:checked').length) {
        		_form.find('.control-group').addClass('error');
        		HCA.alertMessage('error', 'Please select at least one service.');
            	return false;
        	}
        }

    	if(_form.parents('.modal:first').length) {
    		_button = _form.parents('.modal:first').find('._submit-button:first');
    	} else {
        	_button = _form.find('button[type=submit]:first');    		
    	}

        if(_form.attr('id') == 'awardsForm') {
    		$("div[id^='show-']").animate({opacity: 0.25});
    	}

        _buttonHtml = _button.html();
        _button.html("Processing...").attr('disabled', true);

        $.ajax({
            url: _form.attr('action'),
            data: _form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                switch(_form.attr('id')){
                    case 'nameModalForm':
                        $('#institutionNameText').html(ucwords(response.institution.name));
                        institutionGroup = typeof(response.institution.medicalProviderGroups) != 'undefined' ? response.institution.medicalProviderGroups[0] : ''; 
                        $('#networkName').html(ucwords(institutionGroup));

                        _form.parents('div.modal:first').modal('hide');
                    	break;
    
                    case 'descriptionModalForm':
                        $('#institutionDescriptionText').html(response.institution.description);
                        if($.trim(response.institution.description)  == '') {
                        	$('#institutionDescriptionText').siblings('.alert').show();                        	
                        } else {
                        	$('#institutionDescriptionText').siblings('.alert').hide();
                        }
                        break;
    
                    case 'addressModalForm':
                        $('.address_column').html(response.institution.stringAddress);

                        if(HCAGoogleMap.map) { 
                        	mapStaticUrl = 'http://maps.googleapis.com/maps/api/staticmap?center='+ response.institution.coordinates + '&zoom=15&size=260x200&sensor=false&markers=%7Alabel:S%7C' + response.institution.coordinates;
                        	$('#institution-static-map').prop('src', mapStaticUrl);
                        }
                        break;
    
                    case 'numberModalForm':
                    	var emptyString = '<b>no <span>{FIELD_LABEL}</b> added. <a onclick="InstitutionProfile.toggleForm($(\'#institution-edit-contacts-btn\'))" class="btn btn-primary btn-small"><i class="icon-plus"></i> Add {FIELD_LABEL}';

                    	if(!response.institution.websites == null || response.institution.contactEmail == null || !response.institution.contactDetails){                    		
                    		$("#alertDiv").addClass('alert alert-block');
                    	}else{
                    		$("#alertDiv").removeClass('alert alert-block');
                    	}
                    	
                       	if(response.institution.websites){
                    		$('#profileWebsitesText').html('<b>'+response.institution.websites + '</b>');
                    	}else{
                    		$('#profileWebsitesText').html(emptyString.replace(/{FIELD_LABEL}/g,'website'));
                    	}
                       	
                     	if(response.institution.contactEmail){
                     		$('#profileEmailText').html('<b>'+response.institution.contactEmail+ '</b>');
                    	}else{
                    		$('#profileEmailText').html(emptyString.replace(/{FIELD_LABEL}/g,'contact email'));
                    	}

                     	if(response.institution.contactDetails){ 
                     		$('#PhoneNumberText').html('<b>'+ response.institution.contactDetails + '</b>');
                    	}else{
                    		$('#PhoneNumberText').html(emptyString.replace(/{FIELD_LABEL}/g,'phone number'));
                    	}

                        break;
                    case 'socialMediaForm':
                    	var websites = response.institution.socialMediaSites;
                    	$.each(websites, function(type, value) {
                    		if($.trim(websites[type]) != '') {
                				$('#institution-socialMediaSites').find('._' + type + '-wrapper').html('<b>'+ value +'</b>');
                    		} else{
                    			$('#institution-socialMediaSites').find('._'+ type + '-wrapper').html('<b>no '+type+' account.</b> added <a onclick="InstitutionProfile.toggleForm($(\'#institution-edit-socialmedia-btn\'))" class="btn btn-primary btn-small"><i class="icon-plus"></i> Add '+type+' Account');
                        	}
                    	});
                    	
                    	if($('#institution-socialMediaSites ._social-media-sites a.btn').length){
                    		$('#institution-socialMediaSites > div').addClass('alert alert-block');
                    	} else {
                    		$('#institution-socialMediaSites > div').removeClass('alert alert-block');
                    	}
                    	
                    	break;
                    case 'serviceForm':
                    	liList = '';
                    	$.each($('#' + _form.attr('id') + ' ul.services-listing > li input:checked'), function(){
                    		liList += '<li>' + $(this).next().text() + '</li>';
                    	});

                    	if($('#servicesText > ul.single-listing').length) {
                    		$('#servicesText > ul.single-listing:first').html(liList);                        	                    		
                    	} else {
                    		$('#servicesText').html('<ul class="single-listing">' + liList + '</ul>');
                    	}

                    	break;
                    case 'awardsForm':
            			$("div[id^='show-']").animate({opacity: 1});

                		/* NOTE: DO NOT REMOVE this line. This is a temporary fix for edit award's year. */
                		if(!$('#_edit-award-form-container').find('#_edit-award-form').length) {
                			$('#_edit-award-form-container').html($('#_edit-award-form'));
                		} /* End of NOTE: DO NOT REMOVE this line */

                   		$('#listing-'+response.awardsType).find("input[type=checkbox].old:not(:checked)").removeClass('old');
                		$('#listing-'+response.awardsType).find("input[type=checkbox]:checked:not(.old)").addClass('old');
                    	$('#'+response.awardsType+'sText').html(response.awardsHtml);
                    	break;
                }

                _button.html(_buttonHtml).attr('disabled', false);
                _button.parents('section.hca-main-profile').find('.btn-edit').click();

                HCA.alertMessage('success', 'Profile has been updated!');
            },
            error: function(response) {
                _button.html(_buttonHtml).attr('disabled', false);
                if (response.status==400) {
                    var responseText = $.parseJSON(response.responseText);

                    if (responseText.errors.length) {
                        $.each(responseText.errors, function(i, each){
                        	$('.control-group.' + each.field).addClass('error');
                        	isLocationDropdown = each.field == 'city' || each.field == 'state' || each.field == 'country';
                        	$('<ul class="error"><li>'+each.error+'</li></ul>').insertAfter(_form.find('.'+each.field+' > ' + (isLocationDropdown ? '.fancy-dropdown-wrapper' : 'input')));
                        });
                    }

                	HCA.alertMessage('error', 'We need you to correct some of your input. Please check the fields in red.');
                }
            }
        });
        return false;
    }
};