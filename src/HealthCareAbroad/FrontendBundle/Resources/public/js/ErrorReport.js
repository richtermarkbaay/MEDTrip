var ErrorReport = {
		
	errorReportComponents: {},
	
	ErrorReportFormInputIdPrefix: 'ExceptionForm',
	    
	    restoreInitialState: function() {
	    	ErrorReport.clearErrors().resetForm().resetAlertBox();
	    },
	    
	    resetForm: function() {
	        this.errorReportComponents.form.show().find('input[type="text"],input[type="email"], textarea').val('');
	        this.errorReportComponents.submitButton.attr('disabled', false).show();
	        return this;
	    },
	    
	    clearErrors: function() {
	        ErrorReport.errorReportComponents.form.find('.error').removeClass('error');
	        return this;
	    },
	    
	    resetAlertBox: function(){
	        ErrorReport.errorReportComponents.modal.find('.alert-box').removeClass('alert alert-error alert-success').html("");
	        return this;
	    },
	    
	    showAlertError: function(_errorString) {
	        ErrorReport
	            .resetAlertBox()
	            .errorReportComponents.modal.find('.alert-box')
	            .addClass('alert alert-error').html(_errorString);
	            //.html('Please fill up the form properly.');
	        return this;
	    },
	    
	    showAlertSuccess: function() {
	        ErrorReport
	        .resetAlertBox()
	        .errorReportComponents.modal.find('.alert-box')
	        .addClass('alert alert-success')
	        .html('Your message has been sent! Thank you.');
	        return this;
	    },
	
	submitReportForm: function() {
		ErrorReport.clearErrors();
		 $.ajax({
	            url: ErrorReport.errorReportComponents.form.attr('action'),
	            data: ErrorReport.errorReportComponents.form.serialize(),
	            type: 'POST',
	            dataType: 'json',
	            success: function(response){
	            	ErrorReport.errorReportComponents.submitButton
	                .html(ErrorReport.errorReportComponents.submitButton.attr('data-html'))
	                .attr('disabled', false)
	                .hide();
	            	window.location =  ErrorReport.errorReportComponents.form.find('a.captcha_reload').attr('href');
	            	ErrorReport.errorReportComponents.form.hide();
	            	ErrorReport.showAlertSuccess();
	            },
	            error: function(response){
	            	ErrorReport.errorReportComponents.submitButton
	                .html(ErrorReport.errorReportComponents.submitButton.attr('data-html'))
	                .attr('disabled', false);
	            	 $('#ExceptionForm_captcha').val('');
	            	window.location =  ErrorReport.errorReportComponents.form.find('a.captcha_reload').attr('href');
	                if (response.status==400) {
	                    var errors = $.parseJSON(response.responseText).html;
	                    if (errors.length) {
	                    	var _errorString = "";
	                        $.each(errors, function(key, item){
	                            ErrorReport.errorReportComponents.form.find('div.'+item.field).addClass('error');
	                            _errorString += item.error+"<br>";
	                        });
	                        ErrorReport.showAlertError(_errorString);
	                    }
	                }
	            }
	        });
	}
};

(function($){
    $.fn.errorReportModalForm = function(_components){
        
    	ErrorReport.errorReportComponents = {
            'modal': this,
            'submitButton': _components.submitButton,
            'form': _components.form
        };
        
    	ErrorReport.errorReportComponents.modal.live('show', function(){
        	//console.log();
        	$('body').append($(this));
        	ErrorReport.restoreInitialState();
        });
    	
    	  // initialize submit submitButton
    	ErrorReport.errorReportComponents.submitButton.click(function(){
            $(this).attr('data-html', $(this).html())
                .html($(this).attr('data-loader-text'))
                .attr('disabled', true);
            
            ErrorReport.errorReportComponents.form.submit();
            
            return false;
        });
    	
    	ErrorReport.errorReportComponents.form.submit(function(){
            
    		ErrorReport.submitReportForm();
            
            return false;
        });
        
        return this;
    }
})(jQuery);