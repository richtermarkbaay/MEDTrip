var CommonConfirmModal= Backbone.View.extend({
    tagName: 'div',
    
    className: 'modal hide fade modal-box',
    
    events: {
        'hidden': 'onHideModal'
    },
    
    initialize: function(){
        this.$el
            .attr('role', 'dialog')
            .attr('tabindex', "-1");
        
        this.rendered = false;
    },
    
    onHideModal: function(e) {
        this.remove();
    },
    
    render: function(){
        var prototype = ich.common_confirm_modal_prototype();
        this.$el.html(prototype.html());
        
        this.$el.appendTo($('body'));
        
        this.$el.find('.modal-header h3').html(this.header || ''); // set the header
        this.$el.find('.modal-body').html(this.body || ''); // set the body
        this.$el.find('.modal-footer').html(this.footer || '');
        
        this.rendered = true;
    },
    
    hide: function() {
        this.$el.modal('hide');
    },
    
    show: function() {
        if (!this.rendered) {
            this.render();
        }
        this.$el.modal('show');
    }
    
});
/**
 * keep this file simple and match only the API
 */
var Treatment = Backbone.Model.extend({
    
    isNew: function(){
        return this.get('id') == 0;
    },
    
    getStatusLabel: function() {
        return this.get('status') == 1
            ? 'Active'
            : 'Inactive';
    }
});

var TreatmentCollection = Backbone.Collection.extend({
    model: Treatment, 
    url: ApiUtility.getApiRootUrl()+'/treatments',
    parse: function(response) {
        return response['treatments'];
    }
});
var Specialization = Backbone.Model.extend({
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var SpecializationCollection = Backbone.Collection.extend({
    model: Specialization,
    
    url: '/api/specializations'
})
var MergeTreatmentApp = Backbone.View.extend({
    el: $('#app_canvass'),
    
    events: {
        'change select#specialization': 'onChangeSpecialization',
        'change select#from_treatment': 'onChangeFromTreatment',
        'submit #merge_treatment_form': 'onSubmitForm'
    },
    
    initialize: function(options) {
        this.specializationsData = options.specializations || [];
        
        this.specializationCollection = new SpecializationCollection();
        this.migrationForm = this.$el.find('#merge_treatment_form');
        this.migrationForm.find('button[type="submit"]').prop('disabled', true);
        this.listenTo(this.specializationCollection, 'reset', this.onResetSpecializationCollection);
        this.specializationCollection.reset(this.specializationsData);
        
        this.treatmentCollection = new TreatmentCollection([]);
        this.listenTo(this.treatmentCollection, 'reset', this.onResetTreatmentCollection);
    },
    
    onResetSpecializationCollection: function(collection, options){
        
        // rebuild the specialization dropdown
        var specializationDropdown = this.migrationForm.find('select#specialization');
        this.migrationForm.find('select#from_treatment').html('').prop('disabled', true);
        this.migrationForm.find('select#to_treatment').html('').prop('disabled', true);
        
        specializationDropdown.html('').append($('<option value="0"></option>').text('Select one'));
        this.specializationCollection.each(function(model, key, list){
            var opt = $('<option value="'+model.get('id')+'"></option>').text(model.get('name'));
            specializationDropdown.append(opt);
        }, this);
    },
    
    onResetTreatmentCollection: function(collection, options){
        // we clear the to_treatment and from_treatment dropdowns
        var fromTreatmentDropdown = this.migrationForm.find('select#from_treatment');
        var toTreatmentDropdown = this.migrationForm.find('select#to_treatment');
        toTreatmentDropdown.html('').prop('disabled', true);
        
        // populate the from_treatment dropdown
        fromTreatmentDropdown
            .html('')
            .append($('<option value="0"></option>').text('Select one'))
            .prop('disabled', false);
        this.treatmentCollection.each(function(model, key, list){
            var opt = $('<option value="'+model.get('id')+'"></option>').text(model.get('name'));
            fromTreatmentDropdown.append(opt);
        }, this);
    },
    
    onChangeSpecialization: function(e) {
        e.preventDefault();
        var selectedSpecializationId = $(e.target).val();
        var manageLink = $(e.target).parents('div.control-group').find('a.manage-specialization-link');
        this.migrationForm.find('button[type="submit"]').prop('disabled', true);
        
        var fromTreatmentDropdown = this.migrationForm.find('select#from_treatment');
        var toTreatmentDropdown = this.migrationForm.find('select#to_treatment');
        fromTreatmentDropdown.html('').prop('disabled', true);
        toTreatmentDropdown.html('').prop('disabled', true);
        
        if (selectedSpecializationId != 0){
            this.treatmentCollection.fetch({
                data: {specialization: selectedSpecializationId},
                reset: true
            });
            
            manageLink
            .prop('href', ApiUtility.getRootUrl()+'/admin/specializations/'+selectedSpecializationId+'/manage')
            .show();
        }
    },
    
    onChangeFromTreatment: function(e){
        e.preventDefault();
        var selectedTreatmentId = $(e.target).val();
        var toTreatmentDropdown = this.migrationForm.find('select#to_treatment');
        toTreatmentDropdown.html('').prop('disabled', true);
        if (selectedTreatmentId != 0){
            // rebuild the to_treatment dropdown
            this.treatmentCollection.each(function(model, key, list){
                if (model.get('id') != selectedTreatmentId) {
                    var opt = $('<option value="'+model.get('id')+'"></option>').text(model.get('name'));
                    toTreatmentDropdown.append(opt);
                }
            }, this);
            toTreatmentDropdown.prop('disabled', false);
            this.migrationForm.find('button[type="submit"]').prop('disabled', false);
        }
        else {
            
            this.migrationForm.find('button[type="submit"]').prop('disabled', true);
        }
    },
    
    onSubmitForm: function(e){
        e.preventDefault();
        
        var confirmModal = new MigrateConfirmModal();
        
        this.fromTreatmentObject = this.treatmentCollection.get(this.migrationForm.find('select#from_treatment').val());
        this.toTreatmentObject = this.treatmentCollection.get(this.migrationForm.find('select#to_treatment').val());
        
        confirmModal.header = confirmModal.header +' '+this.fromTreatmentObject.get('name')+' to '+this.toTreatmentObject.get('name');
        confirmModal.body = 'Are you sure you want to merge '
            +this.fromTreatmentObject.get('name')
            +' to '+this.toTreatmentObject.get('name')
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
                specialization: this.migrationForm.find('select#specialization').val(),
                fromTreatment: this.migrationForm.find('select#from_treatment').val(),
                toTreatment: this.migrationForm.find('select#to_treatment').val(),
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
    header: 'Merge Treatment',
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