var TranslationInstitution = Backbone.Model.extend({
    defaults: {
        id: 0,
    },
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var TranslationInstitutionCollection = Backbone.Collection.extend({
    model: TranslationInstitution,
    
    url: ApiUtility.getApiRootUrl()+'/institution-translation',
    
    parse: function(response) {
        return response['translationInstitutions'];
    }
});