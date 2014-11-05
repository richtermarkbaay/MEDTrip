var BaseTranslationView = Backbone.View.extend({
    tagName: 'div',
    
    className: 'tab-pane fade',

    events: {
        'click a.edit-trigger': 'onEditTranslation'
    },

    initialize: function(options) {
        this.language = options.language;
        this.context = options.context;
        this.$el.attr('id', this.language.iso+'_content');
        
        this.customInitialization(options);

        this.listenTo(this.model, 'change', this.render);
    },
    
    render: function(){
        var isEditable = true;

        if ('en' == this.language.iso) {
            // set default tab to english
            this.$el.addClass('active in');
            isEditable = false;
        }

        var tplData = this.getTemplateData(this.model, this.language, this.context, isEditable);
        
        var prototype = ich.translation_view_prototype(tplData);
        this.$el.html(prototype.html());
    },

    onEditTranslation: function(e){
        e.preventDefault();
        
        var modal = this.getFormModal(this.model, this.language, this.context);
        
        modal.show();
    },
    
    // for initialization of extending view
    customInitialization: function(options){
        
    },
    
    // override
    getTemplateData: function(model, language, context){
        
    },
    
    // override
    getFormModal: function(model, language, context, isEditable){
        
    }
});