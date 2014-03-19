var InstitutionSpecialization = Backbone.Model.extend({
    isNew: function(){
        return this.get('id') == 0;
    }
});

var InstitutionSpecializationCollection = Backbone.Collection.extend({
    model: InstitutionSpecialization,
    
    url: ApiUtility.getApiRootUrl()+'/institution-specializations',
    
    parse: function(response) {
        return response['institutionSpecializations'];
    }
});

