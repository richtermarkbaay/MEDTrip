var StaticContentsApp = Backbone.View.extend({
    el: $('#static_content_app_canvass'),
    
    events: {
        'click a.publish-translations-trigger': 'onClickPublishTranslations',
        'submit form#form_search_filter': 'onSubmitSearchFilter'
    },
    
    initialize: function(options) {
        
        this.staticContentCollection = new StaticContentCollection([], {
            url: options.staticContentCollectionUrl 
        });
        
        this.collectionContainer = this.$el.find('.collection-container');
        
        
        this.listenTo(this.staticContentCollection, 'add', this.onAddStaticContent);
        this.listenTo(this.staticContentCollection, 'reset', this.onResetStaticContentCollection);
        
        this.fetchCollection({});
        
    },
    
    fetchCollection: function(filters) {
        var loader = $('<tr class="info"><td colspan="4"><img src="/images/admin/loading.gif"></td></tr>');
        this.collectionContainer.html('');
        this.collectionContainer.append(loader);
        var data = {};
        
        if (filters.defaultValue) {
            data['defaultValue'] = filters.defaultValue;
        }
        
        this.staticContentCollection.fetch({
            success: function(collection, response, options){
                if (0 == collection.length) {
                    var tr = $('<tr class="warning"></tr>').append($('<td colspan="4"></td>').html('No contents.'));
                    options.collectionContainer.append(tr);
                }
                loader.remove();
            },
            collectionContainer: this.collectionContainer,
            reset: true,
            data: data
        });
        
    },
    
    onResetStaticContentCollection: function(){
        this.staticContentCollection.each(this.onAddStaticContent, this);
    },
    
    onAddStaticContent: function(staticContent) {
        var view  = new StaticContentView({
            model: staticContent
        });
        view.render();
        
        this.collectionContainer.append(view.$el);
    },
    
    onClickPublishTranslations: function (e) {
        e.preventDefault();
        
        var modal = new ConfirmPublishModal();
        modal.show();
        
    },
    
    onSubmitSearchFilter: function(e) {
        e.preventDefault();
        var form = $(e.target);
        this.fetchCollection({defaultValue: form.find('input[name="defaultValue"]').val()});
    }
});

/**
 * Publish confirmation modal
 */
var ConfirmPublishModal = Bootstrap3ConfirmModal.extend({
    
    events: {
        'hidden.bs.modal': 'onHideModal',
        'show.bs.modal': 'onShowModal',
        'submit form': 'onSubmitForm',
        'click button[type="submit"]': 'onClickConfirm'
    },
    
    el: $('#modal_confirm_publish'),
    
    onClickConfirm: function(e) {
        e.preventDefault();
        this.$el.find('form').submit();
    },
    
    onSubmitForm: function(e){
        e.preventDefault()
        var btn = this.$el.find('button[type="submit"]');
        var form = $(e.target);
        btn.prop('disabled', true)
            .html('Publishing...');
        
        var modal = this;
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            dataType: 'json',
            data: form.serialize(),
            success: function(response) {
                btn.prop('disabled', false)
                .html('Publish');
                
                var msg = new CommonFlashMessageView({ message: 'Successfully published translations.'});
                msg.show();
                
                modal.hide();
            },
            error: function(xhr) {
                btn.prop('disabled', false)
                .html('Publish');
                
                var msg = new CommonFlashMessageView({type: 'error', message: 'Failed to publish translations.'});
                msg.show();
                
                modal.hide();
            }
        });
        
        return false;
    }
    
});

/**
 * Remove Translation confirmation modal
 */
var ConfirmRemoveTranslationModal = Bootstrap3ConfirmModal.extend({
    el: $('#modal_confirm_delete_translation'),

    initialize: function(options) {
        this.translation = options.translation;
    },

    onClickConfirm: function(e) {
       e.preventDefault();
       
       this.translation.destroy();
       this.hide();
    },
});
