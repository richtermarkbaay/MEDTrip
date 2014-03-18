var SubSpecialization = Backbone.Model.extend({
    isNew: function(){
        return this.get('id') == 0;
    }
});

var SubSpecializationCollection = Backbone.Collection.extend({
    model: SubSpecialization, 
    url: ApiUtility.getApiRootUrl()+'/sub-specializations',
    parse: function(response) {
        return response['subSpecializations'];
    }
});