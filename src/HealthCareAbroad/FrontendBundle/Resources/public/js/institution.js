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
        InstitutionInquiry.institutionInquiryComponents.form.find('ul').remove();
        return this;
    },
    
    resetAlertBox: function(){
        InstitutionInquiry.institutionInquiryComponents.form.find('.alert-box').removeClass('alert alert-error alert-success').html("");
        InstitutionInquiry.institutionInquiryComponents.form.find('ul').remove();
        return this;
    },
    
    showAlertError: function(_errorString) {
        InstitutionInquiry
            .institutionInquiryComponents.form.find('.alert-box')
            .addClass('alert alert-error')
            .html(_errorString);
            //.html('Please fill up the form properly.');
        return this;
    },
    
    showAlertSuccess: function() {
        InstitutionInquiry
        .institutionInquiryComponents.form.find('.alert-box')
        .addClass('alert alert-success')
        .html('Your message has been sent! Thank you.');
        return this;
    },
    
    saveInquiry: function(){
    	InstitutionInquiry.restoreInitialState();
        $.ajax({
            url: this.institutionInquiryComponents.path,
            data: InstitutionInquiry.institutionInquiryComponents.form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(response){
                InstitutionInquiry.institutionInquiryComponents.submitButton
                .html(InstitutionInquiry.institutionInquiryComponents.submitButton.attr('data-html'))
                .attr('disabled', false)
                .hide();
                $('#_closeBtnTrigger').html('Close');
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
                    $('#institutionInquiry_captcha').val('');
                    if (errors.length) {
                    	var _errorString = 'We need you to correct some of your input. Please check the fields in red.';
                        $.each(errors, function(key, item){
                            InstitutionInquiry.institutionInquiryComponents.form.find('div.'+item.field).addClass('error');
                            isLocationDropdown = item.field == 'country';
                        	$('<ul class="error"><li>'+item.error+'</li></ul>').insertAfter(InstitutionInquiry.institutionInquiryComponents.form.find('.'+item.field+' > ' + (isLocationDropdown ? '.fancy-dropdown-wrapper' : 'input')));
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
            'submitButton': _components.submitButton,
            'form': _components.form,
            'path': _components.path
        };
//        
//        InstitutionInquiry.institutionInquiryComponents.form.live('show', function(){
//        	//console.log();
//        	$('body').append($(this));
//            
//        });
//        
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
