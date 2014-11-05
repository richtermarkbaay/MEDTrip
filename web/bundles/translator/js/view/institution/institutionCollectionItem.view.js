var InstitutionCollectionItemView = Backbone.View.extend({
    tagName: 'tr',
    
    className: 'collection-item',
    
    initialize: function(options) {
        this.viewAllProfileUrl = options.viewAllProfileUrl;
    },
    
    render: function(){
        var tplData = {
            institution: {
                name: this.model.get('name'),
                completeAddress: this.model.completeAddressAsString()
            }
        }
        var prototype = ich.institution_view_prototype(tplData);
        
        // set link of view profile button
        prototype.find('a.view-profile-trigger').attr('href', this.viewAllProfileUrl+'/'+this.model.get('id'));
        
        this.$el.html(prototype.html());
        
    }
});