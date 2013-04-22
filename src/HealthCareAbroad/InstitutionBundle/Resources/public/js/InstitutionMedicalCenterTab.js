var ClinicListing = {
		
   ajaxUrls: {
	    'load': '', 
    },
    setAjaxUrls: function(_val){
        this.ajaxUrls = _val;
        
        return this;
    },
    
    ajaxUrls: {
		'loadPending': '', 
		'loadDraft':'',
		'loadExpired': '',
    }, 
    
	tabbedContentElement: null,
	
	setTabbedContentElement: function(_val) {
		  ClinicListing.tabbedContentElement = _val;
	        
	        return this;
    },
    loadTabbedContentsOfClinics: function(_element) {
    	
    	_status = $(_element);
    	$('#loader_ajax').show();
    	
        $.each(ClinicListing.ajaxUrls, function(_key, _url){
		          $.ajax({
		          url: _url,
		          type: 'get',
		          dataType: 'json',
		          success: function(response){
		          	$('#'+response.status).html(response.output.html);
		          	$('#loader_ajax').hide();
		          }
		      });
        });
        return this;
    },
}