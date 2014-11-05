var OfferedServiceCollectionItemView = Backbone.View.extend({
    tagName: 'tr',
    
    className: 'collection-item',
    
    initialize: function(options) {
        this.addTranslationUrl = options.addTranslationUrl;
    },
    
    render: function(){
        var tplData = {
            offeredService: {
                name: this.model.get('name'),
            }
        }
        var prototype = ich.offered_service_view_prototype(tplData);
        
        prototype.find('a.view-item-trigger').attr('href', this.addTranslationUrl+'/'+this.model.get('id'));
        
        this.$el.html(prototype.html());
        
    }
});