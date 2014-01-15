var UnapprovedInstitutionInquiriesApp = Backbone.View.extend({
    
    events: {
        'change input.toggle-all': 'onChangeToggleAllCheckbox',
        'change input.select-toggle': 'onChangeIndividualSelectToggle',
        'click button.mass-approve-trigger': 'onClickMassApprove',
        'click button.mass-delete-trigger': 'onClickMassDelete'
    },
    
    initialize: function(options){
        this.$el = options.el; // required option
        this.collectionUrl = options.collectionUrl; // required option
        this.modelUrl = options.modelUrl || options.collectionUrl; // this is optional, will default to the collection url
        this.csrfToken = options.csrfToken || '';
        
        this.unapprovedInquiriesCollection = new InstitutionInquiryCollection([], {url: this.collectionUrl});
        this.listenTo(this.unapprovedInquiriesCollection, 'add', this.addInstitutionInquiry);
        
        this.$el.find('button.mass-approve-trigger').prop('disabled', true);
        this.$el.find('button.mass-delete-trigger').prop('disabled', true);
        this.$el.find('input.toggle-all').prop('checked', false);
        
        this.unapprovedInquiriesCollection.fetch({
            success: function(response, col){
                $('#loader').remove();
            }
        });
    },
    
    onChangeToggleAllCheckbox: function(e) {
        this.$el.find('input.select-toggle').prop('checked', e.target.checked).change();
    },
    
    onChangeIndividualSelectToggle: function(e) {
        var enableButtons = this.$el.find('input.select-toggle:checked').length > 0;
        this.$el.find('.mass-approve-trigger').prop('disabled', !enableButtons);
        this.$el.find('.mass-delete-trigger').prop('disabled', !enableButtons);
        
    },
    
    onClickMassApprove: function(e) {
        var confirmModal = new MassApproveConfirmModal();
        confirmModal.show();
        
        this.listenTo(confirmModal, 'confirmedMassApprove', this.onConfirmMassApprove);
    },
    
    onConfirmMassApprove: function(e) {
        var selected = this.$el.find('input.select-toggle:checked');
        var csrfToken = this.csrfToken;
        _.each(selected, function(v,k,l){
            var model = this.unapprovedInquiriesCollection.get($(v).val()); 
            model.set('status', 1); // set to unread
            
            // save the status
            // we pass in totalSelected, currentIndex options to keep track of the processed items
            model.save({
                    institutionInquiry: {
                        status: model.get('status'),
                        inquirerEmail: model.get('inquirerEmail'),
                        inquirerName: model.get('inquirerName'),
                        _token: csrfToken
                    },
                }, 
                {
                    success: function(model, response, options){
                        model.view.remove();
                        if ((options.totalSelected-1) >= options.currentIndex) {
                            options.modal.hide();
                        }
                    },
                    totalSelected: selected.length,
                    currentIndex: k,
                    modal: e.modal
                }
            );
        }, this);
    },
    
    onClickMassDelete: function(e) {
        var confirmModal = new MassDeleteConfirmModal();
        confirmModal.show();
        
        this.listenTo(confirmModal, 'confirmedMassDelete', this.onConfirmMassDelete);
    },
    
    onConfirmMassDelete: function(e){
        var selected = this.$el.find('input.select-toggle:checked');
        _.each(selected, function(v,k,l){
            var model = this.unapprovedInquiriesCollection.get($(v).val());
            // destroy the model
            // we pass in totalSelected, currentIndex options to keep track of the processed items
            model.destroy({
                wait: true,
                success: function(model, response, options){
                    if ((options.totalSelected-1) >= options.currentIndex) {
                        options.modal.hide();
                    }
                },
                totalSelected: selected.length,
                currentIndex: k,
                modal: e.modal
            });
        }, this);
    },

    addInstitutionInquiry: function(institutionInquiry){
        // override url of model with the passed modelUrl option
        institutionInquiry.url = institutionInquiry.isNew() 
            ? this.modelUrl 
            : this.modelUrl+'/'+institutionInquiry.get('id'); 
        var view = new InstitutionInquiryView({model: institutionInquiry});
        institutionInquiry.view = view;
        view.render();
        
        var colEl = this.$el.find('#collection_canvass');
        colEl.append(view.$el);
    }
});

var MassApproveConfirmModal = CommonConfirmModal.extend({
    header: 'Approve Inquiries',
    body: 'Are you sure you want to approve the selected inquiries?',
    footer: '<button class="btn btn-primary confirm-approve">Approve</button><button data-dismiss="modal" class="btn">Cancel</button>',
    events: {
        'hidden': 'onHideModal',
        'click button.confirm-approve': 'onClickConfirmApprove'
    },
    onClickConfirmApprove: function(e){
        e.preventDefault();
        var btn = $(e.target);
        btn.prop('disabled', true);
        this.trigger('confirmedMassApprove', {modal: this});
    }
});

var MassDeleteConfirmModal = CommonConfirmModal.extend({
    header: 'Delete Inquiries',
    body: 'Are you sure you want to delete the selected inquiries? <b>THIS CANNOT BE UNDONE</b>',
    footer: '<button class="btn btn-primary confirm-delete">Delete</button><button data-dismiss="modal" class="btn">Cancel</button>',
    
    events: {
        'hidden': 'onHideModal',
        'click button.confirm-delete': 'onClickConfirmDelete'
    },
    
    onClickConfirmDelete: function(e){
        e.preventDefault();
        var btn = $(e.target);
        btn.prop('disabled', true);
        this.trigger('confirmedMassDelete', {modal: this});
    }
});