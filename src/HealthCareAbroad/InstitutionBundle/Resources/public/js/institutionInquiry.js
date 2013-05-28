var InstitutionInquiry = {
		
    _inquiries: '',
    _prototype: '',
    _tab: '',
    _divId: '',
    _inquiryBannerCntrId: '',
    
    setInquiryData: function(_inquiries) {
    	this._inquiries = _inquiries;
    },
    
    setTemplatePrototype: function(_prototype) {
    	this._prototype = _prototype;
    },
    
    _loadContent: function() {
    	
    	InstitutionInquiry._checkInquiryCounter();
    	$(InstitutionInquiry._divId + ' ul.hca-inquiries').html('');
    	$.each(InstitutionInquiry._inquiries, function(_k, value) {
        	if(_k + 1 <= _cntr && _k + 1 > _lastIndex ) {
            	var el = $(InstitutionInquiry._prototype);
            	el.attr('class', value.status);
            	el.find('a.inquiry_sender').html(value.sender);
            	el.find('a.inquiry_sender').html(value.sender);
            	$(el.find('a.inquiry_sender')).attr('href', value.viewPath);
            	el.find('span.sender-details small').html(value.timeAgo);
            	el.find('a.inquiry_message').html(value.message);
            	$(el.find('a.inquiry_message')).attr('href', value.viewPath);
            	$(el.find('a.inquiry_view')).attr('href', value.viewPath);
            	$(el.find('a.inquiry_remove')).attr('href', value.removePath);
            	el.find('input.inquiry_cntr').val(_k + 1);
            	el.find('input.inquiry_checkList').attr('data-inquiryId',value.id);
            	$(InstitutionInquiry._divId + ' ul.hca-inquiries').append(el);
        	}
    	});
    	
		_lastIndex = _cntr;
    	
    	return false;
    },
    
    _checkInquiryCounter: function() {
    	_cntr = $('#inquiryCntr').val();
    	if(!_cntr) {
    	    _cntr = 10;
    	    _lastIndex = 0;
    	}
    },
    
    selectAllInquiry:function(_linkElement) {
    	_checkAllElement = $(_linkElement).attr('data-divId');
    	if($(_linkElement).attr('checked')) {
    		InstitutionInquiry._markInquiry(_checkAllElement, true);
	    }
    	else {
    		InstitutionInquiry._markInquiry(_checkAllElement, false);
    	}
    	
    	return false;
    },
    
    _markInquiry: function(_checkAllElement ,_status) {
    	$(_checkAllElement +' input[type=checkbox]').each(function() {
			$(this).attr('checked', _status);
		});
    },
    
    _doInquiry: function(_linkElement) {
    	_selected = new Array();
    	_status = $(_linkElement).attr('data-statusInquiry');
    	_href =  $(_linkElement).attr('href');
    	_inputCheckAllId = $(_linkElement).attr('data-checkAllId');
    	_tab = $(_inputCheckAllId).attr('data-tab');
    	_mainDiv = $(_inputCheckAllId).attr('data-mainDivId');
    	_lastIndex = 0;
    	$('#inquiryCntr').val('');
    	$(InstitutionInquiry._divId +' input[type=checkbox]').each(function() {
    		if ($(this).attr('checked')) {
    			id = $(this).attr('data-inquiryId');
    			_selected.push(id);
    		}
    	});

    	$.ajax({
            url: _href,
            type: 'POST',
            data : { 'inquiryListArr' : _selected , 'status' : _status, 'tabName' : _tab },
            success: function(response) {
            	InstitutionInquiry._markInquiry(InstitutionInquiry._divId, false);
            	$(_inputCheckAllId).attr('checked', false);
            	InstitutionInquiry._inquiries = response.inquiryList;
            	InstitutionInquiry._loadContent();
            	InstitutionInquiry._setInquiryCounter(response);
            }
        });
    	
    	return false;
    	
    },
    
    _setInquiryCounter: function(_response) {
    	$('#readTab').find('span').html("("+_response.readCntr.length+")");
    	$('#unreadTab').find('span').html("("+_response.unreadCntr.length+")");
    	$(InstitutionInquiry._inquiryBannerCntrId).find('span').html("("+_response.unreadCntr.length+")");
    },
    
    _removeInquiry: function (_linkElement) {
    	var _href = $(_linkElement).attr('href');
    	_lastIndex = 0;
    	$.ajax({
            url: _href,
            type: 'POST',
            success: function(response) {
            	InstitutionInquiry._inquiries = response.inquiryList;
            	InstitutionInquiry._loadContent();
            	InstitutionInquiry._setInquiryCounter(response);
            }
        });
        
        return false;
    }
}