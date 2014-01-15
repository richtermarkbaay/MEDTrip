/**
 * @author Allejo Chris G. Velarde
 */
var BootstrapModalForm = Backbone.View.extend({
            
    // mapping of form fields to model attributes
    fieldMapping: {},
    
    // events delegated to this modal view
    events: {
        'click button[type="submit"]': 'onClickSubmit',
        'submit form': 'onSubmitForm',
        'hidden': 'onHideModal',
        'show': 'onShowModal'
    },
    
    // view initialization
    initialize: function(options){
        this.form = this.$el.find('form');
        this.reset();
        this.customInitialization(options);
        this.bindModelEvents();
    },
    
    // Form and modal custom initializations, left blank to override
    customInitialization: function(options){},
    
    show: function(){
        this.populateForm();
        this.$el.modal('show');
    },
    
    hide: function(){
        this.$el.modal('hide');
    },
    
    showFormError: function(errorMessage){
        var _alertBox = this.$el.find('div.alert');
        if (!_alertBox.length){
            _alertBox = $('<div></div>').addClass('alert alert-error');
        }
        _alertBox.html(errorMessage);
        this.form.prepend(_alertBox);
    },
    
    reset: function(){
        this.$el.find('button[type=submit]').prop('disabled', false);
        this.$el.find('div.alert').remove();
    },
    
    findField: function(fieldKey){
        var _fieldName = this.fieldMapping[fieldKey] || fieldKey;
        
        return this.form.find('[name="'+_fieldName+'"]');
    },
    
    bindModelEvents: function(){

    	if(typeof(this.model) != 'undefined') {
            // bind on successful sync of model
            this.listenTo(this.model, 'sync', this.onModelSyncOk);

            // bind on failed sync of model
            this.listenTo(this.model, 'error', this.onModelSyncError);            		
    	}
    },
    
    // handler for successful sync of a model
    onModelSyncOk: function(model, response, options){
        this.model.collection.add(model);
        this.hide();
    },
    
    // handler for failed sync of a model
    onModelSyncError: function(model, xhr, options){
        this.reset();
        if (400 == xhr.status){
        	
            var jsonResponse = window.JSON.parse(xhr.responseText); 
            var _errors = [];
            // parse children errors
            if(jsonResponse['children'] != undefined) {
                _errors = $.map(window.JSON.parse(xhr.responseText)['children'], function(v, i){
                    return v['errors'];
                });
            }
            $.merge(_errors, jsonResponse.errors || [])
            this.showFormError(_errors.join('<br />'));
        }
        else {
            this.showFormError('Unexpected error occured.');
        }
    },
    
    onClickSubmit: function(e){
        e.preventDefault();
        this.form.submit();
    },
    
    onSubmitForm: function(e){
        // disable button
        this.$el.find('button[type=submit]').prop('disabled', true);
        e.preventDefault();
        this.model.save(this.getData(),{
            wait: true
        });
    },
    
    onHideModal: function(e){
        this.reset();
        this.remove();
    },
    
    onShowModal: function(e){},
    
    showFlashMessage: function(message, type) {
        var alertView = new CommonFlashMessageView({
            type: type,
            message: message
        });
        alertView.show();
    }
});