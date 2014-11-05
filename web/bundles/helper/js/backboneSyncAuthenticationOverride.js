(function($){
    /*
     * Store a version of Backbone.sync to call from the
     * modified version we create
     */
    var backboneSync = Backbone.sync;

    Backbone.sync = function (method, model, options) {	
        /* 
         * Set the custom 'Authorization' header and get the access
         * token from the `Session` module
         */
        
    	var headerKey = ApiUtility.getAuthorizationHeaderParameter();
    	var headerVal = Session.getSecurityAccessUrlParametersAsBase64EncodedString();
        
    	options.headers = {}
    	options.headers[headerKey] = headerVal;

        /*
         * Call the stored original Backbone.sync method with
         * extra headers argument added
         */
        return backboneSync(method, model, options);
    };
})(jQuery);