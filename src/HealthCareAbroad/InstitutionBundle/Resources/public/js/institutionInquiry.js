var InstitutionInquiry = {
		
    _inquiries: '',
    _prototype: '',
    setAjaxUrls: function(_val){
        this.ajaxUrls = _val;
        
        return this;
    },
    
    setInquiryData: function(_inquiries) {
    	this._inquiries = _inquiries;
    },
    
    setTemplatePrototype: function(_prototype) {
    	this._prototype = _prototype;
    },
    
    ajaxUrls: {
		'loadReadInquiries': '', 
		'loadUndreadInquiries':''
    }, 
    
    _loadContent: function() {
    	
    	_cntr = $('#inquiryCntr').val();
    	if(!_cntr) {
    	    _cntr = 2;
    	    _lastIndex = 0;
    	}
    	$('#inquiryList ul.hca-inquiries').html('');
    	$.each(InstitutionInquiry._inquiries, function(_k, value) {
        	if(_k + 1 <= _cntr && _k + 1 > _lastIndex ) {
            	var el = $(InstitutionInquiry._prototype);
            	el.find('a.inquiry_sender').html(value.sender);
            	$(el.find('a.inquiry_sender')).attr('href', value.viewPath);
            	el.find('a.inquiry_message').html(value.message);
            	$(el.find('a.inquiry_message')).attr('href', value.viewPath);
            	$(el.find('a.inquiry_view')).attr('href', value.viewPath);
            	$(el.find('a.inquiry_remove')).attr('href', value.removePath);
            	el.find('input.inquiry_cntr').val(_k + 1);
            	el.find('input.inquiry_checkList').attr('data-inquiryId',value.id);
            	$('#inquiryList ul.hca-inquiries').append(el);
        	}
    	});
		_lastIndex = _cntr;
    	
    	return false;
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
    	_divId = $(_linkElement).attr('data-divId');
    	_status = $(_linkElement).attr('data-statusInquiry');
    	_href =  $(_linkElement).attr('href');
    	_inputCheckAllId = $(_linkElement).attr('data-checkAllId');
    	_tab = $(_inputCheckAllId).attr('data-tab');
    	_mainDiv = $(_inputCheckAllId).attr('data-mainDivId');
    	
    	$(_divId +' input[type=checkbox]').each(function() {
    		if ($(this).attr('checked')) {
    			id = $(this).attr('data-inquiryId');
    			_selected.push(id);
    		}
    	});

    	$.ajax({
            url: _href,
            type: 'POST',
            data : { 'inquiryListArr' : _selected , 'status' : _status, 'tab' : _tab },
            success: function(response) {
            	InstitutionInquiry._markInquiry(_divId, false);
            	$(_inputCheckAllId).attr('checked', false);
            	//$(_mainDiv).html(response.html);
            	InstitutionInquiry._inquiries = response;
            	InstitutionInquiry._loadContent();
            }
        });
    	
    	return false;
    	
    },
    
    _markAsUnreadInquiry: function(_checkAllElement) {
    	
    	$(_checkAllElement +' input[type=checkbox]').each(function() {
    		if ($(this).attr('checked')) {
    			$(this).attr('checked', false);
    		}
    	});
    	
    	$.ajax({
            url: _href,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
            }
        });
    	
    },
    
    _removeInquiry: function (_linkElement) {
    	var _href = $(_linkElement).attr('href');
    	$.ajax({
            url: _href,
            type: 'POST',
            success: function(response) {
//            	InstitutionInquiry._markInquiry(_divId, false);
//            	$(_inputCheckAllId).attr('checked', false);
//            	$(_mainDiv).html(response.html);
            }
        });
        
        return false;
    }
}