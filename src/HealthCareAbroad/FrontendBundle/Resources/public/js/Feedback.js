var Feedback = {  
    feedbackComponents: {},
    
    feedbackFormInputIdPrefix: 'feedbackMessage',
    
    restoreInitialState: function() {
        Feedback.clearErrors().resetForm().resetAlertBox();
    },
    
    resetForm: function() {
        this.feedbackComponents.form.show().find('input[type="text"],input[type="emailAddress"], textarea').val('');
        this.feedbackComponents.submitButton.attr('disabled', false).show();
        return this;
    },
    
    clearErrors: function() {
    	Feedback.feedbackComponents.form.find('.error').removeClass('error');
        return this;
    },
    
    resetAlertBox: function(){
        Feedback.feedbackComponents.modal.find('.alert-box').removeClass('alert alert-error alert-success').html("");
        return this;
    },
    
    showAlertError: function(_errorString) {
        Feedback
            .resetAlertBox()
            .feedbackComponents.modal.find('.alert-box')
            .addClass('alert alert-error').html(_errorString);
            //.html('Please fill up the form properly.');
        return this;
    },
    
    showAlertSuccess: function() {
        Feedback
        .resetAlertBox()
        .feedbackComponents.modal.find('.alert-box')
        .addClass('alert alert-success')
        .html('Your message has been sent! Thank you for your feedback.');
        return this;
    },
    
    submitFeedbackMessageForm: function(){
        Feedback.clearErrors();
        $.ajax({
            url: Feedback.feedbackComponents.form.attr('action'),
            data: Feedback.feedbackComponents.form.serialize(),
            type: 'POST',
            dataType: 'json',
            success: function(response){
            	window.location =  Feedback.feedbackComponents.form.find('a.captcha_reload').attr('href');
                Feedback.feedbackComponents.submitButton
                .html(Feedback.feedbackComponents.submitButton.attr('data-html'))
                .attr('disabled', false)
                .hide();
                Feedback.feedbackComponents.form.hide();
                Feedback.showAlertSuccess();
            },
            error: function(response){
                Feedback.feedbackComponents.submitButton
                .html(Feedback.feedbackComponents.submitButton.attr('data-html'))
                .attr('disabled', false);
                window.location =  Feedback.feedbackComponents.form.find('a.captcha_reload').attr('href');
                if (response.status==400) {
                    var errors = $.parseJSON(response.responseText).html;
                    if (errors.length) {
                        var _errorString = "";
                        $('#feedbackMessage_captcha').val('');
                        $.each(errors, function(key, item){
                        	_errorString += item.error+"<br>";
                            Feedback.feedbackComponents.form.find('div.'+item.field).addClass('error');
                        });
                        Feedback.showAlertError(_errorString);
                    }
                }
            }
        });
    }
    
};


(function($){
    $.fn.feedbackModalForm = function(_components){
        
        Feedback.feedbackComponents = {
            'modal': this,
            'submitButton': _components.submitButton,
            'form': _components.form
        };
        
        Feedback.feedbackComponents.modal.live('show', function(){
        	$('body').append($(this));
            Feedback.restoreInitialState();
        });
        
        // initialize submit submitButton
        Feedback.feedbackComponents.submitButton.click(function(){
            $(this).attr('data-html', $(this).html())
                .html($(this).attr('data-loader-text'))
                .attr('disabled', true);
            
            Feedback.feedbackComponents.form.submit();
            
            return false;
        });
        Feedback.feedbackComponents.form.submit(function(){
            
            Feedback.submitFeedbackMessageForm();
            
            return false;
        });
        
        return this;
    }
})(jQuery);