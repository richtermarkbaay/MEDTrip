var ClinicListing = {
		
   ajaxUrls: {
	    'load': '', 
    },
    setAjaxUrls: function(_val){
        this.ajaxUrls = _val;
        
        return this;
    },
    
	tabbedContentElement: null,
	
	setTabbedContentElement: function(_val) {
		  ClinicListing.tabbedContentElement = _val;
	        
	        return this;
    },
    loadTabbedContentsOfClinics: function(_element) {
    	
    	_status = $(_element);
    	$('#loader_ajax').show();
    	
        $.ajax({
            url: ClinicListing.ajaxUrls.load+'?status='+_status.val(),
            type: 'get',
            dataType: 'json',
            success: function(response){
            	ClinicListing.tabbedContentElement.html(response.output.html);
            	$('#loader_ajax').hide();
            }
        });
        return this;
    },
}