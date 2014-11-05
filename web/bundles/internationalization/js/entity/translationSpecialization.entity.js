var TranslationSpecialization = Backbone.Model.extend({
    defaults: {
        id: 0,
    },
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var TranslationSpecializationCollection = Backbone.Collection.extend({
    model: TranslationSpecialization,
    
    url: ApiUtility.getApiRootUrl()+'/specialization-translation',
    
    parse: function(response) {
        return response['translationSpecializations'];
    }
});