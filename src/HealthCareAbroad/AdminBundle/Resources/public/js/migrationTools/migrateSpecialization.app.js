
var MigrateSpecializationApp = Backbone.View.extend({
    el: $('#app_canvass'),
    
    events: {
        'change select#from_specialization': 'onChangeFromSpecialization',
        'submit #migration_specialization_form': 'onSubmitForm'
    },
    
    initialize: function(options){
        this.specializationsData = options.specializations || [];
        
        this.specializationCollection = new SpecializationCollection();
        
        this.migrationForm = this.$el.find('#migration_specialization_form');
        this.migrationForm.find('button[type="submit"]').prop('disabled', true);
        
        this.listenTo(this.specializationCollection, 'reset', this.onResetSpecializationCollection);
        
        this.specializationCollection.reset(this.specializationsData);
    },
    
    onResetSpecializationCollection: function(collection, options){
        
        // rebuild the specializations dropdown
        var fromSpecialization = this.migrationForm.find('select#from_specialization');
        var toSpecialization = this.migrationForm.find('select#to_specialization'); 
        fromSpecialization.html('');
        toSpecialization.html('').prop('disabled', true);
        
        fromSpecialization.append($('<option value="0"></option>').text('Select one'));
        
        this.specializationCollection.each(function(model, key, list){
            var opt = $('<option value="'+model.get('id')+'"></option>').text(model.get('name'));
            fromSpecialization.append(opt);
        }, this);
        
        
    },
    
    onChangeFromSpecialization: function(e){
        
        this.fromSpecializationObject = null;
        this.toSpecializationObject = null;
        
        var toSpecialization = this.migrationForm.find('select#to_specialization');
        toSpecialization.html('').prop('disabled', true);
        this.migrationForm.find('button[type="submit"]').prop('disabled', true);
        var selectedSpecializationId = $(e.target).val();
        
        var manageLink = $(e.target).parents('div.control-group').find('a.manage-specialization-link');
        if (selectedSpecializationId != 0){
            // rebuild the toSpecialization dropdown
            this.specializationCollection.each(function(model, key, list){
                if (model.get('id') != selectedSpecializationId) {
                    var opt = $('<option value="'+model.get('id')+'"></option>').text(model.get('name'));
                    toSpecialization.append(opt);
                }
            }, this);
            toSpecialization.prop('disabled', false);
            this.migrationForm.find('button[type="submit"]').prop('disabled', false);
            
            manageLink
                .prop('href', ApiUtility.getRootUrl()+'/admin/specializations/'+selectedSpecializationId+'/manage')
                .show();
        }
        else {
            manageLink
            .prop('href', '#')
            .hide();
        }
        
    },
    
    onSubmitForm: function(e){
        e.preventDefault();
        
        var confirmModal = new MigrateConfirmModal();
        
        this.fromSpecializationObject = this.specializationCollection.get(this.migrationForm.find('select#from_specialization').val());
        this.toSpecializationObject = this.specializationCollection.get(this.migrationForm.find('select#to_specialization').val());
        
        confirmModal.header = confirmModal.header +' '+this.fromSpecializationObject.get('name')+' to '+this.toSpecializationObject.get('name');
        confirmModal.body = 'Are you sure you want to migrate and convert '
            +this.fromSpecializationObject.get('name')
            +' as Sub Specialization of '+this.toSpecializationObject.get('name')
            +'? This process cannot be undone!';
        confirmModal.show();
        
        this.listenTo(confirmModal, 'confirmModal:confirmed', this.startMigration);
    },
    
    startMigration: function(){
        var btn = this.migrationForm.find('button[type="submit"]');
        btn.prop('disabled', true);
        
        var flash = new CommonFlashMessageView({type: 'warning', message: 'Migration started! Do not close this window until the process is completed!'});
        flash.show();
        
        $.ajax({
            url: this.migrationForm.attr('action'),
            data: {
                fromSpecialization: this.fromSpecializationObject.get('id'),
                toSpecialization: this.toSpecializationObject.get('id'),
                _token: this.migrationForm.find('#form__token').val()
            },
            type: 'post',
            dataType: 'json',
            success: function(response){
                var flash = new CommonFlashMessageView({
                    type: 'success', 
                    message: 'Migration successfull.'
                });
                
                flash.show();
                if (response.redirectUrl) {
                    window.location.href = response.redirectUrl;
                }
            },
            error: function(xhr, response){
                var flash = new CommonFlashMessageView({type: 'error', message: 'Failed to migrate. '});
                flash.show();
                
                btn.prop('disabled', false);
            }
        });
    }
});

var MigrateConfirmModal = CommonConfirmModal.extend({
    header: 'Migrate',
    body: '',
    footer: '<button class="btn btn-primary confirm-migrate">Yes, I understand</button><button data-dismiss="modal" class="btn">Cancel</button>',
    events: {
        'hidden': 'onHideModal',
        'click button.confirm-migrate': 'onClickConfirmApprove'
    },
    
    onClickConfirmApprove: function(e){
        e.preventDefault();
        var btn = $(e.target);
        btn.prop('disabled', true);
        
        this.trigger('confirmModal:confirmed');
        this.hide();
    }
});