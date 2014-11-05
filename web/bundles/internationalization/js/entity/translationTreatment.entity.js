var TranslationTreatment = Backbone.Model.extend({
    defaults: {
        id: 0,
    },
    
    isNew: function(){
        return this.get('id') == 0;
    }
});

var TranslationTreatmentCollection = Backbone.Collection.extend({
    model: TranslationTreatment,
    
    url: ApiUtility.getApiRootUrl()+'/treatment-translation',
    
    parse: function(response) {
        return response['translationTreatments'];
    }
});