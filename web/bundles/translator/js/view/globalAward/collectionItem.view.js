var GlobalAwardCollectionItemView = Backbone.View.extend({
    tagName: 'tr',
    
    className: 'collection-item',
    
    initialize: function(options) {
        this.addTranslationUrl = options.addTranslationUrl;
    },
    
    render: function(){
        var tplData = {
            globalAward: {
                name: this.model.get('name'),
                details: this.model.get('details')
            }
        }
        var prototype = ich.global_award_view_prototype(tplData);
        
        prototype.find('a.view-item-trigger').attr('href', this.addTranslationUrl+'/'+this.model.get('id'));
        
        this.$el.html(prototype.html());
        
    }
});