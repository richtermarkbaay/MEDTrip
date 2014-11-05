var MedicalCenterCollectionItemView = Backbone.View.extend({
    tagName: 'tr',
    
    className: 'collection-item',    

    initialize: function(options) {
        this.viewUrl = options.viewUrl;
        this.prototypeId = options.prototypeId;
    },
    
    render: function(){
        var tplData = {
            medicalCenter: {
                name: this.model.get('name'),
                description: this.model.get('description'),
                address: this.model.getAddressAsString()
            }
        }

        var prototype = ich.medicalCenter_view_prototype(tplData);
        
        // set link of view profile button
        prototype.find('a.view-profile-trigger').attr('href', this.viewUrl+'/'+this.model.get('id'));
        
        this.$el.html(prototype.html());
    },
    
});