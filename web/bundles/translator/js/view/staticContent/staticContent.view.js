
var StaticContentView = Backbone.View.extend({
    tagName: 'tr',
    
    className: 'entity-view',
    
    events: {
        'click .add-translation-trigger': 'onClickAddTranslationTrigger'
    },
    
    initialize: function(options) {
        this.staticContentTranslationCollection = new StaticContentTranslationCollection();
        
        this.listenTo(this.staticContentTranslationCollection, 'reset', this.onResetTranslationCollection);
        this.listenTo(this.staticContentTranslationCollection, 'add', this.onAddTranslation);
        
        
    },
    
    render: function() {
        var tplData = {
            staticContent: this.model.attributes
        };
        
        var prototype = ich.static_content_view_prototype(tplData);
        
        this.$el.html(prototype.html());
        
        // render translations
        this.staticContentTranslationCollection.reset(this.model.get('static_content_translations'));
    },
    
    onResetTranslationCollection: function() {
        this.$el.find('.static-content-translations-container').html('');
        this.staticContentTranslationCollection.each(this.onAddTranslation, this);
    },
    
    onAddTranslation: function(staticContentTranslation) {
        staticContentTranslation.set('staticContent', this.model.attributes);
        var view = new StaticContentTranslationView({model: staticContentTranslation});
        view.render();
        
        this.$el.find('.static-content-translations-container').append(view.$el);
    },
    
    onClickAddTranslationTrigger: function(e) {
        e.preventDefault();
        
        var staticContentTranslation = new StaticContentTranslation(
            {staticContent: this.model.attributes}, 
            {collection: this.staticContentTranslationCollection}
        );
        
        var modal = new StaticContentTranslationModalFormView({
            model: staticContentTranslation
        });
        
        modal.show();
    }
});