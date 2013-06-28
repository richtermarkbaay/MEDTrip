var InstitutionInquiry = {  
    institutionInquiryComponents: {},
    
    institutionInquiryFormInputIdPrefix: 'institutionInquiry',
    
    restoreInitialState: function() {
        InstitutionInquiry.clearErrors().resetForm().resetAlertBox();
    },
    
    resetForm: function() {
        this.institutionInquiryComponents.form.show().find('input[type="text"],input[type="email"], textarea').val('');
        this.institutionInquiryComponents.submitButton.attr('disabled', false).show();
        return this;
    },
    
    clearErrors: function() {
        InstitutionInquiry.institutionInquiryComponents.form.find('.error').removeClass('error');
        return this;
    },
    
    resetAlertBox: function(){
        InstitutionInquiry.institutionInquiryComponents.modal.find('.alert-box').removeClass('alert alert-error alert-success').html("");
        return this;
    },
    
    showAlertError: function(_errorString) {
        InstitutionInquiry
            .resetAlertBox()
            .institutionInquiryComponents.modal.find('.alert-box')
            .addClass('alert alert-error')
            .html(_errorString);
            //.html('Please fill up the form properly.');
        return this;
    },
    
    showAlertSuccess: function() {
        InstitutionInquiry
        .resetAlertBox()
        .institutionInquiryComponents.modal.find('.alert-box')
        .addClass('alert alert-success')
        .html('Your message has been sent! Thank you.');
        return this;
    },
    
    saveInquiry: function(){
        InstitutionInquiry.clearErrors();
        $.ajax({
            url: InstitutionInquiry.institutionInquiryComponents.form.attr('action'),
            data: InstitutionInquiry.institutionInquiryComponents.form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(response){
                InstitutionInquiry.institutionInquiryComponents.submitButton
                .html(InstitutionInquiry.institutionInquiryComponents.submitButton.attr('data-html'))
                .attr('disabled', false)
                .hide();
                InstitutionInquiry.showAlertSuccess();
                InstitutionInquiry.institutionInquiryComponents.form.hide();
                
            },
            error: function(response){
                InstitutionInquiry.institutionInquiryComponents.submitButton
                .html(InstitutionInquiry.institutionInquiryComponents.submitButton.attr('data-html'))
                .attr('disabled', false);
                window.location =  InstitutionInquiry.institutionInquiryComponents.form.find('a.captcha_reload').attr('href');
                if (response.status==400) {
                    var errors = $.parseJSON(response.responseText).html;
                    if (errors.length) {
                    	var _errorString = '';
                        $.each(errors, function(key, item){
                    		_errorString += item.error+"<br>";
                            InstitutionInquiry.institutionInquiryComponents.form.find('div.'+item.field).addClass('error');
                        });
                        InstitutionInquiry.showAlertError(_errorString);
                    }
                }
            }
        });

    }
    
};



(function($){
    $.fn.institutionInquiryModalForm = function(_components){
        
        InstitutionInquiry.institutionInquiryComponents = {
            'modal': this,
            'submitButton': _components.submitButton,
            'form': _components.form
        };
        
        InstitutionInquiry.institutionInquiryComponents.modal.live('show', function(){
        	//console.log();
        	$('body').append($(this));
            InstitutionInquiry.restoreInitialState();
        });
        
        // initialize submit submitButton
        InstitutionInquiry.institutionInquiryComponents.submitButton.click(function(){
            $(this).attr('data-html', $(this).html())
                .html($(this).attr('data-loader-text'))
                .attr('disabled', true);
            
            InstitutionInquiry.institutionInquiryComponents.form.submit();
            
            return false;
        });
        InstitutionInquiry.institutionInquiryComponents.form.submit(function(){
            
            InstitutionInquiry.saveInquiry();
            
            return false;
        });
        
        return this;
    }
})(jQuery);
