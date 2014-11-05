var AwardingBodyCollectionItemView = Backbone.View.extend({
    tagName: 'tr',
    
    className: 'collection-item',
    
    initialize: function(options) {
        this.addTranslationUrl = options.addTranslationUrl;
    },
    
    render: function(){
        var tplData = {
            awardingBody: {
                name: this.model.get('name'),
                details: this.model.get('details')
            }
        }
        var prototype = ich.awarding_body_view_prototype(tplData);
        
        prototype.find('a.view-item-trigger').attr('href', this.addTranslationUrl+'/'+this.model.get('id'));
        
        this.$el.html(prototype.html());
        
    }
});