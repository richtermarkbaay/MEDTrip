var StaticContentTranslationView = Backbone.View.extend({
    
    tagName: 'div',
    
    className: 'well well-sm',
    
    events: {
        'click a.edit-translation-trigger': 'onClickEditTranslation',
        'click a.delete-translation-trigger': 'onClickDeleteTranslation'
    },
    
    initialize: function(){
        this.listenTo(this.model, 'change', this.render);
        this.listenTo(this.model, 'destroy', this.remove);
    },
    
    render: function() {
        var tplData = {
            staticContentTranslation: this.model.attributes
        };
        
        var prototype = ich.static_content_translation_view_prototype(tplData);
        this.$el.html(prototype.html());
    },
    
    onClickEditTranslation: function(e){
        var modal = new StaticContentTranslationModalFormView({
            model: this.model
        });
        
        modal.show();
    },
    
    onClickDeleteTranslation: function(e) {
        e.preventDefault();

        var modal = new ConfirmRemoveTranslationModal({
            translation: this.model
        });

        modal.show()
    }
});