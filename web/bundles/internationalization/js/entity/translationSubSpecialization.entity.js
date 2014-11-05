var TranslationSubSpecialization = Backbone.Model.extend({
    defaults: {
        id: 0,
    },
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var TranslationSubSpecializationCollection = Backbone.Collection.extend({
    model: TranslationSubSpecialization,
    
    url: ApiUtility.getApiRootUrl()+'/sub-specialization-translation',
    
    parse: function(response) {
        return response['translationSubSpecializations'];
    }
});