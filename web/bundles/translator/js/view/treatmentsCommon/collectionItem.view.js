var TreatmentsTranslationCollectionItemView = Backbone.View.extend({
    tagName: 'tr',
    
    className: 'collection-item',    

    initialize: function(options) {
        // list of specializations page in translator module
        this.indexUrl = options.indexUrl;
    },
    
    render: function(){
        var tplData = {
            context: {
                name: this.model.get('name'),
                description: this.model.get('description')
            }
        }

        var prototype = ich.translation_view_prototype(tplData);
        
        // set link of view profile button
        prototype.find('a.view-item-trigger').attr('href', this.indexUrl+'/'+this.model.get('id'));
        
        this.$el.html(prototype.html());
    },
    
});