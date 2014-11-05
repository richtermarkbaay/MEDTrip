var Specialization = Backbone.Model.extend({
    defaults: {
        id: 0
    },
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var SpecializationCollection = Backbone.Collection.extend({
    model: Specialization,
    
    url: ApiUtility.getApiRootUrl()+'/specializations',
    
    parse: function(response) {
        return response['specializations'];
    }
});